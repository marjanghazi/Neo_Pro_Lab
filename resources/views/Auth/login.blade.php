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
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
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
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
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
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: var(--gray);
        transition: var(--transition);
    }

    .toggle-password:hover {
        color: var(--teal);
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

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--gray);
        font-size: 0.95rem;
        cursor: pointer;
    }

    .remember-me input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--teal);
        cursor: pointer;
        border-radius: 4px;
        transition: var(--transition);
    }

    .forgot-password {
        color: var(--teal);
        font-size: 0.95rem;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        border-bottom: 1px solid transparent;
    }

    .forgot-password:hover {
        border-bottom-color: var(--teal);
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
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(10, 147, 150, 0.4);
    }

    .auth-btn:hover::before {
        left: 100%;
    }

    .auth-btn:active {
        transform: translateY(0);
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

    .alert ul {
        margin: 0;
        padding-left: 1.25rem;
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

    /* Responsive design */
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

        .remember-forgot {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="auth-logo-text">NeoProLab</div>
            </div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to continue to your account</p>
        </div>

        <div class="auth-body">
            @if (session('status'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 12L11 15L16 9"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        Email Address
                    </label>
                    <div class="input-wrapper {{ $errors->has('email') ? 'error' : '' }}">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="Enter your email"
                            required 
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <span class="error-message">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        Password
                    </label>
                    <div class="input-wrapper {{ $errors->has('password') ? 'error' : '' }}">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password visibility">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="auth-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Sign In
                </button>

                <div class="auth-footer">
                    Don't have an account? 
                    <a href="{{ route('register') }}">Create an account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Update button icon
        const button = document.querySelector('.toggle-password svg');
        if (type === 'text') {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        } else {
            button.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line x1="1" y1="1" x2="23" y2="23"/>';
        }
    }

    // Form validation
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        
        if (!email.value.trim()) {
            e.preventDefault();
            showError(email, 'Email is required');
        } else if (!isValidEmail(email.value)) {
            e.preventDefault();
            showError(email, 'Please enter a valid email address');
        }
        
        if (!password.value) {
            e.preventDefault();
            showError(password, 'Password is required');
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
        
        // Remove error after 3 seconds
        setTimeout(() => {
            wrapper.classList.remove('error');
            const errorElement = wrapper.parentElement.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
        }, 3000);
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
</script>
@endsection