<?php
session_start();
header('Content-Type: application/json');
require '../db.php';
require '../helpers/log.php';

$first  = trim($_POST['first_name']  ?? '');
$middle = trim($_POST['middle_name'] ?? '');
$last   = trim($_POST['last_name']   ?? '');
$suffix = trim($_POST['suffix']      ?? '');
$bd     = $_POST['birthdate']        ?? '';
$age    = (int)($_POST['age']        ?? 0);
$status = $_POST['civil_status']     ?? '';
$gender = $_POST['gender']           ?? '';
$purok  = $_POST['purok']            ?? '';

$ok_status = ['Single','Married','Widowed','Separated'];
$ok_gender = ['Male','Female','Other'];
$ok_purok  = ['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'];
$name_re   = '/^[A-Za-z\s.\-\']+$/';

// Middle name may be "N/A" when a person has no middle name.
if (strcasecmp($middle, 'N/A') === 0) $middle = 'N/A';

foreach (['First name' => $first, 'Middle name' => $middle, 'Last name' => $last] as $label => $val) {
    if ($label === 'Middle name' && $val === 'N/A') continue;
    if ($val === '' || !preg_match($name_re, $val)) {
        echo json_encode(['success' => false, 'error' => "$label is required and may only contain letters, spaces, dots, hyphens, and apostrophes."]);
        exit;
    }
}
if (!$bd || $age < 1 || $age > 120
    || !in_array($status, $ok_status)
    || !in_array($gender, $ok_gender)
    || !in_array($purok,  $ok_purok)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}
if ($suffix !== '' && !preg_match('/^[A-Za-z.\s]{1,10}$/', $suffix)) {
    echo json_encode(['success' => false, 'error' => 'Suffix may only contain letters, dots, and spaces (max 10 characters).']);
    exit;
}
$suffix_db = $suffix !== '' ? $suffix : null;
// Exclude an "N/A" middle name from the displayed full name.
$middle_name_part = ($middle === 'N/A') ? '' : $middle;
$full_name = trim(preg_replace('/\s+/', ' ', "$first $middle_name_part $last"));

// A name alone is not unique (two different people can share a name), so a
// resident counts as a duplicate only when the full name AND birthdate match.
// `<=>` is MySQL's NULL-safe equality, needed because suffix may be NULL.
$dup = $conn->prepare(
    "SELECT id FROM residents
     WHERE first_name = ? AND middle_name = ? AND last_name = ?
       AND birthdate = ? AND suffix <=> ?
     LIMIT 1"
);
$dup->bind_param('sssss', $first, $middle, $last, $bd, $suffix_db);
$dup->execute();
if ($dup->get_result()->fetch_assoc()) {
    echo json_encode(['success' => false, 'error' => 'A resident with the same name and birthdate already exists.']);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO residents (first_name, middle_name, last_name, full_name, suffix, birthdate, age, civil_status, gender, purok)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssssisss', $first, $middle, $last, $full_name, $suffix_db, $bd, $age, $status, $gender, $purok);

if ($stmt->execute()) {
    $logName = $suffix_db ? "$full_name $suffix_db" : $full_name;
    logAction($conn, 'Added resident', $logName);
    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
}
