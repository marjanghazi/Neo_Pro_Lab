<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoProLab Couriers - @yield('title', 'Medical Courier Services')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    @stack('styles')
    <style>
        :root {
            --navy: #0D1B2A;
            --teal: #00A9A5;
            --white: #FFFFFF;
            --gray: #7A7F85;
            --light-gray: #F5F7FA;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--white);
            color: var(--navy);
            overflow-x: hidden;
        }

        /* Navbar Styles */
        nav {
            animation: slideDown 0.8s ease-out;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.98);
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .nav-link {
            color: var(--navy);
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--teal), #008B85);
            transition: width 0.3s ease;
            border-radius: 3px;
        }

        .nav-link:hover {
            color: var(--teal);
            transform: translateY(-2px);
        }

        .nav-link:hover::before {
            width: 100%;
        }

        .nav-link.active {
            color: var(--teal);
        }

        .nav-link.active::before {
            width: 100%;
            background: linear-gradient(90deg, var(--teal), #008B85);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
            color: var(--white);
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: inline-block;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 169, 165, 0.4);
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(1.02);
        }

        /* Logo Animation */
        .logo-container {
            position: relative;
            animation: fadeInRight 0.8s ease-out;
        }

        .logo-container img {
            transition: transform 0.5s ease;
        }

        .logo-container:hover img {
            transform: scale(1.1) rotate(3deg);
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Mobile Menu Animation */
        #mobile-menu {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-origin: top;
            animation: slideDown 0.4s ease-out;
        }

        #mobile-menu.hidden {
            display: none;
        }

        #mobile-menu:not(.hidden) {
            display: block;
            animation: slideDown 0.4s ease-out;
        }

        .mobile-nav-link {
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s;
            background: linear-gradient(90deg, transparent, transparent);
        }

        .mobile-nav-link:hover {
            background: linear-gradient(90deg, rgba(0, 169, 165, 0.1), transparent);
            transform: translateX(10px);
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #0f1a27 0%, #1a2f47 100%);
            position: relative;
            overflow: hidden;
            animation: fadeIn 1s ease-out;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 10s infinite alternate;
        }

        footer::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -20%;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--teal) 0%, transparent 80%);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 10s infinite alternate-reverse;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(30px, 30px) rotate(10deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .footer-link {
            position: relative;
            display: inline-block;
            transition: all 0.3s;
        }

        .footer-link::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: all 0.3s;
        }

        .footer-link:hover {
            transform: translateX(20px);
            color: var(--teal) !important;
        }

        .footer-link:hover::before {
            opacity: 1;
            left: -5px;
        }

        .footer-section {
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .footer-section:nth-child(1) { animation-delay: 0.2s; }
        .footer-section:nth-child(2) { animation-delay: 0.3s; }
        .footer-section:nth-child(3) { animation-delay: 0.4s; }
        .footer-section:nth-child(4) { animation-delay: 0.5s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .contact-item {
            transition: all 0.3s;
            padding: 8px 12px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid transparent;
        }

        .contact-item:hover {
            background: rgba(0, 169, 165, 0.1);
            border-color: var(--teal);
            transform: translateX(10px);
        }

        .contact-item i {
            transition: all 0.3s;
        }

        .contact-item:hover i {
            transform: rotate(360deg);
            color: var(--teal);
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid transparent;
        }

        .social-icon:hover {
            background: var(--teal);
            transform: translateY(-5px) rotate(360deg);
            border-color: var(--white);
        }

        .copyright {
            position: relative;
            overflow: hidden;
        }

        .copyright::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--teal), transparent);
            animation: scan 3s infinite;
        }

        @keyframes scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Loading Animation for Main Content */
        main {
            animation: contentFadeIn 0.8s ease-out;
        }

        @keyframes contentFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Pulse Animation for Schedule Button */
        .btn-primary.pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
            }
            50% {
                box-shadow: 0 4px 30px rgba(0, 169, 165, 0.6);
            }
            100% {
                box-shadow: 0 4px 15px rgba(0, 169, 165, 0.3);
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .btn-primary {
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .footer-section {
                text-align: center;
            }
            
            .contact-item {
                justify-content: center;
            }
            
            .footer-link:hover {
                transform: translateX(0);
            }
            
            .footer-link::before {
                display: none;
            }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-gray);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--teal), #008B85);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #008B85, var(--navy));
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50" id="navbar">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center justify-center logo-container">
                    <div class="h-32 w-32 md:h-36 md:w-36 flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}"
                            alt="NeoPro Lab Logo"
                            class="h-full w-full object-contain">
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('services') }}" class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}">Services</a>
                    <a href="{{ route('coverage') }}" class="nav-link {{ request()->routeIs('coverage') ? 'active' : '' }}">Coverage</a>
                    <a href="{{ route('pricing') }}" class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                    <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                    <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
                    <a href="{{ route('pickup.create') }}" class="btn-primary pulse">
                        <i class="fas fa-calendar-alt mr-2"></i>Schedule Pickup
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-teal-500 transition-transform hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden py-4 border-t border-gray-100">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}" class="mobile-nav-link nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="mobile-nav-link nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('services') }}" class="mobile-nav-link nav-link {{ request()->routeIs('services') ? 'active' : '' }}">Services</a>
                    <a href="{{ route('coverage') }}" class="mobile-nav-link nav-link {{ request()->routeIs('coverage') ? 'active' : '' }}">Coverage</a>
                    <a href="{{ route('pricing') }}" class="mobile-nav-link nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a>
                    <a href="{{ route('contact') }}" class="mobile-nav-link nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                    <a href="{{ route('login') }}" class="mobile-nav-link nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                    <a href="{{ route('register') }}" class="mobile-nav-link nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>
                    <div class="pt-4">
                        <a href="{{ route('pickup.create') }}" class="btn-primary w-full text-center">
                            <i class="fas fa-calendar-alt mr-2"></i>Schedule Pickup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="footer-section">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                            <div class="h-10 w-10 flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="NeoPro Lab Logo"
                                    class="h-full w-full object-contain filter brightness-0 invert">
                            </div>
                        </div>
                        <span class="font-bold text-xl">NeoProLab Couriers</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Reliable medical courier services for healthcare providers in 878 Washington street  #19 , Attleboro , Ma 02703. HIPAA-compliant and certified medical transport.
                    </p>
                    <div class="flex space-x-3 mt-6">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h3 class="font-bold text-lg mb-4 relative inline-block">
                        Quick Links
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-teal-500"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('about') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                ℹ️ About Us
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('services') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                🚚 Services
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pricing') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                💰 Pricing
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coverage') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                📍 Coverage
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forms') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                📅 Forms
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div class="footer-section">
                    <h3 class="font-bold text-lg mb-4 relative inline-block">
                        Legal
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-teal-500"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('hipaa-notice') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                🏥 HIPAA Notice
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                🔒 Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                📜 Terms & Conditions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('insurance') }}" class="footer-link text-gray-400 hover:text-teal-400 transition">
                                🛡️ Insurance
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="footer-section">
                    <h3 class="font-bold text-lg mb-4 relative inline-block">
                        Contact Us
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-teal-500"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li class="contact-item flex items-center space-x-3 text-gray-400">
                            <i class="fas fa-phone text-teal-400 w-5"></i>
                            <a href="tel:7742970597" class="hover:text-teal-400 transition flex-1">(508) 933-6750</a>
                        </li>
                        <li class="contact-item flex items-center space-x-3 text-gray-400">
                            <i class="fas fa-envelope text-teal-400 w-5"></i>
                            <a href="mailto:info@neoprolab.com" class="hover:text-teal-400 transition flex-1">info@neoprolab.com</a>
                        </li>
                        <li class="contact-item flex items-center space-x-3 text-gray-400">
                            <i class="fas fa-map-marker-alt text-teal-400 w-5"></i>
                            <span class="flex-1">878 Washington street  #19 , Attleboro , Ma 02703</span>
                        </li>
                        <li class="contact-item flex items-center space-x-3 text-gray-400">
                            <i class="fas fa-clock text-teal-400 w-5"></i>
                            <span class="flex-1">Mon-Fri: 8AM-6PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center copyright">
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} NeoProLab Couriers LLC. All rights reserved.</p>
                <p class="text-gray-600 text-xs mt-2">HIPAA Compliant • Certified Medical Transport</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mobile menu toggle with animation
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const icon = this.querySelector('svg');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                menu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            const icon = button.querySelector('svg');

            if (!menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Form submission handler (for contact form)
        if (typeof handleContactSubmit === 'function') {
            document.getElementById('contactForm')?.addEventListener('submit', handleContactSubmit);
        }

        // Add smooth reveal animation for footer sections
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.footer-section').forEach(section => {
            observer.observe(section);
        });

        // Add loading animation for page transitions
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s ease';
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
    @stack('scripts')
</body>



</html>