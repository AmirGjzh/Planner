# fa.json usage context

Line-by-line companion to `lang/fa.json`: every translation key with **what kind of UI text it is**
(role, first) and **where it is used** (detail, after `·`). Blocks mirror `fa.json` in the same order
(Login | Password recovery | Register | Nav | Profile | Categories | Pagination | Plans | Tasks |
Reports | Dashboard | Errors | Components).

- Find every text of one kind — e.g. all button labels for a consistency sweep:
  `grep -n '→ button' lang/fa.context.md`, `grep -n '→ input placeholder' lang/fa.context.md`,
  `grep -n '→ validation message' lang/fa.context.md`.
- **Parity with `fa.json` is enforced by `tests/Feature/LangContextTest.php`** (exact key list and
  order, one annotation line per key). Add, remove, or rename a key in `fa.json` → update the matching
  line here in the same change, or the test fails.
- Roles in use: page heading, section heading, supporting text, button, button label, link, nav item,
  nav submenu item, skip link, aria-label, input label, input placeholder, checkbox label, select
  option, select placeholder, datepicker label, datepicker quick option, validation message, inline
  alert title, inline alert body, toast title, modal heading, table header, sort option, filter
  option, status label, stat label, badge text, card label, chart label, workload label, workload
  legend, empty state, placeholder text, paginator text, paginator button, error page title,
  error page body, email subject, email heading, email body, email button, email hint, unit text,
  unused.


## Login

"Welcome back"                                   → page heading · login card title
"Enter your information to sign in"              → supporting text · under login heading
"Email"                                          → input label · email field (login, register, forgot-password)
"Your email"                                     → input placeholder · email field (login, register, forgot-password)
"Password"                                       → input label · password field (login, register, reset, profile confirm)
"Your password"                                  → input placeholder · password field (login, register, reset, profile)
"Remember me"                                    → checkbox label · login form
"Forgot password?"                               → link · login form → forgot-password page
"Sign in"                                        → button · login submit; also login page title, header guest CTA, auth-page footer links
"Show password"                                  → aria-label · password visibility toggle (shared input component)
"New to Planner?"                                → supporting text · login footer prompt
"Create your account"                            → link · login footer → register; also register page title
"Too many sign-in attempts!"                     → inline alert title · warning · login rate limit
"Try again in a minute."                         → inline alert body · shared rate-limit follow-up (login, categories, profile, plans)
"Sign-in failed!"                                → inline alert title · danger · bad credentials
"Check your email and password, then try again." → inline alert body · sign-in failed
"Your session has expired, sign in again"        → toast title · info · session expiry (login redirect)
"Your password was changed, sign in again"       → toast title · info · forced re-login (ExpireSessionsOnPasswordChange middleware)
"Email is required."                             → validation message · email required (login, register, forgot)
"Enter a valid email."                           → validation message · email format (login, register, forgot)
"Password is required."                          → validation message · password required (login, register, reset, profile)
"Use at most 256 characters."                    → validation message · password max (all password fields)
"You're signed in"                               → toast title · success · sign-in (login action)

## Password recovery

"Reset your password"                                                                         → page heading · forgot-password card; also reset email subject + heading
"Enter your email to receive a reset link"                                                    → supporting text · under forgot-password heading
"Send reset link"                                                                             → button · primary submit (forgot-password)
"Too many password-reset requests!"                                                           → inline alert title · warning · forgot rate limit
"Check your email"                                                                            → page heading · forgot-password success step
"If an account exists for :email, a reset link has been sent."                                → supporting text · forgot success body (email shown highlighted)
"Check your inbox!"                                                                           → inline alert title · success · forgot success step
"The link expires in :count minutes, don't forget to check your spam folder."                 → supporting text · forgot success meta; also verify-email notice meta
"Use a different email"                                                                       → button · secondary · back to forgot form
"Remember your password?"                                                                     → supporting text · footer prompt (forgot + reset forms)
"Choose a new password"                                                                       → page heading · reset-password card; also reset page title
"Enter and confirm your new password"                                                         → supporting text · under reset heading
"Resetting password for :email"                                                               → supporting text · account email line on reset card
"Reset password"                                                                              → button · primary submit (reset form)
"Too many password-reset attempts!"                                                           → inline alert title · warning · reset rate limit
"Link invalid or expired!"                                                                    → inline alert title · danger · invalid reset link
"This password reset link is invalid or has expired."                                         → inline alert body · invalid reset link
"Request a new link"                                                                          → link · invalid-link alert → forgot-password
"Password didn't change!"                                                                     → inline alert title · danger · change-password failure (profile + reset)
"New password is equal to current one."                                                       → validation message · same-as-current password (profile + reset)
"Your password has been reset, sign in to continue"                                           → toast title · success · reset done
"Reset Password"                                                                              → email button · reset email CTA (mail view + notification action label)
"You are receiving this email because we received a password reset request for your account." → email body · reset email intro
"This password reset link will expire in :count minutes."                                     → email hint · reset email expiry
"If you didn't request a password reset, no further action is required."                      → email body · reset email footer

