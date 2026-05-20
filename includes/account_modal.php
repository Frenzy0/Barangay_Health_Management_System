<!-- ===== ACCOUNT MODAL ===== -->
<div class="modal" id="accountModal">
    <div class="modal-content account-modal">

        <div class="modal-header">
            <span class="material-icons modal-header-icon account-header-icon">manage_accounts</span>
            <div>
                <h3>Account Settings</h3>
                <p>Manage your profile, password, and view activity</p>
            </div>
            <button class="modal-close-btn closeAccountModal" aria-label="Close">
                <span class="material-icons">close</span>
            </button>
        </div>

        <!-- TABS -->
        <div class="account-tabs">
            <button type="button" class="account-tab active" data-tab="profile">
                <span class="material-icons">person</span> Profile
            </button>
            <button type="button" class="account-tab" data-tab="password">
                <span class="material-icons">lock</span> Password
            </button>
            <button type="button" class="account-tab" data-tab="activity">
                <span class="material-icons">history</span> Activity Log
            </button>
        </div>

        <!-- PROFILE PANEL -->
        <div class="account-panel active" id="panel-profile">
            <div class="form-group full">
                <label>
                    <span class="material-icons form-icon">badge</span>
                    Username <span class="required-star">*</span>
                </label>
                <input type="text" id="acctUsername" value="<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>"
                       placeholder="3–50 characters">
                <small style="font-size:12px;color:#94a3b8;margin-top:4px;">Letters, numbers, dots, underscores, hyphens only.</small>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary closeAccountModal">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button type="button" class="btn" id="saveUsernameBtn">
                    <span class="material-icons">save</span> Save Username
                </button>
            </div>
        </div>

        <!-- PASSWORD PANEL -->
        <div class="account-panel" id="panel-password">
            <div class="form-group full">
                <label>
                    <span class="material-icons form-icon">lock</span>
                    Current Password <span class="required-star">*</span>
                </label>
                <input type="password" id="oldPassword" autocomplete="current-password" placeholder="Enter your current password">
            </div>
            <div class="form-group full">
                <label>
                    <span class="material-icons form-icon">key</span>
                    New Password <span class="required-star">*</span>
                </label>
                <input type="password" id="newPassword" autocomplete="new-password" placeholder="At least 12 characters">
                <small style="font-size:12px;color:#94a3b8;margin-top:4px;">Must be 12+ characters with uppercase, lowercase, a number, and a special character.</small>
            </div>
            <div class="form-group full">
                <label>
                    <span class="material-icons form-icon">key</span>
                    Confirm New Password <span class="required-star">*</span>
                </label>
                <input type="password" id="confirmPassword" autocomplete="new-password" placeholder="Re-enter new password">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary closeAccountModal">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button type="button" class="btn" id="changePasswordBtn">
                    <span class="material-icons">vpn_key</span> Update Password
                </button>
            </div>
        </div>

        <!-- ACTIVITY PANEL -->
        <div class="account-panel" id="panel-activity">
            <div class="activity-list" id="activityList">
                <div class="activity-loading">Loading activity…</div>
            </div>
        </div>

    </div>
</div>
