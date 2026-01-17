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
        max-width: 480px;
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

    input, select {
        width: 100%;
        padding: 14px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 15px;
        transition: all 0.3s;
        background-color: var(--white);
    }

    input:focus, select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(0, 169, 165, 0.1);
    }

    select {
        cursor: pointer;
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

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">I am a *</label>
                    <select id="role" name="role" required>
                        <option value="">Select role...</option>
                        <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Healthcare Facility Staff</option>
                        <option value="courier" {{ old('role') == 'courier' ? 'selected' : '' }}>Courier/Driver</option>
                    </select>
                    @error('role')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Create Account</button>

                <div class="auth-footer">
                    Already have an account? 
                    <a href="{{ route('login') }}">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection