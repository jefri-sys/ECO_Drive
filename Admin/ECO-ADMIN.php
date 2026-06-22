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
if(isset($_GET['service'])){
    echo "<script>changeContent('viewservices')<script>";
}
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
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a',
                        secondary: '#0ea5e9'
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
        function changeContent(sectionId) {
            // Hide all sections
            document.querySelectorAll('.content').forEach(section => {
                section.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');
        }

        // Existing dropdown functions
        function toggleDropdown(id) {
            try {
                const dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            } catch (error) {
                console.error('Error toggling dropdown:', error);
            }
        }

        function closeAllDropdowns() {
            try {
                const dropdowns = document.querySelectorAll('[id$="Dropdown"]');
                dropdowns.forEach(dropdown => dropdown.classList.add('hidden'));
            } catch (error) {
                console.error('Error closing dropdowns:', error);
            }
        }

        function toggleProfileDropdown() {
            try {
                const dropdown = document.getElementById('profileDropdown');
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                }
            } catch (error) {
                console.error('Error toggling profile dropdown:', error);
            }
        }

        document.addEventListener('click', function(event) {
            try {
                if (!event.target.closest('button')) {
                    closeAllDropdowns();
                }
            } catch (error) {
                console.error('Error in click event handler:', error);
            }
        });

        function updateDashboard() {
            fetch('/S6 PROJECT(TEAM 6)/Admin/fetch_dashboard_stats.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update the dashboard cards with fetched data
                    document.querySelector('#dashboard .bg-blue-500 p').textContent = data.mechanics || '0';
                    document.querySelector('#dashboard .bg-green-500 p').textContent = data.services || '0';
                    document.querySelector('#dashboard .bg-orange-500 p').textContent = data.finished || '0';
                })
                .catch(error => {
                    console.error('Error fetching dashboard stats:', error);
                    // Optionally display an error message on the UI
                    document.querySelector('#dashboard .bg-blue-500 p').textContent = 'Error';
                    document.querySelector('#dashboard .bg-green-500 p').textContent = 'Error';
                    document.querySelector('#dashboard .bg-orange-500 p').textContent = 'Error';
            });
        }

        // Show dashboard by default when page loads
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!isset($_GET['medit_id']) && !isset($_GET['cedit_id']) && !isset($_GET['iedit_id'])) { ?>
                changeContent('dashboard');
            <?php } ?>

            setInterval(updateDashboard(), 6000);
        });

    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white relative">
            <div class="p-4">
                <h1 class="text-2xl font-bold">ECO-DRIVE</h1>
            </div>
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="#" onclick="changeContent('dashboard')" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-dashboard-line"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>

                    <a href="#" onclick="changeContent('viewservices')" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-tools-line"></i>
                        </div>
                        <span>View Services</span>
                    </a>

                    <a href="/S6 PROJECT(TEAM 6)/Admin/ADMIN-2.php" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded">
                        <div class="w-5 h-5 flex items-center justify-center">
                            <i class="ri-tools-line"></i>
                        </div>
                        <span>Service & Plans</span>
                    </a>

                    <!-- Inventory Dropdown -->
                    <div class="space-y-1">
                        <button onclick="toggleDropdown('inventoryDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="ri-archive-line"></i>
                                </div>
                                <span>Inventory</span>
                            </div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="inventoryDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addinventory')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Item</a>
                            <a href="#" onclick="changeContent('viewinventory')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Items</a>
                        </div>
                    </div>
                    <!-- User Management -->
                    <div class="pt-4">
                        <div class="px-2 text-xs font-semibold text-gray-300 uppercase tracking-wide">User Management</div>
                    </div>
                    <!-- Mechanics Dropdown -->
                    <div class="mt-2 space-y-1">
                        <button onclick="toggleDropdown('mechanicsDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="ri-user-settings-line"></i>
                                </div>
                                <span>Mechanics</span>
                            </div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="mechanicsDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addmechanic')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Mechanic</a>
                            <a href="#" onclick="changeContent('viewmechanic')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Mechanics</a>
                        </div>
                    </div>
                    <!-- Customers Dropdown -->
                    <div class="mt-2 space-y-1">
                        <button onclick="toggleDropdown('customersDropdown')" class="w-full flex items-center justify-between p-2 hover:bg-gray-700 rounded">
                            <div class="flex items-center space-x-2">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <i class="ri-user-line"></i>
                                </div>
                                <span>Customers</span>
                            </div>
                            <i class="ri-arrow-down-s-line"></i>
                        </button>
                        <div id="customersDropdown" class="hidden pl-8 space-y-1">
                            <a href="#" onclick="changeContent('addcustomer')" class="block p-2 hover:bg-gray-700 rounded text-sm">Add Customer</a>
                            <a href="#" onclick="changeContent('viewcustomer')" class="block p-2 hover:bg-gray-700 rounded text-sm">View Customers</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Profile Dropdown -->
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
            <!-- Dashboard Section -->
            <div id="dashboard" class="content">
                <div class="section p-8">
                    <h2 class="text-2xl font-bold mb-8">Welcome to ECO-DRIVE Admin Dashboard</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-500 text-white rounded-lg shadow-lg p-6 transition-transform duration-200 hover:scale-105 hover:shadow-xl">
                            <h3 class="text-lg font-semibold mb-2">Mechanics</h3>
                            <p class="text-3xl font-bold">0</p>
                        </div>
                        <div class="bg-green-500 text-white rounded-lg shadow-lg p-6 transition-transform duration-200 hover:scale-105 hover:shadow-xl">
                            <h3 class="text-lg font-semibold mb-2">Services</h3>
                            <p class="text-3xl font-bold">0</p>
                        </div>
                        <div class="bg-orange-500 text-white rounded-lg shadow-lg p-6 transition-transform duration-200 hover:scale-105 hover:shadow-xl">
                            <h3 class="text-lg font-semibold mb-2">Finished Requests</h3>
                            <p class="text-3xl font-bold">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Mechanic Form -->
            <div id="addmechanic" class="content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add Mechanic</h3>
                    <form method="POST" action="backend.php" class="space-y-4">
                        <input type="hidden" name="action" value="add_mechanic">
                        <input type="text" name="fname" placeholder="First Name" required class="w-full p-2 border rounded">
                        <input type="text" name="lname" placeholder="Last Name" required class="w-full p-2 border rounded">
                        <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded">
                        <input type="text" name="contact" placeholder="Phone" required class="w-full p-2 border rounded">
                        <input type="text" name="add" placeholder="Address" required class="w-full p-2 border rounded">
                        <input type="text" name="specialization" placeholder="Specialization" required class="w-full p-2 border rounded">
                        <input type="number" name="exp" placeholder="Years of Experience" required class="w-full p-2 border rounded">
                        <input type="password" id="mechanicpassword" name="pass" placeholder="password" required class="w-full p-2 border rounded">
                        <input type="password" id="mechaniccpassword" name="cpass" placeholder="confirm password" required class="w-full p-2 border rounded">
                        <p id="mechanicerrorMessage" style="color: red;"></p>
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Mechanic</button>
                    </form>
                </div>
            </div>

            <!-- View Mechanics -->
            <div id="viewmechanic" class="content">
               <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/mechanic/viewmechanic.php';  ?>
            </div>

            <!-- Update Mechanic Form -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/mechanic/updatemechanic.php';  ?>


            <!-- Add Customer Form -->
            <div id="addcustomer" class="content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add Customer</h3>
                    <form method="POST" action="backend.php" class="space-y-4">
                        <input type="hidden" name="action" value="add_customer">
                        <input type="text" name="fname" placeholder="First Name" required class="w-full p-2 border rounded">
                        <input type="text" name="lname" placeholder="Last Name" required class="w-full p-2 border rounded">
                        <input type="email" id="email" name="email" placeholder="Email" required class="w-full p-2 border rounded">
                        <input type="text" name="contact" placeholder="Phone" required class="w-full p-2 border rounded">
                        <input type="text" name="add" placeholder="Address" required class="w-full p-2 border rounded">
                        <input type="password" id="customerpassword" name="pass" placeholder="password" required class="w-full p-2 border rounded">
                        <input type="password" id="customercpassword" name="cpass" placeholder="confirm password" required class="w-full p-2 border rounded">
                        <p id="customererrorMessage" style="color: red;"></p>
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Customer</button>
                    </form>
                </div>
            </div>
            

            <!-- View Customers -->
            <div id="viewcustomer" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/customer/viewcustomer.php';  ?>
            </div>

            <!-- Update Customer Form -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/customer/updatecustomer.php';  ?>

            <!-- script for Checking Password of both addcustomer and addmechanic -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/passwordcheck.php';  ?>

            <!-- Add Inventory Form -->
            <div id="addinventory" class="content">
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-4">Add Item</h3>
                    <form method="POST" action="backend.php" class="space-y-4" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_inventory">
                        <input type="text" name="item_name" placeholder="Item Name" required class="w-full p-2 border rounded">
                        <input type="number" name="quantity" placeholder="Quantity" required class="w-full p-2 border rounded">
                        <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full p-2 border rounded"><br><br>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Image (optional)</label>
                            <input type="file" name="item_image" accept="image/*" class="w-full p-2 border rounded">
                            <p class="text-sm text-gray-500">Max file size: 5MB. Formats: JPG, PNG, GIF</p>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add Item</button>
                    </form>
                </div>
            </div>

            <!-- View Inventory -->
            <div id="viewinventory" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/inventory/viewinventory.php';  ?>
            </div>
            
            <!-- Update Inventory Form -->
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/inventory/updateinventory.php';  ?>
            
            <!-- View Services -->
            <div id="viewservices" class="content">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Admin/service/viewservicerq.php';  ?>
            </div>

            <!-- Service Requests -->
            <div id="servicerequest" class="content">
                <h2 class="text-2xl font-bold mb-8">Service Requests</h2>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2 border border-gray-300">ID</th>
                            <th class="p-2 border border-gray-300">Customer</th>
                            <th class="p-2 border border-gray-300">Service</th>
                            <th class="p-2 border border-gray-300">Status</th>
                            <th class="p-2 border border-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT sr.id, CONCAT(u.fname, ' ', u.lname) AS customer_name, s.service_type, sr.status 
                                                  FROM service_request sr
                                                  JOIN user_tbl u ON sr.user_id = u.id
                                                  JOIN service s ON sr.service_id = s.id
                                                  WHERE sr.status = 'Pending'");
                        while ($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td class='p-2 border border-gray-300'>{$row['id']}</td>
                                <td class='p-2 border border-gray-300'>{$row['customer_name']}</td>
                                <td class='p-2 border border-gray-300'>{$row['service_type']}</td>
                                <td class='p-2 border border-gray-300'>{$row['status']}</td>
                                <td class='p-2 border border-gray-300'>
                                    <a href='backend.php?action=approve_service&id={$row['id']}' class='text-green-500 hover:text-green-700'>Approve</a>
                                    <a href='backend.php?action=reject_service&id={$row['id']}' class='text-red-500 hover:text-red-700'>Reject</a>
                                </td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>


</body>
</html>