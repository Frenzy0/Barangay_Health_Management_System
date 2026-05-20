<?php
session_start();
header('Content-Type: application/json');
require '../db.php';
require '../helpers/log.php';

$id     = (int)($_POST['id']          ?? 0);
$name   = trim($_POST['full_name']    ?? '');
$suffix = trim($_POST['suffix']       ?? '');
$bd     = $_POST['birthdate']         ?? '';
$age    = (int)($_POST['age']         ?? 0);
$status = $_POST['civil_status']      ?? '';
$gender = $_POST['gender']            ?? '';
$purok  = $_POST['purok']             ?? '';

$ok_status = ['Single','Married','Widowed','Separated'];
$ok_gender = ['Male','Female','Other'];
$ok_purok  = ['Purok 1','Purok 2','Purok 3','Purok 4','Purok 5'];

if (!$id || !$name || !$bd || $age < 1 || $age > 120
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

$stmt = $conn->prepare(
    "UPDATE residents
     SET full_name=?, suffix=?, birthdate=?, age=?, civil_status=?, gender=?, purok=?
     WHERE id=?"
);
$stmt->bind_param('sssisssi', $name, $suffix_db, $bd, $age, $status, $gender, $purok, $id);

if ($stmt->execute()) {
    $logName = $suffix_db ? "$name $suffix_db" : $name;
    logAction($conn, 'Edited resident', $logName);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
}