## Register

"Welcome to Planner"                                                          → page heading · register card title
"Enter your information to create your account"                               → supporting text · under register heading
"Too many sign-up attempts!"                                                  → inline alert title · warning · register rate limit
"Username"                                                                    → input label · username field (register + profile)
"Your username"                                                               → input placeholder · username field (register + profile)
"Confirm password"                                                            → input label · confirm field (register + reset)
"Your password again"                                                         → input placeholder · confirm field (register + reset)
"Username taken!"                                                             → inline alert title · danger · duplicate username (register + profile)
"Try a different username."                                                   → inline alert body · duplicate username
"Email already registered!"                                                   → inline alert title · danger · register existing account
"Sign in, or use another email."                                              → inline alert body · register existing-account hint
"Sign up"                                                                     → button · register submit; also header guest CTA
"Already have an account?"                                                    → supporting text · register footer prompt
"Username is required."                                                       → validation message · username required (register + profile)
"Start with a letter, use 3 to 30 characters."                                → validation message · username regex (register + profile)
"Use at least 8 characters."                                                  → validation message · password min (register + reset + profile)
"Use at most 255 characters."                                                 → validation message · email max (register)
"Confirm your password."                                                      → validation message · confirmation required (register + reset + profile)
"The passwords don't match."                                                  → validation message · password mismatch (register + reset + profile)
"Your account is ready, sign in to continue"                                  → toast title · success · registration done
"Verify your email address"                                                   → page heading · verify-email notice; also verification email subject + heading
"Open the verification link in your :email inbox"                             → supporting text · verify notice subtitle (email shown)
"Resend verification email"                                                   → button · verify notice resend
"Verification email sent"                                                     → toast title · success · resend done
"Too many verification emails!"                                               → inline alert title · warning · resend rate limit
"Not your account?"                                                           → supporting text · verify notice footer prompt
"Email verified"                                                              → toast title · success · verification link opened (routes/web.php)
"Thanks for signing up! Click the button below to verify your email address." → email body · verification email intro
"Verify email"                                                                → email button · verification email CTA (mail view + notification action label)
"This verification link will expire in :count minutes."                       → email hint · verification email expiry
"If you didn't create an account, no further action is required."             → email body · verification email footer

## Nav

"Dashboard"       → nav item · header main nav
"Tasks"           → nav item · header main nav
"Plans"           → nav item · header main nav
"Categories"      → nav item · header main nav
"Reports"         → nav item · header main nav
"Profile"         → nav submenu item · header user menu
"Settings"        → nav submenu item · header user menu
"Log out"         → nav submenu item · header user menu; also verify-email notice logout button
"Menu"            → aria-label · mobile menu toggle
"Main navigation" → aria-label · nav landmark (desktop + mobile)
"Skip to content" → skip link · accessibility first focus

## Profile

