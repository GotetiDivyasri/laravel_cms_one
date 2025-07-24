// User Management System - Custom JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize form validation
    initFormValidation();
    
    // Initialize search functionality
    initSearchFunctionality();
    
    // Initialize delete confirmations
    initDeleteConfirmations();
    
    // Initialize auto-hide alerts
    initAutoHideAlerts();
});

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Real-time validation
    const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const name = field.name;
    
    // Remove existing validation classes
    field.classList.remove('is-valid', 'is-invalid');
    
    // Remove existing feedback
    const existingFeedback = field.parentNode.querySelector('.invalid-feedback, .valid-feedback');
    if (existingFeedback) {
        existingFeedback.remove();
    }
    
    let isValid = true;
    let message = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'This field is required.';
    }
    
    // Email validation
    else if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Please enter a valid email address.';
        }
    }
    
    // Password validation
    else if (name === 'password' && value) {
        if (value.length < 6) {
            isValid = false;
            message = 'Password must be at least 6 characters long.';
        }
    }
    
    // Confirm password validation
    else if (name === 'confirm_password' && value) {
        const passwordField = document.querySelector('input[name="password"]');
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            message = 'Passwords do not match.';
        }
    }
    
    // Apply validation result
    if (isValid && value) {
        field.classList.add('is-valid');
        showFieldFeedback(field, message || 'Looks good!', 'valid');
    } else if (!isValid) {
        field.classList.add('is-invalid');
        showFieldFeedback(field, message, 'invalid');
    }
}

function showFieldFeedback(field, message, type) {
    const feedbackDiv = document.createElement('div');
    feedbackDiv.className = type === 'valid' ? 'valid-feedback' : 'invalid-feedback';
    feedbackDiv.textContent = message;
    field.parentNode.appendChild(feedbackDiv);
}

// Search Functionality
function initSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#dataTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Delete Confirmations
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            const itemName = this.dataset.name || 'this item';
            
            showDeleteConfirmation(itemName, url);
        });
    });
}

function showDeleteConfirmation(itemName, url) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>${itemName}</strong>?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="${url}" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </a>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Auto-hide Alerts
function initAutoHideAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-hide]');
    alerts.forEach(alert => {
        const delay = parseInt(alert.dataset.autoHide) || 5000;
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, delay);
    });
}

// Show Loading Spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="spinner-border spinner-border-custom text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

// Hide Loading Spinner
function hideLoading() {
    const spinner = document.querySelector('.spinner-overlay');
    if (spinner) {
        document.body.removeChild(spinner);
    }
}

// AJAX Form Submission
function submitFormAjax(form, successCallback, errorCallback) {
    const formData = new FormData(form);
    
    showLoading();
    
    fetch(form.action, {
        method: form.method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            if (errorCallback) errorCallback(data);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        if (errorCallback) errorCallback({message: 'An error occurred'});
    });
}

// Show Alert
function showAlert(message, type = 'success', autoHide = true) {
    const alertContainer = document.getElementById('alertContainer') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    if (autoHide) alert.setAttribute('data-auto-hide', '5000');
    
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alert, alertContainer.firstChild);
    
    if (autoHide) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    }
}

// Format Date
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Debounce Function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize DataTable (if available)
function initDataTable(tableId, options = {}) {
    const table = document.getElementById(tableId);
    if (table && typeof DataTable !== 'undefined') {
        return new DataTable(table, {
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            ...options
        });
    }
}

// Password Strength Checker
function checkPasswordStrength(password) {
    let strength = 0;
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        numbers: /\d/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    Object.values(checks).forEach(check => {
        if (check) strength++;
    });
    
    return {
        score: strength,
        checks: checks,
        level: strength < 2 ? 'weak' : strength < 4 ? 'medium' : 'strong'
    };
}

// Show Password Strength
function showPasswordStrength(inputId, containerId) {
    const input = document.getElementById(inputId);
    const container = document.getElementById(containerId);
    
    if (!input || !container) return;
    
    input.addEventListener('input', function() {
        const password = this.value;
        if (!password) {
            container.innerHTML = '';
            return;
        }
        
        const strength = checkPasswordStrength(password);
        const colors = {
            weak: 'danger',
            medium: 'warning',
            strong: 'success'
        };
        
        container.innerHTML = `
            <div class="password-strength mt-2">
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-${colors[strength.level]}" 
                         style="width: ${(strength.score / 5) * 100}%"></div>
                </div>
                <small class="text-${colors[strength.level]} mt-1 d-block">
                    Password strength: ${strength.level.toUpperCase()}
                </small>
            </div>
        `;
    });
}