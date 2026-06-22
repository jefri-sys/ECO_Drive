<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}
$user_id = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO-DRIVE Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
        .content { display: none; margin: 50px; }
        .content.active { display: block; }

        #image-container::before,
        #image-container::after {
            content: "";
            position: absolute;
            left: 0;
            width: 100%;
            height: 20%;
        }

        #image-container::before {
            top: 0;
            background: linear-gradient(to bottom, white 0%, rgba(255, 255, 255, 0) 100%);
        }

        #image-container::after {
            bottom: 0;
            background: linear-gradient(to top, white 0%, rgba(255, 255, 255, 0) 100%);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#1e3a8a', secondary: '#0ea5e9' },
                    borderRadius: {
                        'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px',
                        'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px'
                    }
                }
            }
        }

        function changeContent(sectionId) {
            document.querySelectorAll('.content').forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }

        function toggleDropdown(id) {
            try { document.getElementById(id)?.classList.toggle('hidden'); } catch (e) { console.error(e); }
        }

        function closeAllDropdowns() {
            try {
                // Only close sidebar dropdowns (exclude serviceSelectionDropdown)
                document.querySelectorAll('[id$="Dropdown"]:not(#serviceSelectionDropdown)').forEach(d => d.classList.add('hidden'));
            } catch (e) { console.error(e); }
        }

        function toggleProfileDropdown() {
            try { document.getElementById('profileDropdown')?.classList.toggle('hidden'); } catch (e) { console.error(e); }
        }

        document.addEventListener('click', (e) => {
            try { if (!e.target.closest('button')) closeAllDropdowns(); } catch (e) { console.error(e); }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section === 'viewserviceplans') changeContent('viewserviceplans');
            else if (section === 'addserviceplans') changeContent('addserviceplans');
            else if (urlParams.get('vedit_id')) changeContent('updatevehicle');
            else if (urlParams.get('sedit_id')) changeContent('updateservice');
            else changeContent('dashboard');
        });

        // Close form dropdown when clicking outside (isolated from sidebar dropdowns)
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#serviceTags') && !e.target.closest('#serviceSelectionDropdown')) {
                    document.getElementById('serviceSelectionDropdown')?.classList.add('hidden');
            }
        });
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white relative">
            <div class="p-4"><h1 class="text-2xl font-bold">ECO-DRIVE</h1></div>
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="/S6 PROJECT(TEAM 6)/Admin/ECO-ADMIN.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded">
                        <div class="w-5 h-5 flex items-center justify-center"><i class="ri-dashboard-line w-5 h-5 mr-3"></i></div><span>Dashboard</span>
                    </a>
                    <div class="space-y-1">
                        <button onclick="toggleDropdown('serviceDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-service-line w-5 h-5 mr-3"></i></div><span>Service</span></div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="serviceDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addservices')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Service</a>
                            <a href="#" onclick="changeContent('viewservices')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Services</a>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <button onclick="toggleDropdown('serviceplanDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-file-list-line w-5 h-5 mr-3"></i></div><span>Service Plans</span></div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="serviceplanDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addserviceplans')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Service Plan</a>
                            <a href="#" onclick="changeContent('viewserviceplans')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Service Plans</a>
                        </div>
                    </div>
                    <div class="mt-2 space-y-1">
                        <button onclick="toggleDropdown('vehicleDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-car-line w-5 h-5 mr-3"></i></div><span>Vehicle</span></div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="vehicleDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addvehicles')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Vehicle Model</a>
                            <a href="#" onclick="changeContent('viewvehicles')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Vehicle Models</a>
                        </div>
                    </div>
                    <div class="mt-2 space-y-1">
                        <button onclick="changeContent('leaverequests')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-calendar-event-line w-5 h-5 mr-3"></i></div><span>Leave Request</span></div>
                        </button>
                    </div>
                </div>
            </nav>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="relative px-4">
                    <button onclick="toggleProfileDropdown()" class="w-full flex items-center space-x-2 p-2 hover:bg-gray-700 rounded">
                        <div  class="w-5 h-5 flex items-center justify-center"><i class="ri-user-line"></i></div><span>Profile</span>
                        <div class="w-5 h-5 flex items-center justify-center ml-auto"><i class="ri-arrow-down-s-line"></i></div>
                    </button>
                    <div id="profileDropdown" class="hidden absolute bottom-full left-0 mb-1 bg-white text-primary rounded shadow-lg w-64">
                        <a href="#" onclick="openProfileModal()" class="flex items-center space-x-2 p-3 hover:bg-gray-100"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-user-line"></i></div><span>View Profile</span></a>
                        <a href="/S6 PROJECT(TEAM 6)/signout.php" class="flex items-center space-x-2 p-3 hover:bg-gray-100 text-red-600"><div class="w-5 h-5 flex items-center justify-center"><i class="ri-logout-box-line"></i></div><span>Sign Out</span></a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
        <div id="dashboard" class="content bg-cover bg-center min-h-[400px] flex items-center justify-center relative rounded-lg" style="background-image: url('https://public.readdy.ai/ai/img_res/a00011f1421f87c8cf1653594db36b29.jpg');">
            <!-- Text above the image -->
            <div class="absolute top-8 text-center text-white">
                <h2 class="text-5xl font-bold p-4 drop-shadow-lg text-gray-500">Services & Service Plans</h2>
                <p class="text-lg text-2xl mt-2  drop-shadow-md text-gray-500">Manage your inventory with ease</p>
            </div>
            <!-- Dashboard Section (additional content can go here) -->
        </div>

            <!-- Add Services Form -->
            <div id="addservices" class="content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add Service</h3>
                    <form method="POST" action="backend2.php" class="space-y-4">
                        <input type="hidden" name="action" value="add_services">
                        <input type="text" name="service_name" placeholder="Service Name" required class="w-full p-2 border rounded">
                        <textarea name="description" placeholder="Description" required class="w-full p-2 border rounded"></textarea>
                        <input type="number" step="0.01" name="price" placeholder="Price (INR)" required class="w-full p-2 border rounded">
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Service</button>
                    </form>
                </div>
            </div>

            <!-- View Services -->
            <div id="viewservices" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/services/viewServices.php'; ?>
            </div>

            <!-- Update Services Form -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/services/updateservices.php'; ?>

            <!-- Add Service Plans Form -->
            <div id="addserviceplans" class="content">
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold mb-8">Add Service Plan</h3>
                    <form action="backend2.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="add_service_plans">
                        <input type="text" name="plan_name" placeholder="Plan Name" required class="w-full p-2 border rounded">
                        <textarea name="description" placeholder="Description" required class="w-full p-2 border rounded"></textarea>
                        <input type="number" step="0.01" name="total_cost_inr" placeholder="Total Cost (INR)" required class="w-full p-2 border rounded" min="0">
                        <input type="number" name="duration_months" placeholder="Duration (Months)" required class="w-full p-2 border rounded" min="1">
                        <label class="block mt-2 text-sm font-medium text-gray-700">Select Services:</label>
                        <div class="relative w-full">
                            <div id="serviceTags" class="w-full p-2 border rounded flex flex-wrap gap-2 items-center cursor-text" onclick="document.getElementById('serviceSelectionDropdown').classList.toggle('hidden')">
                                <span id="placeholder" class="text-gray-400">Select services...</span>
                            </div>
                            <div id="serviceSelectionDropdown" class="absolute z-10 w-full bg-white border rounded mt-1 shadow-lg hidden max-h-60 overflow-y-auto">
                                <?php
                                $services_result = $conn->query("SELECT id, service_name FROM services ORDER BY id");
                                while ($service = $services_result->fetch_assoc()) {
                                    echo "<label class='flex items-center p-2 hover:bg-gray-100 cursor-pointer'>
                                            <input type='checkbox' name='service_ids[]' value='{$service['id']}' class='mr-2 service-checkbox' data-name='" . htmlspecialchars($service['service_name']) . "'>
                                            " . htmlspecialchars($service['service_name']) . "
                                          </label>";
                                }
                                ?>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Plan</button>
                    </form>
                </div>
            </div>

            <!-- View Service Plans -->
            <div id="viewserviceplans" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/service_plans/viewservice_plans.php'; ?>
            </div>

            <!-- Add Vehicles Form -->
            <div id="addvehicles" class="content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add Vehicle Model</h3>
                    <form method="POST" action="backend2.php" class="space-y-4">
                        <input type="hidden" name="action" value="add_vehicles">
                        <input type="text" name="model" placeholder="Model" required class="w-full p-2 border rounded">
                        <input type="text" name="manufacturer" placeholder="Manufacturer" required class="w-full p-2 border rounded">
                        <input type="number" name="launch_year" placeholder="Launch Year" required class="w-full p-2 border rounded">
                        <textarea name="notes" placeholder="Notes (Optional)" class="w-full p-2 border rounded"></textarea>
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Vehicle</button>
                    </form>
                </div>
            </div>

            <!-- View Vehicles -->
            <div id="viewvehicles" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/vehicle/viewvehicle.php'; ?>
            </div>

            <!-- Update Vehicles Form -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/vehicle/updatevehicle.php'; ?>

            <!-- Leave Requests -->
            <div id="leaverequests" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/leaves/viewleaverq.php'; ?>
            </div>
        </main>
    </div>
</body>
</html>