"My profile"                                                           → page heading · profile card title
"Preferences"                                                          → section heading · preferences card
"Select your language"                                                 → select placeholder · language select
"Language is required."                                                → validation message · locale required
"Selected language is invalid."                                        → validation message · locale in-list
"Language"                                                             → section label · preferences row; also input label on select
"Theme"                                                                → section label · preferences row; also input label on select
"Ocean"                                                                → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Amber"                                                                → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Saffron"                                                              → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Chocolate"                                                            → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Magic"                                                                → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Gol-goli"                                                             → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Midnight"                                                             → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Forest"                                                               → select option · theme (translated dynamically via __(ucfirst(name)) from config/themes)
"Select your theme"                                                    → select placeholder · theme select
"Theme is required."                                                   → validation message · theme required
"Selected theme is invalid."                                           → validation message · theme in-list
"Edit profile"                                                         → button · profile action card (opens edit modal)
"Too many edit-profile attempts!"                                      → inline alert title · warning · edit profile rate limit
"Too many delete-account attempts!"                                    → inline alert title · warning · delete rate limit
"Change password"                                                      → button · profile action card + change-password section action
"Current password"                                                     → input label · change-password modal
"New password"                                                         → input label · change-password modal
"Confirm new password"                                                 → input label · change-password modal
"Your new password"                                                    → input placeholder · change-password modal
"Confirm your new password"                                            → input placeholder · change-password modal
"Password changed"                                                     → toast title · success · password change done
"Current password is required."                                        → validation message · current password required
"Too many password change attempts!"                                   → inline alert title · warning · password change rate limit
"Personal information"                                                 → section heading · personal info card
"Firstname"                                                            → input label · edit form; also info-row label
"Lastname"                                                             → input label · edit form; also info-row label
"Birthday"                                                             → input label · edit form (datepicker); also info-row label
"Gender"                                                               → input label · edit form (select); also info-row label
"Country"                                                              → input label · edit form (select); also info-row label
"Delete your account and all your data."                               → supporting text · delete-account card
"Delete account"                                                       → button · delete-account card + delete modal action
"Male"                                                                 → select option · gender
"Female"                                                               → select option · gender
"Your firstname"                                                       → input placeholder · edit form
"Your lastname"                                                        → input placeholder · edit form
"Select your gender"                                                   → select placeholder · gender select
"Select your country"                                                  → select placeholder · country select
"Save"                                                                 → button · modal primary (profile, plans, tasks edit modals)
"Cancel"                                                               → button · modal secondary (all add/edit/delete/confirm modals)
"Close"                                                                → aria-label · modal close buttons (profile modals)
"Wrong password!"                                                      → inline alert title · danger · wrong current password (profile)
"The password you entered is incorrect."                               → inline alert body · wrong current password
"Be careful!"                                                          → inline alert title · warning · delete-account confirm
"This action is permanent, enter your password to continue."           → inline alert body · delete-account confirm
"Profile updated"                                                      → toast title · success · profile save
"Logged out successfully"                                              → toast title · success · logout flash (routes/web.php)
"Log out failed"                                                       → toast title · danger · logout fetch failure (header + notice logout)
"Account deleted"                                                      → toast title · success · delete account
"No results found"                                                     → empty state · shared select search results
"Search..."                                                            → input placeholder · shared select search box
"Select a date"                                                        → datepicker label · single-date mode prompt
"Select a range"                                                       → datepicker label · range-mode prompt
"Today"                                                                → datepicker quick option; also dashboard day label
"Tomorrow"                                                             → datepicker quick option
"Yesterday"                                                            → datepicker quick option
"Reset"                                                                → button · datepicker/calendar reset
"Edit your information"                                                → modal heading · edit profile modal
"Delete your account"                                                  → modal heading · delete account modal
"Firstname must be at least 2 characters."                             → validation message · firstname min
"Firstname cannot exceed 50 characters."                               → validation message · firstname max
"Firstname can only contain letters, spaces, half-spaces, hyphens and apostrophes." → validation message · firstname charset
"Lastname must be at least 2 characters."                              → validation message · lastname min
"Lastname cannot exceed 50 characters."                                → validation message · lastname max
"Lastname can only contain letters, spaces, half-spaces, hyphens and apostrophes." → validation message · lastname charset
"Selected gender is invalid."                                          → validation message · gender enum
"Selected country is invalid."                                         → validation message · country in-list
"Birthdate must be a valid date."                                      → validation message · birthday format
"Birthdate must be today or earlier."                                  → validation message · birthday not future

## Categories

