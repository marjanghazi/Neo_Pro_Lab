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
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

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
        max-width: 440px;
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

    .form-group {
        margin-bottom: 1.5rem;
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

    .input-wrapper {
        position: relative;
    }

    .input-wrapper input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--gray-light);
        border-radius: 12px;
        font-family: inherit;
        font-size: 1rem;
        transition: var(--transition);
        background-color: var(--white);
        color: var(--gray-dark);
    }

    .input-wrapper input:hover {
        border-color: var(--teal-light);
    }

    .input-wrapper input:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(10, 147, 150, 0.1);
    }

    .input-wrapper input::placeholder {
        color: var(--gray);
        opacity: 0.5;
    }

    .input-wrapper.error input {
        border-color: var(--danger);
        background-color: var(--danger-light);
    }

    .input-wrapper.success input {
        border-color: var(--success);
        background-color: var(--success-light);
    }

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

    .password-strength {
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: var(--gray-light);
        border-radius: 8px;
        font-size: 0.85rem;
    }

    .strength-bar {
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        margin-bottom: 0.5rem;
        overflow: hidden;
    }

    .strength-bar-fill {
        height: 100%;
        width: 0;
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    .strength-text {
        color: var(--gray-dark);
        font-weight: 500;
    }

    .strength-requirements {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .requirement {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        color: var(--gray);
    }

    .requirement.met {
        color: var(--success);
    }

    .requirement svg {
        width: 12px;
        height: 12px;
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
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
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

    .alert-success {
        background: var(--success-light);
        color: #065F46;
        border-left: 4px solid var(--success);
    }

    .alert-error {
        background: var(--danger-light);
        color: #991B1B;
        border-left: 4px solid var(--danger);
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

    @media (max-width: 480px) {
        .auth-header {
            padding: 2rem 1.5rem;
        }

        .auth-body {
            padding: 2rem 1.5rem;
        }

        .auth-title {
            font-size: 1.75rem;
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
            <h1 class="auth-title">Reset Password</h1>
            <p class="auth-subtitle">Create a new password for your account</p>
        </div>

        <div class="auth-body">
            @if ($errors->any())
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        Email Address
                    </label>
                    <div class="input-wrapper {{ $errors->has('email') ? 'error' : '' }}">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ $email ?? old('email') }}"
                            placeholder="Enter your email"
                            required
                            readonly
                            autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        New Password
                    </label>
                    <div class="input-wrapper {{ $errors->has('password') ? 'error' : '' }}">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter new password"
                            required
                            autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')" aria-label="Toggle password visibility">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Strength Meter -->
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-bar-fill" id="strengthBarFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Enter a password</div>
                        <div class="strength-requirements">
                            <div class="requirement" id="reqLength">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12L11 15L16 9" />
                                </svg>
                                Min 8 characters
                            </div>
                            <div class="requirement" id="reqUpper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12L11 15L16 9" />
                                </svg>
                                Uppercase letter
                            </div>
                            <div class="requirement" id="reqLower">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12L11 15L16 9" />
                                </svg>
                                Lowercase letter
                            </div>
                            <div class="requirement" id="reqNumber">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12L11 15L16 9" />
                                </svg>
                                Number
                            </div>
                            <div class="requirement" id="reqSpecial">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M8 12L11 15L16 9" />
                                </svg>
                                Special character
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        Confirm Password
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirm new password"
                            required
                            autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')" aria-label="Toggle password visibility">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn" id="submitBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" />
                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2" />
                    </svg>
                    Reset Password
                </button>

                <div class="auth-footer">
                    <a href="{{ route('login') }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="margin-right: 4px;">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        Back to Login
                    </a>
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
        const button = event.currentTarget.querySelector('svg');
        if (type === 'text') {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        } else {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="1" y1="1" x2="23" y2="23"/>';
        }
    }

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthBarFill = document.getElementById('strengthBarFill');
    const strengthText = document.getElementById('strengthText');
    const reqLength = document.getElementById('reqLength');
    const reqUpper = document.getElementById('reqUpper');
    const reqLower = document.getElementById('reqLower');
    const reqNumber = document.getElementById('reqNumber');
    const reqSpecial = document.getElementById('reqSpecial');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        checkPasswordStrength(password);
    });

    function checkPasswordStrength(password) {
        const requirements = {
            length: password.length >= 8,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        // Update requirement indicators
        reqLength.className = requirements.length ? 'requirement met' : 'requirement';
        reqUpper.className = requirements.upper ? 'requirement met' : 'requirement';
        reqLower.className = requirements.lower ? 'requirement met' : 'requirement';
        reqNumber.className = requirements.number ? 'requirement met' : 'requirement';
        reqSpecial.className = requirements.special ? 'requirement met' : 'requirement';

        // Calculate strength
        const metCount = Object.values(requirements).filter(Boolean).length;
        let strengthPercent = (metCount / 5) * 100;
        let strengthLabel = '';
        let color = '';

        if (password.length === 0) {
            strengthPercent = 0;
            strengthLabel = 'Enter a password';
            color = '#64748B';
        } else if (metCount <= 2) {
            strengthLabel = 'Weak';
            color = '#EF4444';
        } else if (metCount <= 3) {
            strengthLabel = 'Fair';
            color = '#F59E0B';
        } else if (metCount <= 4) {
            strengthLabel = 'Good';
            color = '#3B82F6';
        } else {
            strengthLabel = 'Strong';
            color = '#10B981';
        }

        strengthBarFill.style.width = strengthPercent + '%';
        strengthBarFill.style.backgroundColor = color;
        strengthText.textContent = strengthLabel;
        strengthText.style.color = color;
    }

    // Form validation
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const submitBtn = document.getElementById('submitBtn');
        
        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="spinner" style="animation: spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" stroke-dasharray="32" stroke-dashoffset="32" />
            </svg>
            Resetting...
        `;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            resetButton();
        }
    });

    function resetButton() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" />
                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2" />
            </svg>
            Reset Password
        `;
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);

    // Add spinner animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection