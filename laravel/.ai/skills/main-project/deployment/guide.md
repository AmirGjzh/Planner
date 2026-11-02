# VPS Deployment Guide

> Reference documentation. A complete, ordered path from a fresh Ubuntu 24.04
> VPS (1 vCPU, 2 GB RAM) and a domain to a running Planner installation:
> hardened host, Cloudflare, TLS, real email, backups, monitoring, performance
> and rollback.

**See also:** [production stack](../docker/production.md) · [shared docker conventions](../docker/shared-conventions.md) · [docs map](../SKILL.md)

## Purpose and Audience

Describes a single-VPS deployment behind Cloudflare: one Ubuntu 24.04 host
running the existing `planner` compose stack, a first-level subdomain for the
app, TLS terminated at Cloudflare with an origin certificate, and transactional
email through an SMTP provider. Not user documentation.

[`docker/production.md`](../docker/production.md) remains the reference for the
compose services themselves; this guide is the end-to-end runbook around them.

## 0. Before you start

### 0.1 What you need

- A domain with registrar access (nameserver changes).
- An Ubuntu 24.04 LTS VPS with 1 vCPU, 2 GB RAM, root access and a public IPv4.
- A Cloudflare account (free plan is enough).
- An SMTP provider account; Resend is the worked example.
- An SSH key pair on your workstation.
- A local terminal (Linux, macOS, WSL or PowerShell with `ssh`).

### 0.2 Decisions

| Decision | Recommended | Alternative |
| --- | --- | --- |
| Edge | Cloudflare proxied (orange) | DNS-only (grey) |
| Origin certificate | Cloudflare Origin CA | Let's Encrypt |
| App host | `planner.example.com` | apex `example.com` |
| Mail | Resend | any SMTP provider |

The rest of the guide assumes the recommended path. The DNS-only/Let's Encrypt
branch is documented where it differs.

### 0.3 Conventions

- Placeholders: `example.com` (apex), `planner.example.com` (app),
  `send.example.com` (sending subdomain), `<VPS_IP>`, `<owner>/<repo>`.