"My categories"                                           → page heading · categories card title
"Search categories..."                                    → input placeholder · toolbar search
"New"                                                     → button label · toolbar (responsive short form of New category/plan/task)
"New category"                                            → button label · toolbar (full form; short form is New)
"Sort"                                                    → button label · toolbar (responsive short form of Sort by)
"Sort by"                                                 → button label · toolbar (full form; short form is Sort)
"Date created"                                            → sort option · newest-first sort (categories, plans, tasks)
"Category name"                                           → input label · add/edit modal; also table header + sort option
"Number of tasks"                                         → table header · categories list count column
"No"                                                      → inline text · task-count cell when count is zero (categories list)
"Edit"                                                    → button · row action + edit modal action (categories, plans, tasks)
"Delete"                                                  → button · row action + delete modal action (categories, plans, tasks)
"View tasks"                                              → link · category row → filtered tasks; also plan card → filtered tasks
"No categories found"                                     → empty state · search/filter yielded nothing
"No categories yet"                                       → empty state · no data yet; also empty select option on task form
"Add new category"                                        → modal heading · create category modal
"Duplicate category!"                                     → inline alert title · danger · duplicate name (add + edit)
"You have a category with this name."                     → inline alert body · duplicate category name
"Too many add-category attempts!"                         → inline alert title · warning · create rate limit
"Too many edit-category attempts!"                        → inline alert title · warning · edit rate limit
"Your category name"                                      → input placeholder · add/edit modal
"Create"                                                  → button · modal primary (add category/plan/task modals)
"Edit category"                                           → modal heading · edit category modal
"Delete category"                                         → modal heading · delete category modal
"Cannot delete category!"                                 → inline alert title · danger · delete guard (has tasks)
"This category has tasks, reassign or delete them first." → inline alert body · delete guard
"Are you sure?"                                           → inline alert title · warning · delete confirm (categories, plans, tasks)
"This action cannot be undone."                           → inline alert body · delete confirm (categories, plans, tasks)
"Your category created"                                   → toast title · success · create done
"Your category updated"                                   → toast title · info · update done
"Your category deleted"                                   → toast title · info · delete done
"Category name is required."                              → validation message · name required (add + edit)
"Category name cannot exceed 255 characters."             → validation message · name max (add + edit)

## Pagination

"Previous" → paginator button · previous page (published Livewire tailwind view)
"Next"     → paginator button · next page (published Livewire tailwind view)
"Showing"  → paginator text · range summary start (published Livewire tailwind view)
"to"       → paginator text · range separator
"of"       → paginator text · range total separator

## Plans

"My plans"                                                             → page heading · plans card title
"Search plans..."                                                      → input placeholder · toolbar search
"New plan"                                                             → button label · toolbar (full form; short form is New)
"State"                                                                → table header + sort option · state column (plans, tasks)
"Deadline"                                                             → table header + sort option · deadline column (plans)
"Load"                                                                 → table header + sort option · difficulty column (plans, tasks)
"Plan name"                                                            → input label · add/edit modal; also table header + sort option + validation prefix
"Filter"                                                               → button · toolbar filter toggle (plans, tasks)
"All"                                                                  → filter option · state filter (plans, tasks); also dashboard view-all link
"Active"                                                               → status label · filter option (plans, tasks) + card badge
"Completed"                                                            → status label · filter option (plans, tasks) + reports stat + chart legend
"Overdue"                                                              → status label · filter option (plans, tasks) + dashboard badge
"Date range"                                                           → section label · reports date-range presets
"Calendar"                                                             → button · date-range calendar toggle (plans, tasks)
"Plan range"                                                           → input label · add/edit date-range field
"Add task"                                                             → button · plan card add-task action
"Complete"                                                             → button · card action + complete modal action (plans, tasks)
"Re active"                                                            → button · card action + reactivate modal action (plans, tasks)
"Progress"                                                             → section label · plan card progress
"days left"                                                            → badge text · plan card countdown (chart label)
"Re active plan"                                                       → button · plan card action; also reactivate-plan modal heading
"No plans found"                                                       → empty state · search/filter yielded nothing
"No plans yet"                                                         → empty state · no data yet
"days overdue"                                                         → badge text · plan card overdue count (chart label)
"Add new plan"                                                         → modal heading · create plan modal
"Duplicate plan!"                                                      → inline alert title · danger · duplicate name (add + edit)
"You have a plan with this name."                                      → inline alert body · duplicate plan name
"Too many add-plan attempts!"                                          → inline alert title · warning · create rate limit
"Too many edit-plan attempts!"                                         → inline alert title · warning · edit rate limit
"Your plan name"                                                       → input placeholder · add/edit modal
"Plan description"                                                     → input label · add/edit textarea; also validation prefix (max)
"Your plan description"                                                → input placeholder · add/edit textarea
"Your plan created"                                                    → toast title · success · create done
"Edit plan"                                                            → modal heading · edit plan modal
"Your plan updated"                                                    → toast title · info · update done
"Delete plan"                                                          → modal heading · delete plan modal
"Cannot delete plan!"                                                  → inline alert title · danger · delete guard (has tasks)
"This plan has tasks, reassign or delete them first."                  → inline alert body · delete guard
"Your plan deleted"                                                    → toast title · info · delete done
"Cannot complete plan!"                                                → inline alert title · danger · completion guard (unfinished tasks)
"Complete plan"                                                        → modal heading · complete plan modal
"This plan still has unfinished tasks, complete or remove them first." → inline alert body · completion guard
"This will mark the plan as completed."                                → inline alert body · completion confirm
"Your plan completed"                                                  → toast title · info · completion done
"Your plan re activated"                                               → toast title · info · reactivate done
"This plan will be moved back to active."                              → inline alert body · reactivate confirm
"Mark as completed"                                                    → button · card action (plans, tasks)
"No description"                                                       → placeholder text · card description fallback (plans, tasks)
"Plan name is required."                                               → validation message · name required (add + edit)
"Plan name cannot exceed 255 characters."                              → validation message · name max (add + edit)
"Plan description cannot exceed 5000 characters."                      → validation message · description max (add + edit)
"Plan range is required."                                              → validation message · range required (add + edit)
"Start date is required."                                              → validation message · range start required (add + edit)
"End date is required."                                                → validation message · range end required (add + edit)
"End date must be after the start date."                               → validation message · range order (add + edit)

