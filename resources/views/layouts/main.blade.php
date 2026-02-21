<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoProLab Couriers - @yield('title', 'Medical Courier Services')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/Icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/Icons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}"> @stack('styles')
    <style>
        :root {
            --navy: #0D1B2A;
            --teal: #00A9A5;
            --white: #FFFFFF;
            --gray: #7A7F85;
            --light-gray: #F5F7FA;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--white);
            color: var(--navy);
        }

        .nav-link {
            color: var(--navy);
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link:hover {
            color: var(--teal);
        }

        .nav-link.active {
            color: var(--teal);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--teal);
            border-radius: 3px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--teal) 0%, #008B85 100%);
            color: var(--white);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 169, 165, 0.3);
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-3"> <!-- Increased space -->
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-teal-700 rounded-lg flex items-center justify-center"> <!-- Increased background size -->
                        <div class="h-32 w-32 md:h-36 md:w-36 flex items-center justify-center"> <!-- Much larger -->
                            <img src="{{ asset('images/logo.png') }}"
                                alt="NeoPro Lab Logo"
                                class="h-full w-full object-contain">
                        </div>
                    </div>
                    <div>
                        <span class="font-bold text-xl text-gray-900">NeoProLab</span>
                        <span class="text-sm text-gray-600 block -mt-1">Couriers</span>
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
                    <a href="{{ route('pickup.create') }}" class="btn-primary">
                        <i class="fas fa-calendar-alt mr-2"></i>Schedule Pickup
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden py-4 border-t">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('services') }}" class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}">Services</a>
                    <a href="{{ route('coverage') }}" class="nav-link {{ request()->routeIs('coverage') ? 'active' : '' }}">Coverage</a>
                    <a href="{{ route('pricing') }}" class="nav-link {{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                    <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}">Login</a>
                    <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">Register</a>

                    <a href="{{ route('pickup.create') }}" class="btn-primary w-full text-center">
                        <i class="fas fa-calendar-alt mr-2"></i>Schedule Pickup
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-teal-500 rounded-lg flex items-center justify-center">
                            <div class="h-16 w-16 md:h-20 md:w-20 flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}"
                                    alt="NeoPro Lab Logo"
                                    class="h-full w-full object-contain">
                            </div>
                        </div>
                        <span class="font-bold text-lg">NeoProLab Couriers</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Reliable medical courier services for healthcare providers in Massachusetts & Rhode Island.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('about') }}" class="text-gray-400 hover:text-teal-400 transition">
                                ℹ️ About Us
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('services') }}" class="text-gray-400 hover:text-teal-400 transition">
                                🚚 Services
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pricing') }}" class="text-gray-400 hover:text-teal-400 transition">
                                💰 Pricing
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coverage') }}" class="text-gray-400 hover:text-teal-400 transition">
                                📍 Coverage
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forms') }}" class="text-gray-400 hover:text-teal-400 transition">
                                📅 Forms
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('hipaa-notice') }}" class="text-gray-400 hover:text-teal-400 transition">
                                🏥 HIPAA Notice
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="text-gray-400 hover:text-teal-400 transition">
                                🔒 Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}" class="text-gray-400 hover:text-teal-400 transition">
                                📜 Terms &amp; Conditions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('insurance') }}" class="text-gray-400 hover:text-teal-400 transition">
                                🛡️ Insurance
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-phone text-teal-400"></i>
                            <a href="tel:7742970597" class="hover:text-teal-400 transition">(774) 297-0597</a>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-envelope text-teal-400"></i>
                            <a href="mailto:info@neoprolab.com" class="hover:text-teal-400 transition">info@neoprolab.com</a>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-map-marker-alt text-teal-400"></i>
                            <span>Massachusetts & Rhode Island</span>
                        </li>
                        <li class="text-gray-400 text-sm mt-4">
                            <i class="fas fa-clock text-teal-400 mr-2"></i>
                            Mon-Fri: 8AM-6PM
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} NeoProLab Couriers LLC. All rights reserved.</p>
                <p class="mt-2">HIPAA Compliant • Certified Medical Transport</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');

            if (!menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Form submission handler (for contact form)
        if (typeof handleContactSubmit === 'function') {
            document.getElementById('contactForm')?.addEventListener('submit', handleContactSubmit);
        }
    </script>
    @stack('scripts')
</body>

</html>