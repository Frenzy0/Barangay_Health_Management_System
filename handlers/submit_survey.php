<?php
header('Content-Type: application/json');
require '../db.php';

$name      = trim($_POST['full_name']    ?? '');
$suffix    = trim($_POST['suffix']       ?? '');
$birthdate = trim($_POST['birthdate']    ?? '');
$age       = (int)($_POST['age']          ?? 0);
$purok     = $_POST['purok']              ?? '';
$civil     = $_POST['civil_status']       ?? '';
$gender    = $_POST['gender']             ?? '';

$vaccination  = $_POST['vaccination_status']       ?? '';
$last_checkup = trim($_POST['last_checkup'] ?? '');
$symptoms     = $_POST['symptoms']                 ?? [];
$has_fever    = in_array('fever',    $symptoms) ? 1 : 0;
$has_cough    = in_array('cough',    $symptoms) ? 1 : 0;
$has_fatigue  = in_array('fatigue',  $symptoms) ? 1 : 0;
$has_headache = in_array('headache', $symptoms) ? 1 : 0;
$no_symptoms  = in_array('none',     $symptoms) ? 1 : 0;
$notes        = trim($_POST['health_notes']        ?? '');

$ok_gender = ['Male', 'Female', 'Other'];
$ok_purok  = ['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'];
$ok_status = ['Single','Married','Widowed','Separated'];
$ok_vax    = ['Vaccinated', 'Unvaccinated', 'Partially Vaccinated'];

$today_end = strtotime('today 23:59:59');

if (!$name || !preg_match('/^[A-Za-z\s.\-\']+$/', $name)) {
    echo json_encode(['success' => false, 'error' => 'Full name may only contain letters, spaces, dots, hyphens, and apostrophes.']);
    exit;
}
if ($suffix !== '' && !preg_match('/^[A-Za-z.\s]{1,10}$/', $suffix)) {
    echo json_encode(['success' => false, 'error' => 'Suffix may only contain letters, dots, and spaces (max 10 characters).']);
    exit;
}
$suffix_db = $suffix !== '' ? $suffix : null;
if ($age < 1 || $age > 120) {
    echo json_encode(['success' => false, 'error' => 'Age must be between 1 and 120.']);
    exit;
}
if (!$birthdate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate) || strtotime($birthdate) > $today_end) {
    echo json_encode(['success' => false, 'error' => 'Birthdate is required and cannot be in the future.']);
    exit;
}
if (!$last_checkup || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $last_checkup) || strtotime($last_checkup) > $today_end) {
    echo json_encode(['success' => false, 'error' => 'Last checkup date is required and cannot be in the future.']);
    exit;
}
if (strtotime($last_checkup) < strtotime($birthdate)) {
    echo json_encode(['success' => false, 'error' => 'Last checkup cannot be before the birthdate.']);
    exit;
}
if (!in_array($civil, $ok_status)) {
    echo json_encode(['success' => false, 'error' => 'Please select a civil status.']);
    exit;
}
if (!in_array($gender, $ok_gender) || !in_array($purok, $ok_purok) || !in_array($vaccination, $ok_vax)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields correctly.']);
    exit;
}

// Match resident by name + suffix (case-insensitive). If found, update demographics; otherwise create.
$lookup = $conn->prepare(
    "SELECT id FROM residents
     WHERE LOWER(full_name) = LOWER(?)
       AND COALESCE(LOWER(suffix), '') = COALESCE(LOWER(?), '')
     LIMIT 1"
);
$lookup->bind_param('ss', $name, $suffix_db);
$lookup->execute();
$existing = $lookup->get_result()->fetch_assoc();

if ($existing) {
    $resident_id = (int)$existing['id'];
    $upd = $conn->prepare("UPDATE residents SET birthdate=?, age=?, civil_status=?, gender=?, purok=? WHERE id=?");
    $upd->bind_param('sisssi', $birthdate, $age, $civil, $gender, $purok, $resident_id);
    $upd->execute();
} else {
    $ins = $conn->prepare("INSERT INTO residents (full_name, suffix, birthdate, age, civil_status, gender, purok) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $ins->bind_param('sssisss', $name, $suffix_db, $birthdate, $age, $civil, $gender, $purok);
    if (!$ins->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to register resident: ' . $conn->error]);
        exit;
    }
    $resident_id = $conn->insert_id;
}

// Insert the survey response
$stmt = $conn->prepare(
    "INSERT INTO survey_responses
        (resident_id, vaccination_status, last_checkup,
         has_fever, has_cough, has_fatigue, has_headache, no_symptoms, health_notes)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    'issiiiiis',
    $resident_id, $vaccination, $last_checkup,
    $has_fever, $has_cough, $has_fatigue, $has_headache, $no_symptoms, $notes
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error: ' . $conn->error]);
}
