<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}

$user_id = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Notification/notification_JS.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/automate.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';

$query = "SELECT * FROM mechanic WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Mechanic not found.");
}
$row = $result->fetch_assoc();
$mechanic_id = $row['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Drive - Mechanic Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#64748b'
                    },
                    borderRadius: {
                        'button': '8px'
                    }
                }
            }
        }
    </script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation (unchanged) -->
    <nav class="bg-white shadow-sm fixed top-0 left-0 w-full">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/S6 PROJECT(TEAM 6)/Mechanic/dashboard.php" class="text-primary font-medium">Dashboard</a>
                    <a href="/S6 PROJECT(TEAM 6)/Mechanic/inventory.php" class="text-gray-500 hover:text-primary">Inventory</a>
                    <a href="#" onclick="showleaveRequestModal()" class="text-gray-500 hover:text-primary" id="leaveRequestLink">Leave Request</a>
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

    <main class="max-w-7xl mx-auto px-4 py-8 pt-20">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-gray-900">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <div id="dashboardStats" class="mt-5 grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 flex items-center justify-center bg-blue-100 rounded-full">
                            <i class="ri-tools-line text-primary text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p id="assignedservices" class="text-2xl font-semibold text-gray-900">0</p>
                            <h3 class="text-gray-500">Assigned Services</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 flex items-center justify-center bg-yellow-100 rounded-full">
                            <i class="ri-service-line text-yellow- Converter600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p id="servicingstatus" class="text-2xl font-semibold text-gray-900">0</p>
                            <h3 class="text-gray-500">Servicing</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="w-12 h-12 flex items-center justify-center bg-green-100 rounded-full">
                            <i class="ri-checkbox-circle-line text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p id="completedservices" class="text-2xl font-semibold text-gray-900">0</p>
                            <h3 class="text-gray-500">Completed Services</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Assigned Requests</h2>
                <div class="flex space-x-4">
                    <select id="statusFilter" onchange="applyFilters()" class="bg-gray-50 border border-gray-200 rounded-button px-4 py-2 text-sm">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Servicing">Servicing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="requestGrid">
                <?php
                $query = "
                    SELECT 
                        sr.service_id AS id,
                        COALESCE(sp.plan_name, GROUP_CONCAT(s.service_name SEPARATOR ', '), 'No Services Assigned') AS service_info,
                        CONCAT(u.fname, ' ', u.lname) AS customer,
                        sr.request_date AS date,
                        sr.service_status AS status,
                        vl.model AS vehicle,
                        COALESCE(GROUP_CONCAT(s.description SEPARATOR '; '), 'Plan-based service') AS descriptions,
                        'High' AS priority,
                        'TBD' AS location
                    FROM service_rq sr
                    JOIN user_tbl u ON sr.user_id = u.id
                    JOIN vehicle v ON sr.vehicle_id = v.id
                    JOIN vehicles_list vl ON v.vehicle_list_id = vl.id
                    LEFT JOIN service_plans sp ON sr.plan_id = sp.id
                    LEFT JOIN service_rq_services srs ON sr.service_id = srs.service_rq_id
                    LEFT JOIN services s ON srs.service_id = s.id
                    WHERE sr.assigned_mechanic_id = ? AND sr.request_status NOT IN ('Pending', 'Cancelled')
                    GROUP BY sr.service_id, u.fname, u.lname, sr.request_date, sr.service_status, vl.model, sp.plan_name";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $mechanic_id);
                $stmt->execute();
                $result = $stmt->get_result();

                function getStatusColor($status) {
                    switch (strtolower($status)) {
                        case 'pending': return 'bg-yellow-100 text-yellow-800';
                        case 'servicing': return 'bg-blue-100 text-blue-800';
                        case 'completed': return 'bg-green-100 text-green-800';
                        default: return 'bg-gray-100 text-gray-800';
                    }
                }

                function getPriorityColor($priority) {
                    switch (strtolower($priority)) {
                        case 'high': return 'text-red-600';
                        case 'medium': return 'text-orange-600';
                        case 'low': return 'text-green-600';
                        default: return 'text-gray-600';
                    }
                }

                while ($request = $result->fetch_assoc()) {
                    echo "
                    <div class='bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:shadow-md transition-shadow service-card' 
                         onclick='showRequestDetails(\"{$request['id']}\")' 
                         data-status='" . htmlspecialchars($request['status']) . "' >
                        <div class='flex justify-between items-start mb-4'>
                            <span class='text-sm font-medium text-gray-500'>{$request['id']}</span>
                            <span class='px-3 py-1 rounded-full text-xs font-bold " . getStatusColor($request['status']) . "'>{$request['status']}</span>
                        </div>
                        <h3 class='text-lg font-medium text-gray-900 mb-2'>{$request['service_info']}</h3>
                        <p class='text-gray-600 mb-4'>{$request['customer']}</p>
                        <div class='flex justify-between items-center'>
                            <span class='text-sm text-gray-500'>{$request['date']}</span>
                            <span class='text-sm font-medium " . getPriorityColor($request['priority']) . "'>{$request['priority']} Priority</span>
                        </div>
                    </div>";
                }
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
    </main>

    <!-- Modals (unchanged) -->
    <div class="modal" id="requestModal">
        <div class="modal-content bg-white rounded-lg w-full max-w-2xl mx-auto mt-20 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Request Details</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeModal()">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <div class="modal" id="updateStatusModal">
        <div class="modal-content bg-white rounded-lg w-full max-w-2xl mx-auto mt-20 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Update Service Status</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeUpdateModal()">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="updateStatusForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Status</label>
                    <select name="service_status" class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary" required>
                        <option value="Pending">Pending</option>
                        <option value="Servicing">Servicing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Spare Parts Used</label>
                    <div id="sparePartsContainer" class="space-y-4"></div>
                    <button type="button" onclick="fetchSpareParts()" class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-button hover:bg-gray-200">Add Spare Part</button>
                </div>
                <input type="hidden" name="service_id" id="serviceIdInput">
                <div class="flex justify-end space-x-4">
                    <button type="button" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-button hover:bg-gray-200" onclick="closeUpdateModal()">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-white bg-primary rounded-button hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="leaveRequestModal" class="modal">
        <div class="modal-content bg-white rounded-lg w-full max-w-2xl mx-auto mt-20 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Request Leave</h3>
                <button class="text-gray-400 hover:text-gray-600" onclick="closeLeaveModal()">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <form id="leaveRequestForm" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="startDate" class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary" required min="2025-03-27">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="endDate" class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary" required min="2025-03-27">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Leave</label>
                    <textarea name="reason" class="w-full px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary h-32" placeholder="Please provide detailed reason for your leave request" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary text-white rounded-button hover:bg-blue-700 transition-colors" onclick="closeLeaveModal()">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Filter and Sort Functions
    function applyFilters() {
        const statusFilter = document.getElementById('statusFilter').value;
        const serviceCards = document.querySelectorAll('.service-card');

        serviceCards.forEach(card => {
            const status = card.getAttribute('data-status');
            card.style.display = (!statusFilter || status === statusFilter) ? 'block' : 'none';
        });


    }

    // Navigation Dropdowns
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

    // Fetch and update dashboard stats
    function updateDashboard() {
        fetch('/S6 PROJECT(TEAM 6)/Mechanic/fetch_dashboard_stats.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('assignedservices').textContent = data.a_requests || '0';
                document.getElementById('servicingstatus').textContent = data.s_status || '0';
                document.getElementById('completedservices').textContent = data.c_requests || '0';
            })
            .catch(error => {
                console.error('Error fetching dashboard stats:', error);
                document.getElementById('assignedservices').textContent = 'Error';
                document.getElementById('servicingstatus').textContent = 'Error';
                document.getElementById('completedservices').textContent = 'Error';
            });
    }

    // Leave Request Modal Logic
    function showleaveRequestModal() {
        document.getElementById('leaveRequestModal').style.display = 'block';
    }

    function closeLeaveModal() {
        document.getElementById('leaveRequestModal').style.display = 'none';
    }

    const startDateInput = document.querySelector('input[name="startDate"]');
    const endDateInput = document.querySelector('input[name="endDate"]');
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });

    document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/S6 PROJECT(TEAM 6)/Mechanic/submit_leave_request.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center';
            if (data.success) {
                modal.innerHTML = `
                    <div class="bg-white p-6 rounded-lg max-w-md w-full">
                        <h3 class="text-lg font-medium mb-4">Leave Request Submitted</h3>
                        <p class="text-gray-600 mb-4">Your leave request has been successfully submitted and is pending approval.</p>
                        <button class="w-full px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700" onclick="this.parentElement.parentElement.remove(); closeLeaveModal();">Close</button>
                    </div>
                `;
                this.reset();
            } else {
                modal.innerHTML = `
                    <div class="bg-white p-6 rounded-lg max-w-md w-full">
                        <h3 class="text-lg font-medium mb-4 text-red-600">Submission Failed</h3>
                        <p class="text-gray-600 mb-4">${data.message || 'An error occurred while submitting your request.'}</p>
                        <button class="w-full px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700" onclick="this.parentElement.parentElement.remove()">Close</button>
                    </div>
                `;
            }
            document.body.appendChild(modal);
        })
        .catch(error => {
            console.error('Error:', error);
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center';
            modal.innerHTML = `
                <div class="bg-white p-6 rounded-lg max-w-md w-full">
                    <h3 class="text-lg font-medium mb-4 text-red-600">Submission Failed</h3>
                    <p class="text-gray-600 mb-4">An unexpected error occurred. Please try again.</p>
                    <button class="w-full px-4 py-2 bg-primary text-white rounded-button hover:bg-blue-700" onclick="this.parentElement.parentElement.remove()">Close</button>
                </div>
            `;
            document.body.appendChild(modal);
        });
    });

    // Request Details Modal
    function showRequestDetails(requestId) {
        fetch('/S6 PROJECT(TEAM 6)/Mechanic/get_request_details.php?request_id=' + requestId)
            .then(response => response.json())
            .then(request => {
                const modal = document.getElementById('requestModal');
                const content = document.getElementById('modalContent');
                content.innerHTML = `
                    <div class="space-y-6">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Request ID</p>
                                <p class="font-medium">${request.id}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(request.status)}">${request.status}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Service Type</p>
                                <p class="font-medium">${request.type}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Customer</p>
                                <p class="font-medium">${request.customer}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Vehicle</p>
                                <p class="font-medium">${request.vehicle}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="font-medium">${request.location}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Description</p>
                            <p class="mt-1">${request.description}</p>
                        </div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button class="px-4 py-2 text-gray-600 bg-gray-100 rounded-button hover:bg-gray-200" onclick="closeModal()">Close</button>
                            <button class="px-4 py-2 text-white bg-primary rounded-button hover:bg-blue-700" onclick="showUpdateStatusModal('${request.id}')">Update Status</button>
                        </div>
                    </div>
                `;
                modal.style.display = 'block';
            });
    }

    function closeModal() {
        document.getElementById('requestModal').style.display = 'none';
    }

    // Update Status Modal Logic
    function showUpdateStatusModal(serviceId) {
        const modal = document.getElementById('updateStatusModal');
        document.getElementById('serviceIdInput').value = serviceId;
        document.getElementById('sparePartsContainer').innerHTML = '';
        fetchSpareParts();
        modal.style.display = 'block';
        closeModal();
    }

    function closeUpdateModal() {
        document.getElementById('updateStatusModal').style.display = 'none';
        document.getElementById('sparePartsContainer').innerHTML = '';
    }

    function fetchSpareParts() {
        fetch('/S6 PROJECT(TEAM 6)/Mechanic/get_inventory.php')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(parts => {
                addSparePart(parts);
            })
            .catch(error => {
                console.error('Error fetching spare parts:', error);
                alert('Failed to load spare parts.');
            });
    }

    function addSparePart(parts = null) {
        const container = document.getElementById('sparePartsContainer');
        const div = document.createElement('div');
        div.className = 'flex space-x-4 items-center';
        div.innerHTML = `
            <select name="spare_part_ids[]" class="w-1/2 px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Spare Part</option>
                ${parts ? parts.map(part => `<option value="${part.id}">${part.spare_part_name} (Available: ${part.quantity})</option>`).join('') : ''}
            </select>
            <input type="number" name="quantities[]" min="0" placeholder="Qty" class="w-1/4 px-4 py-2 border border-gray-200 rounded-button focus:outline-none focus:ring-2 focus:ring-primary" required>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">Remove</button>
        `;
        container.appendChild(div);

        const select = div.querySelector('select[name="spare_part_ids[]"]');
        const qtyInput = div.querySelector('input[name="quantities[]"]');
        select.addEventListener('change', function() {
            qtyInput.min = this.value === "" ? "0" : "1";
        });
    }

    document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('/S6 PROJECT(TEAM 6)/Mechanic/update_service_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeUpdateModal();
                displayAlert(1, "Status updated successfully!","/S6 PROJECT(TEAM 6)/Mechanic/dashboard.php");
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    });

    function getStatusColor(status) {
        switch(status.toLowerCase()) {
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'servicing': return 'bg-blue-100 text-blue-800';
            case 'completed': return 'bg-green-100 text-green-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    // Initial load
    window.onload = function() {
        updateDashboard();
        applyFilters(); // Apply initial filter and sort
    };

    if (window.location.hash === '#leaveRequest') {
        showleaveRequestModal();
        history.replaceState({}, document.title, window.location.pathname);
    }
    </script>
</body>
</html>