## Tasks

"My tasks"                                        → page heading · tasks card title
"Search tasks..."                                 → input placeholder · toolbar search
"New task"                                        → button label · toolbar (full form; short form is New)
"Priority"                                        → table header + sort option · priority column; also input label (add/edit)
"Category"                                        → card label · task detail category; also input label (add/edit)
"Plan"                                            → card label · task detail plan; also input label (add/edit)
"Date"                                            → table header + sort option · date column (tasks)
"Add new task"                                    → modal heading · create task modal
"Edit task"                                       → modal heading · edit task modal
"Delete task"                                     → modal heading · delete task modal
"Complete task"                                   → modal heading · complete task modal
"Re active task"                                  → modal heading · reactivate task modal
"Task title"                                      → input label · add/edit modal; also validation prefix
"Task date"                                       → input label · add/edit datepicker; also validation prefix
"Estimated minutes"                               → input label · add/edit number field
"Days before alarm"                               → input label · add/edit number field
"Task description"                                → input label · add/edit textarea; also validation prefix
"Your task title"                                 → input placeholder · add/edit modal
"Your task description"                           → input placeholder · add/edit textarea
"e.g. 30"                                         → input placeholder · estimated minutes hint
"e.g. 1"                                          → input placeholder · days-before-alarm hint
"Select priority"                                 → select placeholder · priority select (add/edit)
"Select category"                                 → select placeholder · category select (add/edit)
"Select plan"                                     → select placeholder · plan select (add/edit)
"Low"                                             → select option · priority (add/edit, filter, dashboard)
"Medium"                                          → select option · priority (add/edit, filter, dashboard)
"High"                                            → select option · priority (add/edit, filter, dashboard)
"No plan"                                         → select option · unassigned plan (add/edit)
"Too many create-task attempts!"                  → inline alert title · warning · create rate limit
"Too many edit-task attempts!"                    → inline alert title · warning · edit rate limit
"Too many complete-task attempts!"                → inline alert title · warning · complete rate limit
"Too many re-active-task attempts!"               → inline alert title · warning · reactivate rate limit
"Invalid category!"                               → inline alert title · danger · stale category on add/edit
"Please choose a valid category."                 → inline alert body · stale category
"Invalid plan!"                                   → inline alert title · danger · stale plan on add/edit
"Please choose a valid plan."                     → inline alert body · stale plan
"This will mark the task as completed."           → inline alert body · completion confirm
"This task will be moved back to active."         → inline alert body · reactivate confirm
"Your task created successfully"                  → toast title · success · create done
"Your task updated"                               → toast title · info · update done
"Your task deleted"                               → toast title · info · delete done
"Your task completed"                             → toast title · info · completion done
"Your task re activated"                          → toast title · info · reactivate done
"Task title is required."                         → validation message · title required
"Task title cannot exceed 255 characters."        → validation message · title max
"Task description cannot exceed 5000 characters." → validation message · description max
"Task date is required."                          → validation message · date required
"No tasks yet"                                    → empty state · no data yet
"No tasks found"                                  → empty state · search/filter yielded nothing
"min"                                             → unit text · duration display (task card + Minutes support)
"hour"                                            → unit text · duration display (Minutes support)
"and"                                             → unit text · duration join word (Minutes support)

