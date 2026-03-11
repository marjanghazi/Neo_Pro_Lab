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

    .info-message {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: var(--gray-dark);
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--gray-light);
        border-radius: 12px;
        border-left: 4px solid var(--teal);
    }

    .info-message svg {
        width: 20px;
        height: 20px;
        stroke: var(--teal);
        flex-shrink: 0;
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

    .alert-warning {
        background: var(--warning-light);
        color: #92400E;
        border-left: 4px solid var(--warning);
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
            <h1 class="auth-title">Forgot Password?</h1>
            <p class="auth-subtitle">Enter your email to reset your password</p>
        </div>

        <div class="auth-body">
            @if (session('status'))
            <div class="alert alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <path d="M8 12L11 15L16 9" />
                </svg>
                {{ session('status') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <div class="info-message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
                <span>We'll send a password reset link to your email address.</span>
            </div>

            <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                @csrf

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
                            value="{{ old('email') }}"
                            placeholder="Enter your registered email"
                            required
                            autofocus
                            autocomplete="email">
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

                <button type="submit" class="auth-btn" id="submitBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                    </svg>
                    Send Reset Link
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
    // Form validation
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        const submitBtn = document.getElementById('submitBtn');
        
        // Disable button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="spinner" style="animation: spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" stroke-dasharray="32" stroke-dashoffset="32" />
            </svg>
            Sending...
        `;

        if (!email.value.trim()) {
            e.preventDefault();
            showError(email, 'Email is required');
            resetButton();
        } else if (!isValidEmail(email.value)) {
            e.preventDefault();
            showError(email, 'Please enter a valid email address');
            resetButton();
        }
    });

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(input, message) {
        const wrapper = input.closest('.input-wrapper');
        const existingError = wrapper.parentElement.querySelector('.error-message');

        if (!existingError) {
            const error = document.createElement('span');
            error.className = 'error-message';
            error.innerHTML = `
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                ${message}
            `;
            wrapper.parentElement.appendChild(error);
        }

        wrapper.classList.add('error');
    }

    function resetButton() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
            </svg>
            Send Reset Link
        `;
    }

    // Clear errors on input
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            const wrapper = this.closest('.input-wrapper');
            wrapper.classList.remove('error');
            const errorElement = wrapper.parentElement.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
        });
    });

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