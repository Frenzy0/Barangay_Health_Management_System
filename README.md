# Barangay Health Management System (BHMS)

A simple online system that lets barangay residents fill out a health survey
and lets the barangay admin view, manage, and keep track of those records
in one place.

---

## Table of Contents

1. [Features](#features)
2. [What's Inside](#whats-inside)
3. [Records We Keep](#records-we-keep)
4. [How to Set It Up](#how-to-set-it-up)
5. [How It Works](#how-it-works)
6. [Who Can Use What](#who-can-use-what)

---

## Features

**For residents (no login needed)**
- Easy-to-fill health survey form
- Age is filled in automatically once the birthday is entered
- A printable blank survey form for offline use

**For the barangay admin (login required)**
- Safe and private login
- Dashboard that shows live numbers at a glance:
  - Total residents
  - Number of males and females
  - Vaccinated and unvaccinated counts
  - Residents showing symptoms
- Quick filters — tap a card (like "Vaccinated") to instantly filter the list
- Add, edit, or remove resident records with a confirmation step
- Health notes view that shows each resident's latest survey on a card
- Account settings to change username and password
- Activity history showing what the admin has done (logins, edits, etc.)
- Works on both phones and computers, with a sidebar that tucks away on small screens

---

## What's Inside

```
bhms/
├── index.php              # The survey form residents fill out
├── login.php              # Admin login page
├── logout.php             # Logs the admin out
├── dashboard.php          # Main admin page with stats and resident list
├── residents.php          # Where the admin adds, edits, or removes residents
├── notes.php              # Health notes for each resident
├── print_survey.php       # Printable blank survey
├── setup_admin.php        # Used once to create the first admin account
│
├── handlers/              # Behind-the-scenes pages that save and update data
├── helpers/               # Small helper files
├── includes/              # Reusable parts of the pages
├── css/                   # Styling for how the pages look
├── js/                    # Makes buttons, filters, and modals work
└── bhms_db.sql            # The starting database file
```

---

## Records We Keep

The system saves four kinds of records:

### Admin Accounts
Login details for the barangay admin.

### Residents
Basic info about each resident — name, birthday, age, civil status, gender, and purok.

### Health Surveys
Each survey a resident submits — vaccination status, last checkup, symptoms (fever, cough, fatigue, headache), and any health notes.

### Activity History
A record of what the admin does in the system (logins, adding or editing residents, etc.).

---

## How to Set It Up

### 1. What You Need
- [XAMPP](https://www.apachefriends.org/) installed on the computer

### 2. Put the Project in the Right Place
Copy the project folder to:
```
C:\xampp\htdocs\bhms\
```

### 3. Start XAMPP
Open the XAMPP control panel and start **Apache** and **MySQL**.

### 4. Set Up the Database
1. Open `http://localhost/phpmyadmin` in a browser
2. Create a new database called **`bhms_db`**
3. Open it, click **Import**, choose [bhms_db.sql](bhms_db.sql), then click **Go**

### 5. Create the First Admin Account
Open this link once:
```
http://localhost/bhms/setup_admin.php
```
This creates the default account:
- **Username:** `admin`
- **Password:** `admin123`

### 6. Open the App
- Survey for residents: `http://localhost/bhms/`
- Admin login: `http://localhost/bhms/login.php`

---

## How It Works

### 1. Resident Submits a Survey
A resident opens the homepage and fills out the survey (personal info, vaccination status, symptoms, and notes). When they submit, the system either updates their existing record or creates a new one, then saves the survey. A success message appears once it's done.

### 2. Admin Logs In
The admin enters their username and password on the login page. If correct, they're taken to the dashboard. The login is also saved in the activity history.

### 3. Pages Are Protected
The dashboard and other admin pages can't be opened by just anyone — if someone isn't logged in, they're sent back to the login page.

### 4. Dashboard
The dashboard shows live counts (total residents, vaccinated, with symptoms, etc.) and a list of all residents with their latest health status. Clicking a stat card filters the list instantly.

### 5. Managing Residents
The admin can add, edit, or delete residents. Deleting a resident asks for confirmation first, and also removes their old surveys. Every action is saved in the activity history.

### 6. Health Notes
This page shows one card per resident with their latest survey — vaccination status, last checkup, symptoms, and any notes from the survey.

### 7. Account Settings
From any admin page, the admin can open the settings to change their username, change their password, or view a list of their recent activity.

### 8. Logging Out
Clicking logout ends the session and brings the admin back to the login page.

---

## Who Can Use What

| Page                | Who Can Open It    | What It's For                          |
|---------------------|--------------------|----------------------------------------|
| Home (survey)       | Anyone             | Submit a health survey                 |
| Print Survey        | Anyone             | Print a blank survey form              |
| Login               | Anyone             | Admin login                            |
| Setup Admin         | One-time use       | Create the first admin account         |
| Dashboard           | Admin only         | View stats and resident list           |
| Residents           | Admin only         | Add, edit, or remove residents         |
| Health Notes        | Admin only         | View each resident's latest survey     |
| Logout              | Admin only         | Sign out of the admin account          |
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
"# Barangay_Health_Management_System" 
