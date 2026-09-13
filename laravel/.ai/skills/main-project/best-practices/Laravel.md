# The Complete Laravel Performance & Best Practices Bible

Welcome to the definitive guide to building faster, more maintainable Laravel applications using modern patterns and principles. This isn't just a summary—it's a comprehensive playbook covering every aspect of the Laravel lifecycle, from development patterns to production optimization.

---

## Phase 1: Foundation & Setup Patterns

### 1.1 Project Structure Best Practices

**Modern Directory Organization:**
```
app/
├── Actions/           # Single-purpose action classes (prefer over controllers)
├── Contracts/         # Interfaces
├── Domain/            # Domain-driven design (DDD) structure
│   ├── Orders/
│   │   ├── Actions/
│   │   ├── Models/
│   │   └── Services/
├── Enums/             # PHP 8.1 Enums
├── Services/          # Application services
├── ValueObjects/      # Immutable value objects
└── ViewModels/        # Presenter pattern for views
```

**Key Principle**: Keep your `app/Models` thin. Use **Actions** and **Services** for business logic.

### 1.2 Modern PHP Features You Should Use Daily

```php
// 1. Constructor Property Promotion (PHP 8.0)
class UserService
{
    public function __construct(
        private UserRepository $repository,
        private Cache $cache,
        private Logger $logger
    ) {}
}

// 2. Named Arguments
$user = User::create(
    name: 'Taylor',
    email: 'taylor@laravel.com',
    active: true
);

// 3. Match Expressions (instead of switch)
$status = match ($user->role) {
    'admin' => 'Administrator',
    'editor' => 'Content Editor',
    default => 'User'
};

// 4. Enums (PHP 8.1)
enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
```

### 1.3 Environment Configuration Power Moves

```env
# .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

# Multiple Redis instances for different purposes
REDIS_CACHE_HOST=redis-cache
REDIS_SESSION_HOST=redis-session
REDIS_QUEUE_HOST=redis-queue

# Database read/write separation
DB_HOST_READ=read-replica.example.com
DB_HOST_WRITE=master.example.com

# Feature flags for gradual rollouts
FEATURE_NEW_CHECKOUT=true
FEATURE_AI_SEARCH=false
```

---

## Phase 2: Core Laravel Patterns & Modern Approaches

### 2.1 Service Container - Beyond Basic Bindings

**Modern Binding Patterns:**

```php
// 1. Contextual Binding (in AppServiceProvider)
$this->app->when(PhotoController::class)
    ->needs(Filesystem::class)
    ->give(fn () => Storage::disk('local'));

$this->app->when(VideoController::class)
    ->needs(Filesystem::class)
    ->give(fn () => Storage::disk('s3'));

// 2. Tagging for collections
$this->app->tag([
    CpuReport::class,
    MemoryReport::class,
], 'reports');

$this->app->bind(ReportAnalyzer::class, fn ($app) =>
    new ReportAnalyzer($app->tagged('reports'))
);

// 3. Deferred Providers - Load only when needed
class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [Connection::class];
    }
}

// 4. Using #[Singleton] and #[Scoped] attributes
#[Singleton]
class AnalyticsService { ... }

#[Scoped]
class RequestContext { ... } // Scoped to request/job lifecycle
```

### 2.2 Service Providers - The Smart Way

```php
// Use bindings/singletons properties instead of manual registration
class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        ServerProvider::class => DigitalOceanServerProvider::class,
    ];

    public $singletons = [
        DowntimeNotifier::class => PingdomDowntimeNotifier::class,
    ];

    // Conditionally register services
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        // Defer loading heavy services
        $this->app->defer(function () {
            // Heavy initialization only when needed
        });
    }
}
```

### 2.3 Facades vs. Dependency Injection - When to Use Which

**Rule of thumb:**
- **Use DI** for business logic, services, repositories
- **Use facades** for Laravel core services (Cache, Log, DB)
- **Use real-time facades** for testing convenience

```php
// BAD: Static facade in business logic
class OrderProcessor
{
    public function process()
    {
        Log::info('Processing...'); // Hard to test
    }
}

// GOOD: DI for business logic
class OrderProcessor
{
    public function __construct(
        private LoggerInterface $logger,
        private OrderRepository $repository
    ) {}
}

// REAL-TIME FACADE - Perfect for quick tests
use Facades\App\Services\PaymentProcessor;

class OrderController
{
    public function pay()
    {
        return PaymentProcessor::process(); // Resolves from container
    }
}
```

---

## Phase 3: Database Mastery - Modern Eloquent Patterns

### 3.1 The NEW Way to Write Eloquent Queries

**Replace Old Patterns with Modern Ones:**

```php
// ❌ OLD: Massive controller with query logic
$users = User::where('active', 1)
    ->whereNotNull('email_verified_at')
    ->where('created_at', '>', now()->subDays(30))
    ->orderBy('name')
    ->get();

// ✅ NEW: Use Local Scopes
class User extends Model
{
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', 1);
    }

    #[Scope]
    protected function verified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    #[Scope]
    protected function recent(Builder $query, int $days = 30): void
    {
        $query->where('created_at', '>', now()->subDays($days));
    }
}

// Clean controller
$users = User::active()
    ->verified()
    ->recent(30)
    ->orderBy('name')
    ->get();

// ✅ EVEN BETTER: Use withAttributes for default values on created models
#[Scope]
protected function draft(Builder $query): void
{
    $query->withAttributes(['status' => 'draft']);
}

$post = Post::draft()->create(['title' => 'New Post']);
// $post->status === 'draft' (auto-set!)
```