## Reports

"My reports"      → page heading · reports card title
"This week"       → filter option · date-range preset (reports)
"Last week"       → filter option · date-range preset (reports)
"This month"      → filter option · date-range preset (reports)
"Last month"      → filter option · date-range preset (reports)
"Custom"          → filter option · date-range preset (reports)
"Tasks Summary"   → section heading · reports tasks card
"Total Tasks"     → stat label · reports tasks card
"Completed Tasks" → stat label · reports tasks card
"Completion Rate" → stat label · reports tasks card
"Estimated Time"  → stat label · reports tasks card
"Plans Summary"   → section heading · reports plans card
"Total Plans"     → stat label · reports plans card
"Completed Plans" → stat label · reports plans card
"Workload Chart"  → section heading · reports workload card
"Remaining"       → stat label + chart legend · workload chart (reports)

## Dashboard

"Tasks needing attention"                                   → section heading · attention card
"These tasks are due or their alarm time has been reached." → supporting text · attention card subtitle
"View"                                                      → button label · dashboard view-all link (with All + tasks)
"tasks"                                                     → button label · dashboard view-all link count word
"View task"                                                 → link · attention-list item action
"1 day ago"                                                 → badge text · overdue age, singular (attention list)
":count days ago"                                           → badge text · overdue age, plural (attention list)
"Due today"                                                 → badge text · due-today chip
"Due tomorrow"                                              → badge text · due-tomorrow chip
"Due in"                                                    → badge prefix · due countdown (attention list)
":count days"                                               → badge value · due countdown (attention list)
"No tasks need your attention right now."                   → empty state · attention card
"This week's workload"                                      → section heading · workload card
"Estimated workload based on task duration."                → supporting text · workload card subtitle
"No tasks"                                                  → workload label · level none (workload grid)
"Light"                                                     → workload label · level short name (attention chips)
"Moderate"                                                  → workload label · level short name (attention chips)
"Heavy"                                                     → workload label · level short name (attention chips)
"Very heavy"                                                → workload label · level short name (attention chips)
"Light (1–120 min)"                                         → workload legend · level with range (workload grid)
"Moderate (121–240 min)"                                    → workload legend · level with range (workload grid)
"Heavy (241–360 min)"                                       → workload legend · level with range (workload grid)
"Very heavy (361+ min)"                                     → workload legend · level with range (workload grid)

## Errors

"Page not found"                                                      → error page title · 404 (translated dynamically in errors/shell.blade.php)
"The page you are looking for does not exist or may have been moved." → error page body · 404 (dynamic __() in errors/shell.blade.php)
"You do not have access to this page"                                 → error page title · 403 (dynamic __() in errors/shell.blade.php)
"Page expired"                                                        → error page title · 419 (dynamic __() in errors/shell.blade.php)
"The form timed out, go back and try again."                          → error page body · 419 (dynamic __() in errors/shell.blade.php)
"Too many requests"                                                   → error page title · 429 (dynamic __() in errors/shell.blade.php)
"Take a short break and try again."                                   → error page body · 429 (dynamic __() in errors/shell.blade.php)
"Something went wrong"                                                → error page title · 500 + fallback for uncovered statuses (dynamic __())
"An unexpected error occurred, please try again."                     → error page body · 500 (dynamic __() in errors/shell.blade.php)
"Under maintenance"                                                   → error page title · 503 (dynamic __() in errors/shell.blade.php)
"We will be back soon."                                               → error page body · 503 (dynamic __() in errors/shell.blade.php)
"Go to home"                                                          → button · error card home link

## Components

"Loading"                 → aria-label · busy indicator (categories, plans, tasks)
"Close notification"      → aria-label · toast close button
"Scroll to the beginning" → aria-label · horizontal-scroll start button
"Scroll to the end"       → aria-label · horizontal-scroll end button
