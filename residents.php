<?php
require 'auth.php';
require 'db.php';

$res = $conn->query("
    SELECT id, first_name, middle_name, last_name, full_name, suffix, birthdate, age, civil_status, gender, purok
    FROM residents
    ORDER BY full_name ASC
");
$residents = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residents Management</title>

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
                <a href="residents.php" class="active" data-tooltip="Residents">
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

    <!-- MAIN CONTENT -->
    <div class="residents-main">

        <div class="residents-section">

            <div class="residents-table-header">
                <div class="residents-title-group">
                    <span class="material-icons residents-table-icon">groups</span>
                    <div>
                        <h2>Resident Records</h2>
                        <p>Manage and monitor all barangay residents</p>
                    </div>
                </div>

                <button class="btn" id="openAddModalBtn">
                    <span class="material-icons">person_add</span>
                    Add Resident
                </button>
            </div>

            <div class="residents-table-tools">
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="residentSearch" placeholder="Search by name...">
                    <button type="button" class="search-filter-btn" id="residentFilterBtn"
                            title="Sort by surname (A–Z)" aria-pressed="false" aria-label="Sort by surname">
                        <span class="material-icons">sort_by_alpha</span>
                    </button>
                </div>
                <div class="export-group">
                    <a href="handlers/export_residents.php?format=csv" class="btn btn-export">
                        <span class="material-icons">table_view</span>
                        Export CSV
                    </a>
                    <a href="handlers/export_residents.php?format=pdf" class="btn btn-export"
                       onclick="window.open(this.href, '_blank'); return false;">
                        <span class="material-icons">picture_as_pdf</span>
                        Export PDF
                    </a>
                </div>
            </div>

            <!-- TABLE -->
            <table>
                <thead>
                    <tr>
                        <th><span class="th-cell"><span class="material-icons th-icon">account_circle</span>Name</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">calendar_today</span>Birthdate</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">123</span>Age</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">assignment_ind</span>Status</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">wc</span>Gender</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">place</span>Purok</span></th>
                        <th><span class="th-cell"><span class="material-icons th-icon">manage_accounts</span>Action</span></th>
                    </tr>
                </thead>

                <tbody id="residentTable">
                <?php
                foreach ($residents as $r):
                    $suffix  = trim($r['suffix'] ?? '');
                    $display = $suffix !== '' ? $r['full_name'] . ' ' . $suffix : $r['full_name'];
                ?>
                    <tr data-id="<?= $r['id'] ?>"
                        data-fullname="<?= htmlspecialchars($r['full_name'], ENT_QUOTES) ?>"
                        data-firstname="<?= htmlspecialchars($r['first_name'] ?? '', ENT_QUOTES) ?>"
                        data-middlename="<?= htmlspecialchars($r['middle_name'] ?? '', ENT_QUOTES) ?>"
                        data-lastname="<?= htmlspecialchars($r['last_name'] ?? '', ENT_QUOTES) ?>"
                        data-suffix="<?= htmlspecialchars($suffix, ENT_QUOTES) ?>">
                        <td data-label="Name"><?= htmlspecialchars($display) ?></td>
                        <td data-label="Birthdate"><?= htmlspecialchars($r['birthdate'] ?? '') ?></td>
                        <td data-label="Age"><?= (int)$r['age'] ?></td>
                        <td data-label="Status"><?= htmlspecialchars($r['civil_status'] ?? '') ?></td>
                        <td data-label="Gender"><?= htmlspecialchars($r['gender']) ?></td>
                        <td data-label="Purok"><?= htmlspecialchars($r['purok']) ?></td>
                        <td data-label="Action" class="action-cell">
                            <div class="action-btns">
                                <button class="edit-btn editResidentBtn" title="Edit">
                                    <span class="material-icons">edit_note</span> Edit
                                </button>
                                <button class="delete-btn deleteResidentBtn" title="Delete">
                                    <span class="material-icons">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($residents)): ?>
                    <tr id="emptyRow"><td colspan="7" class="empty-state-cell">No residents found. Click "Add Resident" to get started.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination" id="residentsPagination">
                <button class="pg-btn pg-prev" title="Previous page">
                    <span class="material-icons">chevron_left</span>
                </button>
                <span class="pg-info">Page 1 of 1</span>
                <button class="pg-btn pg-next" title="Next page">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>

        </div>

    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div class="modal" id="editModal">
        <div class="modal-content">

            <div class="modal-header">
                <span class="material-icons modal-header-icon edit-header-icon">edit_note</span>
                <div>
                    <h3>Edit Resident</h3>
                    <p>Update the resident's information below</p>
                </div>
                <button class="modal-close-btn closeEditModal" aria-label="Close">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-grid">
                <div class="full name-parts-row">
                    <div class="form-group">
                        <label>
                            <span class="material-icons form-icon">person</span>
                            First Name <span class="required-star">*</span>
                        </label>
                        <input type="text" id="editFirstName" placeholder="e.g. Juan">
                        <span class="field-error" id="editFirstNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <span class="material-icons form-icon">person</span>
                            Middle Name <span class="required-star">*</span>
                        </label>
                        <input type="text" id="editMiddleName" placeholder="e.g. Santos">
                        <span class="field-error" id="editMiddleNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <span class="material-icons form-icon">person</span>
                            Last Name <span class="required-star">*</span>
                        </label>
                        <input type="text" id="editLastName" placeholder="e.g. Dela Cruz">
                        <span class="field-error" id="editLastNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <span class="material-icons form-icon">badge</span>
                            Suffix
                        </label>
                        <input type="text" id="editSuffix" placeholder="e.g. Jr." maxlength="10">
                        <span class="field-error" id="editSuffixError"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <span class="material-icons form-icon">calendar_month</span>
                        Birthdate <span class="required-star">*</span>
                    </label>
                    <input type="date" id="editBirthdate" max="<?= date('Y-m-d') ?>">
                    <span class="field-error" id="editBirthdateError"></span>
                </div>

                <div class="form-group">
                    <label>
                        <span class="material-icons form-icon">pin</span>
                        Age <span class="required-star">*</span>
                    </label>
                    <input type="number" id="editAge" placeholder="Auto-calculated from birthdate" min="1" max="120" readonly>
                    <span class="field-error" id="editAgeError"></span>
                </div>

                <div class="form-group">
                    <label>
                        <span class="material-icons form-icon">badge</span>
                        Civil Status <span class="required-star">*</span>
                    </label>
                    <select id="editStatus">
                        <option value="">-- Select Status --</option>
                        <option>Single</option>
                        <option>Married</option>
                        <option>Widowed</option>
                        <option>Separated</option>
                    </select>
                    <span class="field-error" id="editStatusError"></span>
                </div>

                <div class="form-group">
                    <label>
                        <span class="material-icons form-icon">wc</span>
                        Gender <span class="required-star">*</span>
                    </label>
                    <select id="editGender">
                        <option value="">-- Select Gender --</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                    <span class="field-error" id="editGenderError"></span>
                </div>

                <div class="form-group full">
                    <label>
                        <span class="material-icons form-icon">location_on</span>
                        Purok <span class="required-star">*</span>
                    </label>
                    <select id="editPurok">
                        <option value="">-- Select Purok --</option>
                        <option>Purok 1</option>
                        <option>Purok 2</option>
                        <option>Purok 3</option>
                        <option>Purok 4</option>
                        <option>Purok 5</option>
                    </select>
                    <span class="field-error" id="editPurokError"></span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary closeEditModal">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button class="btn" id="saveChangesBtn">
                    <span class="material-icons">save</span> Save Changes
                </button>
            </div>

        </div>
    </div>

    <!-- ===== ADD RESIDENT MODAL ===== -->
    <div class="modal" id="addModal">
        <div class="modal-content">

            <div class="modal-header">
                <span class="material-icons modal-header-icon add-header-icon">person_add</span>
                <div>
                    <h3>Add New Resident</h3>
                    <p>Fill in the details to register a resident</p>
                </div>
                <button class="modal-close-btn closeAddModal" aria-label="Close">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-grid">
                <div class="full name-parts-row">
                    <div class="form-group">
                        <label>First Name <span class="required-star">*</span></label>
                        <input type="text" id="addFirstName" placeholder="e.g. Juan">
                        <span class="field-error" id="addFirstNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>Middle Name <span class="required-star">*</span></label>
                        <input type="text" id="addMiddleName" placeholder="e.g. Santos">
                        <span class="field-error" id="addMiddleNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required-star">*</span></label>
                        <input type="text" id="addLastName" placeholder="e.g. Dela Cruz">
                        <span class="field-error" id="addLastNameError"></span>
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <input type="text" id="addSuffix" placeholder="e.g. Jr." maxlength="10">
                        <span class="field-error" id="addSuffixError"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Birthdate <span class="required-star">*</span></label>
                    <input type="date" id="addBirthdate" max="<?= date('Y-m-d') ?>">
                    <span class="field-error" id="addBirthdateError"></span>
                </div>

                <div class="form-group">
                    <label>Age <span class="required-star">*</span></label>
                    <input type="number" id="addAge" placeholder="Auto-calculated from birthdate" min="1" max="120" readonly>
                    <span class="field-error" id="addAgeError"></span>
                </div>

                <div class="form-group">
                    <label>Civil Status <span class="required-star">*</span></label>
                    <select id="addStatus">
                        <option value="">-- Select Status --</option>
                        <option>Single</option>
                        <option>Married</option>
                        <option>Widowed</option>
                        <option>Separated</option>
                    </select>
                    <span class="field-error" id="addStatusError"></span>
                </div>

                <div class="form-group">
                    <label>Gender <span class="required-star">*</span></label>
                    <select id="addGender">
                        <option value="">-- Select Gender --</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                    <span class="field-error" id="addGenderError"></span>
                </div>

                <div class="form-group full">
                    <label>Purok <span class="required-star">*</span></label>
                    <select id="addPurok">
                        <option value="">-- Select Purok --</option>
                        <option>Purok 1</option>
                        <option>Purok 2</option>
                        <option>Purok 3</option>
                        <option>Purok 4</option>
                        <option>Purok 5</option>
                    </select>
                    <span class="field-error" id="addPurokError"></span>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-secondary closeAddModal">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button class="btn" id="confirmAddBtn">
                    <span class="material-icons">person_add</span> Add Resident
                </button>
            </div>

        </div>
    </div>

    <!-- ===== DELETE CONFIRM MODAL ===== -->
    <div class="modal" id="deleteModal">
        <div class="modal-content delete-modal">
            <div class="delete-modal-icon">
                <span class="material-icons">delete_forever</span>
            </div>
            <h3>Delete Resident?</h3>
            <p>This action cannot be undone. The resident record will be permanently removed.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary closeDeleteModal">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button class="btn btn-danger" id="confirmDeleteBtn">
                    <span class="material-icons">delete</span> Delete
                </button>
            </div>
        </div>
    </div>

    <?php include 'includes/account_modal.php'; ?>

    <script src="js/script.js"></script>

</body>
</html>