### 3.2 The Query Builder - Hidden Gems

```php
// 1. Conditional Clauses with when() - Eliminate IF statements
$users = User::query()
    ->when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
    ->when($role, fn ($q) => $q->where('role', $role))
    ->when($sortBy, fn ($q) => $q->orderBy($sortBy, $sortDirection ?? 'asc'))
    ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
    ->get();

// 2. whereAny/whereAll/whereNone - Multiple column conditions
$users = User::whereAny([
    'name',
    'email',
    'phone',
], 'like', '%Example%')->get();

// 3. Subquery joins (instead of N+1 or complex relationships)
$latestPosts = DB::table('posts')
    ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
    ->where('published', true)
    ->groupBy('user_id');

$users = DB::table('users')
    ->joinSub($latestPosts, 'latest_posts', function ($join) {
        $join->on('users.id', '=', 'latest_posts.user_id');
    })
    ->get();

// 4. LATERAL JOINS (PostgreSQL/MySQL 8.0.14+) - Each row gets custom subquery
$latestPosts = DB::table('posts')
    ->select('id as post_id', 'title as post_title', 'created_at')
    ->whereColumn('user_id', 'users.id')
    ->orderBy('created_at', 'desc')
    ->limit(3);

$users = DB::table('users')
    ->joinLateral($latestPosts, 'latest_posts')
    ->get();

// 5. Subquery where clauses (compare column to subquery result)
$incomes = Income::where('amount', '<', function ($query) {
    $query->selectRaw('avg(amount)')->from('incomes');
})->get();

// 6. SELECT with subquery (get related data in one query)
$destinations = Destination::addSelect([
    'last_flight' => Flight::select('name')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderByDesc('arrived_at')
        ->limit(1)
])->get();

// 7. Order by subquery (sort by related data)
$destinations = Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderByDesc('arrived_at')
        ->limit(1)
)->get();

// 8. Reusable Query Components with tap() and pipe()
class DestinationFilter
{
    public function __construct(private ?string $destination) {}

    public function __invoke(Builder $query): void
    {
        $query->when($this->destination, fn ($q) => $q->where('destination', $this->destination));
    }
}

DB::table('flights')
    ->tap(new DestinationFilter($destination))
    ->paginate();

// 9. pipe() - Execute query and return different result
class Paginate
{
    public function __construct(private int $perPage = 25) {}

    public function __invoke(Builder $query): LengthAwarePaginator
    {
        return $query->paginate($this->perPage, pageName: 'p');
    }
}

$flights = DB::table('flights')->pipe(new Paginate(15));
```

### 3.3 Relationships - Modern Patterns

**Use Scoped Relationships:**

```php
class User extends Model
{
    // NEVER define relationships without scopes
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)
            ->latest()
            ->withAttributes(['status' => 'published']);
    }

    // Featured posts with default attribute
    public function featuredPosts(): HasMany
    {
        return $this->posts()->withAttributes(['featured' => true]);
    }
}

// Create model with auto-set attributes
$post = $user->featuredPosts()->create(['title' => 'Featured!']);
$post->featured; // true (auto-set!)
```

**Auto-Hydrate Parent Models (Prevent N+1 in views):**

```php
class Post extends Model
{
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->chaperone();
    }
}

// No N+1 when accessing $comment->post->title
$posts = Post::with(['comments' => fn ($q) => $q->chaperone()])->get();
```

**HasOneOfMany - Get specific related model:**

```php
class User extends Model
{
    // Latest order (most recent)
    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }

    // Oldest order
    public function oldestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->oldestOfMany();
    }

    // Most expensive order
    public function largestOrder(): HasOne
    {
        return $this->hasOne(Order::class)->ofMany('total', 'max');
    }

    // Complex: Current pricing (published, not future)
    public function currentPricing(): HasOne
    {
        return $this->hasOne(Price::class)->ofMany([
            'published_at' => 'max',
            'id' => 'max',
        ], fn ($query) => $query->where('published_at', '<', now()));
    }
}
```

**HasManyThrough - Fluent (new pattern):**

```php
class Application extends Model
{
    // OLD: $this->hasManyThrough(Deployment::class, Environment::class)

    // NEW FLUENT: Reuse existing relationships
    public function deployments(): HasManyThrough
    {
        return $this->through('environments')->has('deployments');
        // OR: return $this->throughEnvironments()->hasDeployments();
    }
}
```

### 3.4 Eager Loading - Advanced Techniques

```php
// 1. Constrained eager loading with where condition
$users = User::with(['posts' => fn ($q) => $q->where('published', 1)->orderBy('created_at')])
    ->get();

// 2. Nested eager loading with constraints
$books = Book::with(['author' => fn ($q) => $q->select('id', 'name', 'bio')->with('contacts')])
    ->get();

// 3. withWhereHas - Load and filter in one go
$users = User::withWhereHas('posts', fn ($q) => $q->where('featured', true))
    ->get();

// 4. Load specific columns in relationships
$books = Book::with('author:id,name,book_id')->get();

// 5. Conditional eager loading
$books = Book::all();
$books->loadMissing('author'); // Only if not already loaded
$books->load(['comments' => fn ($q) => $q->where('approved', true)]);

// 6. Automatic eager loading (enable in AppServiceProvider)
Model::automaticallyEagerLoadRelationships();

// 7. Prevent lazy loading (production-safe)
Model::preventLazyLoading(! $this->app->isProduction());

// 8. Lazy eager loading with nested morphTo
$activities->loadMorph('parentable', [
    Event::class => ['calendar'],
    Photo::class => ['tags'],
    Post::class => ['author'],
]);
```

