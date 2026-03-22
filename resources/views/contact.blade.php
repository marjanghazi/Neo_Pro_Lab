@extends('layouts.main')

@section('content')
<!-- CONTACT PAGE HTML -->
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
    }

    .hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
        font-weight: 800;
    }

    .hero p {
        font-size: 18px;
        opacity: 0.9;
    }

    .section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 100px 30px;
    }

    .contact-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-top: 60px;
        align-items: start;
    }

    .contact-info-section h2 {
        color: var(--navy);
        font-size: 28px;
        margin-bottom: 30px;
        font-weight: 700;
    }

    .info-box {
        background: var(--light-gray);
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        border-left: 5px solid var(--teal);
    }

    .info-box h3 {
        color: var(--navy);
        margin-bottom: 10px;
        font-size: 18px;
        font-weight: 700;
    }

    .info-box p {
        color: var(--gray);
        font-weight: 500;
    }

    .info-box a {
        color: var(--teal);
        text-decoration: none;
        font-weight: 700;
    }

    .info-box a:hover {
        text-decoration: underline;
    }

    .form-section h2 {
        color: var(--navy);
        font-size: 28px;
        margin-bottom: 30px;
        font-weight: 700;
    }

    .form-section {
        background: var(--light-gray);
        padding: 40px;
        border-radius: 12px;
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
        min-height: 120px;
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

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 36px;
        }
        .section {
            padding: 60px 20px;
        }
        .contact-wrapper {
            grid-template-columns: 1fr;
            gap: 40px;
        }
    }
</style>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Get in Touch</h1>
    <p>We're here to answer your questions and help with your courier needs</p>
</section>

<!-- CONTACT CONTENT SECTION -->
<section class="section">
    <div class="contact-wrapper">
        <!-- LEFT SIDE - CONTACT INFO -->
        <div class="contact-info-section">
            <h2>Contact Information</h2>

            <div class="info-box">
                <h3>📞 Phone</h3>
                <p><a href="tel:7742970597">(774) 297-0597</a></p>
            </div>

            <div class="info-box">
                <h3>📧 Email</h3>
                <p><a href="mailto:info@neoprolab.com">info@neoprolab.com</a></p>
            </div>

            <div class="info-box">
                <h3>🕒 Hours</h3>
                <p><strong>Monday-Friday:</strong> 8:00 AM – 6:00 PM</p>
                <p style="margin-top: 8px;"><strong>Saturday-Sunday:</strong> By appointment</p>
            </div>

            <div class="info-box">
                <h3>📍 Service Area</h3>
                <p>Massachusetts & Rhode Island</p>
                <p style="font-size: 14px; margin-top: 8px;">Attleboro • North Attleboro • Providence • Pawtucket • Plainville • Seekonk</p>
            </div>
        </div>

        <!-- RIGHT SIDE - CONTACT FORM -->
        <div class="form-section">
            <h2>Quick Message</h2>
            <form id="contactForm" onsubmit="handleContactSubmit(event)">
                <div class="form-group">
                    <label for="contactName">Your Name *</label>
                    <input type="text" id="contactName" name="contactName" required>
                </div>

                <div class="form-group">
                    <label for="contactEmail">Email Address *</label>
                    <input type="email" id="contactEmail" name="contactEmail" required>
                </div>

                <div class="form-group">
                    <label for="contactPhone">Phone Number</label>
                    <input type="tel" id="contactPhone" name="contactPhone">
                </div>

                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <select id="subject" name="subject" required>
                        <option value="">Select subject...</option>
                        <option value="pricing">Pricing Question</option>
                        <option value="service">Service Inquiry</option>
                        <option value="partnership">Partnership Opportunity</option>
                        <option value="support">Support/Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" placeholder="Your message here..." required></textarea>
                </div>

                <button type="submit" class="form-submit">Send Message</button>
            </form>
        </div>
    </div>
</section>

<script>
function handleContactSubmit(event) {
    event.preventDefault();
    
    // Show loading state
    Swal.fire({
        title: 'Sending...',
        text: 'Please wait while we send your message.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Get form data
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);
    
    // Send AJAX request
    fetch('{{ route("contact.send") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Message Sent!',
                text: data.message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#00A9A5'
            });
            form.reset(); // Reset the form
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'There was a problem sending your message. Please try again.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#00A9A5'
        });
    });
}
</script>

@endsection