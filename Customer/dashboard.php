<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}
$user_id = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Notification/notification_JS.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/automate.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoDrive - Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { 
                extend: { 
                    colors: { primary: '#10B981', secondary: '#064E3B' }, 
                    borderRadius: { 'button': '8px' } 
                } 
            }
        };
    </script>
    <style>
        .dashboard-grid { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 24px;}
        .service-card:hover { transform: translateY(-4px); transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm fixed top-0 left-0 w-full">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                    <nav class="flex items-center space-x-6">
                        <a href="#" class="text-primary font-medium">
                            <i class="ri-home-line"></i><span>Dashboard</span>
                        </a>
                        <a href="#" onclick="showServiceRequestModal()" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-service-line"></i><span>Request Service</span>
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/request_details.php" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-settings-line mr-2"></i>My Services
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/community.php" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-team-line"></i><span>Community</span>
                        </a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative cursor-pointer" id="notificationIcon" onclick="toggleNotifications()">
                        <i class="ri-notification-3-line text-gray-600 ri-lg"></i>
                        <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-medium">Notifications</h3>
                                <button onclick="markAllRead()" class="text-xs text-primary hover:underline">Mark all as read</button>
                            </div>
                            <div id="notificationList" class="max-h-96 overflow-y-auto"></div>
                            <div class="p-3 border-t border-gray-100 text-center">
                                <button onclick="loadMore()" class="text-sm text-primary hover:underline">Load more</button>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="flex items-center space-x-2 cursor-pointer" onclick="toggleUserMenu()">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="ri-user-line text-gray-600"></i>
                            </div>
                            <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="ri-arrow-down-s-line text-gray-600"></i>
                        </div>
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="p-4 border-b border-gray-100">
                                <p class="text-sm text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            </div>
                            <div onclick="openProfileModal()" class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i  class="ri-user-line w-5 h-5 mr-3"></i>My Profile
                                </a>
                            </div>
                            <div class="py-2 border-t border-gray-100">
                                <a href="/S6 PROJECT(TEAM 6)/signout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <i class="ri-logout-box-line w-5 h-5 mr-3"></i>Sign Out
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-20">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div class="mt-4 grid grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Vehicles</p>
                            <p id="totalVehicles" class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <i class="ri-car-line text-primary ri-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Pending Services</p>
                            <p id="pendingServices" class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="ri-timer-line text-yellow-600 ri-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Completed Services</p>
                            <p id="completedServices" class="text-2xl font-semibold text-gray-900">0</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="ri-checkbox-circle-line text-green-600 ri-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-grid">
            <div class="bg-white rounded-lg shadow-sm p-6 overflow-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">My Vehicles</h2>
                    <button class="bg-primary text-white px-4 py-2 rounded-button flex items-center space-x-2" onclick="showAddVehicleModal()">
                        <i class="ri-add-line"></i><span>Add Vehicle</span>
                    </button>
                </div>
                <div class="space-y-4" id="vehicleList"></div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 overflow-auto">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Available Service Plans</h2>
                <div id="planList" class="grid grid-cols-2 gap-4 mt-2"></div>
            </div>
        </div>
    </main>

    <!-- Service Request Modal -->
    <div id="serviceRequestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Request New Service</h3>
                <button onclick="hideServiceRequestModal()" class="text-gray-500 hover:text-gray-700"><i class="ri-close-line ri-lg"></i></button>
            </div>
            <form id="serviceRequestForm" class="space-y-4">
                <div>
                    <label for="vehicleSelect" class="block text-sm font-medium text-gray-700">Select Vehicle</label>
                    <select id="vehicleSelect" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required></select>
                </div>
                <div class="relative">
                    <label for="selectedServiceIds" class="block text-sm font-medium text-gray-700">Service Type</label>
                    <div id="serviceDropdownToggle" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm p-1 cursor-pointer bg-white flex justify-between items-center" onclick="toggleServiceDropdown()">
                        <span id="selectedServicesText">Select services</span>
                        <i class="ri-arrow-down-s-line text-gray-600"></i>
                    </div>
                    <div id="serviceDropdown" class="absolute z-10 w-full bg-white border rounded mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    <input type="hidden" id="selectedServiceIds" name="serviceIds" required>
                </div>
                <div id="servicePlanSection">
                    <label for="planSelect" class="block text-sm font-medium text-gray-700 mt-4">Service Plan</label>
                    <select id="planSelect" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm p-1 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                        <option value="">Select a plan</option>
                    </select>
                </div>
                <div>
                    <label for="serviceDate" class="block text-sm font-medium text-gray-700 mt-4">Preferred Date</label>
                    <input type="date" id="serviceDate" onchange="fetchAvailableSlots()" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                </div>
                <div>
                    <label for="selectedTimeSlot" class="block text-sm font-medium text-gray-700 mt-4">Preferred Time Slot</label>
                    <div id="timeSlots" class="grid grid-cols-3 gap-4 mt-2"></div>
                    <input type="hidden" id="selectedTimeSlot" name="slotId" required>
                </div>
                <div>
                    <label for="serviceNotes" class="block text-sm font-medium text-gray-700 mt-4">Additional Notes</label>
                    <textarea id="serviceNotes" rows="3" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideServiceRequestModal()" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-checkbox-circle-line text-green-500 ri-2x"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Service Request Submitted!</h3>
            <p class="text-gray-600 mb-6">We'll contact you shortly to confirm your appointment.</p>
            <button onclick="hideSuccessModal()" class="w-full px-4 py-2 bg-primary text-white rounded-button">Done</button>
        </div>
    </div>

    <!-- Add Vehicle Modal -->
    <div id="addVehicleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Add New Vehicle</h3>
                <button onclick="hideAddVehicleModal()" class="text-gray-500 hover:text-gray-700"><i class="ri-close-line ri-lg"></i></button>
            </div>
            <form id="addVehicleForm" class="space-y-4">
                <div>
                    <label for="vehicleModelSelect" class="block text-sm font-medium text-gray-700">Vehicle Model</label>
                    <select id="vehicleModelSelect" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                        <option value="">Select a vehicle model</option>
                    </select>
                </div>
                <div>
                    <label for="vehicleNumber" class="block text-sm font-medium text-gray-700">Vehicle Number</label>
                    <input type="text" id="vehicleNumber" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideAddVehicleModal()" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Add Vehicle</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fetchData(action, callback, extraData = '') {
            fetch('/S6 PROJECT(TEAM 6)/Customer/fetchrq.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=${action}${extraData}`
            })
            .then(response => response.json())
            .then(callback)
            .catch(error => console.error('Fetch error:', error));
        }

        function updateVehicle(action, vehicleId = null, callback = null) {
            let data;
            if (action === 'add_vehicle') {
                const vehicleListId = document.getElementById('vehicleModelSelect').value;
                const vehicleNumber = document.getElementById('vehicleNumber').value;
                const userId = <?php echo $_SESSION['user_id']; ?>;
                data = `action=add_vehicle&vehicleListId=${encodeURIComponent(vehicleListId)}&vehicleNumber=${encodeURIComponent(vehicleNumber)}&userId=${encodeURIComponent(userId)}`;

                fetch('/S6 PROJECT(TEAM 6)/Customer/updaterq.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    if (callback) {
                        callback(result);
                    } else {
                        if (result.success) {
                            displayAlert(1, "Vehicle added successfully!", null);
                            hideAddVehicleModal();
                            renderVehicles();
                            updateDashboardStats();
                        } else {
                            displayAlert(3, result.error || "Failed to add vehicle", null);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    displayAlert(3, "An unexpected error occurred while adding the vehicle", null);
                });
            } else if (action === 'delete_vehicle') {
                displayAlert(2, "", "", vehicleId, "vehicle", (result) => {
                    if (result && result.isConfirmed) {
                        const userId = <?php echo $_SESSION['user_id']; ?>;
                        data = `action=delete_vehicle&vehicleId=${encodeURIComponent(vehicleId)}&userId=${encodeURIComponent(userId)}`;

                        fetch('/S6 PROJECT(TEAM 6)/Customer/updaterq.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (callback) {
                                callback(result);
                            } else {
                                if (result.success) {
                                    displayAlert(1, "Vehicle deleted successfully!", null);
                                    renderVehicles();
                                    updateDashboardStats();
                                } else {
                                    displayAlert(3, result.error || "Failed to delete vehicle", null);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            displayAlert(3, "An unexpected error occurred while deleting the vehicle", null);
                        });
                    }
                });
            } else {
                displayAlert(3, "Invalid action", null);
                return;
            }
        }

        function updateDashboardStats() {
            fetchData('summary_stats', data => {
                document.getElementById('totalVehicles').textContent = data.total_vehicles || '0';
                document.getElementById('pendingServices').textContent = data.pending_services || '0';
                document.getElementById('completedServices').textContent = data.completed_services || '0';
            });
        }

        function renderVehicles() {
            fetchData('vehicles', vehicles => {
                document.getElementById('vehicleList').innerHTML = vehicles.map(v => `
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">${v.model} (${v.vehicle_number})</h3>
                            <p class="text-sm text-gray-600">${v.manufacturer}</p>
                        </div>
                        <button class="text-red-600 hover:text-red-800" onclick="updateVehicle('delete_vehicle', ${v.id})">
                            <i class="ri-delete-bin-line ri-lg"></i>
                        </button>
                    </div>
                `).join('');
            });
            renderformvehicle();
        }

        function renderformvehicle() {
            fetchData('vehicles_list', vehicleslist => {
                document.getElementById('vehicleSelect').innerHTML = '<option value="">Select a vehicle</option>' + 
                    vehicleslist.map(v => `<option value="${v.id}">${v.model} (${v.vehicle_number})</option>`).join('');
                
            })
        }

        function renderServices() {
            fetchData('services', services => {
                document.getElementById('serviceDropdown').innerHTML = services.map(s => `
                    <label class="flex items-center p-2 hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="service_ids[]" value="${s.id}" class="mr-2 service-checkbox" data-name="${s.name}" onchange="updateSelectedServices()">
                        ${s.name}
                    </label>
                `).join('');
            });
        }

        function renderServicePlans() {
            fetchData('service_plans', plans => {
                document.getElementById('planList').innerHTML = plans.map(p => `
                    <div class="border rounded-lg p-8 cursor-pointer hover:border-primary" id="plan-${p.id}">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-medium mb-1">${p.name}</h4>
                            <span class="text-primary">₹${p.price}</span>
                        </div>
                        <p class="text-sm text-gray-600">${p.description}</p>
                    </div>
                `).join('');
                document.getElementById('planSelect').innerHTML = '<option value="">Select a plan</option>' + 
                    plans.map(p => `<option value="${p.id}">${p.name} - ₹${p.price}</option>`).join('');
            });
        }

        function fetchVehicleModels() {
            fetchData('vehicle_models', models => {
                document.getElementById('vehicleModelSelect').innerHTML = '<option value="">Select a vehicle model</option>' + 
                    models.map(m => `<option value="${m.id}">${m.model} - ${m.manufacturer}</option>`).join('');
            });
        }

        function toggleServiceDropdown() {
            document.getElementById('serviceDropdown').classList.toggle('hidden');
        }

        function updateSelectedServices() {
            const checkboxes = document.querySelectorAll('.service-checkbox:checked');
            const selectedIds = Array.from(checkboxes).map(cb => cb.value);
            const selectedNames = Array.from(checkboxes).map(cb => cb.dataset.name);
            document.getElementById('selectedServiceIds').value = JSON.stringify(selectedIds);
            document.getElementById('selectedServicesText').textContent = selectedNames.length ? selectedNames.join(', ') : 'Select services';
            document.getElementById('servicePlanSection').classList.toggle('hidden', !selectedIds.length);
            if (selectedIds.length) renderServicePlans();
        }

        function fetchAvailableSlots() {
            const date = document.getElementById('serviceDate').value;
            if (!date) return;
            fetchData('slots', slots => {
                document.getElementById('timeSlots').innerHTML = slots.length ? slots.map(s => `
                    <button type="button" onclick="selectTimeSlot('${s.id}')" 
                            class="text-left p-3 border rounded-lg hover:border-primary ${s.available ? '' : 'opacity-50 cursor-not-allowed'}" 
                            ${s.available ? '' : 'disabled'} id="slot-${s.id}">
                        <div class="font-medium">${s.time}</div>
                        <div class="text-sm text-gray-500">${s.available ? 'Available' : 'Booked'}</div>
                    </button>
                `).join('') : '<p class="text-sm text-gray-500">No slots available.</p>';
            }, `&date=${encodeURIComponent(date)}`);
        }

        function selectTimeSlot(slotId) {
            console.log("Selected slot ID:", slotId);
            document.getElementById('selectedTimeSlot').value = slotId;
            document.querySelectorAll('#timeSlots button').forEach(btn => btn.classList.remove('border-primary', 'border-2'));
            document.getElementById(`slot-${slotId}`).classList.add('border-primary', 'border-2');
        }

        function showServiceRequestModal() {
            document.getElementById('serviceRequestModal').style.display = 'flex';
            document.getElementById('selectedTimeSlot').value = '';
            document.getElementById('serviceDate').value = '';
            document.getElementById('serviceNotes').value = '';
            document.getElementById('selectedServicesText').textContent = 'Select services';
            document.getElementById('selectedServiceIds').value = '';
            document.getElementById('planSelect').value = '';
            document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('serviceDropdown').classList.add('hidden');
            renderformvehicle();
        }

        function hideServiceRequestModal() {
            document.getElementById('serviceRequestModal').style.display = 'none';
            document.getElementById('serviceDropdown').classList.add('hidden');
        }


        function showSuccessModal() {
            document.getElementById('successModal').style.display = 'flex';
        }

        function hideSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        function showAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'flex';
            document.getElementById('vehicleNumber').value = '';
            fetchVehicleModels();
        }

        function hideAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'none';
        }

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
            document.addEventListener('click', function handler(event) {
                if (!event.target.closest('.cursor-pointer') && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', handler);
                }
            });
        }

        document.getElementById('serviceRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const data = {
                vehicleId: document.getElementById('vehicleSelect').value,
                serviceIds: JSON.parse(document.getElementById('selectedServiceIds').value || '[]'),
                planId: document.getElementById('planSelect').value,
                slotId: document.getElementById('selectedTimeSlot').value,
                date: document.getElementById('serviceDate').value,
                notes: document.getElementById('serviceNotes').value
            };

            console.log("Submitting:", data); // Debug log

            fetch('/S6 PROJECT(TEAM 6)/Customer/updaterq.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log("Response:", result); // Debug log
                if (result.success) {
                    hideServiceRequestModal();
                    showSuccessModal();
                    updateDashboardStats();
                } else {
                    displayAlert(3, result.error || "Failed to submit service request", null);
                    if (result.details) {
                        console.error("Error details:", result.details);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayAlert(3, "An unexpected error occurred while submitting the service request", null);
            });
        });

        document.getElementById('addVehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateVehicle('add_vehicle');
        });

        // Initial renders and dynamic date setting
        window.onload = function() {
            renderVehicles();
            renderformvehicle();
            renderServices();
            renderServicePlans();
            updateDashboardStats();
            document.getElementById('serviceDate').min = new Date().toISOString().split('T')[0];
            
            // Handle hash for service request and reschedule
            if (window.location.hash === '#serviceRequest') {
                showServiceRequestModal();
                history.replaceState({}, document.title, window.location.pathname);
            } 
        };
    </script>
</body>
</html>