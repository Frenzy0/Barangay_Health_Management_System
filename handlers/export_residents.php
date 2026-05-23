<?php
session_start();
require '../db.php';
require '../helpers/log.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$format = strtolower($_GET['format'] ?? 'csv');

$res = $conn->query("
    SELECT id, full_name, suffix, birthdate, age, civil_status, gender, purok
    FROM residents
    ORDER BY full_name ASC
");
$residents = $res->fetch_all(MYSQLI_ASSOC);

$generatedAt = date('F j, Y \a\t g:i A');
$fileDate    = date('Y-m-d');
$total       = count($residents);

/* ============================ CSV EXPORT ============================ */
if ($format === 'csv') {
    logAction($conn, 'Exported residents', 'CSV', "$total record(s)");

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="residents_backup_' . $fileDate . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    // UTF-8 BOM so Excel reads accented characters correctly
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, ['BHMS Resident Records Backup']);
    fputcsv($out, ['Generated: ' . $generatedAt]);
    fputcsv($out, ['Total Residents: ' . $total]);
    fputcsv($out, []);
    fputcsv($out, ['#', 'Full Name', 'Suffix', 'Birthdate', 'Age', 'Civil Status', 'Gender', 'Purok']);

    $i = 1;
    foreach ($residents as $r) {
        fputcsv($out, [
            $i++,
            $r['full_name'],
            $r['suffix'],
            $r['birthdate'],
            $r['age'],
            $r['civil_status'],
            $r['gender'],
            $r['purok'],
        ]);
    }
    fclose($out);
    exit;
}

/* ============================ PDF EXPORT ============================ */
// Rendered as a print-ready page; the browser's "Save as PDF" produces the file.
logAction($conn, 'Exported residents', 'PDF', "$total record(s)");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents Backup — BHMS</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
        body { background: #e2e8f0; color: #0f172a; padding: 30px 16px; }

        .print-controls {
            max-width: 1000px; margin: 0 auto 20px; display: flex; gap: 10px; justify-content: flex-end;
        }
        .pc-btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px;
            border-radius: 8px; border: none; cursor: pointer; font-size: 14px;
            text-decoration: none; font-weight: 600;
        }
        .pc-btn .material-icons { font-size: 18px; }
        .pc-btn-primary { background: #0d9488; color: #fff; }
        .pc-btn-primary:hover { background: #0f766e; }
        .pc-btn-secondary { background: #64748b; color: #fff; }
        .pc-btn-secondary:hover { background: #475569; }

        .sheet {
            max-width: 1000px; margin: 0 auto; background: #fff; padding: 40px;
            box-shadow: 0 8px 24px rgba(15,23,42,.15);
        }

        .report-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            border-bottom: 3px solid #0d9488; padding-bottom: 16px; margin-bottom: 6px;
        }
        .report-header h1 { font-size: 26px; color: #0d9488; letter-spacing: 1px; }
        .report-header .subtitle { font-size: 12px; color: #64748b; }
        .report-meta { text-align: right; font-size: 12px; color: #475569; line-height: 1.6; }

        .doc-title { text-align: center; margin: 18px 0 22px; }
        .doc-title h2 { font-size: 16px; letter-spacing: .5px; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead th {
            background: #0d9488; color: #fff; padding: 9px 8px; text-align: left;
            border: 1px solid #0d9488;
        }
        tbody td { padding: 7px 8px; border: 1px solid #cbd5e1; }
        tbody tr:nth-child(even) { background: #f1f5f9; }
        td.num, th.num { text-align: center; width: 38px; }

        .empty { text-align: center; padding: 24px; color: #64748b; }

        .report-footer {
            margin-top: 26px; padding-top: 14px; border-top: 1px solid #cbd5e1;
            font-size: 11px; color: #64748b; display: flex; justify-content: space-between;
        }
        .sign-row { margin-top: 40px; display: flex; gap: 60px; }
        .sign-row div { flex: 1; }
        .sign-line { border-top: 1px solid #0f172a; padding-top: 4px; font-size: 11px; text-align: center; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-controls { display: none; }
            .sheet { box-shadow: none; max-width: none; padding: 0; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <a href="../residents.php" class="pc-btn pc-btn-secondary"
       onclick="window.close(); return false;">
        <span class="material-icons">arrow_back</span> Back
    </a>
    <button class="pc-btn pc-btn-primary" onclick="window.print()">
        <span class="material-icons">picture_as_pdf</span> Print / Save as PDF
    </button>
</div>

<div class="sheet">

    <div class="report-header">
        <div>
            <h1>BHMS</h1>
            <p class="subtitle">Barangay Health Management System</p>
        </div>
        <div class="report-meta">
            <div><strong>Generated:</strong> <?= htmlspecialchars($generatedAt) ?></div>
            <div><strong>Total Residents:</strong> <?= $total ?></div>
            <div><strong>Prepared by:</strong> <?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></div>
        </div>
    </div>

    <div class="doc-title">
        <h2>RESIDENT RECORDS — BACKUP REPORT</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">#</th>
                <th>Full Name</th>
                <th>Birthdate</th>
                <th class="num">Age</th>
                <th>Civil Status</th>
                <th>Gender</th>
                <th>Purok</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($residents)): ?>
            <tr><td colspan="7" class="empty">No resident records found.</td></tr>
        <?php else: $i = 1; foreach ($residents as $r):
            $suffix  = trim($r['suffix'] ?? '');
            $display = $suffix !== '' ? $r['full_name'] . ' ' . $suffix : $r['full_name'];
        ?>
            <tr>
                <td class="num"><?= $i++ ?></td>
                <td><?= htmlspecialchars($display) ?></td>
                <td><?= htmlspecialchars($r['birthdate'] ?? '') ?></td>
                <td class="num"><?= (int)$r['age'] ?></td>
                <td><?= htmlspecialchars($r['civil_status'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['gender'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['purok'] ?? '') ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <div class="sign-row">
        <div><div class="sign-line">Prepared by</div></div>
        <div><div class="sign-line">Verified by — Barangay Health Officer</div></div>
    </div>

    <div class="report-footer">
        <span>BHMS Resident Records Backup · For official use only</span>
        <span><?= htmlspecialchars($fileDate) ?></span>
    </div>

</div>

<script>
    // Auto-open the print dialog so the user can save directly as PDF.
    window.addEventListener('load', () => setTimeout(() => window.print(), 400));
</script>

</body>
</html>
