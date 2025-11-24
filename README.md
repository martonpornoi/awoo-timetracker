<p align="center"><a href="https://github.com/martonpornoi" target="_blank"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRH0pBR5XcMZUmLkvykceRZ8JsPZwFW_EQZKkaci6q8Nx04m2e99-ccEAqjSbwxRlgaghw&usqp=CAU" width="400" alt="Laravel Logo"></a></p>

# Awoo TimeTracker

A simple tool for booking time spent on projects. This project is a homework for a volunteer position.

## Features

### Roles

- **User** can create time entries, and modify them until the project is Active.

- **Admin**, beyond User roles, can create projects and generate monthly reports. Admins can only be assigned through phpMyAdmin.

### Tools

- **Login System**: An add-on by Laravel, where users can add e-mail, password, and username. Since the task is not built around this module, integrating an SMTP server is not happening.

- **Time Entries**: A form, where all users can assign date time, a project, time spent on a task, and can add description on of the task.

- **Projects**: Admins can create and modify projects by renaming and changing status of the project. It also lists all time entries.

- **Monthly Reports**: Admins can collect aggregated monthly reports. Per project, per user, as a total sum of overall time invested in all projects on a monthly basis. Reports can be deleted or re-generated.

## Setup

### Git

https://git-scm.com/install/windows

---

### XAMPP (PHP 8.2 versions are preferred)

https://www.apachefriends.org/download.html

Make sure it's installs at C:\xampp

Inside `C:\xampp\php\php.ini` enable **extension=zip** or **extension=php_zip.dll**

---

### Composer

https://getcomposer.org/Composer-Setup.exe

When asked, select PHP executable: `C:\xampp\php\php.exe`

---

### Node.js + npm (LTS)

https://nodejs.org/en/download

---

### MySQL

If you want to run against MySQL locally, open `.env` and update the defaults:

```makefile
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=timetracker
DB_USERNAME=root
DB_PASSWORD=
```

Prefer the file-based session driver in local/dev so you don't depend on the `sessions` table:

```
SESSION_DRIVER=file
```

You can switch back to `database` on staging/production once MySQL is available.

---

### Getting Started

```powershell
# 0) Clone and move to project root
git clone git@github.com:martonpornoi/awoo-timetracker.git
cd ./awoo-timetracker/timetracker

# 1) Install PHP dependencies
composer install

# 2) Prepare environment
cp .env.example .env
php artisan key:generate
# edit .env for DB / session driver as noted above

# 3) Install Node dependencies & build assets
npm install
npm run build

# 4) Run database migrations (MySQL must be running if you use it)
php artisan migrate
```

### Everyday Dev Servers

1. Start Apache/MySQL from XAMPP (or run `php artisan serve` + `php artisan queue:listen` if you prefer artisan).
2. Terminal 1: `npm run dev` (Vite).
3. Terminal 2: `php artisan serve`.

### Demo Data

After running migrations, populate the demo accounts/projects/time entries:

```bash
php artisan db:seed
# or explicitly: php artisan db:seed --class=SuperUserSeeder
```

You’ll get:

| Role      | Email               | Password  |
|-----------|---------------------|-----------|
| Super     | `super1@example.com` | `password` |
| Super     | `super2@example.com` | `password` |
| User      | `user1@example.com`  | `password` |
| User      | `user2@example.com`  | `password` |

Three projects (Alpha Portal, Beta Console, Legacy Archive) plus ~30 time entries are created so the UI looks busy when demoing admin reports or the timesheet.

Need to reset your local DB? Use:

```bash
php artisan migrate:fresh --seed
```

This drops all tables, re-runs migrations, and repopulates the demo data above.

## Test

Love yourself. Sleep without stress.

### Automated Tests

This project uses Laravel's built-in **PHPUnit** test runner. Coverage focuses on feature/integration workflows (HTTP + Livewire + sqlite in memory), so `php artisan test` executes the full stack.

---

#### Running Tests

```bash
# run all tests
php artisan test
# run a specific test class
php artisan test tests/Feature/Admin/AdminAccessTest.php
# run a filtered test
php artisan test --filter="locks entries when month is closed"
```

Laravel uses `.env.testing` when running tests. Your normal `.env` (MySQL, database sessions, queues, etc.) is **not** used.

Create a `.env.testing` file to override only what tests need. Recommended setup:

```sh
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

This makes tests fast (in‑memory DB) and isolated (fresh DB every run), without touching local MySQL config.

---

#### What Is Tested

**1. Authorization**

* Non-admins are blocked from `/admin/*`.

**2. Project Management**

* Create, edit, and archive via Livewire.
* Projects sorted with active ones first.

**3. Time Entries**

* Users can create entries for active projects.
* Validation rules (minutes, date, project).
* Locked entries cannot be edited or deleted.

**4. Monthly Reports**

* `generate()` aggregates minutes correctly.
* `close()` locks all entries for that month.
* `reopen()` unlocks only entries locked by that report.

**5. CSV Export**

* Admins can download exports.
* Missing or unauthorized access returns proper status.

---

#### Factories

Factories make test data simple:

```php
User::factory()->admin()->create();
Project::factory()->create();
TimeEntry::factory()->create([
    'project_id' => $project->id,
]);
```

## Contribution

Be not afraid to reach out with ideas. Even if you can't make it come true on your own, your enthusiasm will always be heard and prioritized.

### GitHub Workflow

1. `master` always mirrors what is deployed. Cut short-lived feature branches from `master` using the `type/short-description` pattern (for example `feature/report-csv-export` or `fix/timesheet-lock`).
2. Keep your branch up to date by rebasing on top of `master` after every pull so the history that lands back on `master` is linear and conflict-free.
3. Commit early and often, but keep each commit scoped to one concern; prefer imperative messages (`Add admin CSV guard`) and reference an issue number when possible.
4. Before pushing, run `php artisan test`, `npm run build`, and `php artisan pint` (once Pint is configured) locally so GitHub Actions can mirror the same steps without surprise failures.
5. Push the branch to origin, open or update the issue the work belongs to, and then open a pull request referencing that issue.

### Pull Request

1) Create a new branch from `master` that follows the naming scheme above.

2) Implement the change, add or update tests, and keep `README.md` or other docs in sync.

3) Run the local verification checklist:

   - `php artisan test`
   - `npm run build`
   - Database migrations (if any) applied locally without errors

4) Push the branch and open a pull request on GitHub. Fill out the PR template, tick the “tests ran” checkbox, summarize impacts, and link any related issues.

5) Request a reviewer (or assign the repo owner). GitHub Actions will run the same test/build jobs; expect ~2–3 minutes before statuses report back.

6) Address review comments with follow-up commits (avoid force-pushing unless you are still in draft). Once the reviewer approves and checks are green, the PR is merged via “Squash and Merge” to keep history tidy.

7) Magic-magic (Ooh-Ooh). Magic-magic (Ooh-Ooh). Magic-magic-magic-magic~

### Review Guide

- Git commit message makes sense. Ticket is connected to task (not happening here).

- Code is easy to follow, or documentation can justify complexity.

- Proof that it works through tests and manual testing branch locally.

- Formatting and linting make the code seem like a nice picture on your wall.

- Documentation updated if necessary.
