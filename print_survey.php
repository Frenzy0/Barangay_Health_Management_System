<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Health Survey — BHMS</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/print_survey.css">
</head>
<body>

<div class="print-controls">
    <a href="index.php" class="pc-btn pc-btn-secondary"
       onclick="window.close(); return false;">
        <span class="material-icons">arrow_back</span> Back
    </a>
    <button class="pc-btn pc-btn-primary" onclick="window.print()">
        <span class="material-icons">print</span> Print / Save as PDF
    </button>
</div>

<div class="sheet">

    <!-- HEADER -->
    <div class="header">
        <div class="brand">
            <h1>BHMS</h1>
            <p class="subtitle">Barangay Health Management System</p>
        </div>
        <div class="form-id">
            <div><strong>Form No.:</strong> ____________</div>
            <div><strong>Date Issued:</strong> ____________</div>
            <div><strong>Officer:</strong> ____________</div>
        </div>
    </div>

    <!-- TITLE -->
    <div class="doc-title">
        <h2>RESIDENT HEALTH SURVEY FORM</h2>
        <p>Please complete all required information using black or blue ink. Mark applicable boxes with <strong>✕</strong>.</p>
    </div>

    <!-- SECTION 1: PERSONAL INFO -->
    <div class="section">
        <h3>1. PERSONAL INFORMATION</h3>

        <div class="row-inline">
            <div>
                <label>Full Name:</label>
                <span class="line medium" style="width:55%;"></span>
                <div class="note">(First Name, Middle Name, Last Name)</div>
            </div>
            <div class="tight">
                <label>Suffix:</label>
                <span class="line short"></span>
                <div class="note">(optional)</div>
            </div>
        </div>

        <div class="row-inline">
            <div>
                <label>Birthdate:</label>
                <span class="line medium"></span>
                <div class="note">(MM / DD / YYYY)</div>
            </div>
            <div class="tight">
                <label>Age:</label>
                <span class="line short"></span>
            </div>
        </div>

        <div class="row">
            <label>Civil Status:</label>
            <div class="options">
                <span class="opt"><span class="checkbox"></span> Single</span>
                <span class="opt"><span class="checkbox"></span> Married</span>
                <span class="opt"><span class="checkbox"></span> Widowed</span>
                <span class="opt"><span class="checkbox"></span> Separated</span>
            </div>
        </div>

        <div class="row">
            <label>Address (Purok):</label>
            <div class="options">
                <span class="opt"><span class="checkbox"></span> Purok 1</span>
                <span class="opt"><span class="checkbox"></span> Purok 2</span>
                <span class="opt"><span class="checkbox"></span> Purok 3</span>
                <span class="opt"><span class="checkbox"></span> Purok 4</span>
                <span class="opt"><span class="checkbox"></span> Purok 5</span>
            </div>
        </div>

        <div class="row">
            <label>Gender:</label>
            <div class="options">
                <span class="opt"><span class="checkbox"></span> Male</span>
                <span class="opt"><span class="checkbox"></span> Female</span>
                <span class="opt"><span class="checkbox"></span> Other</span>
            </div>
        </div>
    </div>

    <!-- SECTION 2: HEALTH STATUS -->
    <div class="section">
        <h3>2. HEALTH STATUS</h3>

        <div class="row">
            <label>Vaccination Status:</label>
            <div class="options">
                <span class="opt"><span class="checkbox"></span> Vaccinated</span>
                <span class="opt"><span class="checkbox"></span> Partially Vaccinated</span>
                <span class="opt"><span class="checkbox"></span> Unvaccinated</span>
            </div>
        </div>

        <div class="row">
            <label>Last Medical Checkup:</label>
            <span class="line medium"></span>
            <div class="note">(MM / DD / YYYY)</div>
        </div>

        <div class="row">
            <label>Recent Symptoms (check all that apply):</label>
            <div class="options">
                <span class="opt"><span class="checkbox"></span> Fever</span>
                <span class="opt"><span class="checkbox"></span> Cough</span>
                <span class="opt"><span class="checkbox"></span> Fatigue</span>
                <span class="opt"><span class="checkbox"></span> Headache</span>
                <span class="opt"><span class="checkbox"></span> None</span>
            </div>
        </div>

        <div class="row">
            <label>Additional Health Notes:</label>
            <div class="ruled">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </div>
    </div>

    <!-- SECTION 3: CERTIFICATION -->
    <div class="section">
        <h3>3. CERTIFICATION</h3>
        <p style="font-size:11px;margin:0 0 14px;">
            I hereby certify that the information provided in this form is true and correct
            to the best of my knowledge.
        </p>

        <div class="signature-block">
            <div>
                <span class="line full" style="height:32px;"></span>
                <div class="sig-line">Signature of Resident</div>
            </div>
            <div>
                <span class="line full" style="height:32px;"></span>
                <div class="sig-line">Date Signed</div>
            </div>
        </div>

        <div class="signature-block">
            <div>
                <span class="line full" style="height:32px;"></span>
                <div class="sig-line">Health Officer / Witness</div>
            </div>
            <div>
                <span class="line full" style="height:32px;"></span>
                <div class="sig-line">Position / Title</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Submit completed form to your Barangay Health Officer · BHMS Health Survey · For official use only
    </div>

</div>

</body>
</html>
