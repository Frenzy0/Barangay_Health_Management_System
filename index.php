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

        <!-- CONSENT -->
        <div class="survey-card consent-card">
            <label class="consent-label">
                <input type="checkbox" id="consentCheckbox" name="consent">
                <span>
                    I have read and agree to the
                    <a href="#" id="openPrivacyModal" class="consent-link">Terms of Service</a>
                    and consent to the collection and processing of my personal
                    information in accordance with the
                    <a href="#" id="openPrivacyModalLink" class="consent-link">Data Privacy Act of 2012 (RA 10173)</a>.
                </span>
            </label>
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

    <!-- DATA PRIVACY ACT MODAL -->
    <div class="modal" id="privacyModal" role="dialog" aria-modal="true" aria-labelledby="privacyModalTitle">
        <div class="modal-content privacy-modal">
            <div class="modal-header">
                <div class="modal-header-icon privacy-header-icon">
                    <span class="material-icons-outlined">privacy_tip</span>
                </div>
                <div>
                    <h3 id="privacyModalTitle">Data Privacy Act of 2012</h3>
                    <p>Republic Act No. 10173 &mdash; Terms of Service &amp; Privacy Notice</p>
                </div>
                <button type="button" class="modal-close-btn closePrivacyModal" aria-label="Close">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="privacy-body">
                <h4>1. Purpose of Collection</h4>
                <p>
                    The Barangay Health Monitoring System (BHMS) collects your
                    personal and health information solely for the purpose of
                    delivering barangay health services, maintaining accurate
                    resident health records, monitoring community health trends,
                    and responding to medical emergencies.
                </p>

                <h4>2. Information We Collect</h4>
                <ul>
                    <li>Personal details (name, birthdate, age, civil status, gender, address/purok)</li>
                    <li>Health information (vaccination status, recent symptoms, last checkup, health notes)</li>
                    <li>Emergency contact details (name, relationship, contact number)</li>
                </ul>

                <h4>3. Your Rights as a Data Subject</h4>
                <p>
                    Under RA 10173, you have the right to be informed, to
                    object, to access, to rectify, to erase or block, to
                    damages, to data portability, and to file a complaint with
                    the National Privacy Commission (NPC) regarding the
                    processing of your personal data.
                </p>

                <h4>4. Data Protection &amp; Storage</h4>
                <p>
                    Your information is stored securely and accessed only by
                    authorized barangay health personnel. We implement
                    reasonable organizational, physical, and technical security
                    measures to protect your data against unauthorized access,
                    alteration, disclosure, or destruction.
                </p>

                <h4>5. Sharing of Information</h4>
                <p>
                    Your information will not be shared with third parties
                    without your consent, except as required by law, public
                    health authorities, or in response to a legitimate medical
                    emergency.
                </p>

                <h4>6. Retention</h4>
                <p>
                    Your data will be retained only for as long as necessary to
                    fulfill the purposes stated above, or as required by
                    applicable laws and regulations.
                </p>

                <h4>7. Consent</h4>
                <p>
                    By ticking the consent checkbox and submitting this survey,
                    you confirm that you have read and understood this notice,
                    and you freely give your consent to the collection,
                    processing, and storage of your personal information for
                    the purposes described above.
                </p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary closePrivacyModal">Close</button>
                <button type="button" class="btn btn-primary" id="agreePrivacyBtn">I Agree</button>
            </div>
        </div>
    </div>

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
