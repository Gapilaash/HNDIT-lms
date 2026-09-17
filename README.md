# HNDIT Learning Management System

A simple PHP + MySQL LMS where an Admin manages subject PDFs organized by
academic year and semester, and Students register, log in, browse materials,
download PDFs, and submit feedback.

## Tech Stack
- PHP (plain, PDO for database access)
- MySQL
- No frameworks, no build tools — just upload and run

## Folder Structure
```
/lms
  /admin        -> admin login, dashboard, add/edit/delete subject, view feedback
  /student      -> register, login, dashboard, subjects, download, feedback
  /includes     -> db connection, shared functions, header/footer
  /assets       -> style.css
  /uploads      -> uploaded subject PDFs (must be writable)
  config.php    -> database credentials (EDIT THIS)
  database.sql  -> run this once to create the database & seed admin
  index.php     -> landing page
```

## 1. Local Setup (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start Apache + MySQL.
2. Copy the `lms` folder into `htdocs` (e.g. `C:\xampp\htdocs\lms`).
3. Open `http://localhost/phpmyadmin`, click **Import**, and import `database.sql`.
   This creates the `hndit_lms` database, its tables, and a seed admin account.
4. Open `config.php` and confirm the values match your local MySQL
   (defaults `root` / empty password work for most XAMPP installs).
5. Visit `http://localhost/lms/` in your browser.

### Default Admin Login
- Email: `admin@hndit.lk`
- Password: `admin123`

**Change this password after your first login for security** (you can do this
directly in phpMyAdmin by updating the `users` table with a new bcrypt hash,
or add a "change password" feature later).

## 2. Deploying to Free Hosting (e.g. InfinityFree)

1. Create a free account and a hosting slot on InfinityFree (or similar).
2. In their control panel, create a MySQL database — note the host, database
   name, username, and password they give you.
3. Open `config.php` and replace the 4 values (`DB_HOST`, `DB_NAME`,
   `DB_USER`, `DB_PASS`) with those credentials. Nothing else needs to change.
4. Open their phpMyAdmin and import `database.sql` into the new database.
5. Upload all project files via FTP (e.g. FileZilla) or their File Manager
   into the `htdocs` (or `public_html`) folder.
6. Make sure the `uploads/` folder has write permissions (`755` or `775`)
   so the Admin can upload PDFs.
7. Visit your site's URL and test registration, login, subject upload, and
   PDF download end-to-end.

## Features

**Admin**
- Login (seeded account, see above)
- Add / edit / delete subjects (name, year, semester, PDF upload)
- View all student feedback

**Student**
- Register / login
- Browse subjects by year (1st/2nd) and semester (1st/2nd)
- Download subject PDFs
- Submit feedback (general or about a specific subject)

## Notes / Next Steps You May Want
- Add a "forgot password" flow
- Add pagination if subject/feedback lists grow large
- Add search/filter on the admin subject list
- Enforce HTTPS in production
- Consider moving to cloud storage (e.g. S3) if PDF volume grows large