### 3.5 Performance-Critical Query Techniques

```php
// 1. CHUNKING (for large datasets)
Flight::chunk(200, function ($flights) {
    foreach ($flights as $flight) {
        // Process
    }
});

// 2. chunkById (when updating)
Flight::where('active', true)
    ->chunkById(200, function ($flights) {
        $flights->each->update(['active' => false]);
    }, column: 'id');

// 3. Lazy Collections (streaming)
foreach (Flight::lazy() as $flight) {
    // One model at a time in memory
}

// 4. Cursors (single query, one model at a time)
foreach (Flight::where('destination', 'Zurich')->cursor() as $flight) {
    // Like lazy but even less memory
}

// 5. Select specific columns
Flight::select('id', 'name', 'destination', 'created_at')->get();

// 6. AddSelect for additional columns
$query = Flight::select('id', 'name');
$flights = $query->addSelect('destination')->get();

// 7. When to use chunk vs lazy vs cursor:
// - chunk: When you need to process in batches with callback
// - lazy: When you need collection methods on large datasets
// - cursor: When you only need to iterate once, lowest memory
```

### 3.6 Aggregates - The Efficient Way

```php
// 1. withCount (load counts in same query)
$posts = Post::withCount('comments')->get();
echo $posts[0]->comments_count;

// 2. withSum, withAvg, withMin, withMax
$posts = Post::withSum('comments', 'votes')->get();
echo $posts[0]->comments_sum_votes;

// 3. Deferred loading (after model retrieved)
$book = Book::first();
$book->loadCount('genres');
$book->loadSum('reviews', 'rating');

// 4. Dynamic relationship counts
$comments = Comment::whereHasMorph('commentable', '*', fn ($q) => $q->where('title', 'like', 'foo%'))
    ->get();

// 5. withExists (boolean)
$posts = Post::withExists('comments')->get();
if ($post->comments_exists) { /* ... */ }
```

### 3.7 Indexing Strategy for Maximum Performance

```php
// Migration - The RIGHT indexes
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->index();
    $table->string('status')->index();

    // Composite index (for WHERE status AND created_at)
    $table->index(['status', 'created_at']);

    // Unique index
    $table->unique(['user_id', 'order_number']);

    // Full-text index
    $table->fullText(['title', 'body']);

    // Spatial index
    $table->spatialIndex('location');

    // Vector index (PostgreSQL + pgvector)
    $table->vector('embedding', 1536)->index();
});

// PRO TIP: Online index creation (PostgreSQL/SQL Server)
$table->string('email')->unique()->online();

// PRO TIP: Conditionally create indexes
if (!Schema::hasIndex('users', ['email', 'status'], 'index')) {
    Schema::table('users', fn ($table) => $table->index(['email', 'status']));
}
```

### 3.8 Database Performance Rules

```php
// 1. NEVER use N+1 queries - Always eager load
// BAD
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name; // N queries
}

// GOOD
$posts = Post::with('user')->get();

// 2. Use chunk() for large datasets
User::chunk(1000, function ($users) {
    // Process 1000 at a time
});

// 3. Use select() to limit columns
User::select('id', 'name', 'email')->get();

// 4. Use whereIn() with caution
// For large arrays, use whereIntegerInRaw
User::whereIntegerInRaw('id', $ids)->get();

// 5. Use exists() instead of count() for checking existence
if (User::where('email', $email)->exists()) { ... }

// 6. Use chunkById when updating records you're chunking
User::where('active', true)->chunkById(100, function ($users) {
    $users->each->update(['last_login' => now()]);
});

// 7. Use lazy() for memory efficiency when using collection methods
User::lazy()->filter(fn ($u) => $u->isActive())->take(10);

// 8. Use cursor() when you need raw iteration
foreach (User::cursor() as $user) { ... }

// 9. Redis for caching - Always use cache tags
Cache::tags(['users', 'profiles'])->remember('user:'.$id, 3600, function () use ($id) {
    return User::with('profile')->find($id);
});

// 10. Use atomic locks for critical sections
Cache::lock('process:'.$id, 60)->block(10, function () use ($id) {
    // Only one process can execute this
});
```

---

## Phase 4: Modern Eloquent Mutators & Casts

### 4.1 The New Attribute API (Instead of old getters/setters)

```php
// ❌ OLD: getFirstNameAttribute, setFirstNameAttribute
// ✅ NEW: Attribute class

class User extends Model
{
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        )->shouldCache(); // Cache expensive computations
    }

    // Value object from multiple attributes
    protected function address(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => new Address(
                $attributes['address_line_one'],
                $attributes['address_line_two'],
                $attributes['city'],
                $attributes['postcode']
            ),
            set: fn (Address $value) => [
                'address_line_one' => $value->lineOne,
                'address_line_two' => $value->lineTwo,
                'city' => $value->city,
                'postcode' => $value->postcode,
            ]
        )->withoutObjectCaching(); // Disable caching for objects if needed
    }
}

// MUTATING MULTIPLE ATTRIBUTES
$user->address = new Address('123 Main St', 'Apt 4B', 'New York', '10001');
$user->save(); // All fields updated automatically
```

