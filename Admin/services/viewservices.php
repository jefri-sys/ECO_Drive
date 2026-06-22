<?php 
    include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
    if (isset($_GET['sdelete_id'])) {
        $id = $_GET['sdelete_id'];
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? "<script>displayAlert(1, 'Service deleted successfully', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error deleting service', 'ADMIN-2.php');</script>";
        
        $stmt->close();
    }
?>
<div style="text-align: center;">
        <h3 class="text-lg font-semibold mb-4">Services List</h3>
        <?php 
        $result = $conn->query("SELECT * FROM services ORDER BY id DESC");
        if ($result->num_rows > 0) { ?>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border border-gray-300">ID</th>
                    <th class="p-2 border border-gray-300">Service Name</th>
                    <th class="p-2 border border-gray-300">Description</th>
                    <th class="p-2 border border-gray-300">Price (INR)</th>
                    <th class="p-2 border border-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td class='p-2 border border-gray-300'>{$row['id']}</td>
                        <td class='p-2 border border-gray-300'>{$row['service_name']}</td>
                        <td class='p-2 border border-gray-300'>{$row['description']}</td>
                        <td class='p-2 border border-gray-300'>{$row['price']}</td>
                        <td class='p-2 border border-gray-300'>
                            <a href='?sedit_id={$row['id']}' class='text-blue-500 hover:text-blue-700 mr-2'>Edit</a>
                            <a href='#' onclick='displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"services\");' class='text-red-500 hover:text-red-700'>Delete</a>                        </td>
                    </tr>";
                }
            } else {
                echo "<h1 style='font-size: 27px; font-weight: bold;'>Services List is Empty!!!</h1>";
            }?>
            </tbody>
        </table>
    </div>