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
        max-width: 420px;
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

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: var(--navy);
        font-weight: 600;
        font-size: 14px;
    }

    input {
        width: 100%;
        padding: 14px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 15px;
        transition: all 0.3s;
        background-color: var(--white);
    }

    input:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(0, 169, 165, 0.1);
    }

    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .remember-me {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--gray);
        font-size: 14px;
    }

    .forgot-password {
        color: var(--teal);
        font-size: 14px;
        text-decoration: none;
        font-weight: 600;
    }

    .forgot-password:hover {
        text-decoration: underline;
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

    .alert-success {
        background: linear-gradient(135deg, #d4ffd4 0%, #b8e8b8 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-error {
        background: linear-gradient(135deg, #ffd4d4 0%, #ffb8b8 100%);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="auth-logo-icon">N</div>
                <div class="auth-logo-text">NeoProLab</div>
            </div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account</p>
        </div>

        <div class="auth-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit" class="auth-btn">Sign In</button>

                <div class="auth-footer">
                    Don't have an account? 
                    <a href="{{ route('register') }}">Register here</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection