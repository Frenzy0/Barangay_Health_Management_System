<?php
require 'auth.php';
require 'db.php';

$res = $conn->query("
    SELECT r.id, r.first_name, r.middle_name, r.last_name,
           r.full_name, r.suffix, r.gender, r.purok,
           sr.vaccination_status,
           sr.has_fever, sr.has_cough, sr.has_fatigue, sr.has_headache, sr.no_symptoms,
           sr.last_checkup, sr.health_notes, sr.submitted_at,
           sr.ec_first_name, sr.ec_middle_name, sr.ec_last_name,
           sr.ec_contact_number, sr.ec_relationship
    FROM residents r
    INNER JOIN survey_responses sr ON sr.id = (
        SELECT id FROM survey_responses
        WHERE resident_id = r.id
        ORDER BY submitted_at DESC LIMIT 1
    )
    ORDER BY r.full_name ASC
");
$notes_data = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Notes — BHMS</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <script>
        // Block sidebar transitions until JS finishes applying the saved state.
        document.documentElement.classList.add('sb-preload');
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

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <script>
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
                <a href="dashboard.php" data-tooltip="Dashboard">
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
                <a href="notes.php" class="active" data-tooltip="Health Notes">
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

    <!-- MAIN CONTENT -->
    <div class="residents-main">

        <div class="notes-header">
            <div class="notes-title-group">
                <span class="material-icons notes-title-icon">medical_information</span>
                <div>
                    <h2>Resident Health Notes</h2>
                    <p>Review residents' reported symptoms, checkups, and health concerns</p>
                </div>
            </div>

            <div class="search-box">
                <span class="material-icons">search</span>
                <input type="text" id="notesSearch" placeholder="Search by name...">
                <button type="button" class="search-filter-btn" id="notesFilterBtn"
                        title="Sort by surname (A–Z)" aria-pressed="false" aria-label="Sort by surname">
                    <span class="material-icons">sort_by_alpha</span>
                </button>
            </div>
            
        </div>
        

        <?php if (empty($notes_data)): ?>
            <div class="notes-empty">
                <span class="material-icons">inbox</span>
                <h3>No health notes yet</h3>
                <p>Once residents submit health surveys, their notes will appear here.</p>
            
            </div>
            
        <?php else: ?>

        <div class="notes-grid" id="notesGrid">
            <?php foreach ($notes_data as $row):
                $submitted = date('M j, Y · g:i A', strtotime($row['submitted_at']));
                $checkup   = $row['last_checkup'] ? date('M j, Y', strtotime($row['last_checkup'])) : '—';
                $vax       = $row['vaccination_status'] ?: '—';
                $notes     = trim((string)$row['health_notes']);

                $row_suffix  = trim($row['suffix'] ?? '');
                $row_display = $row_suffix !== '' ? $row['full_name'] . ' ' . $row_suffix : $row['full_name'];

                // Emergency contact (from the latest survey response).
                // An "N/A" middle name is excluded from the displayed name.
                $ec_first_part  = trim((string)($row['ec_first_name']  ?? ''));
                $ec_middle_part = trim((string)($row['ec_middle_name'] ?? ''));
                $ec_last_part   = trim((string)($row['ec_last_name']   ?? ''));
                if (strcasecmp($ec_middle_part, 'N/A') === 0) $ec_middle_part = '';
                $ec_name = trim(preg_replace('/\s+/', ' ', "$ec_first_part $ec_middle_part $ec_last_part"));
                $ec_rel    = trim((string)($row['ec_relationship']   ?? ''));
                $ec_number = trim((string)($row['ec_contact_number'] ?? ''));

                $sym_list = [];
                if ($row['has_fever'])    $sym_list[] = ['fever',    'Fever',    'thermostat'];
                if ($row['has_cough'])    $sym_list[] = ['cough',    'Cough',    'sick'];
                if ($row['has_fatigue'])  $sym_list[] = ['fatigue',  'Fatigue',  'bedtime'];
                if ($row['has_headache']) $sym_list[] = ['headache', 'Headache', 'psychology_alt'];
            ?>
            <div class="note-card" tabindex="0" role="button"
                 data-name="<?= htmlspecialchars(strtolower($row_display)) ?>"
                 data-fullname="<?= htmlspecialchars($row_display, ENT_QUOTES) ?>"
                 data-firstname="<?= htmlspecialchars($row['first_name'] ?? '', ENT_QUOTES) ?>"
                 data-middlename="<?= htmlspecialchars($row['middle_name'] ?? '', ENT_QUOTES) ?>"
                 data-lastname="<?= htmlspecialchars($row['last_name'] ?? '', ENT_QUOTES) ?>"
                 data-suffix="<?= htmlspecialchars($row_suffix, ENT_QUOTES) ?>"
                 data-purok="<?= htmlspecialchars($row['purok'] ?? '', ENT_QUOTES) ?>"
                 data-gender="<?= htmlspecialchars($row['gender'] ?? '', ENT_QUOTES) ?>"
                 data-ec-name="<?= htmlspecialchars($ec_name, ENT_QUOTES) ?>"
                 data-ec-rel="<?= htmlspecialchars($ec_rel, ENT_QUOTES) ?>"
                 data-ec-number="<?= htmlspecialchars($ec_number, ENT_QUOTES) ?>">
                <div class="note-card-top">
                    <div class="note-avatar"><span class="material-icons">person</span></div>
                    <div class="note-identity">
                        <h3><?= htmlspecialchars($row_display) ?></h3>
                        <p><?= htmlspecialchars($row['purok']) ?> · <?= htmlspecialchars($row['gender']) ?></p>
                    </div>
                    <span class="note-date" title="Last submitted"><?= $submitted ?></span>
                </div>
                
                <div class="note-stats">
                    <div class="note-stat">
                        <span class="material-icons stat-icon">vaccines</span>
                        <div>
                            <span class="stat-label">Vaccination</span>
                            <span class="stat-value"><?= htmlspecialchars($vax) ?></span>
                        </div>
                    </div>
                    <div class="note-stat">
                        <span class="material-icons stat-icon">event</span>
                        <div>
                            <span class="stat-label">Last Checkup</span>
                            <span class="stat-value"><?= htmlspecialchars($checkup) ?></span>
                        </div>
                    </div>
                    
                </div>

                <div class="note-section">
                    <span class="note-section-label">Symptoms Reported</span>
                    <div class="symptom-badges">
                        <?php if ($row['no_symptoms'] || empty($sym_list)): ?>
                            <span class="symptom-badge none">
                                <span class="material-icons">check_circle</span> None
                            </span>
                        <?php else: foreach ($sym_list as [$cls, $label, $icon]): ?>
                            <span class="symptom-badge <?= $cls ?>">
                                <span class="material-icons"><?= $icon ?></span> <?= $label ?>
                            </span>
                        
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <div class="note-section">
                    <span class="note-section-label">Health Notes</span>
                    <textarea readonly placeholder="No additional notes provided."><?= htmlspecialchars($notes) ?></textarea>
                </div>

                <div class="note-card-foot">
                    <span class="material-icons">contact_phone</span>
                    View contact information
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="pagination" id="notesPagination">
            <button class="pg-btn pg-prev" title="Previous page">
                <span class="material-icons">chevron_left</span>
            </button>

                <span class="pg-info">Page 1 of 1</span>

            <button class="pg-btn pg-next" title="Next page">
                <span class="material-icons">chevron_right</span>
            </button>
        </div>

        <?php endif; ?>

    </div>

    <!-- ===== CONTACT DETAIL PANEL ===== -->
    <div class="detail-backdrop" id="detailBackdrop"></div>
    <aside class="detail-panel" id="contactPanel" aria-hidden="true" aria-label="Resident contact information">
        <div class="detail-panel-header">
            <span class="material-icons">contact_phone</span>
            <h3>Contact Information</h3>
            <button class="detail-panel-close" id="closeContactPanel" type="button" aria-label="Close">
                <span class="material-icons">close</span>
            </button>
        </div>

        <div class="detail-panel-body">
            <div class="detail-card">
                <div class="detail-card-head">
                    <div class="note-avatar"><span class="material-icons">person</span></div>
                    <div>
                        <h4 id="dpResidentName">—</h4>
                        <p id="dpResidentMeta">—</p>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <span class="detail-card-label">Emergency Contact</span>
                <div class="detail-row">
                    <span class="material-icons">badge</span>
                    <div>
                        <span class="detail-row-label">Contact Person</span>
                        <span class="detail-row-value" id="dpEcName">—</span>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="material-icons">diversity_1</span>
                    <div>
                        <span class="detail-row-label">Relationship</span>
                        <span class="detail-row-value" id="dpEcRel">—</span>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="material-icons">call</span>
                    <div>
                        <span class="detail-row-label">Contact Number</span>
                        <a class="detail-row-value" id="dpEcNumber">—</a>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <?php include 'includes/account_modal.php'; ?>

    <script src="js/script.js"></script>

</body>
</html>
