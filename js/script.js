document.addEventListener("DOMContentLoaded", () => {

    /* =============================================
       TOAST NOTIFICATION
    ============================================= */
    let toastContainer = document.getElementById("toastContainer");
    if (!toastContainer) {
        toastContainer = document.createElement("div");
        toastContainer.id = "toastContainer";
        toastContainer.className = "toast-container";
        document.body.appendChild(toastContainer);
    }

    function showToast(type, title, message, duration = 4000) {
        const toast = document.createElement("div");
        toast.className = "toast " + type;
        toast.innerHTML = `
            <span class="material-icons toast-icon">${type === "success" ? "check_circle" : "error"}</span>
            <div class="toast-content">
                <div class="toast-title"></div>
                <div class="toast-message"></div>
            </div>
            <button class="toast-close" aria-label="Close">
                <span class="material-icons">close</span>
            </button>`;
        toast.querySelector(".toast-title").textContent = title;
        toast.querySelector(".toast-message").textContent = message;

        const remove = () => {
            toast.classList.add("removing");
            setTimeout(() => toast.remove(), 300);
        };
        toast.querySelector(".toast-close").addEventListener("click", remove);
        setTimeout(remove, duration);

        toastContainer.appendChild(toast);
    }

    /* =============================================
       MODAL HELPERS
    ============================================= */
    function openModal(modal) { modal.classList.add("show"); }
    function closeModal(modal) { modal.classList.remove("show"); }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;")
            .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    /* =============================================
       PASSWORD STRENGTH RULE
       At least 12 chars, with uppercase, lowercase,
       a number, and a special character.
    ============================================= */
    function validatePasswordStrength(pwd) {
        if (pwd.length < 12) return "Password must be at least 12 characters long.";
        if (!/[a-z]/.test(pwd)) return "Password must include a lowercase letter.";
        if (!/[A-Z]/.test(pwd)) return "Password must include an uppercase letter.";
        if (!/[0-9]/.test(pwd)) return "Password must include a number.";
        if (!/[^A-Za-z0-9]/.test(pwd)) return "Password must include a special character.";
        return "";
    }

    /* =============================================
       VALIDATION HELPERS (add/edit modals)
    ============================================= */
    function showError(inputEl, errorEl, msg) {
        inputEl.classList.add("input-error");
        errorEl.textContent = msg;
        errorEl.classList.add("show");
    }

    function clearError(inputEl, errorEl) {
        inputEl.classList.remove("input-error");
        errorEl.textContent = "";
        errorEl.classList.remove("show");
    }

    function clearAllErrors(prefix) {
        ["FirstName", "MiddleName", "LastName", "Suffix", "Birthdate", "Age", "Status", "Gender", "Purok"].forEach(f => {
            const input = document.getElementById(prefix + f);
            const error = document.getElementById(prefix + f + "Error");
            if (input && error) clearError(input, error);
        });
    }

    function attachLiveValidation(prefix) {
        ["FirstName", "MiddleName", "LastName", "Suffix", "Birthdate", "Age", "Status", "Gender", "Purok"].forEach(f => {
            const input = document.getElementById(prefix + f);
            const error = document.getElementById(prefix + f + "Error");
            if (!input || !error) return;
            ["input", "change"].forEach(ev =>
                input.addEventListener(ev, () => clearError(input, error))
            );
        });
    }
    attachLiveValidation("edit");
    attachLiveValidation("add");

    /* =============================================
       AUTO-CALC AGE FROM BIRTHDATE (add/edit modals)
    ============================================= */
    function calcAgeFromDate(dateStr) {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return "";
        const today = new Date();
        if (d > today) return "";
        let a = today.getFullYear() - d.getFullYear();
        const m = today.getMonth() - d.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) a--;
        if (a < 1 || a > 120) return "";
        return a;
    }

    function bindAutoAge(prefix) {
        const bd = document.getElementById(prefix + "Birthdate");
        const ageEl = document.getElementById(prefix + "Age");
        if (!bd || !ageEl) return;
        bd.addEventListener("change", () => {
            const a = calcAgeFromDate(bd.value);
            ageEl.value = a === "" ? "" : a;
        });
    }
    bindAutoAge("add");
    bindAutoAge("edit");

    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b[a-zà-ÿ]/g, ch => ch.toUpperCase());
    }

    function validateForm(prefix) {
        let valid = true;
        const get = id => document.getElementById(id);

        // First / Middle / Last name — each required, letters-only.
        [["FirstName", "First name"], ["MiddleName", "Middle name"], ["LastName", "Last name"]]
        .forEach(([idSuffix, label]) => {
            const el  = get(prefix + idSuffix);
            const err = get(prefix + idSuffix + "Error");
            if (!el) return;
            const val = el.value.trim();
            if (!val) {
                showError(el, err, label + " is required.");
                showToast("error", "Invalid Name", label + " is required.");
                valid = false;
            } else if (idSuffix === "MiddleName" && val.toUpperCase() === "N/A") {
                // Middle name may be "N/A" for a person with no middle name.
                el.value = "N/A";
                clearError(el, err);
            } else if (/\d/.test(val)) {
                showError(el, err, label + " must not contain numbers.");
                showToast("error", "Invalid Name", label + " must not contain numbers.");
                valid = false;
            } else if (!/^[A-Za-zÀ-ÿ\s.\-']+$/.test(val)) {
                showError(el, err, label + " may only contain letters, spaces, dots, hyphens, apostrophes.");
                showToast("error", "Invalid Name", label + " may only contain letters, spaces, dots, hyphens, apostrophes.");
                valid = false;
            } else {
                el.value = toTitleCase(val);
                clearError(el, err);
            }
        });

        const sxEl = get(prefix + "Suffix");
        const sxErr = get(prefix + "SuffixError");
        if (sxEl && sxErr) {
            const sxVal = sxEl.value.trim();
            if (sxVal && !/^[A-Za-z.\s]{1,10}$/.test(sxVal)) {
                showError(sxEl, sxErr, "Suffix may only contain letters, dots, and spaces (max 10).");
                valid = false;
            } else {
                clearError(sxEl, sxErr);
            }
        }

        const bdEl = get(prefix + "Birthdate");
        const bdErr = get(prefix + "BirthdateError");
        if (!bdEl.value) {
            showError(bdEl, bdErr, "Birthdate is required.");
            valid = false;
        } else if (new Date(bdEl.value) > new Date()) {
            showError(bdEl, bdErr, "Birthdate cannot be in the future.");
            valid = false;
        } else {
            clearError(bdEl, bdErr);
        }

        const ageEl = get(prefix + "Age");
        const ageErr = get(prefix + "AgeError");
        const ageVal = ageEl.value.trim();
        if (!ageVal) {
            showError(ageEl, ageErr, "Age is required.");
            valid = false;
        } else {
            const age = parseInt(ageVal, 10);
            if (isNaN(age) || !Number.isInteger(age) || age < 1 || age > 120) {
                showError(ageEl, ageErr, "Age must be a whole number between 1 and 120.");
                showToast("error", "Invalid Age", "Age must be a whole number between 1 and 120.");
                valid = false;
            } else {
                clearError(ageEl, ageErr);
            }
        }

        const stEl = get(prefix + "Status");
        const stErr = get(prefix + "StatusError");
        if (!stEl.value) {
            showError(stEl, stErr, "Civil status is required.");
            valid = false;
        } else {
            clearError(stEl, stErr);
        }

        const gnEl = get(prefix + "Gender");
        const gnErr = get(prefix + "GenderError");
        if (!gnEl.value) {
            showError(gnEl, gnErr, "Gender is required.");
            valid = false;
        } else {
            clearError(gnEl, gnErr);
        }

        const pkEl = get(prefix + "Purok");
        const pkErr = get(prefix + "PurokError");
        if (!pkEl.value) {
            showError(pkEl, pkErr, "Purok is required.");
            valid = false;
        } else {
            clearError(pkEl, pkErr);
        }

        return valid;
    }

    /* =============================================
       EMPTY STATE HELPER
    ============================================= */
    function checkEmptyState(tbody, colSpan) {
        if (!tbody) return;
        const dataRows = Array.from(tbody.querySelectorAll("tr")).filter(r => !r.id);
        if (dataRows.length === 0) {
            let empty = document.getElementById("emptyRow");
            if (!empty) {
                empty = document.createElement("tr");
                empty.id = "emptyRow";
                empty.innerHTML = `<td colspan="${colSpan}" class="empty-state-cell">No residents found. Click &quot;Add Resident&quot; to get started.</td>`;
                tbody.appendChild(empty);
            }
            empty.style.display = "";
        }
    }

    /* =============================================
       PAGINATION
    ============================================= */
    function initPagination(tbodyId, pgId, pageSize) {
        const tbody = document.getElementById(tbodyId);
        const pg = document.getElementById(pgId);
        if (!tbody || !pg) return null;

        let page = 1;
        const prevBtn = pg.querySelector(".pg-prev");
        const nextBtn = pg.querySelector(".pg-next");
        const infoSpan = pg.querySelector(".pg-info");

        function visibleRows() {
            return Array.from(tbody.querySelectorAll("tr")).filter(
                r => r.dataset.searchHidden !== "1" && !r.id
            );
        }

        function update() {
            const rows = visibleRows();
            const total = Math.max(1, Math.ceil(rows.length / pageSize));
            page = Math.min(Math.max(1, page), total);

            Array.from(tbody.querySelectorAll("tr")).forEach(r => { r.style.display = "none"; });

            rows.forEach((r, i) => {
                if (i >= (page - 1) * pageSize && i < page * pageSize) r.style.display = "";
            });

            // Always keep empty-state row visible
            const emptyRow = document.getElementById("emptyRow");
            if (emptyRow) emptyRow.style.display = "";

            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= total;
            infoSpan.textContent = `Page ${page} of ${total}`;
            pg.style.display = rows.length > pageSize ? "flex" : "none";
        }

        prevBtn.addEventListener("click", () => { page--; update(); });
        nextBtn.addEventListener("click", () => { page++; update(); });

        update();
        return { update, goFirst() { page = 1; update(); } };
    }

    /* =============================================
       PAGINATION FOR HEALTH NOTES
    ============================================= */
    (() => {
        const searchInput = document.getElementById('notesSearch');
        const grid = document.getElementById('notesGrid');
        const pagination = document.getElementById('notesPagination');
        if (!searchInput || !grid || !pagination) return;

        const prevBtn = pagination.querySelector('.pg-prev');
        const nextBtn = pagination.querySelector('.pg-next');
        const pageInfo = pagination.querySelector('.pg-info');

        const ITEMS_PER_PAGE = 6;
        let currentPage = 1;

        function getFilteredCards() {
            return [...grid.querySelectorAll('.note-card')]
                .filter(card => card.dataset.filtered !== "true");
        }

        function renderPagination() {
            const cards = getFilteredCards();
            const totalPages = Math.max(1, Math.ceil(cards.length / ITEMS_PER_PAGE));

            if (currentPage > totalPages) currentPage = totalPages;

            cards.forEach((card, index) => {
                const start = (currentPage - 1) * ITEMS_PER_PAGE;
                const end = start + ITEMS_PER_PAGE;

                card.style.display = (index >= start && index < end) ? '' : 'none';
            });

            grid.querySelectorAll('.note-card').forEach(card => {
                if (card.dataset.filtered === "true") {
                    card.style.display = 'none';
                }
            });

            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            pagination.style.display = cards.length > ITEMS_PER_PAGE ? 'flex' : 'none';
        }

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();

            grid.querySelectorAll('.note-card').forEach(card => {
                const name = card.dataset.name || '';
                card.dataset.filtered = (!query || name.includes(query)) ? "false" : "true";
            });

            currentPage = 1;
            renderPagination();
        });

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPagination();
            }
        });

        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(getFilteredCards().length / ITEMS_PER_PAGE);

            if (currentPage < totalPages) {
                currentPage++;
                renderPagination();
            }
        });

        /* Sort-by-surname filter: A–Z sort with "Last, First Middle [Suffix]"
           display in the card heading. Click again to restore. */
        const notesFilterBtn = document.getElementById("notesFilterBtn");
        if (notesFilterBtn) {
            let sortActive = false;
            let originalOrder = null;

            // The note cards bake the suffix into data-fullname, so the
            // original display is just data-fullname (no suffix re-append).
            function setCardHeading(card, text) {
                const h = card.querySelector(".note-identity h3");
                if (h) h.textContent = text;
            }

            notesFilterBtn.addEventListener("click", () => {
                const cards = Array.from(grid.querySelectorAll(".note-card"));
                if (cards.length === 0) return;

                if (!sortActive) {
                    originalOrder = cards.slice();
                    cards.slice().sort(compareBySurname).forEach(c => {
                        grid.appendChild(c);
                        setCardHeading(c, buildSurnameFirst(c));
                    });
                    sortActive = true;
                    notesFilterBtn.classList.add("active");
                    notesFilterBtn.setAttribute("aria-pressed", "true");
                    notesFilterBtn.title = "Sorted by surname (A–Z) — click to restore";
                } else {
                    originalOrder.forEach(c => {
                        grid.appendChild(c);
                        setCardHeading(c, c.dataset.fullname || "");
                    });
                    sortActive = false;
                    notesFilterBtn.classList.remove("active");
                    notesFilterBtn.setAttribute("aria-pressed", "false");
                    notesFilterBtn.title = "Sort by surname (A–Z)";
                }
                currentPage = 1;
                renderPagination();
            });
        }

        renderPagination();
    })();

    /* =============================================
       CONTACT DETAIL PANEL (Health Notes)
    ============================================= */
    (() => {
        const panel    = document.getElementById('contactPanel');
        const backdrop = document.getElementById('detailBackdrop');
        const grid     = document.getElementById('notesGrid');
        if (!panel || !backdrop || !grid) return;

        const closeBtn = document.getElementById('closeContactPanel');

        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = (val && val.trim()) ? val.trim() : '—';
        };

        function openPanel(card) {
            setText('dpResidentName', card.dataset.fullname);
            setText('dpResidentMeta',
                [card.dataset.purok, card.dataset.gender].filter(Boolean).join(' · '));
            setText('dpEcName', card.dataset.ecName);
            setText('dpEcRel',  card.dataset.ecRel);

            const numEl = document.getElementById('dpEcNumber');
            const num   = (card.dataset.ecNumber || '').trim();
            if (numEl) {
                numEl.textContent = num || '—';
                if (num) numEl.setAttribute('href', 'tel:' + num);
                else     numEl.removeAttribute('href');
            }

            panel.classList.add('show');
            panel.setAttribute('aria-hidden', 'false');
            backdrop.classList.add('show');
        }

        function closePanel() {
            panel.classList.remove('show');
            panel.setAttribute('aria-hidden', 'true');
            backdrop.classList.remove('show');
        }

        grid.addEventListener('click', e => {
            if (e.target.closest('textarea')) return;
            const card = e.target.closest('.note-card');
            if (card) openPanel(card);
        });

        grid.addEventListener('keydown', e => {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const card = e.target.closest('.note-card');
            if (card && e.target === card) {
                e.preventDefault();
                openPanel(card);
            }
        });

        closeBtn?.addEventListener('click', closePanel);
        backdrop.addEventListener('click', closePanel);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && panel.classList.contains('show')) closePanel();
        });
    })();

    /* =============================================
       RESIDENT SEARCH (by surname only)
    ============================================= */
    function extractSurname(fullName) {
        if (!fullName) return "";
        const parts = fullName.toLowerCase().split(/\s+/).filter(Boolean);
        if (parts.length === 0) return "";
        if (parts.length === 1) return parts[0];

        const end = parts.length - 1;
        let surname = parts[end];

        // Combine Filipino surname prefixes (dela, del, delos, san, sta., st., de la, de los)
        const prefixes = ["dela", "del", "delos", "san", "sta", "sta.", "st", "st."];
        if (end >= 1 && prefixes.includes(parts[end - 1])) {
            surname = parts[end - 1] + " " + surname;
            if (end >= 2 && (parts[end - 2] === "de" || parts[end - 2] === "de.")) {
                surname = parts[end - 2] + " " + surname;
            }
        } else if (end >= 2 && parts[end - 2] === "de" &&
                   (parts[end - 1] === "la" || parts[end - 1] === "los" || parts[end - 1] === "las")) {
            surname = parts[end - 2] + " " + parts[end - 1] + " " + surname;
        } else if (end >= 1 && parts[end - 1] === "de") {
            surname = parts[end - 1] + " " + surname;
        }
        return surname;
    }

    const residentSearch = document.getElementById("residentSearch");
    let residentPg = null;

    if (residentSearch) {
        residentSearch.addEventListener("input", () => {
            const val = residentSearch.value.toLowerCase().trim();
            document.querySelectorAll("#residentTable tr").forEach(row => {
                if (row.id) return;
                const fullName = row.dataset.fullname || "";
                const surname = extractSurname(fullName);
                if (!val || surname.startsWith(val) || surname.includes(val)) {
                    delete row.dataset.searchHidden;
                } else {
                    row.dataset.searchHidden = "1";
                }
            });
            residentPg?.goFirst();
        });
    }

    /* =============================================
       SORT-BY-SURNAME FILTER (Residents)
       Toggle: click once to sort A–Z by surname and
       display names as "Last, First Middle [Suffix]".
       Click again to restore the original order and
       the original "First Middle Last" display.
    ============================================= */
    // Sort uses the actual last_name column when present (correct for
    // compound Filipino surnames like "Dela Cruz"); falls back to a
    // best-effort surname split for cards/rows without parts.
    function surnameKey(el) {
        const last = (el.dataset.lastname || "").trim();
        if (last) return last.toLowerCase();
        return extractSurname(el.dataset.fullname || "");
    }
    function compareBySurname(a, b) {
        const cmp = surnameKey(a).localeCompare(surnameKey(b));
        if (cmp !== 0) return cmp;
        const fa = (a.dataset.firstname || a.dataset.fullname || "").toLowerCase();
        const fb = (b.dataset.firstname || b.dataset.fullname || "").toLowerCase();
        return fa.localeCompare(fb);
    }
    // "Last, First Middle [Suffix]" — middle is omitted when "N/A".
    function buildSurnameFirst(el) {
        const last   = (el.dataset.lastname   || "").trim();
        const first  = (el.dataset.firstname  || "").trim();
        const middle = (el.dataset.middlename || "").trim();
        const suffix = (el.dataset.suffix     || "").trim();
        const middlePart = (middle && middle.toUpperCase() !== "N/A") ? " " + middle : "";
        const suffixPart = suffix ? " " + suffix : "";
        const rest = `${first}${middlePart}${suffixPart}`.trim();
        return last ? (rest ? `${last}, ${rest}` : last) : rest;
    }

    const residentFilterBtn = document.getElementById("residentFilterBtn");
    if (residentFilterBtn) {
        const tbody = document.getElementById("residentTable");
        let sortActive = false;
        let originalOrder = null;

        // The residents table stores data-fullname WITHOUT the suffix, so
        // the original display = fullname + " " + suffix (when present).
        function originalResidentDisplay(row) {
            const full   = row.dataset.fullname || "";
            const suffix = (row.dataset.suffix || "").trim();
            return suffix ? `${full} ${suffix}` : full;
        }
        function setNameCell(row, text) {
            const cell = row.querySelector('td[data-label="Name"]');
            if (cell) cell.textContent = text;
        }

        residentFilterBtn.addEventListener("click", () => {
            if (!tbody) return;
            const dataRows = Array.from(tbody.querySelectorAll("tr")).filter(r => !r.id);
            if (dataRows.length === 0) return;

            if (!sortActive) {
                originalOrder = dataRows.slice();
                dataRows.slice().sort(compareBySurname).forEach(r => {
                    tbody.appendChild(r);
                    setNameCell(r, buildSurnameFirst(r));
                });
                sortActive = true;
                residentFilterBtn.classList.add("active");
                residentFilterBtn.setAttribute("aria-pressed", "true");
                residentFilterBtn.title = "Sorted by surname (A–Z) — click to restore";
            } else {
                originalOrder.forEach(r => {
                    tbody.appendChild(r);
                    setNameCell(r, originalResidentDisplay(r));
                });
                sortActive = false;
                residentFilterBtn.classList.remove("active");
                residentFilterBtn.setAttribute("aria-pressed", "false");
                residentFilterBtn.title = "Sort by surname (A–Z)";
            }
            residentPg?.goFirst();
        });
    }

    /* =============================================
       EDIT MODAL
    ============================================= */
    const editModal = document.getElementById("editModal");
    const saveChangesBtn = document.getElementById("saveChangesBtn");
    let currentEditRow = null;

    document.addEventListener("click", e => {
        const btn = e.target.closest(".editResidentBtn");
        if (!btn || !editModal) return;

        currentEditRow = btn.closest("tr");
        const cells = currentEditRow.querySelectorAll("td");

        document.getElementById("editFirstName").value = currentEditRow.dataset.firstname || "";
        document.getElementById("editMiddleName").value = currentEditRow.dataset.middlename || "";
        document.getElementById("editLastName").value = currentEditRow.dataset.lastname || "";
        document.getElementById("editSuffix").value = currentEditRow.dataset.suffix || "";
        document.getElementById("editBirthdate").value = cells[1].textContent.trim();
        document.getElementById("editAge").value = cells[2].textContent.trim();
        document.getElementById("editStatus").value = cells[3].textContent.trim();
        document.getElementById("editGender").value = cells[4].textContent.trim();
        document.getElementById("editPurok").value = cells[5].textContent.trim();

        clearAllErrors("edit");
        openModal(editModal);
    });

    saveChangesBtn?.addEventListener("click", () => {
        if (!validateForm("edit") || !currentEditRow) return;

        const first    = document.getElementById("editFirstName").value.trim();
        const middle   = document.getElementById("editMiddleName").value.trim();
        const last     = document.getElementById("editLastName").value.trim();
        const suffix   = document.getElementById("editSuffix").value.trim();
        // "N/A" middle name is excluded from the displayed full name.
        const fullName = [first, middle, last].filter(p => p && p.toUpperCase() !== "N/A").join(" ");
        const display  = suffix ? `${fullName} ${suffix}` : fullName;

        const fd = new FormData();
        fd.append("id", currentEditRow.dataset.id);
        fd.append("first_name", first);
        fd.append("middle_name", middle);
        fd.append("last_name", last);
        fd.append("suffix", suffix);
        fd.append("birthdate", document.getElementById("editBirthdate").value.trim());
        fd.append("age", document.getElementById("editAge").value.trim());
        fd.append("civil_status", document.getElementById("editStatus").value.trim());
        fd.append("gender", document.getElementById("editGender").value.trim());
        fd.append("purok", document.getElementById("editPurok").value.trim());

        saveChangesBtn.disabled = true;
        fetch("handlers/edit_resident.php", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    const isDuplicate = /already exists/i.test(data.error || "");
                    showToast("error",
                        isDuplicate ? "Duplicate Resident" : "Update Failed",
                        data.error || "Could not update the resident.");
                    return;
                }
                const cells = currentEditRow.querySelectorAll("td");
                cells[0].textContent = display;
                cells[1].textContent = fd.get("birthdate");
                cells[2].textContent = fd.get("age");
                cells[3].textContent = fd.get("civil_status");
                cells[4].textContent = fd.get("gender");
                cells[5].textContent = fd.get("purok");
                currentEditRow.dataset.fullname = fullName;
                currentEditRow.dataset.firstname = first;
                currentEditRow.dataset.middlename = middle;
                currentEditRow.dataset.lastname = last;
                currentEditRow.dataset.suffix = suffix;
                closeModal(editModal);
                showToast("success", "Changes Saved", `${display}'s information has been updated.`);
            })
            .catch(() => showToast("error", "Network Error", "Could not connect. Please try again."))
            .finally(() => { saveChangesBtn.disabled = false; });
    });

    document.querySelectorAll(".closeEditModal").forEach(btn =>
        btn.addEventListener("click", () => closeModal(editModal))
    );
    editModal?.addEventListener("click", e => {
        if (e.target === editModal) closeModal(editModal);
    });

    /* =============================================
       ADD RESIDENT MODAL
    ============================================= */
    const addModal = document.getElementById("addModal");
    const openAddBtn = document.getElementById("openAddModalBtn");
    const confirmAddBtn = document.getElementById("confirmAddBtn");

    openAddBtn?.addEventListener("click", () => {
        addModal.querySelectorAll("input").forEach(el => el.value = "");
        addModal.querySelectorAll("select").forEach(el => el.selectedIndex = 0);
        clearAllErrors("add");
        openModal(addModal);
    });

    confirmAddBtn?.addEventListener("click", () => {
        if (!validateForm("add")) return;

        const first    = document.getElementById("addFirstName").value.trim();
        const middle   = document.getElementById("addMiddleName").value.trim();
        const last     = document.getElementById("addLastName").value.trim();
        const suffix   = document.getElementById("addSuffix").value.trim();
        // "N/A" middle name is excluded from the displayed full name.
        const fullName = [first, middle, last].filter(p => p && p.toUpperCase() !== "N/A").join(" ");
        const display  = suffix ? `${fullName} ${suffix}` : fullName;

        const fd = new FormData();
        fd.append("first_name", first);
        fd.append("middle_name", middle);
        fd.append("last_name", last);
        fd.append("suffix", suffix);
        fd.append("birthdate", document.getElementById("addBirthdate").value.trim());
        fd.append("age", document.getElementById("addAge").value.trim());
        fd.append("civil_status", document.getElementById("addStatus").value.trim());
        fd.append("gender", document.getElementById("addGender").value.trim());
        fd.append("purok", document.getElementById("addPurok").value.trim());

        confirmAddBtn.disabled = true;
        fetch("handlers/add_resident.php", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    const isDuplicate = /already exists/i.test(data.error || "");
                    showToast("error",
                        isDuplicate ? "Duplicate Resident" : "Failed to Add",
                        data.error || "Could not add the resident.");
                    return;
                }
                const emptyRow = document.getElementById("emptyRow");
                if (emptyRow) emptyRow.remove();

                const tbody = document.getElementById("residentTable");
                const newRow = document.createElement("tr");
                newRow.dataset.id = data.id;
                newRow.dataset.fullname = fullName;
                newRow.dataset.firstname = first;
                newRow.dataset.middlename = middle;
                newRow.dataset.lastname = last;
                newRow.dataset.suffix = suffix;
                newRow.innerHTML = `
                    <td data-label="Name">${escapeHtml(display)}</td>
                    <td data-label="Birthdate">${escapeHtml(fd.get("birthdate"))}</td>
                    <td data-label="Age">${escapeHtml(fd.get("age"))}</td>
                    <td data-label="Status">${escapeHtml(fd.get("civil_status"))}</td>
                    <td data-label="Gender">${escapeHtml(fd.get("gender"))}</td>
                    <td data-label="Purok">${escapeHtml(fd.get("purok"))}</td>
                    <td data-label="Action" class="action-cell">
                        <div class="action-btns">
                            <button class="edit-btn editResidentBtn" title="Edit">
                                <span class="material-icons">edit_note</span> Edit
                            </button>
                            <button class="delete-btn deleteResidentBtn" title="Delete">
                                <span class="material-icons">delete_outline</span>
                            </button>
                        </div>
                    </td>`;
                tbody.appendChild(newRow);
                residentPg?.goFirst();
                closeModal(addModal);
                showToast("success", "Resident Added", `${display} has been registered successfully.`);
            })
            .catch(() => showToast("error", "Network Error", "Could not connect. Please try again."))
            .finally(() => { confirmAddBtn.disabled = false; });
    });

    document.querySelectorAll(".closeAddModal").forEach(btn =>
        btn.addEventListener("click", () => closeModal(addModal))
    );
    addModal?.addEventListener("click", e => {
        if (e.target === addModal) closeModal(addModal);
    });

    /* =============================================
       DELETE MODAL
    ============================================= */
    const deleteModal = document.getElementById("deleteModal");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    let rowToDelete = null;

    document.addEventListener("click", e => {
        const btn = e.target.closest(".deleteResidentBtn");
        if (!btn || !deleteModal) return;
        rowToDelete = btn.closest("tr");
        openModal(deleteModal);
    });

    confirmDeleteBtn?.addEventListener("click", () => {
        if (!rowToDelete) return;

        const fd = new FormData();
        fd.append("id", rowToDelete.dataset.id);
        const deletedName = rowToDelete.querySelectorAll("td")[0]?.textContent.trim() || "Resident";

        confirmDeleteBtn.disabled = true;
        fetch("handlers/delete_resident.php", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showToast("error", "Failed to Delete", data.error || "Could not delete the resident.");
                    return;
                }
                const tbody = rowToDelete.closest("tbody");
                rowToDelete.remove();
                rowToDelete = null;
                residentPg?.update();
                checkEmptyState(tbody, 7);
                closeModal(deleteModal);
                showToast("success", "Resident Deleted", `${deletedName} has been permanently removed.`);
            })
            .catch(() => showToast("error", "Network Error", "Could not connect. Please try again."))
            .finally(() => { confirmDeleteBtn.disabled = false; });
    });

    document.querySelectorAll(".closeDeleteModal").forEach(btn =>
        btn.addEventListener("click", () => closeModal(deleteModal))
    );
    deleteModal?.addEventListener("click", e => {
        if (e.target === deleteModal) closeModal(deleteModal);
    });

    /* =============================================
       SURVEY FORM (validation + AJAX submit)
    ============================================= */
    const surveyForm = document.getElementById("surveyForm");
    if (surveyForm) {
        /* "None" symptom mutually-exclusive logic (bidirectional) */
        const noneCb = surveyForm.querySelector('input[name="symptoms[]"][value="none"]');
        const otherCbs = surveyForm.querySelectorAll('input[name="symptoms[]"]:not([value="none"])');

        function setLocked(cb, locked) {
            cb.disabled = locked;
            const lbl = cb.closest('.checkbox-label');
            if (lbl) lbl.classList.toggle('disabled', locked);
        }

        function syncSymptomLock() {
            if (!noneCb) return;
            const anyOtherChecked = Array.from(otherCbs).some(cb => cb.checked);

            // Lock "None" while any symptom is checked.
            if (anyOtherChecked) {
                noneCb.checked = false;
                setLocked(noneCb, true);
            } else {
                setLocked(noneCb, false);
            }

            // Lock all symptoms while "None" is checked.
            const noneLocked = noneCb.checked;
            otherCbs.forEach(cb => {
                if (noneLocked) cb.checked = false;
                setLocked(cb, noneLocked);
            });
        }

        if (noneCb) {
            [noneCb, ...otherCbs].forEach(cb =>
                cb.addEventListener('change', syncSymptomLock)
            );
            syncSymptomLock();

            // Toast when clicking a disabled "None" (symptoms are checked).
            const noneLbl = noneCb.closest('.checkbox-label');
            noneLbl?.addEventListener('click', e => {
                if (noneCb.disabled) {
                    e.preventDefault();
                    showToast('error', 'Action Blocked', 'Uncheck the selected symptoms first before marking "None".');
                }
            });

            // Toast when clicking a disabled symptom ("None" is checked).
            otherCbs.forEach(cb => {
                const lbl = cb.closest('.checkbox-label');
                lbl?.addEventListener('click', e => {
                    if (cb.disabled) {
                        e.preventDefault();
                        showToast('error', 'Action Blocked', 'Uncheck "None" first before selecting a symptom.');
                    }
                });
            });

            // Re-sync after Clear Form — reset doesn't fire change events,
            // so "None" would otherwise stay locked until page reload.
            surveyForm.addEventListener('reset', () => {
                setTimeout(syncSymptomLock, 0);
            });
        }

        /* "No middle name" toggle — fills the field with N/A and locks it.
           Uses readOnly (not disabled) so the value is still submitted. */
        function bindNoMiddleName(inputId, cbId) {
            const input = document.getElementById(inputId);
            const cb = document.getElementById(cbId);
            if (!input || !cb) return;
            function sync() {
                if (cb.checked) {
                    input.value = "N/A";
                    input.readOnly = true;
                    input.classList.add("na-locked");
                } else {
                    if (input.value === "N/A") input.value = "";
                    input.readOnly = false;
                    input.classList.remove("na-locked");
                }
            }
            cb.addEventListener("change", sync);
            // Reset doesn't fire change events — re-sync after Clear Form.
            surveyForm.addEventListener("reset", () => setTimeout(() => {
                cb.checked = false;
                sync();
            }, 0));
            sync();
        }
        bindNoMiddleName("surveyMiddleName", "surveyMiddleNameNA");
        bindNoMiddleName("ecMiddleName", "ecMiddleNameNA");

        surveyForm.addEventListener("submit", e => {
            e.preventDefault();

            const fd = new FormData(surveyForm);
            const suffix = (fd.get("suffix") || "").trim();
            const birthdate = (fd.get("birthdate") || "").trim();
            const age = (fd.get("age") || "").trim();
            const purok = fd.get("purok") || "";
            const civilStatus = fd.get("civil_status") || "";
            const gender = fd.get("gender") || "";
            const vax = fd.get("vaccination_status") || "";
            const lastCheckup = (fd.get("last_checkup") || "").trim();

            // Date constants
            const today = new Date();
            today.setHours(23, 59, 59, 999); // end of today, allow today's date

            // Resident name — First / Middle / Last, each required and letters-only.
            const nameRe = /^[A-Za-zÀ-ÿ\s.\-']+$/;
            const nameChecks = [
                ["First Name",  "first_name"],
                ["Middle Name", "middle_name"],
                ["Last Name",   "last_name"],
            ];
            for (const [label, field] of nameChecks) {
                const value = (fd.get(field) || "").trim();
                if (!value) {
                    showToast("error", "Missing Field", `Please enter the ${label.toLowerCase()}.`);
                    return;
                }
                // Middle name may be "N/A" for residents with no middle name.
                if (field === "middle_name" && value.toUpperCase() === "N/A") {
                    const el = surveyForm.querySelector(`[name="${field}"]`);
                    if (el) el.value = "N/A";
                    fd.set(field, "N/A");
                    continue;
                }
                if (!nameRe.test(value)) {
                    showToast("error", `Invalid ${label}`, `${label} may only contain letters, spaces, dots, hyphens, and apostrophes.`);
                    return;
                }
                // Auto-capitalize, then write back to the field & FormData.
                const titled = toTitleCase(value);
                const el = surveyForm.querySelector(`[name="${field}"]`);
                if (el) el.value = titled;
                fd.set(field, titled);
            }
            if (suffix && !/^[A-Za-z.\s]{1,10}$/.test(suffix)) {
                showToast("error", "Invalid Suffix", "Suffix may only contain letters, dots, and spaces (e.g. Jr., Sr., III).");
                return;
            }
            if (!birthdate) {
                showToast("error", "Missing Field", "Please enter the birthdate.");
                return;
            }
            const bdDate = new Date(birthdate);
            if (isNaN(bdDate.getTime())) {
                showToast("error", "Invalid Birthdate", "Please enter a valid birthdate.");
                return;
            }
            if (bdDate > today) {
                showToast("error", "Invalid Birthdate", "Birthdate cannot be in the future.");
                return;
            }
            if (bdDate.getFullYear() < 1900) {
                showToast("error", "Invalid Birthdate", "Birthdate year must be 1900 or later.");
                return;
            }
            if (!age) {
                showToast("error", "Missing Field", "Please enter the age.");
                return;
            }
            const ageNum = parseInt(age, 10);
            if (isNaN(ageNum) || ageNum < 1 || ageNum > 120) {
                showToast("error", "Invalid Age", "Age must be a number between 1 and 120.");
                return;
            }
            if (!civilStatus) {
                showToast("error", "Missing Field", "Please select a civil status.");
                return;
            }
            if (!purok) {
                showToast("error", "Missing Field", "Please select an address (Purok).");
                return;
            }
            if (!gender) {
                showToast("error", "Missing Field", "Please select a gender.");
                return;
            }
            if (!vax) {
                showToast("error", "Missing Field", "Please select a vaccination status.");
                return;
            }
            if (!lastCheckup) {
                showToast("error", "Missing Field", "Please enter the last medical checkup date.");
                return;
            }
            const lcDate = new Date(lastCheckup);
            if (isNaN(lcDate.getTime()) || lcDate > today) {
                showToast("error", "Invalid Date", "Last checkup cannot be in the future.");
                return;
            }
            if (lcDate < bdDate) {
                showToast("error", "Invalid Date", "Last checkup cannot be before the birthdate.");
                return;
            }

            const symptomsChecked = surveyForm.querySelectorAll('input[name="symptoms[]"]:checked');
            if (symptomsChecked.length === 0) {
                showToast("error", "Missing Field", "Please answer the recent symptoms (select at least one, or 'None').");
                return;
            }

            /* ---- Emergency Contact validation ---- */
            const ecFirst   = (fd.get("ec_first_name")     || "").trim();
            const ecMiddle  = (fd.get("ec_middle_name")    || "").trim();
            const ecLast    = (fd.get("ec_last_name")      || "").trim();
            const ecNumber  = (fd.get("ec_contact_number") || "").trim();
            const ecRel     = fd.get("ec_relationship")    || "";

            const ecNameChecks = [
                ["First Name",  ecFirst,  "ec_first_name"],
                ["Middle Name", ecMiddle, "ec_middle_name"],
                ["Last Name",   ecLast,   "ec_last_name"],
            ];
            for (const [label, value, field] of ecNameChecks) {
                if (!value) {
                    showToast("error", "Missing Field", `Please enter the contact person's ${label.toLowerCase()}.`);
                    return;
                }
                // Middle name may be "N/A" for a contact person with no middle name.
                if (field === "ec_middle_name" && value.toUpperCase() === "N/A") {
                    const el = surveyForm.querySelector(`[name="${field}"]`);
                    if (el) el.value = "N/A";
                    fd.set(field, "N/A");
                    continue;
                }
                if (!nameRe.test(value)) {
                    showToast("error", `Invalid ${label}`, `${label} may only contain letters, spaces, dots, hyphens, and apostrophes.`);
                    return;
                }
                // Auto-capitalize and write back to the field & FormData.
                const titled = toTitleCase(value);
                const el = surveyForm.querySelector(`[name="${field}"]`);
                if (el) el.value = titled;
                fd.set(field, titled);
            }

            if (!ecNumber) {
                showToast("error", "Missing Field", "Please enter the contact person's contact number.");
                return;
            }
            if (!/^09\d{9}$/.test(ecNumber)) {
                showToast("error", "Invalid Contact Number", "Contact number must be 11 digits and start with 09 (e.g. 09171234567).");
                return;
            }
            if (!ecRel) {
                showToast("error", "Missing Field", "Please select the relationship to the contact person.");
                return;
            }

            const submitBtn = surveyForm.querySelector('[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            fetch("handlers/submit_survey.php", { method: "POST", body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        surveyForm.reset();
                        showToast("success",
                            data.updated ? "Survey Updated" : "Survey Submitted",
                            data.updated
                                ? "This resident already had a survey on record, so their existing response was updated."
                                : "Health survey saved. The dashboard and residents list have been updated.");
                    } else {
                        showToast("error", "Submission Failed",
                            data.error || "Please check your input and try again.");
                    }
                })
                .catch(() => showToast("error", "Network Error", "Could not connect. Please try again."))
                .finally(() => { if (submitBtn) submitBtn.disabled = false; });
        });
    }

    /* =============================================
       DASHBOARD FILTER
    ============================================= */
    const dashboardTitle = document.getElementById("dashboardTableTitle");
    const dashboardFilterText = document.getElementById("dashboardFilterText");
    const dashboardSearch = document.getElementById("dashboardSearch");
    let dashboardPg = null;

    function matchesFilter(row, filter) {
        if (filter === "all") return true;
        if (filter === "Male") return row.dataset.gender === "Male";
        if (filter === "Female") return row.dataset.gender === "Female";
        if (filter === "Vaccinated") return row.dataset.vaccine === "Vaccinated";
        if (filter === "Unvaccinated") return row.dataset.vaccine === "Unvaccinated";
        if (filter === "Symptoms") return row.dataset.symptoms === "Yes";
        return false;
    }

    document.querySelectorAll(".dashboard-filter").forEach(card => {
        card.addEventListener("click", () => {
            const filter = card.dataset.filter;
            document.querySelectorAll("#dashboardResidentTable tr").forEach(row => {
                if (matchesFilter(row, filter)) {
                    delete row.dataset.searchHidden;
                } else {
                    row.dataset.searchHidden = "1";
                }
            });
            dashboardPg?.goFirst();
            if (dashboardTitle)
                dashboardTitle.textContent = filter === "all" ? "All Residents" : `${filter} Residents`;
            if (dashboardFilterText)
                dashboardFilterText.textContent = filter === "all"
                    ? "Showing complete resident records"
                    : `Showing filtered resident records for ${filter}`;
        });
    });

    dashboardSearch?.addEventListener("input", () => {
        const val = dashboardSearch.value.toLowerCase().trim();
        document.querySelectorAll("#dashboardResidentTable tr").forEach(row => {
            if (!val || row.innerText.toLowerCase().includes(val)) {
                delete row.dataset.searchHidden;
            } else {
                row.dataset.searchHidden = "1";
            }
        });
        dashboardPg?.goFirst();
    });

    /* =============================================
       SIDEBAR: MOBILE TOGGLE + DESKTOP COLLAPSE
    ============================================= */
    const openSidebarBtn = document.getElementById("openSidebar");
    const closeSidebarBtn = document.getElementById("closeSidebar");
    const collapseSidebarBtn = document.getElementById("collapseSidebar");
    const sidebarEl = document.querySelector(".sidebar");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    function openSidebar() {
        sidebarEl?.classList.add("open");
        sidebarBackdrop?.classList.add("show");
    }
    function closeSidebar() {
        sidebarEl?.classList.remove("open");
        sidebarBackdrop?.classList.remove("show");
    }
    function toggleSidebar() {
        if (sidebarEl?.classList.contains("open")) closeSidebar();
        else openSidebar();
    }

    openSidebarBtn?.addEventListener("click", toggleSidebar);
    closeSidebarBtn?.addEventListener("click", closeSidebar);
    sidebarBackdrop?.addEventListener("click", closeSidebar);

    document.querySelectorAll(".sidebar ul li a").forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth > 768) closeSidebar();
    });

    // ----- Desktop / tablet collapse toggle -----
    function applySidebarState() {
        if (!sidebarEl) return;
        const w = window.innerWidth;
        const userPref = localStorage.getItem("sidebarState"); // "collapsed" | "expanded" | null

        // On mobile (≤768px), overlay handles things; ignore collapsed/force-expanded look.
        if (w <= 768) {
            sidebarEl.classList.remove("collapsed");
            sidebarEl.classList.remove("force-expanded");
            return;
        }

        // Tablet (769–1024px): default to icon-only unless user forced expand.
        if (w <= 1024) {
            sidebarEl.classList.remove("collapsed");
            if (userPref === "expanded") {
                sidebarEl.classList.add("force-expanded");
            } else {
                sidebarEl.classList.remove("force-expanded");
            }
            return;
        }

        // Desktop (>1024px): full sidebar unless user collapsed it.
        sidebarEl.classList.remove("force-expanded");
        if (userPref === "collapsed") {
            sidebarEl.classList.add("collapsed");
        } else {
            sidebarEl.classList.remove("collapsed");
        }
    }

    collapseSidebarBtn?.addEventListener("click", () => {
        if (!sidebarEl) return;
        const w = window.innerWidth;

        if (w <= 1024 && w > 768) {
            // Tablet: toggle force-expanded (icon-only ↔ full)
            if (sidebarEl.classList.contains("force-expanded")) {
                sidebarEl.classList.remove("force-expanded");
                localStorage.setItem("sidebarState", "collapsed");
            } else {
                sidebarEl.classList.add("force-expanded");
                localStorage.setItem("sidebarState", "expanded");
            }
        } else if (w > 1024) {
            // Desktop: toggle collapsed
            if (sidebarEl.classList.contains("collapsed")) {
                sidebarEl.classList.remove("collapsed");
                localStorage.setItem("sidebarState", "expanded");
            } else {
                sidebarEl.classList.add("collapsed");
                localStorage.setItem("sidebarState", "collapsed");
            }
        }
    });

    applySidebarState();
    // After the initial state is settled, re-enable transitions for user-driven toggles.
    requestAnimationFrame(() => {
        document.documentElement.classList.remove("sb-preload");
    });
    window.addEventListener("resize", applySidebarState);

    /* =============================================
       INIT PAGINATION
    ============================================= */
    residentPg = initPagination("residentTable", "residentsPagination", 5);
    dashboardPg = initPagination("dashboardResidentTable", "dashboardPagination", 5);

    /* =============================================
       ACCOUNT MODAL (clickable sidebar user)
    ============================================= */
    const accountModal = document.getElementById("accountModal");
    const sidebarUser = document.getElementById("sidebarUser");

    function switchAccountTab(name) {
        document.querySelectorAll(".account-tab").forEach(t =>
            t.classList.toggle("active", t.dataset.tab === name)
        );
        document.querySelectorAll(".account-panel").forEach(p =>
            p.classList.toggle("active", p.id === "panel-" + name)
        );
        if (name === "activity") loadActivityLogs();
    }

    if (sidebarUser && accountModal) {
        sidebarUser.addEventListener("click", () => {
            switchAccountTab("profile");
            ["oldPassword", "newPassword", "confirmPassword"].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = "";
            });
            openModal(accountModal);
        });

        document.querySelectorAll(".closeAccountModal").forEach(btn =>
            btn.addEventListener("click", () => closeModal(accountModal))
        );
        accountModal.addEventListener("click", e => {
            if (e.target === accountModal) closeModal(accountModal);
        });

        document.querySelectorAll(".account-tab").forEach(tab => {
            tab.addEventListener("click", () => switchAccountTab(tab.dataset.tab));
        });
    }

    // ----- Save username -----
    const saveUsernameBtn = document.getElementById("saveUsernameBtn");
    saveUsernameBtn?.addEventListener("click", () => {
        const username = document.getElementById("acctUsername").value.trim();
        if (username.length < 3 || username.length > 50) {
            showToast("error", "Invalid Username", "Username must be 3–50 characters.");
            return;
        }
        if (!/^[A-Za-z0-9_.\-]+$/.test(username)) {
            showToast("error", "Invalid Username", "Only letters, numbers, dots, underscores, hyphens allowed.");
            return;
        }

        const fd = new FormData();
        fd.append("username", username);

        saveUsernameBtn.disabled = true;
        fetch("handlers/update_username.php", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showToast("error", "Update Failed", data.error || "Could not update username.");
                    return;
                }
                document.querySelectorAll(".sidebar-user-name").forEach(el => {
                    el.textContent = data.username;
                });
                if (data.unchanged) {
                    showToast("success", "No Changes", "Username is already up to date.");
                } else {
                    showToast("success", "Username Updated", `You are now '${data.username}'.`);
                }
                closeModal(accountModal);
            })
            .catch(() => showToast("error", "Network Error", "Could not connect."))
            .finally(() => { saveUsernameBtn.disabled = false; });
    });

    // ----- Password visibility toggles (account modal) -----
    document.querySelectorAll(".toggle-pwd").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const icon = btn.querySelector(".material-icons");
            const show = input.type === "password";
            input.type = show ? "text" : "password";
            if (icon) icon.textContent = show ? "visibility_off" : "visibility";
            btn.setAttribute("aria-label", show ? "Hide password" : "Show password");
        });
    });

    // ----- Change password -----
    const changePasswordBtn = document.getElementById("changePasswordBtn");
    changePasswordBtn?.addEventListener("click", () => {
        const oldPwd = document.getElementById("oldPassword").value;
        const newPwd = document.getElementById("newPassword").value;
        const confirmPwd = document.getElementById("confirmPassword").value;

        if (!oldPwd) {
            showToast("error", "Missing Field", "Please enter your current password.");
            return;
        }
        const pwdErr = validatePasswordStrength(newPwd);
        if (pwdErr) {
            showToast("error", "Weak Password", pwdErr);
            return;
        }
        if (newPwd !== confirmPwd) {
            showToast("error", "Passwords Do Not Match", "Confirm password must match the new password.");
            return;
        }

        const fd = new FormData();
        fd.append("old_password", oldPwd);
        fd.append("new_password", newPwd);

        changePasswordBtn.disabled = true;
        fetch("handlers/change_password.php", { method: "POST", body: fd })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showToast("error", "Update Failed", data.error || "Could not change password.");
                    return;
                }
                ["oldPassword", "newPassword", "confirmPassword"].forEach(id => {
                    document.getElementById(id).value = "";
                });
                closeModal(accountModal);
                showToast("success", "Password Changed", "Your password has been updated.");
            })
            .catch(() => showToast("error", "Network Error", "Could not connect."))
            .finally(() => { changePasswordBtn.disabled = false; });
    });

    // ----- Load activity logs -----
    function loadActivityLogs() {
        const list = document.getElementById("activityList");
        if (!list) return;
        list.innerHTML = '<div class="activity-loading">Loading activity…</div>';

        fetch("handlers/get_logs.php")
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    list.innerHTML = '<div class="activity-empty">Could not load activity.</div>';
                    return;
                }
                if (!data.logs || data.logs.length === 0) {
                    list.innerHTML =
                        '<div class="activity-empty">' +
                        '<span class="material-icons" style="font-size:48px;color:#cbd5e1;display:block;margin-bottom:8px;">history</span>' +
                        'No activity yet.' +
                        '</div>';
                    return;
                }
                list.innerHTML = data.logs.map(log => {
                    const action = log.action || "";
                    const target = log.target ? `<span class="activity-target"> · ${escapeHtml(log.target)}</span>` : "";
                    const details = log.details ? `<div class="activity-details">${escapeHtml(log.details)}</div>` : "";
                    return `
                        <div class="activity-item">
                            <div class="activity-icon">
                                <span class="material-icons">${getActivityIcon(action)}</span>
                            </div>
                            <div class="activity-text">
                                <div class="activity-action">${escapeHtml(action)}${target}</div>
                                ${details}
                            </div>
                            <span class="activity-time">${formatActivityTime(log.created_at)}</span>
                        </div>`;
                }).join("");
            })
            .catch(() => {
                list.innerHTML = '<div class="activity-empty">Could not load activity.</div>';
            });
    }

    function getActivityIcon(action) {
        const a = (action || "").toLowerCase();
        if (a.includes("added")) return "person_add";
        if (a.includes("edited")) return "edit";
        if (a.includes("deleted")) return "delete";
        if (a.includes("logged")) return "login";
        if (a.includes("password")) return "vpn_key";
        if (a.includes("username")) return "badge";
        return "history";
    }

    function formatActivityTime(timestamp) {
        if (!timestamp) return "";
        const d = new Date(timestamp.replace(' ', 'T'));
        if (isNaN(d.getTime())) return timestamp;
        const now = new Date();
        const diffMin = Math.floor((now - d) / 60000);
        if (diffMin < 1) return "Just now";
        if (diffMin < 60) return diffMin + "m ago";
        if (diffMin < 1440) return Math.floor(diffMin / 60) + "h ago";
        if (diffMin < 10080) return Math.floor(diffMin / 1440) + "d ago";
        return d.toLocaleDateString();
    }

});
