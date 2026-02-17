@extends('layouts.main')

@section('content')
<style>
    :root {
        --navy: #0B1E33;
        --teal: #0A9396;
        --teal-light: #94D2BD;
        --teal-dark: #005F73;
        --white: #FFFFFF;
        --gray: #64748B;
        --gray-light: #F1F5F9;
        --gray-dark: #334155;
        --danger: #EF4444;
        --danger-light: #FEE2E2;
        --success: #10B981;
        --success-light: #D1FAE5;
        --warning: #F59E0B;
        --warning-light: #FEF3C7;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 40px -3px rgba(10, 147, 150, 0.2);
        --transition: all 0.2s ease;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--gray-light) 0%, #E2E8F0 100%);
        padding: 2rem 1.5rem;
        position: relative;
        overflow: hidden;
    }

    /* Decorative elements */
    .auth-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(10, 147, 150, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .auth-container::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(11, 30, 51, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: var(--shadow-lg);
        width: 100%;
        max-width: 800px;
        overflow: hidden;
        position: relative;
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .auth-header {
        background: linear-gradient(135deg, var(--navy) 0%, #1A2F48 100%);
        color: var(--white);
        padding: 2.5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .auth-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .auth-logo-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.5rem;
        font-weight: 700;
        box-shadow: var(--shadow-md);
        transform: rotate(0deg);
        transition: var(--transition);
    }

    .auth-logo:hover .auth-logo-icon {
        transform: rotate(5deg) scale(1.05);
    }

    .auth-logo-text {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        background: linear-gradient(135deg, var(--white) 0%, #E2E8F0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .auth-subtitle {
        opacity: 0.9;
        font-size: 0.95rem;
        font-weight: 400;
    }

    .auth-body {
        padding: 2.5rem 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: var(--gray-dark);
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group label svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: var(--teal);
        stroke-width: 2;
    }

    .required-star {
        color: var(--danger);
        margin-left: 0.25rem;
        font-size: 1.1rem;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper input,
    .input-wrapper select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--gray-light);
        border-radius: 12px;
        font-family: inherit;
        font-size: 1rem;
        transition: var(--transition);
        background-color: var(--white);
        color: var(--gray-dark);
        appearance: none;
    }

    .input-wrapper select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px;
        padding-right: 2.5rem;
    }

    .input-wrapper input:hover,
    .input-wrapper select:hover {
        border-color: var(--teal-light);
    }

    .input-wrapper input:focus,
    .input-wrapper select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(10, 147, 150, 0.1);
    }

    .input-wrapper input::placeholder {
        color: var(--gray);
        opacity: 0.5;
    }

    .input-wrapper.error input,
    .input-wrapper.error select {
        border-color: var(--danger);
        background-color: var(--danger-light);
    }

    .input-wrapper.success input,
    .input-wrapper.success select {
        border-color: var(--success);
        background-color: var(--success-light);
    }

    /* Alternative style - more prominent */
    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
        border: 2px solid var(--white);
        border-radius: 50%;
        cursor: pointer;
        padding: 0.5rem;
        color: var(--white);
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }

    .toggle-password:hover {
        transform: translateY(-50%) scale(1.15);
        box-shadow: 0 4px 12px rgba(10, 147, 150, 0.4);
    }

    .toggle-password svg {
        width: 22px;
        height: 22px;
        stroke: var(--white);
        stroke-width: 2.5;
        filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.1));
    }

    .toggle-password:hover {
        color: var(--teal);
    }

    /* Document Upload Styles */
    .document-section {
        background: linear-gradient(135deg, rgba(10, 147, 150, 0.05) 0%, transparent 100%);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(10, 147, 150, 0.2);
        animation: slideIn 0.5s ease;
    }

    .document-section h3 {
        color: var(--navy);
        font-size: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .document-section h3 svg {
        width: 24px;
        height: 24px;
        stroke: var(--teal);
        fill: none;
    }

    .document-hint {
        font-size: 0.85rem;
        color: var(--gray);
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .document-hint svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
    }

    .file-upload-area {
        border: 2px dashed var(--gray-light);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        background: var(--white);
        position: relative;
        overflow: hidden;
    }

    .file-upload-area:hover {
        border-color: var(--teal);
        background: linear-gradient(135deg, rgba(10, 147, 150, 0.05) 0%, transparent 100%);
    }

    .file-upload-area.has-file {
        border-color: var(--success);
        background: var(--success-light);
    }

    .file-upload-area input[type="file"] {
        display: none;
    }

    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }

    .upload-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--teal-light) 0%, var(--teal) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.5rem;
        transition: var(--transition);
    }

    .file-upload-area:hover .upload-icon {
        transform: scale(1.1);
    }

    .upload-text {
        color: var(--gray-dark);
        font-size: 0.95rem;
        font-weight: 500;
    }

    .upload-hint {
        color: var(--gray);
        font-size: 0.85rem;
    }

    .file-name {
        margin-top: 0.75rem;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        font-size: 0.9rem;
        color: var(--teal-dark);
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        word-break: break-all;
    }

    .file-name svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
    }

    .remove-file {
        background: none;
        border: none;
        color: var(--danger);
        cursor: pointer;
        padding: 0.25rem;
        margin-left: 0.5rem;
        border-radius: 4px;
        transition: var(--transition);
    }

    .remove-file:hover {
        background: var(--danger-light);
    }

    /* Password strength indicator */
    .password-strength {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.25rem;
    }

    .strength-bar {
        height: 4px;
        flex: 1;
        background: var(--gray-light);
        border-radius: 2px;
        transition: var(--transition);
    }

    .strength-bar.weak {
        background: var(--danger);
    }

    .strength-bar.medium {
        background: var(--warning);
    }

    .strength-bar.strong {
        background: var(--success);
    }

    .strength-text {
        font-size: 0.8rem;
        margin-top: 0.25rem;
        color: var(--gray);
    }

    /* Progress tracker for courier documents */
    .document-progress {
        background: var(--white);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-light);
    }

    .progress-bar {
        height: 8px;
        background: var(--gray-light);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--teal) 0%, var(--teal-dark) 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 0.9rem;
        color: var(--gray-dark);
        font-weight: 500;
    }

    .auth-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
        color: var(--white);
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .auth-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(10, 147, 150, 0.4);
    }

    .auth-btn:hover::before {
        left: 100%;
    }

    .auth-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .auth-btn:disabled::before {
        display: none;
    }

    .auth-footer {
        text-align: center;
        color: var(--gray);
        font-size: 0.95rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-light);
    }

    .auth-footer a {
        color: var(--teal);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .auth-footer a:hover {
        color: var(--teal-dark);
        text-decoration: underline;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        color: var(--danger);
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .error-message svg {
        width: 14px;
        height: 14px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideIn 0.3s ease;
    }

    .alert svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .alert-error {
        background: var(--danger-light);
        color: #991B1B;
        border-left: 4px solid var(--danger);
    }

    .alert-warning {
        background: var(--warning-light);
        color: #92400E;
        border-left: 4px solid var(--warning);
    }

    .alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .courier-fields {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .courier-fields.visible {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .auth-header {
            padding: 2rem 1.5rem;
        }

        .auth-body {
            padding: 2rem 1.5rem;
        }

        .auth-title {
            font-size: 1.75rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>
                <div class="auth-logo-text">NeoProLab</div>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join our medical courier network</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <ul>
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
                        <label for="first_name">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            First Name <span class="required-star">*</span>
                        </label>
                        <div class="input-wrapper {{ $errors->has('first_name') ? 'error' : '' }}">
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                placeholder="Enter your first name" required>
                        </div>
                        @error('first_name')
                        <span class="error-message">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Last Name <span class="required-star">*</span>
                        </label>
                        <div class="input-wrapper {{ $errors->has('last_name') ? 'error' : '' }}">
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                placeholder="Enter your last name" required>
                        </div>
                        @error('last_name')
                        <span class="error-message">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        Email Address <span class="required-star">*</span>
                    </label>
                    <div class="input-wrapper {{ $errors->has('email') ? 'error' : '' }}">
                        <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
                            placeholder="Enter your email address" required autocomplete="email">
                    </div>
                    @error('email')
                    <span class="error-message">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">
                        <svg viewBox="0 0 24 24">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                            <line x1="12" y1="18" x2="12.01" y2="18" />
                        </svg>
                        Phone Number <span class="required-star">*</span>
                    </label>
                    <div class="input-wrapper {{ $errors->has('phone') ? 'error' : '' }}">
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            placeholder="Enter your phone number" required>
                    </div>
                    @error('phone')
                    <span class="error-message">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M5.5 20v-2a4 4 0 0 1 4-4h5a4 4 0 0 1 4 4v2" />
                        </svg>
                        I am a <span class="required-star">*</span>
                    </label>
                    <div class="input-wrapper {{ $errors->has('role') ? 'error' : '' }}">
                        <select id="role" name="role" required onchange="toggleCourierFields(this.value)">
                            <option value="">Select your role...</option>
                            <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Healthcare Facility Staff</option>
                            <option value="courier" {{ old('role') == 'courier' ? 'selected' : '' }}>Courier/Driver</option>
                        </select>
                    </div>
                    @error('role')
                    <span class="error-message">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <!-- Courier-specific document upload fields -->
                <div id="courierFields" class="courier-fields {{ old('role') == 'courier' ? 'visible' : '' }}">
                    <div class="document-section">
                        <h3>
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                            Required Documents for Courier Verification
                        </h3>
                        <p style="color: var(--gray); font-size: 0.95rem; margin-bottom: 1.5rem;">
                            Please upload clear, color copies of the following documents. All files should be in JPG, PNG, or PDF format (max 5MB each).
                        </p>

                        <!-- Document Upload Progress -->
                        <div class="document-progress" id="documentProgress">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                            </div>
                            <div class="progress-text" id="progressText">0 of 5 documents uploaded</div>
                        </div>

                        <!-- 1. Profile Picture -->
                        <div class="form-group">
                            <label>1. Profile Picture <span class="required-star">*</span></label>
                            <div class="file-upload-area" id="profileArea" onclick="document.getElementById('profile_image').click()">
                                <input type="file" id="profile_image" name="profile_image"
                                    accept="image/jpeg,image/png"
                                    onchange="handleFileSelect(this, 'profile_file_name', 'profileArea')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">📸</span>
                                    <span class="upload-text">Click to upload your profile picture</span>
                                    <span class="upload-hint">JPG or PNG, max 5MB</span>
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
                            <div class="file-upload-area" id="govtIdArea" onclick="document.getElementById('government_id').click()">
                                <input type="file" id="government_id" name="government_id"
                                    accept="image/jpeg,image/png,application/pdf"
                                    onchange="handleFileSelect(this, 'govt_id_file_name', 'govtIdArea')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🪪</span>
                                    <span class="upload-text">Click to upload your ID</span>
                                    <span class="upload-hint">Passport, Driver's License, or National ID</span>
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
                            <div class="file-upload-area" id="residencyArea" onclick="document.getElementById('proof_of_residency').click()">
                                <input type="file" id="proof_of_residency" name="proof_of_residency"
                                    accept="image/jpeg,image/png,application/pdf"
                                    onchange="handleFileSelect(this, 'residency_file_name', 'residencyArea')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🏠</span>
                                    <span class="upload-text">Click to upload proof of address</span>
                                    <span class="upload-hint">Utility bill, bank statement, or lease agreement</span>
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
                            <div class="file-upload-area" id="licenseArea" onclick="document.getElementById('drivers_license').click()">
                                <input type="file" id="drivers_license" name="drivers_license"
                                    accept="image/jpeg,image/png,application/pdf"
                                    onchange="handleFileSelect(this, 'license_file_name', 'licenseArea')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🚗</span>
                                    <span class="upload-text">Click to upload your driver's license</span>
                                    <span class="upload-hint">Front and back copies preferred</span>
                                </div>
                                <div id="license_file_name" class="file-name"></div>
                            </div>
                            @error('drivers_license')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- 5. Medical Transport Certificate -->
                        <div class="form-group">
                            <label>5. Medical Transport Certificate</label>
                            <div class="file-upload-area" id="certArea" onclick="document.getElementById('medical_transport_cert').click()">
                                <input type="file" id="medical_transport_cert" name="medical_transport_cert"
                                    accept="image/jpeg,image/png,application/pdf"
                                    onchange="handleFileSelect(this, 'cert_file_name', 'certArea')">
                                <div class="file-upload-label">
                                    <span class="upload-icon">🏥</span>
                                    <span class="upload-text">Click to upload your certification</span>
                                    <span class="upload-hint">Medical transport certification (optional)</span>
                                </div>
                                <div id="cert_file_name" class="file-name"></div>
                            </div>
                            @error('medical_transport_cert')
                            <span class="error-message">{{ $message }}</span>
                            @enderror
                            <div class="document-hint">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="12" x2="12" y2="16" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                If you don't have this yet, you can upload it later. Your account will be pending until verified.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Password <span class="required-star">*</span>
                        </label>
                        <div class="input-wrapper {{ $errors->has('password') ? 'error' : '' }}">
                            <input type="password" id="password" name="password"
                                placeholder="Create a password" required
                                oninput="checkPasswordStrength(this.value)">
                            <button type="button" class="toggle-password" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                                <svg viewBox="0 0 24 24" width="20" height="20">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        <div id="passwordStrength" class="password-strength">
                            <div class="strength-bar"></div>
                            <div class="strength-bar"></div>
                            <div class="strength-bar"></div>
                            <div class="strength-bar"></div>
                        </div>
                        <div id="strengthText" class="strength-text">Enter a password</div>
                        @error('password')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                <polyline points="12 15 12 18" />
                            </svg>
                            Confirm Password <span class="required-star">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="Confirm your password" required
                                oninput="validatePasswordMatch(this.value)">
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')" aria-label="Toggle password visibility">
                                <svg viewBox="0 0 24 24" width="20" height="20">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        <div id="passwordMatchMessage" class="strength-text"></div>
                    </div>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                        <line x1="17" y1="11" x2="22" y2="11" />
                    </svg>
                    Create Account
                </button>

                <div class="auth-footer">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Update button icon
        const button = passwordInput.parentElement.querySelector('.toggle-password svg');
        if (type === 'text') {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        } else {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="1" y1="1" x2="23" y2="23"/>';
        }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        const strengthBars = document.querySelectorAll('#passwordStrength .strength-bar');
        const strengthText = document.getElementById('strengthText');

        // Reset bars
        strengthBars.forEach(bar => {
            bar.className = 'strength-bar';
        });

        if (!password) {
            strengthText.textContent = 'Enter a password';
            return;
        }

        let strength = 0;

        // Check length
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Check for mixed case
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;

        // Check for numbers and special characters
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        // Update bars
        for (let i = 0; i < Math.min(strength, 4); i++) {
            if (strength <= 2) {
                strengthBars[i].classList.add('weak');
            } else if (strength <= 3) {
                strengthBars[i].classList.add('medium');
            } else {
                strengthBars[i].classList.add('strong');
            }
        }

        // Update text
        if (strength <= 2) {
            strengthText.textContent = 'Weak password';
            strengthText.style.color = 'var(--danger)';
        } else if (strength <= 3) {
            strengthText.textContent = 'Medium password';
            strengthText.style.color = 'var(--warning)';
        } else {
            strengthText.textContent = 'Strong password';
            strengthText.style.color = 'var(--success)';
        }
    }

    // Validate password match
    function validatePasswordMatch(confirmPassword) {
        const password = document.getElementById('password').value;
        const messageEl = document.getElementById('passwordMatchMessage');

        if (!confirmPassword) {
            messageEl.textContent = '';
            return;
        }

        if (password === confirmPassword) {
            messageEl.textContent = '✓ Passwords match';
            messageEl.style.color = 'var(--success)';
        } else {
            messageEl.textContent = '✗ Passwords do not match';
            messageEl.style.color = 'var(--danger)';
        }
    }

    // Toggle courier fields based on role selection
    function toggleCourierFields(role) {
        const courierFields = document.getElementById('courierFields');
        const fileInputs = document.querySelectorAll('#courierFields input[type="file"]:not(#medical_transport_cert)');

        if (role === 'courier') {
            courierFields.classList.add('visible');
            // Make required file inputs mandatory
            fileInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
            updateDocumentProgress();
        } else {
            courierFields.classList.remove('visible');
            // Remove required attribute
            fileInputs.forEach(input => {
                input.removeAttribute('required');
            });
        }
    }

    // Handle file selection
    function handleFileSelect(input, displayElementId, areaId) {
        const displayElement = document.getElementById(displayElementId);
        const area = document.getElementById(areaId);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                input.value = '';
                displayElement.innerHTML = '';
                area.classList.remove('has-file');
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!validTypes.includes(file.type) && !file.name.match(/\.(jpg|jpeg|png|pdf)$/i)) {
                alert('Please upload a valid file (JPG, PNG, or PDF)');
                input.value = '';
                displayElement.innerHTML = '';
                area.classList.remove('has-file');
                return;
            }

            // Display selected file name with remove option
            displayElement.innerHTML = `
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                ${file.name}
                <button type="button" class="remove-file" onclick="removeFile('${input.id}', '${displayElementId}', '${areaId}')" aria-label="Remove file">
                    <svg viewBox="0 0 24 24" width="14" height="14">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            `;
            area.classList.add('has-file');
        } else {
            displayElement.innerHTML = '';
            area.classList.remove('has-file');
        }

        updateDocumentProgress();
    }

    // Remove selected file
    function removeFile(inputId, displayElementId, areaId) {
        const input = document.getElementById(inputId);
        const displayElement = document.getElementById(displayElementId);
        const area = document.getElementById(areaId);

        input.value = '';
        displayElement.innerHTML = '';
        area.classList.remove('has-file');

        updateDocumentProgress();
    }

    // Update document upload progress
    function updateDocumentProgress() {
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        const fileInputs = [
            'profile_image',
            'government_id',
            'proof_of_residency',
            'drivers_license',
            'medical_transport_cert'
        ];

        let uploaded = 0;
        fileInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input.files && input.files.length > 0) {
                uploaded++;
            }
        });

        const percentage = (uploaded / 5) * 100;
        progressFill.style.width = percentage + '%';
        progressText.textContent = `${uploaded} of 5 documents uploaded`;
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
                    const labels = {
                        'profile_image': 'Profile Picture',
                        'government_id': 'Government ID',
                        'proof_of_residency': 'Proof of Residency',
                        'drivers_license': "Driver's License"
                    };
                    missingFiles.push(labels[fileId]);
                }
            });

            if (missingFiles.length > 0) {
                e.preventDefault();
                alert('Please upload all required documents:\n• ' + missingFiles.join('\n• '));
            }
        }

        // Validate password match
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match. Please try again.');
        }
    });

    // Trigger on page load if old role is courier
    document.addEventListener('DOMContentLoaded', function() {
        const role = document.getElementById('role').value;
        if (role === 'courier') {
            toggleCourierFields('courier');
        }

        // Initialize password strength if password exists
        const password = document.getElementById('password').value;
        if (password) {
            checkPasswordStrength(password);
        }
    });

    // Real-time validation for email
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        const wrapper = this.closest('.input-wrapper');

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            wrapper.classList.add('error');
            if (!wrapper.parentElement.querySelector('.error-message')) {
                const error = document.createElement('span');
                error.className = 'error-message';
                error.innerHTML = `
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Please enter a valid email address
                `;
                wrapper.parentElement.appendChild(error);
            }
        } else {
            wrapper.classList.remove('error');
            const error = wrapper.parentElement.querySelector('.error-message');
            if (error) {
                error.remove();
            }
        }
    });

    // Real-time validation for phone
    document.getElementById('phone').addEventListener('blur', function() {
        const phone = this.value;
        const wrapper = this.closest('.input-wrapper');

        if (phone && !/^[\d\s\+\-\(\)]{10,}$/.test(phone)) {
            wrapper.classList.add('error');
            if (!wrapper.parentElement.querySelector('.error-message')) {
                const error = document.createElement('span');
                error.className = 'error-message';
                error.innerHTML = `
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Please enter a valid phone number
                `;
                wrapper.parentElement.appendChild(error);
            }
        } else {
            wrapper.classList.remove('error');
            const error = wrapper.parentElement.querySelector('.error-message');
            if (error) {
                error.remove();
            }
        }
    });

    // Clear errors on input
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', function() {
            const wrapper = this.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.remove('error');
                const error = wrapper.parentElement.querySelector('.error-message');
                if (error) {
                    error.remove();
                }
            }
        });
    });
</script>
@endsection