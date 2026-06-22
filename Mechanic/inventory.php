<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/Notification/notification_JS.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/profile/profile.php';


// Fetch all inventory data (no server-side search filtering)
$query = "SELECT id, spare_part_name AS name, quantity, price, image_path, low_stock_alert 
          FROM inventory";
$result = $conn->query($query);

$inventory_items = [];
while ($row = $result->fetch_assoc()) {
    $row['status'] = ($row['quantity'] <= $row['low_stock_alert']) ? 'Low Stock' : 'In Stock';
    $inventory_items[] = $row;
}

// Calculate total items and storage capacity
$total_items = count($inventory_items);
$storage_capacity = min(100, ($total_items / 200) * 100); // Assuming 200 is max capacity
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO-DRIVE Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :where([class^="ri-"])::before { content: "\f3c2"; }
    </style>
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
</head>
<body class="bg-gray-50 min-h-screen">
    <main class="max-w-7xl mx-auto px-4 py-8 pt-20">
        <!-- Navigation (unchanged) -->
        <nav class="bg-white shadow-sm fixed top-0 left-0 w-full z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-primary tracking-wider">ECO-DRIVE</span>
                    </div>
                    <div class="flex items-center space-x-8">
                        <a href="/S6 PROJECT(TEAM 6)/Mechanic/dashboard.php" class="text-gray-500 hover:text-primary">Dashboard</a>
                        <a href="/S6 PROJECT(TEAM 6)/Mechanic/inventory.php" class="text-primary font-medium">Inventory</a>
                        <a href="/S6 PROJECT(TEAM 6)/Mechanic/dashboard.php#leaveRequest" class="text-gray-500 hover:text-primary" id="leaveRequestLink">Leave Request</a>
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

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Items</p>
                        <h2 class="text-4xl font-bold text-gray-900 mt-1"><?php echo $total_items; ?></h2>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                        <i class="ri-archive-line text-primary ri-2x"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm">Storage Capacity</p>
                        <h2 class="text-4xl font-bold text-gray-900 mt-1"><?php echo round($storage_capacity); ?>%</h2>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                        <i class="ri-database-2-line text-primary ri-2x"></i>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary h-2 rounded-full" style="width: <?php echo $storage_capacity; ?>%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="searchInput" placeholder="Search inventory items..." onkeyup="searchItems()" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg flex items-center gap-2 hover:bg-gray-200">
                    <i class="ri-filter-3-line"></i>
                    Filter
                </button>
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg flex items-center gap-2 hover:bg-gray-200">
                    <i class="ri-sort-desc"></i>
                    Sort
                </button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6" id="inventoryGrid">
            <?php foreach ($inventory_items as $item): ?>
                <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow inventory-card" data-name="<?php echo htmlspecialchars(strtolower($item['name'])); ?>">
                    <div class="aspect-video rounded-lg bg-gray-100 mb-4 overflow-hidden">
                        <?php if ($item['image_path']): ?>
                            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>">
                        <?php else: ?>
                            <img src="https://public.readdy.ai/ai/img_res/1ac11fe3c73092502eede8be0ae44f6e.jpg" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <span class="px-2 py-1 <?php echo $item['status'] === 'Low Stock' ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700'; ?> text-xs rounded-full">
                            <?php echo $item['status']; ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-2">Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900">Quantity: <?php echo $item['quantity']; ?></span>
                        <span class="text-xs px-2 py-1 bg-blue-50 text-primary rounded-full">Spare Part</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        function searchItems() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const inventoryCards = document.querySelectorAll('.inventory-card');

            inventoryCards.forEach(card => {
                const name = card.getAttribute('data-name');
                card.style.display = name.includes(input) ? 'block' : 'none';
            });
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

        // Apply search filter on page load if there's a value
        document.addEventListener('DOMContentLoaded', () => {
            searchItems();
        });
    </script>
</body>
</html>