- Commands run as root or with `sudo` on Ubuntu 24.04 (`apt`, `systemd`, `ufw`,
  Docker's Ubuntu repository).
- Every step ends with **Expect** — the observable result. If it does not match,
  stop and fix it before continuing.
- The repository lives at `/opt/planner`; the app is `/opt/planner/laravel`.

## 1. Harden the VPS

Target: **Ubuntu 24.04 LTS, 1 vCPU, 2 GB RAM**. Nothing is exposed yet; this
section produces a host that can safely run a public service.

### 1.1 First login and OS check

```bash
ssh root@<VPS_IP>
cat /etc/os-release
nproc
free -h
```

**Expect:** `VERSION_ID="24.04"`, `nproc` prints `1`, and `free -h` shows about
2 GB of RAM.

### 1.2 Update and reboot

```bash
apt-get update && apt-get -y upgrade
```

If a kernel was updated, reboot before continuing:

```bash
reboot
```

Reconnect and confirm the new kernel:

```bash
uname -r
```

**Expect:** the command succeeds and the kernel is the freshly installed one.

### 1.3 Host timezone

The app's own timezone follows the signed-in user's locale; the host clock only
affects system logs.

```bash
timedatectl set-timezone UTC
date -u
```

**Expect:** `date -u` shows UTC.

### 1.4 Swap

Check free disk first (`df -h /`). Add 2 GB:

```bash
fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo '/swapfile none swap sw 0 0' >>/etc/fstab
printf 'vm.swappiness=10\n' >/etc/sysctl.d/99-swap.conf
sysctl --system
```

**Expect:** `swapon --show` lists `/swapfile` with 2G and `free -h` shows swap
available.

### 1.5 Deploy user

```bash
adduser --gecos "" deploy
usermod -aG sudo deploy
passwd deploy
```

Set a real password with `passwd` — `sudo` asks for it. Do not lock the account.

**Expect:** `id deploy` shows the user and its `sudo` group.

### 1.6 SSH keys and hardening

From your workstation:

```bash
ssh-copy-id deploy@<VPS_IP>
```

Then test before changing anything:

```bash
ssh deploy@<VPS_IP>
sudo -v
```

On the server, add the drop-in:

```bash
install -d -m 755 /etc/ssh/sshd_config.d
cat >/etc/ssh/sshd_config.d/99-hardening.conf <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
PubkeyAuthentication yes
EOF
sshd -t
systemctl reload ssh
```

Ubuntu cloud images also ship `50-cloud-init.conf`; `99-hardening.conf` sorts
after it, so these values win.

Keep the current root session open until a second session as `deploy` works.
**Expect:** `ssh deploy@<VPS_IP>` works, `sudo -v` succeeds, and
`ssh root@<VPS_IP>` is refused. Recovery is the provider console.

### 1.7 Firewall

Only 22, 80 and 443 are inbound.

```bash
apt-get install -y ufw
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
ufw status verbose
```

**Expect:** the active rules list only 22/80/443 inbound.

Docker publishes ports through its own iptables chains, bypassing ufw. The
compose file is the source of truth for what is public; the server edits in
section 5 keep it to 80/443.

### 1.8 fail2ban

```bash
apt-get install -y fail2ban
```

`/etc/fail2ban/jail.local`:

```ini
[DEFAULT]
backend = systemd
bantime = 1h
findtime = 10m
maxretry = 5

[sshd]
enabled = true
```

```bash
systemctl enable --now fail2ban
fail2ban-client status sshd
```

**Expect:** the `sshd` jail is active with the configured thresholds.

### 1.9 Automatic security updates

```bash
apt-get install -y unattended-upgrades
cat >/etc/apt/apt.conf.d/20auto-upgrades <<'EOF'
APT::Periodic::Update-Package-Lists "1";
APT::Periodic::Unattended-Upgrade "1";
EOF
systemctl enable --now unattended-upgrades
```

**Expect:** the service is enabled (`systemctl status unattended-upgrades`).

### 1.10 Docker Engine

```bash
apt-get install -y curl ca-certificates
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu noble stable" >/etc/apt/sources.list.d/docker.list
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
systemctl enable --now docker
```

Add the deploy user (the docker group is root-equivalent):

```bash
usermod -aG docker deploy
```

Log out and back in, then:

```bash
docker --version
docker compose version
docker run --rm hello-world
```

**Expect:** all three succeed. On 1 vCPU the Docker install is quick; the
application build in section 6 is the slow part.

### 1.11 Reboot and verify

```bash
reboot
# after reconnecting
swapon --show
systemctl is-active docker
ufw status verbose
fail2ban-client status sshd
ssh deploy@<VPS_IP> 'docker ps'
```

**Expect:** swap active, Docker running, only 22/80/443 open, fail2ban active,
and `deploy` can run Docker.
## 2. Domain, DNS and certificate

Do this while the VPS is being prepared; nameserver propagation is the slowest
step.

### 2.1 Cloudflare zone

1. Cloudflare → **Add a domain** → `example.com` → Free plan.
2. At the registrar, replace both nameservers with Cloudflare's pair.

**Expect:** the zone shows *Pending Nameserver Update* until propagation, then
**Active**. `dig +short NS example.com @1.1.1.1` returns the two
`*.ns.cloudflare.com` names. ccTLDs such as `.ir` can take 24–48 hours.

### 2.2 DNS records

**DNS → Records → Add record**: type `A`, name `planner`, content `<VPS_IP>`,
TTL `Auto`, **Proxy status: DNS only**.

Add `AAAA` only if the VPS has a working IPv6 address (`ip -6 addr show scope
global`), same name and **DNS only**.

**Expect:** grey clouds; `dig +short A planner.example.com @1.1.1.1` returns
`<VPS_IP>`.

If `dig` is missing: `apt-get install -y dnsutils`.

Keep the proxy off until the origin answers HTTPS.

### 2.3 Certificate choice

| Proxy | Certificate | Trusted by | Renewal |
| --- | --- | --- | --- |
| Proxied | Cloudflare Origin CA | Cloudflare only | 15 years, none |
| DNS-only | Let's Encrypt | Browsers | 90 days, automatic |

Pick one now; section 5 installs it and section 7 turns the proxy on (proxied
path only).

### 2.4 Origin CA (proxied)

**SSL/TLS → Origin Server → Origin Certificates → Create Certificate**:
hostnames `example.com` and `*.example.com`, validity 15 years, PEM key format.
Save the certificate and private key as `origin.pem` and `origin.key`.

**Expect:** the certificate is listed with both hostnames and a 15-year expiry.
Cloudflare does not send expiry notices for origin certificates; track the date.

### 2.5 Let's Encrypt (DNS-only)

Use DNS-01 with a Cloudflare API token that has `Zone:DNS:Edit`:

```bash
apt-get install -y certbot python3-certbot-dns-cloudflare

mkdir -p /root/.secrets
cat >/root/.secrets/cloudflare.ini <<'EOF'
dns_cloudflare_api_token = <token>
EOF
chmod 600 /root/.secrets/cloudflare.ini

certbot certonly --dns-cloudflare \
  --dns-cloudflare-credentials /root/.secrets/cloudflare.ini \
  -d planner.example.com --agree-tos -m you@example.com --non-interactive
```

**Expect:** files under `/etc/letsencrypt/live/planner.example.com/`, and
`systemctl list-timers | grep certbot` shows the renewal timer.

### 2.6 SSL/TLS mode

With the proxy on, **SSL/TLS → Overview** must be **Full (strict)**. Never use
**Flexible**: it sends plaintext to the origin and creates redirect loops with
an origin HTTPS redirect.

With DNS-only, Cloudflare is not in the request path and the browser validates
the Let's Encrypt certificate directly.

**Expect:** proxied shows *Full (strict)*; DNS-only has no Cloudflare mode to
set.

## 3. Mail domain

The app gates every page behind email verification, so mail must work before
the first user registers.

### 3.1 Add the sending subdomain

Resend → **Domains → Add Domain** → `send.example.com` (a subdomain keeps the
root domain's reputation separate) → choose the region closest to your users.

**Expect:** the domain is listed as *Pending* with a set of DNS records.

### 3.2 SPF, DKIM and MX

Copy every record Resend shows into **Cloudflare → DNS → Records**, exactly as
given, with **Proxy status: DNS only**. TXT records are never proxied; MX
records cannot be.

**Expect:** Resend reports **Verified** and `dig +short TXT send.example.com`
plus `dig +short TXT resend._domainkey.send.example.com` return the issued
values. If Resend issued an MX, `dig +short MX send.example.com` shows it.

### 3.3 DMARC

**DNS → Records → Add record**: type `TXT`, name `_dmarc`, content:

```text
v=DMARC1; p=none; rua=mailto:dmarc@example.com; adkim=r; aspf=r
```

Start at `p=none` and read the reports; move to `p=quarantine` once only your
own traffic appears.

**Expect:** `dig +short TXT _dmarc.example.com` returns the policy.

### 3.4 API key

Resend → **API Keys → Create**: **Sending access**, restricted to the verified
domain. Copy the key once; it is the SMTP password.

**Expect:** the key starts with `re_` and is stored in your password manager.

## 4. Get the code

### 4.1 Read-only deploy key

As `deploy` on the server:

```bash
sudo mkdir -p /opt/planner
sudo chown deploy:deploy /opt/planner

ssh-keygen -t ed25519 -C "planner-deploy" -f ~/.ssh/planner_deploy -N ""
cat ~/.ssh/planner_deploy.pub
```

GitHub → repository → **Settings → Deploy keys → Add deploy key**: paste the
public key, leave **Allow write access** off.

```bash
cat >>~/.ssh/config <<'EOF'
Host github.com
  IdentityFile ~/.ssh/planner_deploy
  IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config

ssh -T git@github.com
git clone git@github.com:<owner>/<repo>.git /opt/planner
```

**Expect:** `ssh -T` says the key authenticated (GitHub does not provide a
shell), and `/opt/planner/planner` exists.

### 4.2 Environment

```bash
cd /opt/planner/laravel
cp .env.production .env
chmod 600 .env

printf 'base64:%s\n' "$(openssl rand -base64 32)"   # APP_KEY
openssl rand -hex 24                                # run three times: DB, MariaDB root, Redis
```

Edit `.env` and set:

```ini
APP_URL=https://planner.example.com
SESSION_SECURE_COOKIE=true

DB_PASSWORD=<hex>
MARIADB_ROOT_PASSWORD=<hex>
REDIS_PASSWORD=<hex>

MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_USERNAME=resend
MAIL_PASSWORD=re_...
MAIL_SCHEME=smtps
MAIL_FROM_ADDRESS=no-reply@send.example.com
MAIL_FROM_NAME=Planner
```

`APP_ENV=production`, `APP_DEBUG=false` and the Redis connections come from the
template. Paste the `APP_KEY` output without the trailing newline. Use `587` +
`MAIL_SCHEME=smtp` if outbound 465 is blocked.

**Expect:** `grep -E 'APP_URL|MAIL_HOST|SESSION_SECURE' .env` shows the values,
and `stat -c '%a' .env` is `600`.

### 4.3 Ownership

```bash
sudo chown deploy:deploy /opt/planner/laravel/.env
```

**Expect:** `ls -l .env` shows `deploy deploy` and mode `600`.

## 5. Server configuration (uncommitted)

These edits are applied to the VPS checkout and never committed: GitHub keeps
the LAN defaults (HTTP, Mailpit). The guide is their record, and section 10.2
shows how to carry them across a `git pull`.

### 5.1 Cloudflare real-IP file

```bash
cd /opt/planner/laravel
{
  echo '# Generated from https://www.cloudflare.com/ips/'
  curl -fsS https://www.cloudflare.com/ips-v4 | sed 's/^/set_real_ip_from /; s/$/;/'
  curl -fsS https://www.cloudflare.com/ips-v6 | sed 's/^/set_real_ip_from /; s/$/;/'
  echo 'real_ip_header CF-Connecting-IP;'
  echo 'real_ip_recursive on;'
} >cloudflare-ips.conf
```

Re-run when Cloudflare publishes a range change, then
`docker compose restart nginx`.

**Expect:** the file starts with the generated comment and contains
`set_real_ip_from` lines plus `real_ip_header CF-Connecting-IP;`.

### 5.2 Nginx vhost

Replace `docker/production/nginx/conf.d/default.conf` with:

```nginx
include /etc/nginx/cloudflare-ips.conf;

server {
    listen 80;
    listen [::]:80;

    server_name _;

    # Keeps the compose healthcheck meaningful: nginx itself answers here.
    location = /up {
        access_log off;
        return 200 "ok\n";
    }

    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;

    server_name _;

    ssl_certificate /etc/nginx/certs/origin.pem;
    ssl_certificate_key /etc/nginx/certs/origin.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;

    root /var/www/public;
    index index.php;

    charset utf-8;
    server_tokens off;
    client_max_body_size 10m;

    gzip on;
    gzip_vary on;
    gzip_comp_level 6;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/javascript application/xml image/svg+xml font/woff font/woff2;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()";

    # Enable only in section 7.5, after HTTPS is proven:
    # add_header Strict-Transport-Security "max-age=15552000" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location /build {
        try_files $uri =404;
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";
        add_header Referrer-Policy "strict-origin-when-cross-origin";
        add_header Permissions-Policy "camera=(), microphone=(), geolocation=()";
        add_header Cache-Control "public, max-age=31536000, immutable";
        access_log off;
    }

    error_page 404 /index.php;

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php-fpm:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param HTTP_X_FORWARDED_FOR $remote_addr;
        fastcgi_param HTTP_X_FORWARDED_PROTO $scheme;
        include fastcgi_params;
        fastcgi_buffer_size 32k;
        fastcgi_buffers 8 32k;
        fastcgi_busy_buffers_size 64k;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Expect:** the file contains two `server` blocks, the 443 one references
`/etc/nginx/certs/origin.pem` and `/etc/nginx/certs/origin.key`, and HSTS is
commented out.

### 5.3 Compose

In `laravel/compose.production.yml`:

```yaml
  nginx:
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./certs:/etc/nginx/certs:ro
      - ./cloudflare-ips.conf:/etc/nginx/cloudflare-ips.conf:ro
```

Then remove Mailpit from the server stack: delete the whole `mailpit:` service
and the `mailpit:` entry from `depends_on` in **both** `php-fpm` and `queue`.

**Expect:** `docker compose -f compose.production.yml config` succeeds and
mentions `443`, `certs`, `cloudflare-ips.conf`, and no `mailpit`.

### 5.4 Certificates

```bash
cd /opt/planner/laravel
mkdir -p certs
```

Origin CA: from your workstation, copy the two files saved in section 2.4:

```bash
scp origin.pem origin.key deploy@<VPS_IP>:/opt/planner/laravel/certs/
```

Let's Encrypt: copy the issued files to the paths the vhost expects:

```bash
sudo cp /etc/letsencrypt/live/planner.example.com/fullchain.pem certs/origin.pem
sudo cp /etc/letsencrypt/live/planner.example.com/privkey.pem   certs/origin.key
sudo chown deploy:deploy certs/origin.pem certs/origin.key
chmod 600 certs/origin.key
```

**Expect:** `ls -l certs/` shows both files; the key is mode `600`; both paths
match the vhost.

## 6. First deploy

### 6.1 Build and start

The build is the memory peak, and on one vCPU it is CPU-bound too; the stack
must not be serving while it runs.

```bash
cd /opt/planner
./planner down          # no-op on a fresh host
./planner build
./planner up
./planner ps
```

If the build is killed, confirm swap (`swapon --show`), run
`docker builder prune -f`, and retry. Building on another machine and shipping
the image is the fallback.

**Expect:** both images build, and `./planner ps` shows `php-fpm`, `queue`,
`nginx`, `mariadb` and `redis` up (healthy where a healthcheck exists). `down`
never removes volumes; never run `down -v`.

### 6.2 Migrations and boot

The php-fpm entrypoint runs `php artisan migrate --seed --force` and
`php artisan optimize` before starting FPM.

```bash
./planner logs php-fpm
docker exec planner-php-fpm php artisan migrate:status
```

**Expect:** the log has no fatal error, and every migration is marked `Ran`.

### 6.3 Verify

```bash
curl -kI https://127.0.0.1/up
curl -kI --resolve planner.example.com:443:<VPS_IP> https://planner.example.com/up
docker exec planner-nginx nginx -t
```

**Expect:** `200` from both `/up` calls (port 80 answers it from nginx for the
healthcheck, so HTTPS is what proves PHP and the database), and `nginx -t`
reports syntax is ok. `-k` is needed for the Origin CA branch.

### 6.4 Smoke test

Open `https://planner.example.com`. With Origin CA the browser warns until the
proxy is on — accept it. With Let's Encrypt the certificate is trusted.

1. Register a new account.
2. Open the verification mail in a real inbox (check spam), follow the link.
3. Sign in; the dashboard renders.
4. Create a task; mark it done; check the dashboard.
5. Sign out, request a password reset, follow the mail, set a new password,
   sign in again.

**Expect:** the full loop works and every link is `https://`. A failure here is
usually SMTP (check Resend → Emails), `APP_URL`, or the queue worker.

### 6.5 Queue

Both verification and reset notifications are queued.

```bash
docker top planner-queue
docker exec planner-php-fpm php artisan tinker --execute 'dump(DB::table("failed_jobs")->count());'
```

**Expect:** the `queue:work` process is present and `failed_jobs` is zero.

## 7. Cloudflare proxy and edge

> DNS-only path: skip this section. Without the proxy there is no edge cache,
> real-IP header or Cloudflare WAF; enable HSTS in nginx instead, and only after
> HTTPS is proven.

The origin is serving HTTPS; the record is still grey. This section turns the
proxy on and tunes the edge. The real-IP gate is mandatory before inviting
users.

### 7.1 Proxy on

**DNS → Records → `planner` → Edit → Proxy status: Proxied → Save.**

Then:

- **SSL/TLS → Overview**: **Full (strict)**.
- **SSL/TLS → Edge Certificates**: **Always Use HTTPS On**, **Minimum TLS 1.2**,
  **TLS 1.3 On**.
- **Speed → Settings → Content Optimization**: **Rocket Loader Off**. It is off
  by default; check it, because Cloudflare's one-click speed recommendations can
  enable it and it defers scripts, breaking Livewire/Alpine.

Leave 0-RTT, Early Hints, Speed Brain and Cloudflare Fonts at their defaults;
none helps this app, and 0-RTT is replayable.

**Expect:** `dig +short planner.example.com` returns Cloudflare anycast
addresses instead of `<VPS_IP>`; `curl -sI https://planner.example.com` shows
`server: cloudflare` and a `cf-ray` header; the certificate issuer is a public
CA (Google Trust Services or Let's Encrypt), not the Origin CA; Rocket Loader is
off.

The proxy hides the origin from DNS, not from scanners. To close direct-to-IP
access, allow 80/443 only from Cloudflare's ranges at the provider firewall (or
in `DOCKER-USER`, since published ports bypass ufw). Optional, but that is what
"protected by Cloudflare" means.

### 7.2 Cache rules

**Caching → Cache Rules.** Two rules, mutually exclusive:

1. **Bypass app responses** — expression
   `not starts_with(http.request.uri.path, "/build/")` → **Bypass cache**.
   HTML, Livewire endpoints and everything dynamic never enter the edge cache.
2. **Cache build assets** — expression
   `starts_with(http.request.uri.path, "/build/")` → **Eligible for cache**,
   Edge TTL one year, Browser TTL **Respect origin TTL**. Nginx already sends
   `immutable` for `/build`.

Development Mode stays off.

**Expect:**

```bash
curl -sI https://planner.example.com/ | grep -i cf-cache-status        # DYNAMIC or BYPASS
ASSET=$(curl -s https://planner.example.com/ | grep -oE '/build/[^"]+\.(css|js)' | head -1)
# empty ASSET? copy a /build/... path from the page source instead
curl -sI "https://planner.example.com$ASSET" | grep -iE 'cf-cache-status|cache-control'
```

HTML is not `HIT`; the asset is `HIT` with `immutable`.

### 7.3 Real client IP gate

Without this, every `{ip}` bucket (`login:`, `forgot-password:`, `register:`)
collapses onto Cloudflare edge addresses and locks unrelated users out.

```bash
docker exec planner-nginx nginx -T | grep -E 'real_ip|set_real_ip_from'
curl -s https://planner.example.com/cdn-cgi/trace | grep '^ip='
```

`cdn-cgi/trace` shows the address Cloudflare sees. Fail one login (wrong
password), then read the log:

```bash
docker exec planner-php-fpm sh -c "grep -h 'Login failed' storage/logs/laravel-*.log | tail -1"
```

**Expect:** the JSON context contains `"ip":"<your public IP>"`. If it shows
`104.x` or `172.64–71.x`, the include is missing or not mounted — fix it before
anyone else signs in. Five failed logins must trip the app's own limiter on the
sixth, per visitor.

### 7.4 Optional: WAF rate limits and bots

**Security → Security rules → Create rule → Rate limiting rules**:

- `POST /login` — 10 requests per minute per IP, action Block or Managed
  Challenge, duration 10 minutes.
- `POST /livewire/update` — a generous cap (for example 240 per minute per IP),
  because every filter keystroke and modal action hits it.

The Free plan limits how many rate-limiting rules exist; start with `/login` if
only one is available.

Bot Fight Mode (**Security → Settings → filter Bot traffic**) is optional, cannot
be bypassed by WAF rules, and may challenge uptime monitors or non-browser
clients. Leave it off unless you are watching the errors.

**Expect:** the rule appears in Security rules and a burst above the threshold
is blocked/challenged.

### 7.5 HSTS last

Only after the app has run behind the proxy for a while:

**SSL/TLS → Edge Certificates → HSTS** → Enable, max-age six months
(`15552000`), **includeSubDomains off** unless every subdomain is HTTPS, preload
off until certain.

**Expect:** `curl -sI https://planner.example.com | grep -i
strict-transport-security` returns the header. Browsers cache it for the
max-age; there is no server-side undo.

## 8. Reliability

### 8.1 Nightly backup

Back up the database, the `planner_storage` volume, and `.env` (the `APP_KEY`
cannot be regenerated without breaking encrypted data and sessions).

`/usr/local/bin/planner-backup`:

```bash
#!/bin/sh
set -eu
STAMP=$(date -u +%Y%m%dT%H%M%SZ)
DIR=/var/backups/planner
mkdir -p "$DIR"
chmod 700 "$DIR"

docker exec planner-mariadb sh -c 'mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction --routines --triggers --events "$MARIADB_DATABASE"' | gzip >"$DIR/db-$STAMP.sql.gz"

docker run --rm -v planner_storage:/data:ro -v "$DIR":/backup alpine tar czf "/backup/storage-$STAMP.tgz" -C /data .

tar czf "$DIR/env-$STAMP.tgz" -C /opt/planner/laravel .env
chmod 600 "$DIR"/*

rclone copy "$DIR" remote:planner-backups --include "*-$STAMP*"

find "$DIR" -type f -mtime +14 -delete
```

- `--single-transaction` gives a consistent InnoDB snapshot without locking
  writes.
- `rclone` pushes offsite; use a `crypt` remote or an encrypted bucket — the
  dump contains password hashes and personal data.
- Local retention is 14 days; tune the offsite lifecycle separately.
- Install `rclone` and configure the remote as root; the job runs as root:

```bash
apt-get install -y rclone
```

Schedule it with `/etc/cron.d/planner-backup`:

```cron
15 3 * * * root /usr/local/bin/planner-backup >>/var/log/planner-backup.log 2>&1
```

**Expect:** after the first run, `ls -lh /var/backups/planner/` shows the three
files, `gzip -t` passes, and `rclone lsf remote:planner-backups` lists copies.

### 8.2 Restore drill

Restore into a scratch database, never over production:

```bash
docker exec planner-mariadb sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS planner_restore; CREATE DATABASE planner_restore;"'
gunzip -c /var/backups/planner/db-<stamp>.sql.gz | docker exec -i planner-mariadb sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" planner_restore'
docker exec planner-mariadb sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" planner_restore -e "SELECT COUNT(*) FROM users; SELECT COUNT(*) FROM tasks; SELECT COUNT(*) FROM plans; SELECT COUNT(*) FROM categories;"'
```

Compare the counts with production, spot-check a known task and plan, then drop
the scratch database. Time the restore — that duration feeds the RTO.

```bash
mkdir -p /tmp/storage-restore
tar xzf /var/backups/planner/storage-<stamp>.tgz -C /tmp/storage-restore
ls /tmp/storage-restore
```

A full restore replaces production: stop `php-fpm` and `queue`, take a fresh
backup, recreate the database, import, start the services, run the smoke test.
Record the date, file, result and duration; repeat quarterly and after schema
changes.

**Expect:** restored row counts match, the storage archive extracts, and the
drill is recorded.

### 8.3 Uptime and alerts

- **Uptime**: an external monitor (UptimeRobot, Better Stack, or Cloudflare
  Health Checks on paid plans) hitting `https://planner.example.com/up` every
  one to five minutes, alerting by email/webhook. HTTPS covers PHP and the
  database, not just nginx.
- **Disk/memory**: provider resource alerts, or a small cron that mails at 85%
  disk or a memory threshold.
- **Restarts**: `docker inspect -f '{{.RestartCount}}' planner-php-fpm
  planner-queue planner-nginx` after an incident; `docker events` while
  debugging.
- Keep Bot Fight Mode off for the monitor's path, or the check reports false
  downtime.

**Expect:** the monitor is green and its alert fired once in a test.

### 8.4 Logs

```bash
docker exec planner-php-fpm php artisan tinker --execute 'dump(config("logging.channels.daily.days"));'
docker inspect -f '{{.HostConfig.LogConfig.Config}}' planner-php-fpm
```

**Expect:** `14` days for the daily channel and the compose `10m`/`3` Docker
log caps. Rotate `/var/log/planner-backup.log` too; logs contain emails and IPs.

### 8.5 RPO and RTO

| Metric | Target | How |
| --- | --- | --- |
| RPO | 24 hours | nightly dump |
| RTO | about one hour | rebuild host, restore DB, start stack |

If 24 hours is too much, run the dump hourly or enable MariaDB binary logs for
point-in-time recovery; both are deliberate upgrades.

**Expect:** both numbers are written down and accepted.

## 9. Performance

### 9.1 Application cache and OPcache

`php artisan optimize` runs on every php-fpm boot; the base image ships OPcache.

```bash
docker exec planner-php-fpm php -v
docker exec planner-php-fpm php -i | grep -E 'opcache.enable|opcache.validate_timestamps'
docker exec planner-php-fpm php artisan about
```

**Expect:** `with Zend OPcache`, `opcache.enable => On`, and config/routes/views
reported as cached. An `.env` change needs a container recreate; editing the
file alone changes nothing in a running container.

### 9.2 Edge cache and compression

`/build` is cached a year (`immutable`); everything else bypasses. Cloudflare
negotiates Brotli or Zstandard with the browser; the origin gzips text for the
Cloudflare hop.

```bash
curl -sI https://planner.example.com/ | grep -iE 'cf-cache-status|content-encoding'
ASSET=$(curl -s https://planner.example.com/ | grep -oE '/build/[^"]+\.(css|js)' | head -1)
curl -sI "https://planner.example.com$ASSET" | grep -iE 'cf-cache-status|content-encoding|cache-control'
```

**Expect:** HTML not `HIT`; the asset `HIT` with `immutable`; `content-encoding`
`br` or `zstd` on larger responses.

### 9.3 Queue

```bash
docker top planner-queue
docker exec planner-php-fpm php artisan tinker --execute 'dump(DB::table("failed_jobs")->count());'
```

**Expect:** the worker process is present and `failed_jobs` is zero. A dead
worker only backs up mail; the uptime check will not catch it.

### 9.4 Database

The migrations already carry the indexes this workload needs: composites led by
`user_id` on `(user_id, task_date)` and `(user_id, done)`, the per-user unique
`(user_id, name)` on categories and plans, and single-column indexes on
`plan_id`/`category_id`.

At personal scale nothing needs adding. If a page feels slow, `EXPLAIN` that
query before creating an index.

### 9.5 The 1 vCPU / 2 GB host

Never build while serving; on one core the build also competes for CPU:

```bash
cd /opt/planner
./planner down
./planner build
./planner up
```

PHP-FPM keeps the image defaults. One core is shared by every worker; the
defaults are still right for a personal planner. Tune `pm.max_children` only
when metrics say so — 502s under normal load, or workers at the memory ceiling.
Watch with `free -h` and `docker stats --no-stream`.

**Expect:** builds complete without OOM, and normal load leaves headroom.

### 9.6 Baseline

```bash
curl -s -o /dev/null -w 'ttfb=%{time_starttransfer}s total=%{time_total}s\n' https://planner.example.com/
```

**Expect:** a number you can compare after upgrades; direction matters more
than one reading.

## 10. Go-live and operations

### 10.1 Checklist

| Area | Check |
| --- | --- |
| HTTPS | `curl -I http://planner.example.com` → 301; `https` → 200 |
| Cookies | secure, HttpOnly, SameSite after login |
| Debug | `config("app.debug")` → `false` |
| Mail | verification + reset to a real inbox |
| Backups | local + offsite, restore drilled |
| Monitoring | uptime green, alert fired in a test |
| Secrets | `.env` mode `600` |
| Ports | only 22/80/443 public |
| Queue | `queue:work` running |
| Edge | real client IP in the login log |

Final product pass: register, verify, log in, create a task, open dashboard and
reports, log out, reset the password.

### 10.2 Update flow

```bash
cd /opt/planner

/usr/local/bin/planner-backup

docker tag planner-app:latest planner-app:previous || true
docker tag planner-nginx:latest planner-nginx:previous || true

git stash push docker/production/nginx/conf.d/default.conf compose.production.yml
git pull
git stash pop

./planner down
./planner build
./planner up

curl -kI https://127.0.0.1/up
./planner ps
```

Then log in and toggle a task. Confirm migrations:

```bash
docker exec planner-php-fpm php artisan migrate:status
```

The stash carries the uncommitted vhost/compose edits; `.env`, `certs/` and
`cloudflare-ips.conf` are ignored. A `stash pop` conflict means re-apply the
blocks from section 5.

**Expect:** the new image runs, `/up` is 200, the smoke login works, and the
migration list is consistent.

### 10.3 Rollback

```bash
cd /opt/planner
docker tag planner-app:previous planner-app:latest
docker tag planner-nginx:previous planner-nginx:latest
docker compose -f laravel/compose.production.yml up -d --force-recreate php-fpm queue nginx
curl -kI https://127.0.0.1/up
```

Migrations are forward-only: the image rollback handles code, not schema. If the
deploy changed data or is destructive, restore the 10.2 backup before starting
the old image. Keep more rollback points by tagging successful deploys with
their commit: `docker tag planner-app:latest planner-app:$(git rev-parse --short HEAD)`.

**Expect:** the previous image serves again and `/up` returns 200.

### 10.4 Runbook

| Symptom | Likely cause | First checks | Fix |
| --- | --- | --- | --- |
| **502 Bad Gateway** | php-fpm down, OOM, DB unreachable | `./planner ps`, `docker logs --tail=100 planner-php-fpm`, `free -h` | restart, free memory, fix DB/creds |
| **419 Page Expired** | CSRF/session mismatch | browser on HTTPS? `APP_URL`? Redis session? | use HTTPS, fix `APP_URL`, recreate php-fpm |
| **Redirect loop** | Cloudflare Flexible + origin redirect | `curl -I http://…`, `https://…`; SSL mode | set Full (strict); never Flexible |
| **Mixed content** | `APP_URL` http, hardcoded http asset | `config('app.url')`, page source | set `APP_URL=https://…`, recreate php-fpm |
| **Everyone rate-limited** | real client IP missing | `nginx -T` for `real_ip`; login log `ip` | mount/regenerate `cloudflare-ips.conf`, restart nginx, clear limiter |
| **Mail not delivered** | worker down, SMTP rejected | `docker top planner-queue`, `failed_jobs`, Resend → Emails | restart worker, fix key, retry |
| **521/522** | origin down | `./planner ps`, `curl -k https://127.0.0.1/up` | restart php-fpm/nginx |
| **525/526** | origin certificate | cert mounted? expiry? | install/renew cert, `nginx -t`, restart |
| **503** | maintenance mode | `php artisan about` | `docker exec planner-php-fpm php artisan up` |
| **429** | Cloudflare or app limit | response headers, Security events | wait, adjust rule |
| **Disk full** | logs, images, backups | `df -h`, `docker system df` | prune images/build cache, rotate logs; never prune volumes |

Clearing the app limiter (only when it is the cause):

```bash
docker exec planner-php-fpm php artisan cache:clear
```

Cache and sessions use separate Redis databases, so this does not sign users out.

### 10.5 Secrets

- `.env` is owned by `deploy`, mode `600`; the encrypted copy is offsite.
- `APP_KEY` is irreplaceable: without it, encrypted sessions/data are
  unreadable. Rotating it signs everyone out; `APP_PREVIOUS_KEYS` exists but is
  not needed here.
- Keep the Resend key, DB/Redis passwords, Cloudflare API token and deploy key
  in a password manager.
- Rotate on suspicion: provider → `.env` → recreate the affected containers.
  A MariaDB password rotation also needs `ALTER USER` in the database.

### 10.6 Operating cadence

- **Daily** — nothing manual; backups and uptime run themselves.
- **Weekly** — `./planner ps`, `df -h`, glance at the backup log and the queue.
- **Monthly** — OS/Docker updates, app deploy, Cloudflare and Resend review.
- **Quarterly** — restore drill; revisit RPO/RTO.
- **After any incident** — add it to this runbook.

## Appendix — Service and port map

| Port | Service | Exposure |
| --- | --- | --- |
| 22 | SSH | public, key-only |
| 80 | nginx | public, redirects to 443 |
| 443 | nginx | public, TLS |
| 8025 | Mailpit | development only |
| 3306 | MariaDB | compose network only |
| 6379 | Redis | compose network only |
| 1025 | SMTP | compose network only (dev Mailpit) |

## If your setup differs

This guide is written for **Ubuntu 24.04 LTS, 1 vCPU and 2 GB RAM**, with
Cloudflare and Resend. If any of those differ — another distribution or version,
more or fewer resources, a different edge or mail provider, a different deploy
path — do not run the commands blind. Re-derive the affected steps (package
manager, init system, Docker repository, PHP-FPM sizing, backup paths) with an
AI assistant or the provider's documentation, and keep the **Expect** results
in this guide as the acceptance test.
