# Number Gear — Database & Dashboard Setup (XAMPP)

This update moves user accounts from the old `data/users.json` file to a
real MySQL database, adds an **institution / self-paced** choice to
registration, and adds an **Instructor Dashboard** that shows every
learner's progress.

## 1. What changed

- **New:** `config/db.php` — the single place that holds your database
  connection settings.
- **New:** `database/schema.sql` — creates the `number_gear` database and
  its two tables: `users` and `progress`.
- **New:** `api/save_progress.php` — a small endpoint the game pages call
  in the background to save a learner's score to the database.
- **New:** `instructor/dashboard.php` — the progress dashboard
  (instructor/admin only).
- **Changed:** `auth/auth.php` now reads/writes MySQL instead of the JSON
  file.
- **Changed:** `auth/register.php` has two new fields: *I will be
  learning as...* (Institution / Self-paced) and, only when
  "Institution" is picked, *Name of institution*.
- **Changed:** `assets/js/storage.js` still uses `localStorage` for
  instant feedback in the game, but now also pushes every score update to
  the database via `api/save_progress.php`.
- **Changed:** `modules/level1..level6/index.php` now require login (so
  the server knows *which* learner is playing) and pass the learner's id
  to the page's JavaScript.

The old `data/users.json` file is no longer read by the app — it's left
in place only as a backup of your old accounts.

## 2. One-time setup

1. Copy/keep this whole `number-gear` folder inside your XAMPP `htdocs`
   folder, e.g. `C:\xampp\htdocs\number-gear`.
2. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. Go to `http://localhost/phpmyadmin`.
4. Click the **SQL** tab, open `database/schema.sql` from this project,
   paste its contents in, and click **Go**.
   - This creates the `number_gear` database with `users` and
     `progress` tables.
   - **Already set this up before?** Just run
     `database/migration_add_instructor.sql` instead — it adds the new
     `instructor_id` column to your existing `users` table without
     touching any data you already have.
5. Open `config/db.php` and check the values match your MySQL setup.
   The defaults (`root` / empty password / `localhost`) are correct for
   a fresh XAMPP install — most people won't need to change anything.

### Optional: bring back your two existing admin accounts

`database/schema.sql` has a commented-out `INSERT` block at the bottom
with the two admin accounts that were in `data/users.json`
(`gear.admin@gmail.com` and `paul@gmail.com`). Their passwords are
already securely hashed, so you can uncomment those lines and run them
if you want those logins to keep working. Otherwise, just register fresh
accounts in step 3 below.

## 3. Try it out

1. Visit `http://localhost/number-gear/auth/register.php`.
2. Create an **instructor** account — pick either "Institution" (and
   type an institution name) or "Self-paced".
3. Create a **learner** account the same way.
4. Log in as the learner and play a level or two — this writes rows into
   the `progress` table.
5. Log out, log back in as the instructor, and click the **📊 Dashboard**
   button in the top bar (or go straight to
   `http://localhost/number-gear/instructor/dashboard.php`).
   You'll see every learner, their institution/self-paced badge, a
   per-level mini progress chart, an overall % complete, and when they
   were last active. Use the dropdown to filter by institution.

## 4. Assigning learners to instructors (institution mode only)

- When a **learner** picks **Institution** during registration and types
  an institution name, a "Choose your instructor" dropdown appears,
  populated live from whichever instructors are already registered
  under that exact institution name. If the learner can't find their
  instructor yet (e.g. the instructor hasn't registered), they can still
  finish registering — they'll just be unassigned for now.
- When that **instructor** logs in and opens the dashboard, they'll see
  a **"Unassigned learners at <their institution>"** section listing
  anyone waiting to be claimed, with a one-click **Claim as my learner**
  button.
- **Admins** see every learner regardless of institution, and get a
  dropdown on each row to assign or reassign that learner to any
  instructor — useful for fixing mistakes or onboarding manually.
- Instructors only ever see learners assigned to *them* on the main
  dashboard table; the "Unassigned" section is the only way they pick up
  new learners.

## 5. Notes / things you can extend later

- Admin accounts can also open the dashboard (it's not learner-exclusive
  to instructors).
- Right now "institution" is a free-text field on the `users` table
  (`institution_name`). If you later want a fixed list of approved
  institutions (e.g. to avoid typos like "Sunrise Primary" vs "Sunrise
  Primary School"), that's a natural next step: add an `institutions`
  table and turn the text field into a dropdown.
- Progress syncing uses `fetch()` with the session cookie, so a learner
  must be logged in for a score to be saved to the database — this is by
  design, since progress always needs to be tied to a specific account.