### 4.2 Powerful Casts You Should Know

```php
class User extends Model
{
    protected function casts(): array
    {
        return [
            // 1. JSON casts with ArrayObject (mutable!)
            'options' => AsArrayObject::class,

            // 2. Collection cast with custom class
            'tags' => AsCollection::using(TagCollection::class),

            // 3. Map collection items to Value Objects
            'settings' => AsCollection::of(Setting::class),

            // 4. Stringable cast
            'description' => AsStringable::class,

            // 5. URI cast
            'website' => AsUri::class,

            // 6. Encrypted arrays
            'secrets' => 'encrypted:array',

            // 7. Binary UUID/ULID
            'uuid' => AsBinary::uuid(),
            'ulid' => AsBinary::ulid(),

            // 8. Decimal with precision
            'price' => 'decimal:2',

            // 9. Enum casts
            'status' => UserStatus::class,

            // 10. Array of enums
            'permissions' => AsEnumCollection::of(Permission::class),

            // 11. Query-time casting (for raw queries)
            'last_posted_at' => 'datetime',
        ];
    }
}

// 12. Custom Cast with parameters
class AsHash implements CastsInboundAttributes
{
    public function __construct(private ?string $algorithm = null) {}

    public function set($model, $key, $value, $attributes)
    {
        return $this->algorithm ? hash($this->algorithm, $value) : bcrypt($value);
    }
}

protected function casts(): array
{
    return [
        'secret' => AsHash::class.':sha256',
    ];
}
```

---

## Phase 5: Validation - Beyond the Basics

### 5.1 Modern Validation Patterns

```php
// 1. Form Request with after() hooks
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->somethingIsInvalid()) {
                    $validator->errors()->add('field', 'Custom error');
                }
            }
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }
}

// 2. Conditional validation with Rule::* methods
use Illuminate\Validation\Rule;

$validator = Validator::make($data, [
    'role_id' => Rule::requiredIf($request->user()->is_admin),
    'status' => Rule::enum(Status::class)->only([Status::Pending, Status::Active]),
    'uuid' => Rule::uuid()->version(4),
]);

// 3. Custom Rule Objects (reusable)
class Uppercase implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strtoupper($value) !== $value) {
            $fail('validation.uppercase')->translate();
        }
    }
}

// 4. Password validation with rules
use Illuminate\Validation\Rules\Password;

$validator = Validator::make($data, [
    'password' => [
        'required',
        Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()
    ],
]);

// 5. Array validation with wildcards
'users.*.email' => 'email|unique:users',
'users.*.first_name' => 'required_with:users.*.last_name',

// 6. Stop on first failure
$validator = Validator::make($data, $rules);
$validator->stopOnFirstFailure()->fails();

// 7. Custom messages with placeholders
public function messages(): array
{
    return [
        'photos.*.description.required' => 'Please describe photo #:position.',
    ];
}
```

### 5.2 Validation Performance Tips

```php
// 1. Use sometimes() for conditional validation
$validator->sometimes('reason', 'required|max:500', function ($input) {
    return $input->games >= 100;
});

// 2. Use excluded attributes to prevent validation of conditional fields
'appointment_date' => 'exclude_if:has_appointment,false|required|date',

// 3. Use Rule::forEach for dynamic array validation
'companies.*.id' => Rule::forEach(function ($value, $attribute) {
    return [
        Rule::exists(Company::class, 'id'),
        new HasPermission('manage-company', $value),
    ];
}),

// 4. Batch validation with Validator::validate()
use Illuminate\Support\Facades\Validator;

Validator::validate($input, $rules, $messages);
// Throws ValidationException automatically

// 5. Prepare input before validation
protected function prepareForValidation(): void
{
    $this->merge([
        'name' => trim($this->name),
        'email' => strtolower($this->email),
    ]);
}
```

---

## Phase 6: Caching - The Performance Multiplier

### 6.1 Advanced Caching Patterns

```php
// 1. Cache with remember and tags
use Illuminate\Support\Facades\Cache;

$users = Cache::tags(['users', 'active'])->remember('active_users', 3600, function () {
    return User::active()->with('profile')->get();
});

// Clear specific tags
Cache::tags(['users'])->flush();

// 2. Cache with lock (atomic operations)
Cache::lock('generate_report', 300)->block(10, function () {
    // Only one server can generate report at a time
    Report::generate();
});

// 3. Stale-while-revalidate pattern
$value = Cache::flexible('expensive_data', [5, 10], function () {
    return $this->fetchExpensiveData();
});
// First value = fresh seconds, second = stale seconds

// 4. Cache memoization (per-request memory cache)
$value = Cache::memo()->get('key'); // First call hits cache
$value = Cache::memo()->get('key'); // Subsequent calls from memory

// 5. Cache with fallback
$value = Cache::rememberForever('users', function () {
    return User::all();
});

// 6. Cache invalidation on model events
class User extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => Cache::tags(['users'])->flush());
        static::deleted(fn () => Cache::tags(['users'])->flush());
    }
}

// 7. Database query caching
$users = DB::table('users')
    ->cacheFor(3600)
    ->cacheTags(['users'])
    ->get();

// 8. Pagination caching
$users = User::paginate(15);
$users->setCache(3600);
$users->setCacheTags(['users']);
```

