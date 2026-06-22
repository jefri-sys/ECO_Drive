<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/automate.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EV Services Management</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#84cc16',
                secondary: '#365314'
            },
            borderRadius: {
                'none': '0px',
                'sm': '4px',
                DEFAULT: '8px',
                'md': '12px',
                'lg': '16px',
                'xl': '20px',
                '2xl': '24px',
                '3xl': '32px',
                'full': '9999px',
                'button': '8px'
            }
        }
    }
}
</script>
<style>
/* Existing styles */
:where([class^="ri-"])::before { content: "\f3c2"; }
.modal { display: none; position: fixed; top: 9%; left: 0; width: 100%; height: 75%; }
.modal.active { display: flex; }
.fade-in-up { opacity: 0; transform: translateY(20px); transition: opacity 0.6s ease-out, transform 0.6s ease-out; }
.fade-in-up.visible { opacity: 1; transform: translateY(0); }
.stagger-item { transition-delay: var(--delay); }
.value-card { transition: all 0.3s ease; }
.value-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); }
.running-text { display: inline-block; white-space: nowrap; overflow: hidden; border-right: 2px solid #84cc16; animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite; }
@keyframes typing { from { width: 0 } to { width: 100% } }
@keyframes blink-caret { from, to { border-color: transparent } 50% { border-color: #84cc16 } }
.text-reveal { overflow: hidden; }
.text-reveal span { display: inline-block; animation: reveal 0.8s cubic-bezier(0.77, 0, 0.175, 1); }
@keyframes reveal { 0% { transform: translateX(-100%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
.typewriter h2, .typewriter h1 { overflow: hidden; white-space: nowrap; margin: 0 auto; letter-spacing: 0.05em; display: inline-block; max-width: fit-content; }
.typewriter-title h1 { border-right: 3px solid #84cc16; animation: typing 3s steps(30, end) 1s, blink-caret 0.75s step-end infinite; }
.typewriter-motto h2 { animation: typing-motto 3.5s steps(40, end); }
@keyframes typing { from { width: 0 } to { width: 100% } }
@keyframes typing-motto { from { width: 0 } to { width: 100% } }
@keyframes blink-caret { from, to { border-color: transparent } 50% { border-color: #84cc16 } }

/* Service plan card styles */
.plan-card { transition: all 0.3s ease; }
.plan-card:hover { transform: scale(1.05); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15); }
.plan-price { font-size: 2.5rem; line-height: 1; font-weight: bold; color: #84cc16; }

/* Dropdown styles */
.location-select { 
    appearance: none; 
    width: 100%; 
    padding: 0.75rem 1rem; 
    border: 1px solid #d1d5db; 
    border-radius: 8px; 
    background-color: #fff; 
    font-size: 1rem; 
    color: #374151; 
    cursor: pointer; 
    background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%2384cc16"%3E%3Cpath stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"%3E%3C/path%3E%3C/svg%3E');
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.25rem;
}
.location-select:focus { outline: none; border-color: #84cc16; box-shadow: 0 0 0 3px rgba(132, 204, 22, 0.2); }
</style>
</head>
<body class="bg-white">
<div id="main">
    <!-- Navbar unchanged -->
    <nav class="fixed top-0 w-full bg-white shadow-md z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-2xl font-['Righteous'] text-primary">EcoDrive</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#hero" class="text-gray-700 hover:text-primary nav-link">Home</a>
                    <a href="#services" class="text-gray-700 hover:text-primary nav-link">Services</a>
                    <a href="#about" class="text-gray-700 hover:text-primary nav-link">About Us</a>
                    <button onclick="openLoginModal()" class="bg-primary text-white px-6 py-2 !rounded-button hover:bg-green-700 whitespace-nowrap">Login</button>
                </div>
                <div class="md:hidden">
                    <button onclick="toggleMenu()" class="text-gray-700">
                        <i class="ri-menu-line text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="mobile-menu hidden md:hidden">
        <div class="px-4 py-2 bg-white shadow-lg">
            <a href="#hero" class="block py-2 text-gray-700 nav-link" onclick="toggleMenu()">Home</a>
            <a href="#services" class="block py-2 text-gray-700 nav-link" onclick="toggleMenu()">Services</a>
            <a href="#about" class="block py-2 text-gray-700 nav-link" onclick="toggleMenu()">About Us</a>
            <button onclick="openLoginModal()" class="w-full bg-primary text-white px-6 py-2 !rounded-button mt-2">Login</button>
        </div>
    </div>

    <main class="pt-16">
        <!-- Hero section unchanged -->
        <section id="hero" class="relative h-[600px] bg-cover bg-center" style="background-image: url('https://public.readdy.ai/ai/img_res/b3bf2a98f7bca730d6e6fea7dbe94f11.jpg')">
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/50 to-transparent"></div>
            <div class="container mx-auto px-6 relative h-full flex items-center">
                <div class="max-w-2xl text-white">
                    <div class="typewriter typewriter-title">
                        <h1 class="text-5xl font-bold mb-6">Transform Your EV Experience</h1>
                    </div>
                    <p class="text-xl mb-8">Leading the charge in comprehensive electric vehicle services. Experience the future of mobility with our cutting-edge solutions.</p>
                    <a href="#services" onclick="toggleMenu()" class="bg-primary text-white px-8 py-3 !rounded-button hover:bg-green-700 whitespace-nowrap inline-block nav-link">Explore Services</a>
                </div>
            </div>
        </section>

        <!-- Services Section with Increased Width -->
        <section id="services" class="py-20" style="background: linear-gradient(to bottom, #f0fdf4, #ecfdf5)">
            <div class="container mx-auto px-6">
                <h2 class="text-4xl font-bold text-center mb-16">Our Service Plans</h2>
                <div class="relative">
                    <div id="plansSlider" class="flex overflow-x-hidden scroll-smooth gap-6">
                        <?php
                        include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
                        $query = "SELECT plan_name, description, total_cost_inr, duration_months FROM service_plans";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $plan_name = htmlspecialchars($row['plan_name']);
                                $description = htmlspecialchars($row['description']);
                                $total_cost = number_format($row['total_cost_inr'], 2);
                                $duration = $row['duration_months'];
                        ?>
                                <!-- Changed w-80 to w-96 -->
                                <div class="plan-card bg-white p-6 rounded-xl shadow-lg flex-shrink-0 w-96 flex flex-col justify-between border border-gray-100 hover:bg-green-50">
                                    <div>
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="ri-car-line text-2xl text-primary"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-3"><?php echo $plan_name; ?></h3>
                                        <p class="text-gray-600 text-sm mb-4"><?php echo $description; ?></p>
                                    </div>
                                    <div>
                                        <div class="plan-price">₹<?php echo $total_cost; ?></div>
                                        <p class="text-gray-500 text-sm mt-2"><strong>Duration:</strong> <?php echo $duration; ?> months</p>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '<p class="text-center text-gray-600">No service plans available at the moment.</p>';
                        }
                        mysqli_close($conn);
                        ?>
                    </div>
                    <button id="prevBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-primary text-white p-3 rounded-full hover:bg-green-700 disabled:opacity-50">
                        <i class="ri-arrow-left-s-line text-2xl"></i>
                    </button>
                    <button id="nextBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-primary text-white p-3 rounded-full hover:bg-green-700 disabled:opacity-50">
                        <i class="ri-arrow-right-s-line text-2xl"></i>
                    </button>
                </div>
            </div>
        </section>

        </section>
        <section id="about" class="py-20">
        <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
        <h2 class="text-4xl font-bold mb-6">About Our Company</h2>
        <p class="text-gray-600 mb-6">Since 2020, we've been at the forefront of the electric vehicle revolution, providing innovative solutions and exceptional service to our customers.</p>
        <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
        <h4 class="text-2xl font-bold text-primary mb-2">5000+</h4>
        <p class="text-gray-600">Satisfied Customers</p>
        </div>
        <div>
        <h4 class="text-2xl font-bold text-primary mb-2">200+</h4>
        <p class="text-gray-600">Charging Stations</p>
        </div>
        <div>
        <h4 class="text-2xl font-bold text-primary mb-2">24/7</h4>
        <p class="text-gray-600">Customer Support</p>
        </div>
        <div>
        <h4 class="text-2xl font-bold text-primary mb-2">98%</h4>
        <p class="text-gray-600">Service Satisfaction</p>
        </div>
        </div>
        </div>
        <div>
        <img src="https://public.readdy.ai/ai/img_res/b98edb654186209723c88b2c37d042b0.jpg" alt="Company Facility" class="rounded-lg shadow-xl">
        </div>
        </div>
        </div>
        </section>

        <!-- Locations Section -->
        <section class="py-20" style="background: linear-gradient(to bottom, #ecfdf5, #f0fdf4)">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <h2 class="text-4xl font-bold mb-8">Our Locations</h2>
                        <select id="locationSelect" class="location-select">
                            <option value="">Select a Location</option>
                        </select>
                        <div class="mt-8 space-y-4" id="locationDetails"></div>
                    </div>
                    <div>
                        <div id="map" class="h-[400px] rounded-lg overflow-hidden"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer-->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div>
                        <span class="text-2xl font-['Righteous'] text-white mb-6 block">EcoDrive</span>
                        <p class="text-gray-400">Leading the future of electric vehicle services with innovation and excellence.</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold mb-4">Services</h4>
                        <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Charging Solutions</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Maintenance</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Battery Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Consulting</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold mb-4">Newsletter</h4>
                        <form class="space-y-4">
                        <input type="email" placeholder="Your email address" class="w-full px-4 py-2 bg-gray-800 border border-gray-700 !rounded-button text-white">
                        <button type="submit" class="bg-primary text-white px-6 py-2 !rounded-button hover:bg-green-700 whitespace-nowrap w-full">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400">© 2025 EV Services. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                <a href="#" class="text-gray-400 hover:text-white">
                <i class="ri-facebook-fill text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                <i class="ri-twitter-fill text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                <i class="ri-linkedin-fill text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white">
                <i class="ri-instagram-fill text-xl"></i>
                </a>
                </div>
                </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Login Modal-->
    <div id="loginModal" class="modal">
        <div class="bg-white w-full max-w-md mx-auto mt-20 rounded-lg shadow-xl p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Login</h3>
                <button onclick="closeLoginModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="loginForm" class="space-y-6" method="POST" action="/S6 PROJECT(TEAM 6)/Login/ECO-login.php">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="lemail" class="w-full px-4 py-2 border border-gray-300 !rounded-button focus:outline-none focus:border-primary" value="<?php echo isset($_POST['lemail']) ? htmlspecialchars($_POST['lemail']) : ''; ?>" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="lpass" class="w-full px-4 py-2 border border-gray-300 !rounded-button focus:outline-none focus:border-primary" required>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" class="h-4 w-4 text-primary border-gray-300 !rounded-button">
                        <label class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                    <a href="/S6 PROJECT(TEAM 6)/Login/ECO-emailform.php" class="text-sm text-primary hover:text-blue-600">Forgot password?</a>
                </div>
                <button type="submit" name="lbtn" class="w-full bg-primary text-white px-4 py-2 !rounded-button hover:bg-green-700">Login</button>
            </form>
            <br>
            <p class="ml-15 text-sm text-gray-600">New to Eco-Drive? <a href="/S6 PROJECT(TEAM 6)/Login/ECO-register.php" class="text-sm text-primary hover:text-blue-600">Register here</a></p>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    function toggleMenu() {
        const menu = document.querySelector('.mobile-menu');
        menu.classList.toggle('hidden');
    }
    
    function openLoginModal() {
        document.getElementById('loginModal').classList.add('active');
        document.getElementById("main").classList.add('blur');
    }
    
    function closeLoginModal() {
        document.getElementById('loginModal').classList.remove('active');
        document.getElementById('main').classList.remove('blur');
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('loginModal');
        if (event.target == modal) {
            closeLoginModal();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Slider functionality with updated width
        const slider = document.getElementById('plansSlider');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const cardWidth = 384; // Updated to w-96 (384px) + gap-6
        let autoScrollInterval;

        function updateButtons() {
            prevBtn.disabled = slider.scrollLeft <= 0;
            nextBtn.disabled = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth;
        }

        function startAutoScroll() {
            autoScrollInterval = setInterval(() => {
                if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth) {
                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    slider.scrollBy({ left: cardWidth, behavior: 'smooth' });
                }
            }, 3000);
        }

        function stopAutoScroll() {
            clearInterval(autoScrollInterval);
        }

        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -cardWidth, behavior: 'smooth' });
            setTimeout(updateButtons, 300);
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: cardWidth, behavior: 'smooth' });
            setTimeout(updateButtons, 300);
        });

        slider.addEventListener('scroll', updateButtons);
        slider.addEventListener('mouseenter', stopAutoScroll);
        slider.addEventListener('mouseleave', startAutoScroll);
        updateButtons();
        startAutoScroll();

        // Navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                targetSection.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Check URL for ?login=open
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('login') === 'open') {
            openLoginModal();
        }

        // Leaflet Map and Location Dropdown
        const map = L.map('map').setView([20.5937, 78.9629], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let markers = {};

        fetch('get_locations.php')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('locationSelect');
                data.forEach(location => {
                    const option = document.createElement('option');
                    option.value = location.id;
                    option.textContent = location.name;
                    select.appendChild(option);

                    const marker = L.marker([location.latitude, location.longitude]).addTo(map)
                        .bindPopup(`<b>${location.name}</b><br>${location.address}`);
                    markers[location.id] = marker;
                });

                select.addEventListener('change', function() {
                    const selectedId = this.value;
                    if (selectedId) {
                        const selectedLocation = data.find(loc => loc.id == selectedId);
                        map.setView([selectedLocation.latitude, selectedLocation.longitude], 10);
                        markers[selectedId].openPopup();

                        const detailsDiv = document.getElementById('locationDetails');
                        detailsDiv.innerHTML = `
                            <div class="flex items-center">
                                <i class="ri-map-pin-line text-xl text-primary mr-4"></i>
                                <p>${selectedLocation.address}</p>
                            </div>
                            <div class="flex items-center mt-4">
                                <i class="ri-phone-line text-xl text-primary mr-4"></i>
                                <p>${selectedLocation.phone}</p>
                            </div>
                            <div class="flex items-center mt-4">
                                <i class="ri-mail-line text-xl text-primary mr-4"></i>
                                <p>${selectedLocation.email}</p>
                            </div>
                        `;
                    } else {
                        map.setView([20.5937, 78.9629], 5);
                        document.getElementById('locationDetails').innerHTML = '';
                    }
                });
            })
            .catch(error => console.error('Error fetching locations:', error));
    });

    // Fade-in observer unchanged
    function handleIntersection(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }
    const observer = new IntersectionObserver(handleIntersection, { root: null, threshold: 0.1 });
    document.querySelectorAll('.fade-in-up').forEach(element => observer.observe(element));
    </script>
</body>
</html>