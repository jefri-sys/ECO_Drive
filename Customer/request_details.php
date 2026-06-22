<?php
session_start();
$user_id =$_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Notification/notification_JS.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO-DRIVE Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }

        .steps-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .progress-container {
            position: relative;
            width: 100%; /* Full width to match status-labels */
            max-width: 480px; /* Optional: cap the width for consistency */
            height: 4px;
            background: #e5e7eb;
            margin: 34px auto 0;
        }

        .progress-bar {
            height: 100%;
            background: #22c55e;
            width: 0;
        }

        .step-circles {
            display: flex;
            justify-content: space-between;
            width: 100%; /* Full width to match status-labels */
            max-width: 500px; /* Match progress-container */
            position: absolute;
            top: 20px;
            z-index: 5;
            left: 50%;
            transform: translateX(-50%);
        }

        .step-circle {
            width: 32px;
            height: 32px;
            background: #e5e7eb;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            color: #9ca3af;
        }

        .status-labels {
            display: flex;
            justify-content: space-between;
            width: 100%; /* Full width */
            max-width: 620px; /* Match progress-container and step-circles */
            margin-top: 20px;
            margin-left: 37px;
        }

        .status-labels span {
            flex: 1; /* Equal spacing */
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        /* Progress Bar Animations */
        .progress-bar.requested,
        .progress-bar.pending { /* Handle both for consistency */
            width: 0; /* No animation, stays at 0% */
        }

        .progress-bar.assigned {
            animation: progressAssigned 1s ease-out forwards;
        }

        .progress-bar.servicing {
            animation: progressServicing 1s ease-out forwards;
        }

        .progress-bar.completed {
            animation: progressCompleted 1s ease-out forwards;
        }

        .progress-bar.cancelled {
            animation: progressCancelled 1s ease-out forwards;
            background: #ef4444;
        }

        @keyframes progressAssigned {
            0% { width: 0%; }
            100% { width: 33.33%; }
        }

        @keyframes progressServicing {
            0% { width: 0%; }
            100% { width: 66.67%; }
        }

        @keyframes progressCompleted {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes progressCancelled {
            0% { width: 0%; }
            100% { width: 0%; }
        }

        /* Step Circle Animations */
        .step-circle.step-1.active.requested,
        .step-circle.step-1.active.pending {
            animation: step1Animation 0.5s ease-out 0.2s forwards;
        }

        .step-circle.step-1.active.assigned,
        .step-circle.step-1.active.servicing,
        .step-circle.step-1.active.completed {
            animation: step1Animation 0.5s ease-out 0.2s forwards;
        }

        .step-circle.step-2.active.assigned,
        .step-circle.step-2.active.servicing,
        .step-circle.step-2.active.completed {
            animation: step2Animation 0.5s ease-out 0.4s forwards;
        }

        .step-circle.step-3.active.servicing,
        .step-circle.step-3.active.completed {
            animation: step3Animation 0.5s ease-out 0.6s forwards;
        }

        .step-circle.step-4.active.completed {
            animation: step4Animation 0.5s ease-out 0.8s forwards;
        }

        .step-circle.active.cancelled {
            animation: stepCancelled 0.5s ease-out forwards;
        }

        @keyframes step1Animation {
            0% { background: #e5e7eb; color: #9ca3af; }
            100% { background: #22c55e; color: white; }
        }

        @keyframes step2Animation {
            0% { background: #e5e7eb; color: #9ca3af; }
            100% { background: #22c55e; color: white; }
        }

        @keyframes step3Animation {
            0% { background: #e5e7eb; color: #9ca3af; }
            100% { background: #eab308; color: white; }
        }

        @keyframes step4Animation {
            0% { background: #e5e7eb; color: #9ca3af; }
            100% { background: #22c55e; color: white; }
        }

        @keyframes stepCancelled {
            0% { background: #e5e7eb; color: #9ca3af; }
            100% { background: #ef4444; color: white; }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00C853',
                        secondary: '#FFB300'
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
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm fixed top-0 left-0 w-full">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                    <nav class="flex items-center space-x-6">
                        <a href="/S6 PROJECT(TEAM 6)/Customer/dashboard.php" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-home-line"></i><span>Dashboard</span>
                        </a>
                        <a href="/S6 PROJECT(TEAM 6)/Customer/dashboard.php#serviceRequest" class="text-gray-600 hover:text-primary flex items-center space-x-1">
                            <i class="ri-service-line"></i><span>Request Service</span>
                        </a>
                        <a href="#" class="text-primary font-medium">
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
                                    <i class="ri-user-line w-5 h-5 mr-3"></i>My Profile
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
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-8">Services you Requested</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ri-service-line text-primary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-500">Total Service Requests</h2>
                        <p id="totalRequests" class="text-2xl font-semibold text-gray-900"></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="ri-time-line text-secondary text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-500">In Progress</h2>
                        <p id="inProgress" class="text-2xl font-semibold text-gray-900"></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="ri-checkbox-circle-line text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2  class="text-sm font-medium text-gray-500">Completed</h2>
                        <p id="completed" class="text-2xl font-semibold text-gray-900"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Requested Services</h2>
            <div id="serviceRequestsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service requests will be loaded here dynamically -->
            </div>
        </div>
    </main>
        
    <div id="serviceModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Service Request Details</h2>
                        <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-500">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>
                    
                    <!-- Updated Progress Tracking -->
                    <div class="mb-8">
                        <div class="steps-wrapper">
                            <div class="step-circles">
                                <div class="step-circle step-1" id="step1"><i class="ri-check-line"></i></div>
                                <div class="step-circle step-2" id="step2"><i class="ri-check-line"></i></div>
                                <div class="step-circle step-3" id="step3"><i class="ri-time-line"></i></div>
                                <div class="step-circle step-4" id="step4"><i class="ri-flag-line"></i></div>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" id="progressBar"></div>
                            </div>
                        </div>
                        <div class="status-labels">
                            <span class="text-green-500">Requested</span>
                            <span class="text-green-500">Assigned</span>
                            <span class="text-yellow-500">Servicing</span>
                            <span class="text-gray-400">Completed</span>
                        </div>
                    </div>

                    <div class="space-y-4" id="modalDetails">
                        <!-- Dynamic content will be loaded here via JavaScript -->
                    </div>

                    <!-- Reschedule Service Modal -->
                    <div id="rescheduleServiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Reschedule Service</h3>
                                <button onclick="hideRescheduleServiceModal()" class="text-gray-500 hover:text-gray-700"><i class="ri-close-line ri-lg"></i></button>
                            </div>
                            <form id="rescheduleServiceForm" class="space-y-4">
                                <input type="hidden" id="rescheduleServiceId" name="serviceId">
                                <div>
                                    <label for="serviceDate" class="block text-sm font-medium text-gray-700">Preferred Date</label>
                                    <input type="date" id="serviceDate" onchange="fetchAvailableSlots()" class="mt-1 block w-full rounded-button border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                                </div>
                                <div>
                                    <label for="selectedTimeSlot" class="block text-sm font-medium text-gray-700 mt-4">Preferred Time Slot</label>
                                    <div id="timeSlots" class="grid grid-cols-3 gap-4 mt-2"></div>
                                    <input type="hidden" id="selectedTimeSlot" name="slotId" required>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="hideRescheduleServiceModal()" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">Reschedule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>

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

        function updateDashboardStats() {
            fetch('/S6 PROJECT(TEAM 6)/Customer/fetchrq.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=servicerq_stats'  // Fixed: Changed variable to string literal
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Server error:', data.error);
                    return;
                }
                
                // Update the DOM elements with proper fallback values
                document.getElementById('totalRequests').textContent = data.totalRequests || '0';
                document.getElementById('inProgress').textContent = data.inProgress || '0';
                document.getElementById('completed').textContent = data.completed || '0';
            })
            .catch(error => {
                console.error('Fetch error:', error);
                // Optionally show user-friendly error message
                document.getElementById('totalRequests').textContent = 'Error';
                document.getElementById('inProgress').textContent = 'Error';
                document.getElementById('completed').textContent = 'Error';
            });
        }

        // Function to load service requests using .map()
        function loadServiceRequests() {
            fetch('/S6 PROJECT(TEAM 6)/Customer/fetchrq.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=service_requests'
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('serviceRequestsContainer');
                
                if (data.error) {
                    container.innerHTML = `<p class="text-gray-500">${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500">No service requests found</p>';
                    return;
                }

                // Use .map() to create HTML for each service request
                const cardsHTML = data.map(request => {
                    const statusClass = getStatusClass(request.service_status.toLowerCase());
                    const statusLabel = request.service_status || 'Pending';
                    const requestDate = new Date(request.request_date).toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });

                    return `
                        <div class="bg-white rounded-lg shadow p-6 cursor-pointer hover:shadow-lg transition-shadow" 
                            onclick="showServiceDetails(${request.service_id})">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Service Request #SR${String(request.service_id).padStart(3, '0')}</h3>
                                    <p class="text-sm text-gray-500">Requested on ${requestDate}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium ${statusClass.bg} rounded-full">${statusLabel}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p>${request.service_names}</p>
                                <p>Vehicle: ${request.model} (${request.vehicle_number})</p>
                            </div>
                        </div>
                    `;
                }).join(''); // Join the array into a single string

                container.innerHTML = cardsHTML;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('serviceRequestsContainer').innerHTML = 
                    '<p class="text-red-500">Error loading service requests</p>';
            });
        }

        // Helper function for status classes (same as before)
        function getStatusClass(status) {
            switch(status) {
                case 'completed':
                    return { bg: 'bg-green-100 text-green-800' };
                case 'servicing':
                    return { bg: 'bg-yellow-100 text-yellow-800' };
                case 'cancelled':
                    return { bg: 'bg-red-100 text-red-800' };
                default: // pending, requested, etc.
                    return { bg: 'bg-blue-100 text-blue-800' };
            }
        }

        function initializeDashboard() {
                    loadServiceRequests();
                    updateDashboardStats();
                }

                document.addEventListener('DOMContentLoaded', initializeDashboard);

                function showServiceDetails(serviceId) {
            fetch('/S6 PROJECT(TEAM 6)/Customer/fetchrq.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=service_request&service_id=${serviceId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                const modal = document.getElementById('serviceModal');
                const progressBar = document.getElementById('progressBar');
                const steps = [
                    document.getElementById('step1'),
                    document.getElementById('step2'),
                    document.getElementById('step3'),
                    document.getElementById('step4')
                ];
                const modalDetails = document.getElementById('modalDetails');

                // Reset classes
                progressBar.className = 'progress-bar';
                steps.forEach(step => step.className = 'step-circle step-' + (steps.indexOf(step) + 1));

                // Normalize status
                let status = data.service_status.toLowerCase();
                if (status === 'pending') status = 'requested';

                // Apply status classes
                progressBar.classList.add(status);
                steps.forEach(step => step.classList.add('active', status));

                // Update modal details with buttons
                modalDetails.innerHTML = `
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Service Plan</h3>
                        <p class="mt-1 text-sm text-gray-900">${data.plan_name}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Service Type</h3>
                        <p class="mt-1 text-sm text-gray-900">${data.service_names}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Vehicle Information</h3>
                        <p class="mt-1 text-sm text-gray-900">${data.model} (${data.vehicle_number})</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Request Date</h3>
                        <p class="mt-1 text-sm text-gray-900">${new Date(data.request_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Service Status</h3>
                        <p class="mt-1 text-sm text-gray-900">${data.service_status}</p>
                    </div>
                    <div class="mt-4">
                        ${status === 'servicing' || status === 'completed' ? 
                            `<button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="window.open('/S6 PROJECT(TEAM 6)/Customer/view_report.php?id=${serviceId}', '_blank')">View Report</button>` : ''}
                        ${status === 'completed' ? 
                            `<button class="bg-green-500 text-white px-4 py-2 rounded ml-2" onclick="window.open('/S6 PROJECT(TEAM 6)/Customer/view_bill.php?id=${serviceId}', '_blank')">View Invoice</button>` : ''}
                        ${status === 'requested' || status === 'cancelled' ? 
                            `<button class="text-red-600 hover:text-red-800 ml-2" onclick="deleteServicerq('${serviceId}')"><i class="ri-delete-bin-line ri-lg"></i></button>` : ''}
                        ${status === 'cancelled' ? 
                            `<button class="bg-primary text-white px-4 py-2 rounded-button ml-2" 
                                onclick="showRescheduleServiceModal('${serviceId}')">
                                Reschedule
                            </button>` : ''}
                    </div>
                `;

                modal.classList.remove('hidden');
            })
            .catch(error => console.error('Error:', error));
        }

        function deleteServicerq(serviceId) {
            displayAlert(2, "", "", serviceId, "", function(result) {
                if (result && result.isConfirmed) {
                    let data = `action=delete_servicerq&serviceId=${encodeURIComponent(serviceId)}`;

                    // Send delete request via AJAX
                    fetch("updaterq.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: data
                    })
                    .then(response => response.json())
                    .then(responseData => {
                        if (responseData.success) {
                            displayAlert(1, "Service request deleted successfully!", null);
                            loadServiceRequests();
                            updateDashboardStats();
                            closeServiceModal();
                        } else {
                            displayAlert(3, responseData.error || "Failed to delete service request", null);
                        }
                    })
                    .catch(error => displayAlert(3, "An unexpected error occurred while deleting the service request", null));
                }
            });
        }


        function closeServiceModal() {
            const modal = document.getElementById('serviceModal');
            const progressBar = document.getElementById('progressBar');
            const steps = [
                document.getElementById('step1'),
                document.getElementById('step2'),
                document.getElementById('step3'),
                document.getElementById('step4')
            ];

            modal.classList.add('hidden');
            progressBar.className = 'progress-bar';
            steps.forEach(step => step.className = 'step-circle step-' + (steps.indexOf(step) + 1));
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

        function showRescheduleServiceModal(serviceId) {
            document.getElementById('rescheduleServiceModal').style.display = 'flex';
            document.getElementById('rescheduleServiceId').value = serviceId;
            document.getElementById('serviceDate').value = '';
            document.getElementById('selectedTimeSlot').value = '';
            document.getElementById('timeSlots').innerHTML = '';
            document.getElementById('serviceDate').min = new Date().toISOString().split('T')[0]; // Set min date to today
        }

        // Hide the reschedule modal
        function hideRescheduleServiceModal() {
            document.getElementById('rescheduleServiceModal').style.display = 'none';
        }

        document.getElementById('rescheduleServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const serviceId = document.getElementById('rescheduleServiceId').value;
            const slotId = document.getElementById('selectedTimeSlot').value;
            const date = document.getElementById('serviceDate').value;

            // Construct URL-encoded data
            const data = `action=reschedule_service&serviceId=${encodeURIComponent(serviceId)}&slotId=${encodeURIComponent(slotId)}&date=${encodeURIComponent(date)}`;

            console.log('Sending data:', data);

            fetch('/S6 PROJECT(TEAM 6)/Customer/updaterq.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json' // Still expect JSON response
                },
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    hideRescheduleServiceModal();
                    displayAlert(1, "Service rescheduled successfully!", null);
                    updateDashboardStats();
                    loadServiceRequests(); // Refresh the service list
                } else {
                    displayAlert(3, result.error || "Failed to reschedule service", null);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayAlert(3, "An unexpected error occurred while rescheduling the service", null);
            });
        });

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
    </script>
</body>
</html>
<?php $conn->close(); ?>