### 6.2 Redis Best Practices

```php
// 1. Use Redis for session, cache, queue
// config/database.php
'redis' => [
    'cache' => [
        'host' => env('REDIS_CACHE_HOST'),
        'port' => 6379,
        'database' => 0,
    ],
    'session' => [
        'host' => env('REDIS_SESSION_HOST'),
        'port' => 6379,
        'database' => 1,
    ],
    'queue' => [
        'host' => env('REDIS_QUEUE_HOST'),
        'port' => 6379,
        'database' => 2,
    ],
],

// 2. Redis pipeline for multiple operations
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});

// 3. Redis transactions
Redis::transaction(function ($redis) {
    $redis->incr('counter');
    $redis->expire('counter', 60);
});

// 4. Redis pub/sub for real-time
Redis::publish('channel', json_encode(['event' => 'update']));
```

---

## Phase 7: Queues & Jobs - Modern Patterns

### 7.1 Job Design Patterns

```php
// 1. Use Unique Jobs to prevent duplicates
use Illuminate\Contracts\Queue\ShouldBeUnique;

#[UniqueFor(3600)]
class ProcessOrder implements ShouldQueue, ShouldBeUnique
{
    public function uniqueId(): string
    {
        return $this->order->id;
    }
}

// 2. Rate Limited Jobs
class ProcessPodcast implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            new RateLimited('podcasts'), // 5 per minute
            new WithoutOverlapping('podcast:' . $this->podcast->id),
        ];
    }
}

// 3. Job Middleware - Keep jobs clean
class RateLimited
{
    public function handle($job, $next)
    {
        Redis::throttle('key')
            ->block(0)->allow(1)->every(5)
            ->then(fn () => $next($job))
            ->catch(fn () => $job->release(5));
    }
}

// 4. Chained Jobs
use Illuminate\Support\Facades\Bus;

Bus::chain([
    new ProcessPodcast($podcast),
    new OptimizePodcast($podcast),
    new ReleasePodcast($podcast),
])->catch(function (Throwable $e) {
    // Handle chain failure
})->dispatch();

// 5. Batch Jobs
$batch = Bus::batch([
    new ImportCsv(1, 100),
    new ImportCsv(101, 200),
    new ImportCsv(201, 300),
])->then(function (Batch $batch) {
    // All jobs completed
})->catch(function (Batch $batch, Throwable $e) {
    // A job failed
})->progress(function (Batch $batch) {
    // Update progress
})->dispatch();

// 6. Defer jobs for after response
RecordDelivery::dispatch($order)->onConnection('deferred');

// 7. Background jobs (separate process, after response)
RecordDelivery::dispatch($order)->onConnection('background');

// 8. Jobs with exponential backoff
#[Backoff([1, 5, 10])]
class ProcessPodcast implements ShouldQueue
{
    // 1s, 5s, 10s retry delays
}

// 9. Jobs that fail on timeout
#[FailOnTimeout]
class LongRunningJob implements ShouldQueue
{
    // ...
}

// 10. Jobs with max exceptions
#[Tries(25)]
#[MaxExceptions(3)]
class ProcessPodcast implements ShouldQueue
{
    // Will retry 25 times but fail after 3 unhandled exceptions
}
```

### 7.2 Queue Worker Optimization

```bash
# 1. Use Horizon for Redis (beautiful dashboard)
composer require laravel/horizon

# 2. Worker with memory management
php artisan queue:work --max-jobs=1000 --max-time=3600

# 3. Multiple workers per queue
# Supervisor config
[program:laravel-worker]
numprocs=8
command=php artisan queue:work --sleep=3 --tries=3 --max-time=3600

# 4. Queue priorities
php artisan queue:work --queue=high,default,low

# 5. Pause/resume queues during maintenance
php artisan queue:pause database:default
php artisan queue:continue database:default
```

---

## Phase 8: Events & Observers - The Clean Way

### 8.1 Modern Event Patterns

```php
// 1. Use shouldDispatchAfterCommit for transaction safety
class OrderShipped implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}

// 2. Queueable event listeners
class SendShipmentNotification implements ShouldQueue
{
    use Queueable;

    public function handle(OrderShipped $event): void
    {
        // This runs in queue
    }

    public function failed(OrderShipped $event, Throwable $e): void
    {
        // Handle failure
    }
}

// 3. Defer events (dispatch after closure completes)
Event::defer(function () {
    $user = User::create([...]);
    $user->posts()->create([...]); // Events fire after closure
}, ['eloquent.created: ' . User::class]);

// 4. Use subscribers to group related events
class UserEventSubscriber
{
    public function handleUserLogin(Login $event): void {}
    public function handleUserLogout(Logout $event): void {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
        ];
    }
}

// 5. Conditional listeners
public function shouldQueue(OrderCreated $event): bool
{
    return $event->order->subtotal >= 5000;
}

// 6. Unique event listeners (prevent duplicates)
class AcquireProductKey implements ShouldQueue, ShouldBeUnique
{
    public function uniqueId(LicenseSaved $event): string
    {
        return 'listener:' . $event->license->id;
    }
}
```

### 8.2 Observer Best Practices

