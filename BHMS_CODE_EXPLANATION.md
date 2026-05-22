# BHMS — Code Explained

A walkthrough of how the Barangay Health Management System works. Each section shows a small piece of code and tells what it does and why it's there.

## Table of Contents

1. [Big Picture](#big-picture)
2. [The Database (bhms_db.sql)](#1-the-database-bhms_dbsql)
3. [Connecting to the Database (db.php)](#2-connecting-to-the-database-dbphp)
4. [Creating the First Admin (setup_admin.php)](#3-creating-the-first-admin-setup_adminphp)
5. [Protecting Admin Pages (auth.php)](#4-protecting-admin-pages-authphp)
6. [Resident Survey Form (index.php)](#5-resident-survey-form-indexphp)
7. [Saving the Survey (handlers/submit_survey.php)](#6-saving-the-survey-handlerssubmit_surveyphp)
8. [Admin Login Page (login.php)](#7-admin-login-page-loginphp)
9. [Checking the Login (handlers/login.php)](#8-checking-the-login-handlersloginphp)
10. [Activity Logging (helpers/log.php)](#9-activity-logging-helperslogphp)
11. [The Dashboard (dashboard.php)](#10-the-dashboard-dashboardphp)
12. [Managing Residents (residents.php)](#11-managing-residents-residentsphp)
13. [Adding/Editing/Deleting Residents (handlers)](#12-addingeditingdeleting-residents-handlers)
14. [Account Settings (includes/account_modal.php)](#13-account-settings-includesaccount_modalphp)
15. [Exporting Residents (handlers/export_residents.php)](#14-exporting-residents-handlersexport_residentsphp)
16. [Health Notes (notes.php)](#15-health-notes-notesphp)
17. [Printable Blank Form (print_survey.php)](#16-printable-blank-form-print_surveyphp)
18. [Logging Out (logout.php)](#17-logging-out-logoutphp)
19. [Quick Glossary](#quick-glossary)

---

## Big Picture

The system has two kinds of users:

- **Residents** — fill out a health survey on the homepage. No login needed.
- **Admin** — logs in and can view stats, add/edit/delete residents, export records, check health notes, and manage their own account.

Behind the scenes, PHP runs on the server, MySQL stores the data, and HTML/CSS/JavaScript show the pages in the browser.

A simple flow:

> Resident fills survey → PHP saves it to MySQL → Admin sees the data on the Dashboard

---

## 1. The Database (bhms_db.sql)

We have 4 tables:

| Table | What it stores |
|---|---|
| `admins` | Admin username + hashed password |
| `admin_logs` | History of what the admin did (login, edit, export, etc.) |
| `residents` | Personal info of each resident |
| `survey_responses` | Each health survey submitted, plus the emergency contact |

A resident's name is stored in **separate parts** so we can search and match people accurately:

```sql
CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `middle_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `full_name` varchar(100) NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `birthdate` date NOT NULL,
  ...
);
```

`first_name`, `middle_name`, and `last_name` are the editable parts. `full_name` is just those parts joined together for easy display.

The important link: every `survey_responses` row has a `resident_id` that connects it to one row in `residents`.

```sql
CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `vaccination_status` enum('Vaccinated','Unvaccinated','Partially Vaccinated') NOT NULL,
  `has_fever` tinyint(1) DEFAULT 0,
  ...
  `ec_first_name` varchar(50) DEFAULT NULL,
  `ec_contact_number` varchar(15) DEFAULT NULL,
  `ec_relationship` enum('Parent','Spouse','Sibling',...) DEFAULT NULL,
  ...
);
```

The `ec_` columns hold the **emergency contact** — the person to call if something happens to the resident.

**No duplicate people:** the database refuses to store the same person twice.

```sql
ALTER TABLE `residents`
  ADD UNIQUE KEY `uniq_person` (`first_name`,`middle_name`,`last_name`,`birthdate`,`suffix`);
```

A `UNIQUE KEY` means MySQL will reject any insert where all five of those fields match an existing row. Why all five? Because two different people can share a name — so the name **plus the birthdate** is what makes a person unique.

`ON DELETE CASCADE` means: if a resident is deleted, all their surveys are automatically deleted too — no leftover data.

---

## 2. Connecting to the Database (db.php)

```php
<?php
$conn = new mysqli('localhost', 'root', '', 'bhms_db');
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]));
}
$conn->set_charset('utf8mb4');
```

**In simple terms:**
- `mysqli(...)` opens a connection to MySQL (using the default XAMPP settings).
- If something goes wrong, the page stops and shows an error.
- `utf8mb4` lets the database store any character, including emojis or special letters.

This file is included at the top of every page that needs the database — so we don't repeat the same code everywhere.

---

## 3. Creating the First Admin (setup_admin.php)

This page is run only once to make the first admin account.

```php
$username = 'admin';
$password = 'BhmsAdmin@2026';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO admins (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)"
);
```

**Important:** we never store the plain password. `password_hash()` turns it into a long, scrambled string that can't be reversed. Later, when the user logs in, we use `password_verify()` to check if the password they typed matches that scrambled version.

`ON DUPLICATE KEY UPDATE` means: if the `admin` account already exists, just refresh its password instead of erroring out — so re-running the page is safe.

**Why:** even if someone steals the database, they still can't read the real passwords. The page also reminds you to delete `setup_admin.php` after the first run.

---

## 4. Protecting Admin Pages (auth.php)

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
```

**In simple terms:**
- A session is a way for PHP to remember a user across pages (like a wristband at an event).
- If the session doesn't have `admin_id`, the visitor isn't logged in → send them back to the login page.

Every admin page starts with `require 'auth.php';` so random people can't open the dashboard by typing the URL.

---

## 5. Resident Survey Form (index.php)

This is the homepage that any resident can use. It has three sections: **Personal Information**, **Health Status**, and **Emergency Contact**.

The name is collected in separate boxes — first, middle, last, and an optional suffix:

```html
<input type="text" name="first_name" placeholder="Juan">
<div class="input-with-na">
    <input type="text" name="middle_name" id="surveyMiddleName" placeholder="Santos">
    <label class="na-toggle" title="No middle name">
        <input type="checkbox" id="surveyMiddleNameNA"> N/A
    </label>
</div>
<input type="text" name="last_name" placeholder="Dela Cruz">
<input type="text" name="suffix" placeholder="e.g. Jr." maxlength="10">
```

**The "N/A" checkbox:** some people genuinely have no middle name. Instead of leaving the box empty (which the form would reject as missing), the resident ticks **N/A** and the field is filled with the text `N/A`. The server understands this special value and skips that person's middle name when building the full name.

The form also has an Emergency Contact section (`name="ec_first_name"`, `name="ec_contact_number"`, etc.) and a "Print Blank Form" button that opens [print_survey.php](#16-printable-blank-form-print_surveyphp).

A small JavaScript trick auto-fills the age when a birthday is picked:

```javascript
bd.addEventListener('change', () => {
    const d = new Date(bd.value);
    const today = new Date();
    let a = today.getFullYear() - d.getFullYear();
    const m = today.getMonth() - d.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < d.getDate())) a--;
    if (a >= 1 && a <= 120) age.value = a;
    else age.value = "";
});
```

**In simple terms:** subtract the birth year from this year, then take 1 off if the birthday hasn't happened yet this year. That's how we get the correct age.

---

## 6. Saving the Survey (handlers/submit_survey.php)

This is the most important "back-end" file. When the resident clicks Submit, the data is sent here.

**Step 1 — Read the form values:**

```php
$first     = trim($_POST['first_name']  ?? '');
$middle    = trim($_POST['middle_name'] ?? '');
$last      = trim($_POST['last_name']   ?? '');
$age       = (int)($_POST['age'] ?? 0);
$symptoms  = $_POST['symptoms'] ?? [];
$has_fever = in_array('fever', $symptoms) ? 1 : 0;
// Emergency contact
$ec_first  = trim($_POST['ec_first_name'] ?? '');
$ec_number = trim($_POST['ec_contact_number'] ?? '');
```

`trim()` removes accidental spaces. `(int)` makes sure the age is a real number. We check the symptom checkboxes — if "fever" was ticked, save `1`; otherwise `0`. The same is done for the emergency-contact fields.

**Step 2 — Handle the "N/A" middle name:**

```php
if (strcasecmp($middle, 'N/A') === 0) $middle = 'N/A';
...
$middle_name_part = ($middle === 'N/A') ? '' : $middle;
$full_name = trim(preg_replace('/\s+/', ' ', "$first $middle_name_part $last"));
```

If the resident typed "n/a" in any letter case, we store it neatly as `N/A`. When we build the display `full_name`, an `N/A` middle name is left out — so "Juan N/A Dela Cruz" displays simply as "Juan Dela Cruz".

**Step 3 — Validate (check the data is correct):**

```php
$name_re = '/^[A-Za-z\s.\-\']+$/';
foreach (['First name' => $first, 'Middle name' => $middle, 'Last name' => $last] as $label => $val) {
    if ($label === 'Middle name' && $val === 'N/A') continue;
    if ($val === '' || !preg_match($name_re, $val)) {
        echo json_encode(['success' => false, 'error' => "$label is required..."]);
        exit;
    }
}
if (!preg_match('/^09\d{9}$/', $ec_number)) {
    echo json_encode(['success' => false, 'error' => 'Contact number must be 11 digits and start with 09...']);
    exit;
}
```

We reject names with numbers or strange symbols, ages outside 1–120, birthdays or checkup dates in the future, and contact numbers that aren't an 11-digit `09xxxxxxxxx`. Never trust what the user typed — always check.

**Step 4 — Find the resident, or create them:**

```php
$lookup = $conn->prepare(
    "SELECT id FROM residents
     WHERE LOWER(first_name) = LOWER(?)
       AND LOWER(middle_name) = LOWER(?)
       AND LOWER(last_name) = LOWER(?)
       AND COALESCE(LOWER(suffix), '') = COALESCE(LOWER(?), '')
       AND birthdate = ?
     LIMIT 1"
);
```

We look for a resident with the same name parts **and birthdate**. A name alone is not unique (two different people can share a name), so the birthdate is part of the match — this stops a survey being attached to the wrong person. If found → update their details. If not → create a new resident record.

**Step 5 — Save the survey (one survey per resident):**

```php
$found = $conn->prepare("SELECT id FROM survey_responses WHERE resident_id = ? LIMIT 1");
...
if ($existing_survey) {
    $stmt = $conn->prepare("UPDATE survey_responses SET vaccination_status = ?, ... WHERE id = ?");
} else {
    $stmt = $conn->prepare("INSERT INTO survey_responses (resident_id, vaccination_status, ...) VALUES (?, ?, ...)");
}
$stmt->bind_param('issiiiiis...', $resident_id, $vaccination, ...);
```

Each resident keeps **one** survey response. If they already have one, the new answers **overwrite** the old row instead of adding a duplicate.

**Why use `prepare()` + `bind_param()` instead of plain SQL?** This is called a prepared statement. It protects against SQL injection — a famous trick where a hacker types something like `'; DROP TABLE residents; --` into a form to mess up the database. Prepared statements treat user input as data, not code, so the attack fails.

Finally, the page sends back a JSON response like `{"success": true}` and JavaScript shows a "Survey submitted!" message.

---

## 7. Admin Login Page (login.php)

A normal HTML form pointing to `handlers/login.php`:

```html
<form method="POST" action="handlers/login.php" class="login-form" id="loginForm" novalidate>
    <input type="text" id="username" name="username">
    <input type="password" id="password" name="password">
    <button type="submit" class="login-submit-btn">Sign In</button>
</form>
```

It has a small "eye" button to show/hide the password, and JavaScript that checks the typed password **before** sending — it must be 12+ characters with an uppercase letter, a lowercase letter, a number, and a special character. If a check fails, a toast (small popup notice) appears and the form is not submitted.

---

## 8. Checking the Login (handlers/login.php)

```php
$stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE BINARY username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: ../login.php');
    exit;
}

$_SESSION['admin_id']       = $admin['id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['fresh_login']    = true;

logAction($conn, 'Logged in');
header('Location: ../dashboard.php');
```

**In simple terms:**
1. Look up the admin by username. `BINARY` makes the match **case-sensitive** — `Admin` and `admin` are treated as different usernames.
2. If not found, or the password doesn't match the saved hash → show error.
3. If it matches → save the admin's id in the session (so other pages know they're logged in) and send them to the dashboard.
4. `fresh_login` is a one-time flag the dashboard reads to reset the sidebar to its open state right after logging in.
5. Record the login in the activity log.

`password_verify()` is the secure way to compare a typed password against the hashed one in the database.

---

## 9. Activity Logging (helpers/log.php)

```php
function logAction($conn, $action, $target = null, $details = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) return;

    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare(
        "INSERT INTO admin_logs (admin_id, action, target, details) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('isss', $admin_id, $action, $target, $details);
    $stmt->execute();
}
```

A reusable helper. Every time the admin does something important (login, add, edit, delete, export, change password) we call this function so it gets saved in the `admin_logs` table. The admin can review this history later in the Activity Log tab of [Account Settings](#13-account-settings-includesaccount_modalphp).

---

## 10. The Dashboard (dashboard.php)

This page has two big jobs: show summary numbers (cards) and show a table of all residents.

**Counting residents:**

```php
$total      = $conn->query("SELECT COUNT(*) FROM residents")->fetch_row()[0];
$male       = $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Male'")->fetch_row()[0];
$vaccinated = $conn->query("SELECT COUNT(DISTINCT resident_id) FROM survey_responses WHERE vaccination_status='Vaccinated'")->fetch_row()[0];
```

`COUNT(*)` returns how many rows there are. `DISTINCT resident_id` makes sure we count each resident only once, even if they submitted multiple surveys.

**Getting each resident's latest survey:**

```sql
SELECT r.full_name, sr.vaccination_status, sr.has_fever, ...
FROM residents r
LEFT JOIN survey_responses sr ON sr.id = (
    SELECT id FROM survey_responses
    WHERE resident_id = r.id
    ORDER BY submitted_at DESC LIMIT 1
)
```

**In simple terms:** for each resident, attach their most recent survey. `LEFT JOIN` means: include the resident even if they don't have a survey yet (the survey fields will just be empty).

**Showing the data with a loop:**

```php
<?php foreach ($rows as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row_display) ?></td>
        <td><?= htmlspecialchars($row['gender']) ?></td>
        ...
    </tr>
<?php endforeach; ?>
```

`htmlspecialchars()` is a safety function — it makes sure that if someone types `<script>` in their name, it shows as plain text instead of running as code. This protects against XSS attacks (Cross-Site Scripting).

The filter cards (Total, Male, Vaccinated, etc.) use a `data-filter` attribute, and JavaScript reads that to filter the table when a card is clicked.

---

## 11. Managing Residents (residents.php)

Very similar to the dashboard, but each row has Edit and Delete buttons, and the row carries the resident's name parts in `data-` attributes so JavaScript can pre-fill the Edit modal:

```html
<tr data-id="..." data-firstname="..." data-middlename="..."
    data-lastname="..." data-suffix="...">
    ...
    <button class="edit-btn editResidentBtn"><span class="material-icons">edit_note</span> Edit</button>
    <button class="delete-btn deleteResidentBtn"><span class="material-icons">delete_outline</span></button>
</tr>
```

The page includes modals (popup boxes) for **Add**, **Edit**, and **Delete confirmation**. The Add and Edit modals collect the name in separate first/middle/last/suffix boxes, and the Age field is `readonly` because it is auto-calculated from the birthdate. JavaScript opens these modals, fills them with the resident's current data, and sends the form to the right handler file.

There are also **Export CSV** and **Export PDF** buttons that link to [export_residents.php](#14-exporting-residents-handlersexport_residentsphp).

---

## 12. Adding/Editing/Deleting Residents (handlers)

**Add (handlers/add_resident.php):** validates the input, then checks for a duplicate person before inserting:

```php
$dup = $conn->prepare(
    "SELECT id FROM residents
     WHERE first_name = ? AND middle_name = ? AND last_name = ?
       AND birthdate = ? AND suffix <=> ?
     LIMIT 1"
);
$dup->bind_param('sssss', $first, $middle, $last, $bd, $suffix_db);
```

If a resident with the **same name and birthdate** already exists, the handler refuses and returns an error. `<=>` is MySQL's NULL-safe equality — a normal `=` would fail to match when the suffix is empty (`NULL`), so `<=>` is used to compare suffixes correctly.

Once it passes the check, it inserts the resident, logs the action, and responds with JSON.

**Edit (handlers/edit_resident.php):** almost identical, but the duplicate check has one extra condition — `AND id <> ?` — so a resident is not flagged as a duplicate of *themselves* when you save the same person:

```php
"... AND birthdate = ? AND suffix <=> ? AND id <> ? LIMIT 1"
```

It then runs an `UPDATE` instead of an `INSERT`.

**Delete (handlers/delete_resident.php):**

```php
$stmt = $conn->prepare("DELETE FROM residents WHERE id = ?");
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    logAction($conn, 'Deleted resident', $name);
}
```

Because of the `ON DELETE CASCADE` rule in the SQL, deleting one resident automatically deletes their survey too.

All handlers return JSON (`{"success": true}` or `{"success": false, "error": "..."}`). The JavaScript reads that and updates the page without a full reload — this is what makes the app feel quick.

---

## 13. Account Settings (includes/account_modal.php)

This is a shared modal included on every admin page (`<?php include 'includes/account_modal.php'; ?>`). It has three tabs: **Profile**, **Password**, and **Activity Log**.

**Change username (handlers/update_username.php):**

```php
if (strlen($new_username) < 3 || strlen($new_username) > 50) { ... }
if (!preg_match('/^[A-Za-z0-9_.\-]+$/', $new_username)) { ... }

$check = $conn->prepare("SELECT id FROM admins WHERE username = ? AND id != ? LIMIT 1");
```

It checks the new username is 3–50 valid characters and **not already taken** by another admin, then updates it and refreshes `$_SESSION['admin_username']` so the sidebar shows the new name immediately.

**Change password (handlers/change_password.php):**

```php
if (!password_verify($old_pwd, $row['password_hash'])) {
    echo json_encode(['success' => false, 'error' => 'Old password is incorrect.']);
    exit;
}
if (password_verify($new_pwd, $row['password_hash'])) {
    echo json_encode(['success' => false, 'error' => 'New password must be different from the old one.']);
    exit;
}
$new_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
```

It enforces the same strength rules as the login page (12+ characters, upper, lower, number, special), confirms the **old** password is correct, makes sure the new one is actually different, then saves the new hash.

**Activity Log (handlers/get_logs.php):** returns the admin's 50 most recent actions as JSON, which JavaScript displays in the Activity tab:

```php
$stmt = $conn->prepare(
    "SELECT action, target, details, created_at
     FROM admin_logs WHERE admin_id = ?
     ORDER BY created_at DESC LIMIT 50"
);
```

---

## 14. Exporting Residents (handlers/export_residents.php)

This handler lets the admin download the resident list. It checks the `format` in the URL (`?format=csv` or `?format=pdf`).

**CSV export** sends the data as a spreadsheet file:

```php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="residents_backup_' . $fileDate . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accented characters
fputcsv($out, ['#', 'Full Name', 'Suffix', 'Birthdate', 'Age', ...]);
```

`Content-Disposition: attachment` is what makes the browser **download** the file instead of showing it. The BOM bytes at the start tell Excel the file is UTF-8.

**PDF export** instead builds a clean, styled HTML report page and lets the browser's "Save as PDF" produce the file:

```php
window.addEventListener('load', () => setTimeout(() => window.print(), 400));
```

The page auto-opens the print dialog. Either way, the export is recorded in the activity log with how many records were included.

---

## 15. Health Notes (notes.php)

Shows each resident as a card with their latest survey: vaccination status, last checkup, symptoms, the notes they typed, and their emergency contact.

```php
$res = $conn->query("
    SELECT r.full_name, sr.vaccination_status, sr.health_notes, sr.submitted_at,
           sr.ec_first_name, sr.ec_last_name, sr.ec_contact_number, sr.ec_relationship, ...
    FROM residents r
    INNER JOIN survey_responses sr ON sr.id = (
        SELECT id FROM survey_responses
        WHERE resident_id = r.id
        ORDER BY submitted_at DESC LIMIT 1
    )
");
```

Notice this uses `INNER JOIN` instead of `LEFT JOIN`: residents who never submitted a survey won't appear here, because there are no notes to show.

Each card carries the emergency contact in `data-` attributes. Clicking a card slides open a **detail panel** on the side showing the contact person's name, relationship, and phone number — pulled from the `ec_` fields of the survey.

---

## 16. Printable Blank Form (print_survey.php)

A plain, printer-friendly version of the survey with empty lines and checkboxes — for residents who would rather fill it out on paper.

```html
<button class="pc-btn pc-btn-primary" onclick="window.print()">
    <span class="material-icons">print</span> Print / Save as PDF
</button>
```

It has no PHP logic — it's just a styled layout. The "Print Blank Form" button on [index.php](#5-resident-survey-form-indexphp) opens it, and `window.print()` brings up the browser's print dialog.

---

## 17. Logging Out (logout.php)

```php
<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
```

**In simple terms:** empty the session, destroy it, and send the user back to the homepage. Now they're logged out, and trying to open the dashboard will redirect them to the login page.

---

## Quick Glossary

| Term | Meaning |
|---|---|
| PHP | Server-side language that runs before the page is sent to the browser. |
| MySQL | The database — where all the data is stored. |
| Session | Temporary memory the server keeps about a logged-in user. |
| `$_POST` / `$_GET` | How PHP reads form data sent by the browser. |
| `$_SESSION` | How PHP remembers a user across pages. |
| Prepared statement | Safer way to run SQL queries — prevents SQL injection. |
| `password_hash()` / `password_verify()` | Built-in PHP functions to safely store and check passwords. |
| `htmlspecialchars()` | Cleans output so user-typed code can't run on the page (prevents XSS). |
| JSON | A simple text format used to send data back from handlers to JavaScript. |
| JOIN | A SQL way to combine data from two tables (residents + their surveys). |
| CASCADE | Auto-delete related rows. Delete a resident → their survey also vanishes. |
| UNIQUE KEY | A database rule that blocks duplicate rows (same person twice). |
| `<=>` | MySQL's NULL-safe equality — compares values correctly even when one is empty (NULL). |
| `BINARY` | Forces a case-sensitive text comparison in SQL. |
| Emergency contact (`ec_`) | The person to call if something happens to the resident. |
