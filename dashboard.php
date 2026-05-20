<?php
require 'auth.php';
require 'db.php';

$fresh_login = !empty($_SESSION['fresh_login']);
if ($fresh_login) unset($_SESSION['fresh_login']);

$total         = $conn->query("SELECT COUNT(*) FROM residents")->fetch_row()[0];
$male          = $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Male'")->fetch_row()[0];
$female        = $conn->query("SELECT COUNT(*) FROM residents WHERE gender='Female'")->fetch_row()[0];
$vaccinated    = $conn->query("SELECT COUNT(DISTINCT resident_id) FROM survey_responses WHERE vaccination_status='Vaccinated'")->fetch_row()[0];
$unvaccinated  = $conn->query("SELECT COUNT(DISTINCT resident_id) FROM survey_responses WHERE vaccination_status='Unvaccinated'")->fetch_row()[0];
$with_symptoms = $conn->query("SELECT COUNT(DISTINCT resident_id) FROM survey_responses WHERE no_symptoms=0 AND (has_fever=1 OR has_cough=1 OR has_fatigue=1 OR has_headache=1)")->fetch_row()[0];

$res = $conn->query("
    SELECT r.full_name, r.suffix, r.gender,
           COALESCE(sr.vaccination_status, '')   AS vaccination_status,
           COALESCE(sr.has_fever, 0)             AS has_fever,
           COALESCE(sr.has_cough, 0)             AS has_cough,
           COALESCE(sr.has_fatigue, 0)           AS has_fatigue,
           COALESCE(sr.has_headache, 0)          AS has_headache,
           COALESCE(sr.no_symptoms, 0)           AS no_symptoms,
           sr.id                                  AS survey_id,
           sr.last_checkup
    FROM residents r
    LEFT JOIN survey_responses sr ON sr.id = (
        SELECT id FROM survey_responses
        WHERE resident_id = r.id
        ORDER BY submitted_at DESC LIMIT 1
    )
    ORDER BY r.full_name ASC
");
$rows = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <script>
        // Block sidebar transitions until JS finishes applying the saved state.
        document.documentElement.classList.add('sb-preload');
<?php if ($fresh_login): ?>
        // Fresh login: forget any previously saved sidebar state so it opens expanded.
        try { localStorage.removeItem('sidebarState'); } catch(e) {}
<?php endif; ?>
    </script>
</head>

<body>

    <!-- MOBILE TOPBAR -->
    <header class="mobile-topbar">
        <button class="mobile-menu-btn" id="openSidebar" aria-label="Open menu" type="button">
            <span class="material-icons">menu</span>
        </button>
        <span class="mobile-brand">BHMS</span>
    </header>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="sidebar" id="sidebar">
        <script>
            // Apply saved sidebar state immediately so first paint matches the final state.
            (function () {
                try {
                    var sb = document.getElementById('sidebar');
                    if (!sb) return;
                    var pref = localStorage.getItem('sidebarState');
                    var w = window.innerWidth;
                    if (w > 1024) {
                        if (pref === 'collapsed') sb.classList.add('collapsed');
                    } else if (w > 768) {
                        if (pref === 'expanded') sb.classList.add('force-expanded');
                    }
                } catch (e) {}
            })();
        </script>
        <button class="sidebar-close-btn" id="closeSidebar" aria-label="Close menu" type="button">
            <span class="material-icons">close</span>
        </button>
        <div class="sidebar-header">
            <h2>BHMS</h2>
            <button class="sidebar-collapse-btn" id="collapseSidebar" aria-label="Collapse sidebar" type="button" title="Collapse sidebar">
                <span class="material-icons">chevron_left</span>
            </button>
        </div>
        <ul>
            <li>
                <a href="dashboard.php" class="active" data-tooltip="Dashboard">
                    <span class="material-icons">dashboard</span>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="residents.php" data-tooltip="Residents">
                    <span class="material-icons">groups</span>
                    <span class="nav-label">Residents</span>
                </a>
            </li>
            <li>
                <a href="notes.php" data-tooltip="Health Notes">
                    <span class="material-icons">medical_information</span>
                    <span class="nav-label">Health Notes</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <button class="sidebar-user" id="sidebarUser" type="button" title="Account settings" data-tooltip="Account settings">
                <span class="material-icons">account_circle</span>
                <span class="sidebar-user-name"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin') ?></span>
                <span class="material-icons sidebar-user-arrow">chevron_right</span>
            </button>
            <a href="logout.php" class="sidebar-logout-btn" data-tooltip="Logout">
                <span class="material-icons">logout</span>
                <span class="logout-label">Logout</span>
            </a>
        </div>
    </div>

    <div class="dashboard-main">

        <div class="header">
            <h1>Barangay Health Dashboard</h1>
            <p>Resident Health Monitoring Overview</p>
        </div>

        <div class="cards">

            <div class="card total dashboard-filter" data-filter="all">
                <span class="material-icons">group</span>
                <p>Total Residents</p>
                <h3><?= $total ?></h3>
            </div>

            <div class="card male dashboard-filter" data-filter="Male">
                <span class="material-icons">man</span>
                <p>Male Residents</p>
                <h3><?= $male ?></h3>
            </div>

            <div class="card female dashboard-filter" data-filter="Female">
                <span class="material-icons">woman</span>
                <p>Female Residents</p>
                <h3><?= $female ?></h3>
            </div>

            <div class="card vaccinated dashboard-filter" data-filter="Vaccinated">
                <span class="material-icons">vaccines</span>
                <p>Vaccinated</p>
                <h3><?= $vaccinated ?></h3>
            </div>

            <div class="card unvaccinated dashboard-filter" data-filter="Unvaccinated">
                <span class="material-icons">warning</span>
                <p>Unvaccinated</p>
                <h3><?= $unvaccinated ?></h3>
            </div>

            <div class="card symptoms dashboard-filter" data-filter="Symptoms">
                <span class="material-icons">sick</span>
                <p>With Symptoms</p>
                <h3><?= $with_symptoms ?></h3>
            </div>

        </div>

        <div class="dashboard-table-section">

            <div class="dashboard-table-header">
                <div class="dashboard-title-group">
                    <span class="material-icons dashboard-table-icon">table_chart</span>
                    <div>
                        <h2 id="dashboardTableTitle">All Residents</h2>
                        <p id="dashboardFilterText">Showing complete resident records</p>
                    </div>
                </div>

                <div class="dashboard-table-tools">
                    <div class="search-box">
                        <span class="material-icons">search</span>
                        <input type="text" id="dashboardSearch" placeholder="Search residents...">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th><span class="th-cell"><span class="material-icons th-icon">person</span>Name</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">wc</span>Gender</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">vaccines</span>Vaccination</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">sick</span>Symptoms</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">event</span>Last Checkup</span></th>
                    </tr>
                </thead>

                <tbody id="dashboardResidentTable">
                <?php
                $sym_icons = ['Fever' => 'thermostat', 'Cough' => 'sick', 'Fatigue' => 'bedtime', 'Headache' => 'psychology'];
                foreach ($rows as $row):
                    $vax     = htmlspecialchars($row['vaccination_status']);
                    $gender  = htmlspecialchars($row['gender']);
                    $checkup = $row['last_checkup'] ? date('M j, Y', strtotime($row['last_checkup'])) : '—';

                    $vax_pill_class = '';
                    $vax_icon       = '';
                    if ($vax === 'Vaccinated')           { $vax_pill_class = 'vax-yes';     $vax_icon = 'check_circle'; }
                    elseif ($vax === 'Partially Vaccinated') { $vax_pill_class = 'vax-partial'; $vax_icon = 'pending'; }
                    elseif ($vax === 'Unvaccinated')     { $vax_pill_class = 'vax-no';      $vax_icon = 'cancel'; }

                    $symptom_parts = [];
                    if ($row['has_fever'])    $symptom_parts[] = 'Fever';
                    if ($row['has_cough'])    $symptom_parts[] = 'Cough';
                    if ($row['has_fatigue'])  $symptom_parts[] = 'Fatigue';
                    if ($row['has_headache']) $symptom_parts[] = 'Headache';

                    $has_any_symptom = !empty($symptom_parts) && !$row['no_symptoms'];
                    $symptom_filter  = $has_any_symptom ? 'Yes' : 'No';
                ?>
                    <tr data-gender="<?= $gender ?>"
                        data-vaccine="<?= $vax ?>"
                        data-symptoms="<?= $symptom_filter ?>">
                        <?php
                            $row_suffix  = trim($row['suffix'] ?? '');
                            $row_display = $row_suffix !== '' ? $row['full_name'] . ' ' . $row_suffix : $row['full_name'];
                        ?>
                        <td data-label="Name"><?= htmlspecialchars($row_display) ?></td>
                        <td data-label="Gender">
                            <?php if ($gender === 'Male'): ?>
                                <span class="material-icons gender-icon male-icon">man</span> Male
                            <?php elseif ($gender === 'Female'): ?>
                                <span class="material-icons gender-icon female-icon">woman</span> Female
                            <?php else: ?>
                                <span class="material-icons gender-icon">person</span> Other
                            <?php endif; ?>
                        </td>
                        <td data-label="Vaccination">
                            <?php if ($vax): ?>
                                <span class="status-pill <?= $vax_pill_class ?>">
                                    <span class="material-icons"><?= $vax_icon ?></span> <?= $vax ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#94a3b8;">No Survey</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Symptoms">
                            <?php if (!$row['survey_id']): ?>
                                <span style="color:#94a3b8;">—</span>
                            <?php elseif ($row['no_symptoms'] || empty($symptom_parts)): ?>
                                <span class="status-pill none"><span class="material-icons">check_circle</span> None</span>
                            <?php else: ?>
                                <div class="pill-group">
                                    <?php foreach ($symptom_parts as $sp): $cls = strtolower($sp); ?>
                                        <span class="status-pill <?= $cls ?>">
                                            <span class="material-icons"><?= $sym_icons[$sp] ?? 'sick' ?></span> <?= $sp ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td data-label="Last Checkup"><span class="material-icons checkup-icon">calendar_today</span> <?= $checkup ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="5" class="empty-state-cell">No residents found. Add residents first.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination" id="dashboardPagination">
                <button class="pg-btn pg-prev" title="Previous page">
                    <span class="material-icons">chevron_left</span>
                </button>
                <span class="pg-info">Page 1 of 1</span>
                <button class="pg-btn pg-next" title="Next page">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>

        </div>

        <?php include 'includes/account_modal.php'; ?>

        <script src="js/script.js"></script>

</body>

</html>