```php
// 1. Use observers for model events
class UserObserver
{
    public function created(User $user): void
    {
        // User created
    }

    public function updated(User $user): void
    {
        // User updated
    }

    public function deleted(User $user): void
    {
        // User deleted
    }
}

// 2. Register with attribute
#[ObservedBy([UserObserver::class])]
class User extends Model { }

// 3. Handle after commit
class UserObserver implements ShouldHandleEventsAfterCommit
{
    public function created(User $user): void
    {
        // Only runs after transaction commits
    }
}

// 4. Muting events
$user = User::withoutEvents(function () {
    return User::find(1)->delete();
});

// 5. Save without events
$user->saveQuietly();
$user->deleteQuietly();
$user->restoreQuietly();
```

---

## Phase 9: HTTP Layer - Modern Patterns

### 9.1 Controllers - Thin and Clean

```php
// 1. Use Single Action Controllers (Invokable)
class ProvisionServer extends Controller
{
    public function __invoke(Request $request)
    {
        // Single responsibility
    }
}

Route::post('/server', ProvisionServer::class);

// 2. Use Form Requests for validation
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ];
    }
}

// 3. Controllers with middleware attributes
#[Middleware('auth')]
class PostController extends Controller
{
    #[Middleware('can:update,post')]
    public function update(Post $post) { ... }
}

// 4. Resource controllers with only/except
Route::resource('photos', PhotoController::class)
    ->only(['index', 'show'])
    ->missing(fn () => redirect()->route('photos.index'));

// 5. Nested resources with scoped binding
Route::resource('photos.comments', CommentController::class)
    ->scoped(['comment' => 'slug']);

// 6. Singleton resources
Route::singleton('profile', ProfileController::class)->creatable();

// 7. Dependency injection in controllers
class UserController extends Controller
{
    public function __construct(
        private UserRepository $users,
        private Logger $logger
    ) {}

    public function store(Request $request, CreateUserAction $action)
    {
        return $action->execute($request->validated());
    }
}
```

### 9.2 API Resources - The Modern Way

```php
// 1. Use resources for API transformation
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'last_login' => $this->whenNotNull($this->last_login_at),
            'is_admin' => $this->when($request->user()->isAdmin(), true),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => ['version' => '1.0.0'],
        ];
    }
}

// 2. Conditional attributes
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'secret' => $this->when($request->user()->isAdmin(), 'secret-value'),
        $this->mergeWhen($request->user()->isAdmin(), [
            'first_secret' => 'value',
            'second_secret' => 'value',
        ]),
    ];
}

// 3. JSON:API Resources
#[Collects(Member::class)]
class UserCollection extends ResourceCollection
{
    public $collects = UserResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => ['self' => '/users'],
        ];
    }
}

// 4. Preserve collection keys
#[PreserveKeys]
class UserResource extends JsonResource { ... }

// 5. Additional data
return UserResource::collection($users)->additional(['meta' => ['total' => $users->total()]]);
```

---

## Phase 10: Performance Optimization - The Speed Book

### 10.1 Database Performance Checklist

```php
// ✅ DO: Use indexes on foreign keys and where clauses
Schema::table('posts', fn ($table) => $table->index('user_id'));

// ✅ DO: Use composite indexes for multiple where clauses
Schema::table('orders', fn ($table) => $table->index(['user_id', 'status', 'created_at']));

// ✅ DO: Use 'explain' to analyze queries
DB::table('users')->where('email', $email)->explain();

// ✅ DO: Use chunking for large updates
User::chunk(1000, function ($users) {
    $users->each->update(['last_login' => now()]);
});

// ✅ DO: Use select to limit columns
User::select('id', 'name', 'email')->get();

// ❌ DON'T: Use N+1 queries
// BAD: foreach($posts as $post) { $post->user->name; }
// GOOD: $posts = Post::with('user')->get();

// ❌ DON'T: Use count() for existence checks
// BAD: if(User::where('email', $email)->count() > 0)
// GOOD: if(User::where('email', $email)->exists())

// ✅ DO: Use caching for expensive queries
$users = Cache::remember('active_users', 3600, fn () => User::active()->get());

// ✅ DO: Use Redis for session, cache, queue
// config/cache.php
'default' => env('CACHE_STORE', 'redis'),

// ✅ DO: Use database read replicas
'mysql' => [
    'read' => ['host' => env('DB_HOST_READ')],
    'write' => ['host' => env('DB_HOST_WRITE')],
    'sticky' => true, // For immediate read-after-write consistency
],
```

### 10.2 Query Optimization - Advanced

```php
// 1. Use whereIn with caution - For large arrays, use whereIntegerInRaw
User::whereIntegerInRaw('id', $largeArray)->get();

// 2. Subquery joins instead of multiple queries
$latestPosts = DB::table('posts')
    ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
    ->groupBy('user_id');

$users = DB::table('users')
    ->joinSub($latestPosts, 'latest_posts', fn ($join) =>
        $join->on('users.id', '=', 'latest_posts.user_id')
    )->get();

// 3. LATERAL joins (PostgreSQL/MySQL 8.0.14+)
$latestPosts = DB::table('posts')
    ->select('id', 'title', 'created_at')
    ->whereColumn('user_id', 'users.id')
    ->orderBy('created_at', 'desc')
    ->limit(3);

$users = DB::table('users')
    ->joinLateral($latestPosts, 'latest_posts')
    ->get();

// 4. SELECT with subquery
$destinations = Destination::addSelect([
    'last_flight' => Flight::select('name')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderByDesc('arrived_at')
        ->limit(1)
])->get();

// 5. Order by subquery
$destinations = Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderByDesc('arrived_at')
        ->limit(1)
)->get();
```

