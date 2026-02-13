@extends('layouts.main')

@section('content')
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--light-gray) 0%, rgba(0, 169, 165, 0.05) 100%);
        padding: 20px;
    }

    .auth-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0, 169, 165, 0.15);
        width: 100%;
        max-width: 600px;
        overflow: hidden;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 100%);
        color: var(--white);
        padding: 40px;
        text-align: center;
    }

    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .auth-logo-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 24px;
        font-weight: bold;
    }

    .auth-logo-text {
        font-size: 24px;
        font-weight: 800;
    }

    .auth-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .auth-subtitle {
        opacity: 0.9;
        font-size: 14px;
    }

    .auth-body {
        padding: 40px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--navy);
        font-weight: 600;
        font-size: 14px;
    }

    .required-star {
        color: #dc3545;
        margin-left: 3px;
    }

    input,
    select {
        width: 100%;
        padding: 14px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 15px;
        transition: all 0.3s;
        background-color: var(--white);
    }

    input:focus,
    select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(0, 169, 165, 0.1);
    }

    select {
        cursor: pointer;
    }

    .file-upload-area {
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: var(--light-gray);
    }

    .file-upload-area:hover {
        border-color: var(--teal);
        background-color: rgba(0, 169, 165, 0.05);
    }

    .file-upload-area input[type="file"] {
        display: none;
    }

    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .upload-icon {
        font-size: 32px;
        color: var(--teal);
    }

    .upload-text {
        color: var(--gray);
        font-size: 14px;
    }

    .file-name {
        margin-top: 10px;
        font-size: 13px;
        color: var(--teal);
        font-weight: 600;
    }

    .document-section {
        background: linear-gradient(135deg, var(--light-gray) 0%, #ffffff 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 4px solid var(--teal);
    }

    .document-section h3 {
        color: var(--navy);
        font-size: 18px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .document-section h3 i {
        color: var(--teal);
    }

    .document-hint {
        font-size: 13px;
        color: var(--gray);
        margin-top: 5px;
    }

    .auth-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 25px;
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 169, 165, 0.4);
    }

    .auth-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .auth-footer {
        text-align: center;
        color: var(--gray);
        font-size: 14px;
    }

    .auth-footer a {
        color: var(--teal);
        text-decoration: none;
        font-weight: 600;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
        font-weight: 500;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-size: 14px;
    }

    .alert-error {
        background: linear-gradient(135deg, #ffd4d4 0%, #ffb8b8 100%);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .courier-fields {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s ease;
    }

    .courier-fields.visible {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="auth-logo-icon">N</div>
                <div class="auth-logo-text">NeoProLab</div>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join our medical courier network</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registrationForm">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required-star">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required-star">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required-star">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" required>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="required-star">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">I am a <span class="required-star">*</span></label>
                    <select id="role" name="role" required onchange="toggleCourierFields(this.value)">
                        <option value="">Select role...</option>
                        <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Healthcare Facility Staff</option>
                        <option value="courier" {{ old('role') == 'courier' ? 'selected' : '' }}>Courier/Driver</option>
                    </select>
                    @error('role')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Courier-specific document upload fields -->
                <div id="courierFields" class="courier-fields {{ old('role') == 'courier' ? 'visible' : '' }}">
                    <div class="document-section">
                        <h3>
                            <i>📋</i>
                            Required Documents for Courier Verification
                        </h3>
                        <p style="color: var(--gray); font-size: 14px; margin-bottom: 20px;">
                            Please upload clear, color copies of the following documents. All files should be in JPG, PNG, or PDF format (max 5MB each).
                        </p>

                        <!-- 1. Profile Picture -->
                        <div class="form-group">
                            <label>1. Profile Picture <span class="required-star">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('profile_image').click()">
                                <input type="file" id="profile_image" name="profile_image" accept="image/jpeg,image/png" onchange="updateFileName(this, 'profile_file_name')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">📷</span>
                                    <span class="upload-text">Click to upload your profile picture</span>
                                    <span class="upload-text" style="font-size: 12px;">JPG or PNG, max 5MB</span>
                                </div>
                                <div id="profile_file_name" class="file-name"></div>
                            </div>
                            @error('profile_image')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 2. Government Issue ID -->
                        <div class="form-group">
                            <label>2. Government Issue ID <span class="required-star">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('government_id').click()">
                                <input type="file" id="government_id" name="government_id" accept="image/jpeg,image/png,application/pdf" onchange="updateFileName(this, 'govt_id_file_name')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🪪</span>
                                    <span class="upload-text">Click to upload your ID (Passport, Driver's License, or National ID)</span>
                                    <span class="upload-text" style="font-size: 12px;">JPG, PNG, or PDF, max 5MB</span>
                                </div>
                                <div id="govt_id_file_name" class="file-name"></div>
                            </div>
                            @error('government_id')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 3. Proof of Residency -->
                        <div class="form-group">
                            <label>3. Proof of Residency <span class="required-star">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('proof_of_residency').click()">
                                <input type="file" id="proof_of_residency" name="proof_of_residency" accept="image/jpeg,image/png,application/pdf" onchange="updateFileName(this, 'residency_file_name')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🏠</span>
                                    <span class="upload-text">Click to upload proof of address (Utility bill, bank statement, or lease agreement)</span>
                                    <span class="upload-text" style="font-size: 12px;">JPG, PNG, or PDF, max 5MB, less than 3 months old</span>
                                </div>
                                <div id="residency_file_name" class="file-name"></div>
                            </div>
                            @error('proof_of_residency')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 4. Driver's License -->
                        <div class="form-group">
                            <label>4. Driver's License <span class="required-star">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('drivers_license').click()">
                                <input type="file" id="drivers_license" name="drivers_license" accept="image/jpeg,image/png,application/pdf" onchange="updateFileName(this, 'license_file_name')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🚗</span>
                                    <span class="upload-text">Click to upload your valid driver's license (front and back)</span>
                                    <span class="upload-text" style="font-size: 12px;">JPG, PNG, or PDF, max 5MB</span>
                                </div>
                                <div id="license_file_name" class="file-name"></div>
                            </div>
                            @error('drivers_license')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 5. Medical Transport Certificate -->
                        <div class="form-group">
                            <label>5. Medical Transport Certificate <span class="required-star">*</span></label>
                            <div class="file-upload-area" onclick="document.getElementById('medical_transport_cert').click()">
                                <input type="file" id="medical_transport_cert" name="medical_transport_cert" accept="image/jpeg,image/png,application/pdf" onchange="updateFileName(this, 'cert_file_name')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🏥</span>
                                    <span class="upload-text">Click to upload your medical transport certification</span>
                                    <span class="upload-text" style="font-size: 12px;">JPG, PNG, or PDF, max 5MB</span>
                                </div>
                                <div id="cert_file_name" class="file-name"></div>
                            </div>
                            @error('medical_transport_cert')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                            <div class="document-hint">
                                If you don't have this yet, you can upload it later from your profile. Your account will be pending until verified.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">Password <span class="required-star">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <div class="document-hint">Minimum 8 characters with mixed case, numbers, and symbols</div>
                        @error('password')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="required-star">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">Create Account</button>

                <div class="auth-footer">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleCourierFields(role) {
    const courierFields = document.getElementById('courierFields');
    const fileInputs = document.querySelectorAll('#courierFields input[type="file"]');
    
    if (role === 'courier') {
        courierFields.classList.add('visible');
        // Make file inputs required
        fileInputs.forEach(input => {
            if (input.id !== 'medical_transport_cert') { // Medical cert optional initially
                input.setAttribute('required', 'required');
            }
        });
    } else {
        courierFields.classList.remove('visible');
        // Remove required attribute
        fileInputs.forEach(input => {
            input.removeAttribute('required');
        });
    }
}

function updateFileName(input, displayElementId) {
    const displayElement = document.getElementById(displayElementId);
    if (input.files && input.files[0]) {
        displayElement.textContent = `Selected: ${input.files[0].name}`;
    } else {
        displayElement.textContent = '';
    }
}

// Form validation before submit
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    const role = document.getElementById('role').value;
    
    if (role === 'courier') {
        const requiredFiles = ['profile_image', 'government_id', 'proof_of_residency', 'drivers_license'];
        let missingFiles = [];
        
        requiredFiles.forEach(fileId => {
            const input = document.getElementById(fileId);
            if (!input.files || input.files.length === 0) {
                missingFiles.push(fileId.replace('_', ' '));
            }
        });
        
        if (missingFiles.length > 0) {
            e.preventDefault();
            alert('Please upload all required documents: ' + missingFiles.join(', '));
        }
    }
});

// Trigger on page load if old role is courier
document.addEventListener('DOMContentLoaded', function() {
    const role = document.getElementById('role').value;
    if (role === 'courier') {
        toggleCourierFields('courier');
    }
});
</script>
@endsection