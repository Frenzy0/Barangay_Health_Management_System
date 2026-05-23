<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHMS — Health Survey</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/survey.css">
</head>
<body class="public-page">

<!-- ============ PUBLIC NAVBAR ============ -->
<nav class="public-navbar">
    <div class="navbar-content">
        <div class="navbar-brand">
            <span class="material-icons navbar-brand-icon">health_and_safety</span>
            <h1>BHMS</h1>
        </div>
        <a href="login.php" class="navbar-login-btn">
            <span class="material-icons">login</span>
            Admin Login
        </a>
    </div>
</nav>

<!-- ============ MAIN CONTENT ============ -->
<main class="public-main">

    <div class="survey-page-header">
        <h1>Health Survey Form</h1>
        <p>Barangay Health Monitoring System — Resident Health Survey</p>
    </div>

    <form id="surveyForm" method="POST" action="" novalidate>

        <!-- PERSONAL INFO -->
        <div class="survey-card">
            <h3 class="section-title">
                <span class="material-icons-outlined">badge</span>
                Personal Information
            </h3>

            <div class="survey-grid">

                <div class="full name-parts-row">
                    <div class="form-group">
                        <label>First Name <span class="required-star">*</span></label>
                        <input type="text" name="first_name" placeholder="Juan">
                    </div>
                    <div class="form-group">
                        <label>Middle Name <span class="required-star">*</span></label>
                        <div class="input-with-na">
                            <input type="text" name="middle_name" id="surveyMiddleName" placeholder="Santos">
                            <label class="na-toggle" title="No middle name">
                                <input type="checkbox" id="surveyMiddleNameNA"> N/A
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="required-star">*</span></label>
                        <input type="text" name="last_name" placeholder="Dela Cruz">
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <input type="text" name="suffix" placeholder="e.g. Jr." maxlength="10">
                    </div>
                </div>

                <div class="form-group">
                    <label>Birthdate <span class="required-star">*</span></label>
                    <input type="date" name="birthdate" id="surveyBirthdate">
                </div>

                <div class="status-age-row">
                    <div class="form-group status-field">
                        <label>Civil Status <span class="required-star">*</span></label>
                        <select name="civil_status" id="surveyStatus">
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>
                    <div class="form-group age-field">
                        <label>Age <span class="required-star">*</span></label>
                        <input type="number" name="age" id="surveyAge" placeholder="25" min="1" max="120">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address <span class="required-star">*</span></label>
                    <select name="purok">
                        <option value="">Select Purok</option>
                        <option value="Purok 1">Purok 1</option>
                        <option value="Purok 2">Purok 2</option>
                        <option value="Purok 3">Purok 3</option>
                        <option value="Purok 4">Purok 4</option>
                        <option value="Purok 5">Purok 5</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Gender <span class="required-star">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label"><input type="radio" name="gender" value="Male"> Male</label>
                        <label class="radio-label"><input type="radio" name="gender" value="Female"> Female</label>
                        <label class="radio-label"><input type="radio" name="gender" value="Other"> Other</label>
                    </div>
                </div>

            </div>
        </div>

        <!-- HEALTH STATUS -->
        <div class="survey-card">
            <h3 class="section-title">
                <span class="material-icons-outlined">health_and_safety</span>
                Health Status
            </h3>

            <div class="survey-grid">

                <div class="form-group">
                    <label>Vaccination Status <span class="required-star">*</span></label>
                    <select name="vaccination_status">
                        <option value="">Select Status</option>
                        <option value="Vaccinated">Vaccinated</option>
                        <option value="Partially Vaccinated">Partially Vaccinated</option>
                        <option value="Unvaccinated">Unvaccinated</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Last Medical Checkup <span class="required-star">*</span></label>
                    <input type="date" name="last_checkup"
                    min="<?php echo date('Y-m-d', strtotime('-1 year')); ?>"
                    max="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group full">
                    <label>Recent Symptoms <span class="required-star">*</span></label>
                    <div class="checkbox-group">
                        <label class="checkbox-label"><input type="checkbox" name="symptoms[]" value="fever"> Fever</label>
                        <label class="checkbox-label"><input type="checkbox" name="symptoms[]" value="cough"> Cough</label>
                        <label class="checkbox-label"><input type="checkbox" name="symptoms[]" value="fatigue"> Fatigue</label>
                        <label class="checkbox-label"><input type="checkbox" name="symptoms[]" value="headache"> Headache</label>
                        <label class="checkbox-label"><input type="checkbox" name="symptoms[]" value="none"> None</label>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Additional Health Notes</label>
                    <textarea name="health_notes" placeholder="Additional notes..."></textarea>
                </div>

            </div>
        </div>

        <!-- EMERGENCY CONTACT -->
        <div class="survey-card">
            <h3 class="section-title">
                <span class="material-icons-outlined">contact_emergency</span>
                Emergency Contact
            </h3>

            <div class="survey-grid">

                <div class="form-group full">
                    <label>Contact Person Details</label>
                    <div class="name-triple-row">
                        <div class="form-group">
                            <label>First Name <span class="required-star">*</span></label>
                            <input type="text" name="ec_first_name" placeholder="Juan">
                        </div>
                        <div class="form-group">
                            <label>Middle Name <span class="required-star">*</span></label>
                            <div class="input-with-na">
                                <input type="text" name="ec_middle_name" id="ecMiddleName" placeholder="Santos">
                                <label class="na-toggle" title="No middle name">
                                    <input type="checkbox" id="ecMiddleNameNA"> N/A
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required-star">*</span></label>
                            <input type="text" name="ec_last_name" placeholder="Dela Cruz">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Contact Number <span class="required-star">*</span></label>
                    <input type="tel" name="ec_contact_number" placeholder="09171234567" maxlength="11">
                </div>

                <div class="form-group">
                    <label>Relationship to Contact Person <span class="required-star">*</span></label>
                    <select name="ec_relationship">
                        <option value="">Select Relationship</option>
                        <option value="Parent">Parent</option>
                        <option value="Spouse">Spouse</option>
                        <option value="Sibling">Sibling</option>
                        <option value="Child">Child</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Relative">Relative</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- BUTTONS -->
        <div class="form-actions">
            <a href="print_survey.php" class="btn btn-outline"
               onclick="window.open(this.href, '_blank'); return false;">
                <span class="material-icons">print</span> Print Blank Form
            </a>
            <button type="reset" class="btn btn-secondary">Clear Form</button>
            <button type="submit" class="btn btn-primary">Submit Form</button>
        </div>

    </form>

</main>

<script>
    // Auto-calc age from birthdate
    document.addEventListener('DOMContentLoaded', () => {
        const bd  = document.getElementById('surveyBirthdate');
        const age = document.getElementById('surveyAge');
        if (bd && age) {
            bd.addEventListener('change', () => {
                if (!bd.value) return;
                const d = new Date(bd.value);
                if (isNaN(d)) return;
                const today = new Date();
                let a = today.getFullYear() - d.getFullYear();
                const m = today.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < d.getDate())) a--;
                if (a >= 1 && a <= 120) age.value = a;
                else age.value = "";
            });
        }
    });
</script>
<script src="js/script.js"></script>
</body>
</html>
