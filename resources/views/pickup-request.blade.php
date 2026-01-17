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

    .hero {
        background: linear-gradient(135deg, var(--navy) 0%, #1a2f47 50%, var(--teal) 100%);
        color: var(--white);
        padding: 80px 30px;
        text-align: center;
        margin-bottom: 60px;
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        font-weight: 800;
    }

    .hero p {
        font-size: 18px;
        opacity: 0.9;
        max-width: 800px;
        margin: 0 auto;
    }

    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 30px;
    }

    .form-section {
        max-width: 700px;
        margin: 0 auto;
        background: var(--light-gray);
        padding: 50px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .form-section h2 {
        color: var(--navy);
        margin-bottom: 10px;
        font-size: 28px;
        font-weight: 700;
    }

    .form-section > p {
        color: var(--gray);
        margin-bottom: 30px;
        font-weight: 500;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 10px;
        color: var(--navy);
        font-weight: 700;
        font-size: 15px;
    }

    input,
    textarea,
    select {
        width: 100%;
        padding: 14px;
        border: 2.5px solid #e0e0e0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 15px;
        transition: all 0.3s;
        background-color: var(--white);
    }

    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 4px rgba(0, 169, 165, 0.1);
    }

    textarea {
        resize: vertical;
        min-height: 140px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .form-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
        color: var(--white);
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
        box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
    }

    .form-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 169, 165, 0.4);
    }

    .form-note {
        color: var(--gray);
        font-size: 13px;
        margin-top: 20px;
        text-align: center;
        font-weight: 500;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4ffd4 0%, #b8e8b8 100%);
        color: #155724;
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        border-left: 5px solid #28a745;
        font-weight: 600;
    }

    .alert-error {
        background: linear-gradient(135deg, #ffd4d4 0%, #ffb8b8 100%);
        color: #721c24;
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        border-left: 5px solid #dc3545;
        font-weight: 600;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 36px;
        }
        
        .hero {
            padding: 60px 20px;
        }
        
        .section {
            padding: 40px 20px;
        }
        
        .form-section {
            padding: 30px 20px;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Schedule a Pickup in Minutes</h1>
    <p>Fast and easy booking for your medical courier needs</p>
</section>

<!-- FORM SECTION -->
<section class="section">
    <div class="form-section">
        <h2>📋 Pickup Request Form</h2>
        <p>Fill out the form below and we'll confirm your pickup within 2 hours.</p>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pickup.store') }}" id="pickupForm">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="facility">Facility Name *</label>
                    <input type="text" id="facility" name="facility" value="{{ old('facility') }}" required>
                    @error('facility')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="pickupAddress">Pickup Address *</label>
                <input type="text" id="pickupAddress" name="pickupAddress" placeholder="Street, City, State, ZIP" value="{{ old('pickupAddress') }}" required>
                @error('pickupAddress')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="dropoffAddress">Drop-off Address *</label>
                <input type="text" id="dropoffAddress" name="dropoffAddress" placeholder="Street, City, State, ZIP" value="{{ old('dropoffAddress') }}" required>
                @error('dropoffAddress')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="specimenType">Specimen/Item Type *</label>
                    <select id="specimenType" name="specimenType" required>
                        <option value="">Select type...</option>
                        <option value="blood" {{ old('specimenType') == 'blood' ? 'selected' : '' }}>Blood Sample</option>
                        <option value="urine" {{ old('specimenType') == 'urine' ? 'selected' : '' }}>Urine Sample</option>
                        <option value="biopsy" {{ old('specimenType') == 'biopsy' ? 'selected' : '' }}>Biopsy Kit</option>
                        <option value="lab" {{ old('specimenType') == 'lab' ? 'selected' : '' }}>Lab Specimen</option>
                        <option value="document" {{ old('specimenType') == 'document' ? 'selected' : '' }}>Medical Document</option>
                        <option value="medication" {{ old('specimenType') == 'medication' ? 'selected' : '' }}>Medication</option>
                        <option value="vaccine" {{ old('specimenType') == 'vaccine' ? 'selected' : '' }}>Vaccine</option>
                        <option value="supply" {{ old('specimenType') == 'supply' ? 'selected' : '' }}>Medical Supply</option>
                        <option value="other" {{ old('specimenType') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('specimenType')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="temperature">Temperature Requirements *</label>
                    <select id="temperature" name="temperature" required>
                        <option value="">Select requirement...</option>
                        <option value="room" {{ old('temperature') == 'room' ? 'selected' : '' }}>Room Temperature</option>
                        <option value="cool" {{ old('temperature') == 'cool' ? 'selected' : '' }}>Cool (2-8°C)</option>
                        <option value="frozen" {{ old('temperature') == 'frozen' ? 'selected' : '' }}>Frozen (-20°C)</option>
                        <option value="other" {{ old('temperature') == 'other' ? 'selected' : '' }}>Other - Please specify</option>
                    </select>
                    @error('temperature')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="pickupTime">Preferred Pickup Time *</label>
                    <select id="pickupTime" name="pickupTime" required>
                        <option value="">Select time...</option>
                        <option value="800-900" {{ old('pickupTime') == '800-900' ? 'selected' : '' }}>8:00 - 9:00 AM</option>
                        <option value="900-1000" {{ old('pickupTime') == '900-1000' ? 'selected' : '' }}>9:00 - 10:00 AM</option>
                        <option value="1000-1100" {{ old('pickupTime') == '1000-1100' ? 'selected' : '' }}>10:00 - 11:00 AM</option>
                        <option value="1100-1200" {{ old('pickupTime') == '1100-1200' ? 'selected' : '' }}>11:00 AM - 12:00 PM</option>
                        <option value="1200-100" {{ old('pickupTime') == '1200-100' ? 'selected' : '' }}>12:00 - 1:00 PM</option>
                        <option value="100-200" {{ old('pickupTime') == '100-200' ? 'selected' : '' }}>1:00 - 2:00 PM</option>
                        <option value="200-300" {{ old('pickupTime') == '200-300' ? 'selected' : '' }}>2:00 - 3:00 PM</option>
                        <option value="300-400" {{ old('pickupTime') == '300-400' ? 'selected' : '' }}>3:00 - 4:00 PM</option>
                        <option value="400-500" {{ old('pickupTime') == '400-500' ? 'selected' : '' }}>4:00 - 5:00 PM</option>
                        <option value="stat" {{ old('pickupTime') == 'stat' ? 'selected' : '' }}>STAT/Urgent (ASAP)</option>
                    </select>
                    @error('pickupTime')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="pickupDate">Preferred Pickup Date *</label>
                    <input type="date" id="pickupDate" name="pickupDate" value="{{ old('pickupDate') }}" required>
                    @error('pickupDate')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description">Specimen / Item Description *</label>
                <textarea id="description" name="description" placeholder="Any special instructions or details..." required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" placeholder="Any other details or special requirements...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="form-submit">✓ Submit Pickup Request</button>
        </form>

        <p class="form-note">Service hours: Monday-Friday 8:00 AM – 6:00 PM | After-hours service available</p>
    </div>
</section>

<script>
    // Set min date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('pickupDate');
        
        if (dateInput) {
            dateInput.min = today;
            if (!dateInput.value) {
                dateInput.value = today;
            }
        }
    });
</script>
@endsection