### 10.3 Caching Strategies

```php
// 1. Cache with tags (Redis/Memcached only)
Cache::tags(['users', 'posts'])->remember('user_posts', 3600, function () {
    return User::with('posts')->get();
});

// Invalidate by tag
Cache::tags(['users'])->flush();

// 2. Stale-while-revalidate
$data = Cache::flexible('key', [5, 10], function () {
    return expensiveOperation();
});

// 3. Memoization (per-request cache)
$value = Cache::memo()->get('key');
$value = Cache::memo()->get('key'); // From memory

// 4. Cache with lock for heavy operations
Cache::lock('heavy_calc', 300)->block(10, function () {
    // Only one process runs this
    return cache('heavy_calc', fn () => expensiveCalc());
});

// 5. Multi-level caching (Redis + local memory)
class MultiLevelCache
{
    private array $memory = [];

    public function get(string $key, callable $callback, int $ttl = 3600)
    {
        if (array_key_exists($key, $this->memory)) {
            return $this->memory[$key];
        }

        $value = Cache::remember($key, $ttl, $callback);
        $this->memory[$key] = $value;
        return $value;
    }
}
```

---

## Phase 11: Modern Authentication & Security

### 11.1 Authentication Best Practices

```php
// 1. Use Fortify for backend authentication
composer require laravel/fortify

// 2. Custom authentication pipeline
Fortify::authenticateThrough(function (Request $request) {
    return [
        EnsureLoginIsNotThrottled::class,
        RedirectIfTwoFactorAuthenticatable::class,
        AttemptToAuthenticate::class,
        PrepareAuthenticatedSession::class,
    ];
});

// 3. Two-factor authentication
class User extends Authenticatable
{
    use TwoFactorAuthenticatable;
}

// 4. Passkeys (WebAuthn)
class User extends Authenticatable implements PasskeyUser
{
    use PasskeyAuthenticatable;
}

// 5. Rate limiting authentication attempts
RateLimiter::for('login', fn ($job) =>
    Limit::perMinute(5)->by($job->email . $job->ip)
);

// 6. Use password confirmation for sensitive actions
Route::post('/settings', fn () => /* ... */)
    ->middleware(['auth', 'password.confirm']);

// 7. Email verification
Route::get('/dashboard', fn () => /* ... */)
    ->middleware(['auth', 'verified']);
```

### 11.2 Authorization - Gates & Policies

```php
// 1. Policies for models
php artisan make:policy PostPolicy --model=Post

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}

// 2. Register policies
Gate::policy(Post::class, PostPolicy::class);

// 3. Use policy in controllers
if ($user->cannot('update', $post)) {
    abort(403);
}

// 4. Blade authorization
@can('update', $post)
    <button>Edit</button>
@endcan

// 5. Policy responses with messages
public function update(User $user, Post $post): Response
{
    return $user->id === $post->user_id
        ? Response::allow()
        : Response::deny('You do not own this post.', 403);
}

// 6. Intercept checks
Gate::before(function ($user, $ability) {
    if ($user->isAdministrator()) {
        return true;
    }
});
```

---

## Phase 12: Testing - The Confidence Builder

### 12.1 Modern Testing Patterns

```php
// 1. Use RefreshDatabase trait
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

// 2. Model factories
User::factory()->count(5)->create();
User::factory()->hasPosts(3)->create();

// 3. HTTP testing
$response = $this->get('/');
$response->assertStatus(200);
$response->assertSee('Welcome');

// 4. JSON testing with fluent assertions
$response->assertJson(fn (AssertableJson $json) =>
    $json->where('id', 1)
        ->where('name', 'Taylor')
        ->whereType('email', 'string')
        ->has('posts', 3, fn ($json) =>
            $json->where('title', 'First Post')->etc()
        )
        ->etc()
);

// 5. Database assertions
$this->assertDatabaseCount('users', 5);
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

// 6. Mocking facades
Cache::expects('get')->with('key')->andReturn('value');

// 7. Time travel
$this->travel(5)->days(function () {
    // Test 5 days in future
});

// 8. Parallel testing
php artisan test --parallel --processes=4

// 9. Test coverage
php artisan test --coverage --min=80

// 10. Browser testing with Dusk
$this->browse(function (Browser $browser) {
    $browser->visit('/login')
        ->type('email', 'user@example.com')
        ->type('password', 'password')
        ->press('Login')
        ->assertPathIs('/dashboard');
});
```

### 12.2 Performance Testing

```php
// 1. Query count assertions
$this->expectsDatabaseQueryCount(5);

// 2. Profile tests
php artisan test --profile

// 3. Benchmarking
use Illuminate\Support\Benchmark;

[$count, $duration] = Benchmark::value(fn () => User::count());

// 4. Testing queued jobs
Queue::fake();
Queue::assertPushed(ShipOrder::class);

// 5. Testing notifications
Notification::fake();
Notification::assertSentTo($user, InvoicePaid::class);

// 6. Testing events
Event::fake();
Event::assertDispatched(OrderShipped::class);
```

---

## Phase 13: Deployment & Production Optimization

### 13.1 Deployment Checklist

```bash
# 1. Cache everything
php artisan optimize

# 2. Cache routes
php artisan route:cache

# 3. Cache config
php artisan config:cache

# 4. Cache events
php artisan event:cache

# 5. Cache views
php artisan view:cache

# 6. Cache compiled assets
npm run build

# 7. Restart queue workers
php artisan queue:restart

# 8. Run migrations
php artisan migrate --force

# 9. Clear opcache (if using PHP's opcache)
# Restart PHP-FPM or use opcache_reset()
```

### 13.2 Production Configuration

```env
# .env - Production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com

# Database (read/write separation)
DB_HOST_READ=read-replica.example.com
DB_HOST_WRITE=master.example.com

# Cache (Redis)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Horizon (for Redis queues)
HORIZON_ENV=production

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Trust proxies (for load balancers)
TRUSTED_PROXIES=10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
```

### 13.3 Monitoring & Logging

```php
// 1. Slack notifications for errors
Log::channel('slack')->error('Critical error!');

// 2. Database query monitoring
DB::whenQueryingForLongerThan(500, function ($connection, $event) {
    // Notify team of slow queries
});

// 3. Monitor queue health
php artisan queue:monitor redis:default --max=100

// 4. Monitor database connections
php artisan db:monitor --databases=mysql,pgsql --max=100

// 5. Scheduled task monitoring
Schedule::command('backup:run')
    ->daily()
    ->emailOutputOnFailure('admin@example.com')
    ->pingOnSuccess('https://health.example.com/backup');
```

---

## Phase 14: Modern Package Management

### 14.1 Must-Have Packages

```json
{
    "require-dev": {
        "laravel/telescope": "*",          // Debugging
        "laravel/horizon": "*",             // Queue dashboard
        "laravel/dusk": "*",                // Browser testing
        "spatie/laravel-query-builder": "*", // Advanced query building
        "barryvdh/laravel-debugbar": "*",   // Debug bar
        "nunomaduro/collision": "*"         // Error handling
    },
    "require": {
        "laravel/sanctum": "*",            // API authentication
        "laravel/fortify": "*",            // Authentication backend
        "spatie/laravel-permission": "*",  // Roles & permissions
        "spatie/laravel-medialibrary": "*", // File management
        "laravel/tinker": "*",              // REPL
        "predis/predis": "*"                // Redis client
    }
}
```

### 14.2 Custom Package Development

```php
// 1. Package discovery
// composer.json
"extra": {
    "laravel": {
        "providers": [
            "Vendor\\Package\\ServiceProvider"
        ],
        "aliases": {
            "Package": "Vendor\\Package\\Facade"
        }
    }
}

// 2. Service provider
class PackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'package');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'package');
        $this->mergeConfigFrom(__DIR__.'/config/package.php', 'package');

        $this->publishes([
            __DIR__.'/config/package.php' => config_path('package.php'),
            __DIR__.'/resources/views' => resource_path('views/vendor/package'),
        ], 'package-config');
    }
}

// 3. Using singleton bindings
class PackageServiceProvider extends ServiceProvider
{
    public $singletons = [
        PackageService::class => PackageService::class,
    ];
}
```

---

## Phase 15: Final Summary - The Golden Rules

### 15.1 The Ten Commandments of Laravel Performance

1. **Always eager load** – Use `with()` to prevent N+1
2. **Cache everything** – Redis + Cache tags = speed
3. **Use indexes** – Especially on foreign keys and where clauses
4. **Chunk large datasets** – Never `all()` on large tables
5. **Use database transactions** – For data integrity
6. **Queue heavy operations** – Emails, reports, API calls
7. **Use read replicas** – For reporting and analytics
8. **Monitor slow queries** – `DB::whenQueryingForLongerThan()`
9. **Optimize assets** – Use Vite, compress images, CDN
10. **Keep dependencies minimal** – Each package adds weight

### 15.2 The Developer Efficiency Rules

1. **Use PHP 8+ features** – Attributes, enums, constructor promotion
2. **Use Laravel's built-in helpers** – `collect()`, `Str::`, `Arr::`
3. **Use Form Requests** – Keep controllers clean
4. **Use Actions** – Single responsibility classes
5. **Use Policies** – Centralize authorization
6. **Use Observers** – Separate event handling
7. **Use Resources** – API transformation
8. **Use Macros** – Extend core classes
9. **Use Custom Casts** – For complex data types
10. **Use Local Scopes** – Reusable query constraints

### 15.3 The Security Checklist

1. **Use prepared statements** – Laravel does this by default
2. **Validate all input** – Never trust user data
3. **Escape output** – Use `{{ }}` or `e()`
4. **Use CSRF protection** – Forms need `@csrf`
5. **Use encryption** – For sensitive data
6. **Use rate limiting** – Prevent brute force
7. **Use 2FA** – For sensitive accounts
8. **Use password confirmation** – For critical actions
9. **Use verification** – Email verification
10. **Keep dependencies updated** – Security patches

---

**This concludes the Ultimate Laravel Performance & Best Practices Guide.**

Every pattern, trick, and principle listed here has been battle-tested in production applications. Implement them gradually, measure the improvements, and continuously refine your approach.

Remember: The best code is code that is:
- **Simple** – Easy to read and understand
- **Performant** – Fast for your users
- **Secure** – Protected against attacks
- **Maintainable** – Easy to change and extend
- **Tested** – Confidence in your changes

Now go build something amazing! 🚀
