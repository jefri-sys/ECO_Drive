<?php
    include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
    if (isset($_GET['idelete_id'])) {
        $id = $_GET["idelete_id"];
        $stmt = $conn->prepare("SELECT image_path FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM inventory WHERE id=?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? "<script>displayAlert(1, 'Item deleted successfully', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error deleting item', 'ECO-ADMIN.php');</script>";


        if ($success && !empty($item['image_path'])) {
            // Convert the web-relative path to an absolute file system path
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $item['image_path'];
            // Check if the file exists and delete it
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    // Log the error instead of dying, so the process continues
                    error_log("Failed to delete file: $filePath");
                }
            }
        }

        $stmt->close();
    }
?>
<div style="text-align: center;">
    <?php
    $result = $conn->query("SELECT * FROM inventory");
    if ($result->num_rows > 0) { ?>
    <h1 style='font-size: 27px; font-weight: bold; margin-bottom: 20px;'>Inventory Item List</h1>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">ID</th>
                <th class="p-2 border border-gray-300">Item Name</th>
                <th class="p-2 border border-gray-300">Quantity</th>
                <th class="p-2 border border-gray-300">Price</th>
                <th class="p-2 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "
            <tr>
                <td class='p-2 border border-gray-300'>{$row['id']}</td>
                <td class='p-2 border border-gray-300'>{$row['spare_part_name']}</td>
                <td class='p-2 border border-gray-300'>{$row['quantity']}</td>
                <td class='p-2 border border-gray-300'>{$row['price']}</td>
                <td class='p-2 border border-gray-300'>
                    <a href='?iedit_id={$row['id']}' class='text-blue-500 hover:text-blue-700'>Edit</a>
                    <a href='#' onclick='displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"inventory\");' class='text-red-500 hover:text-red-700'>Delete</a>                </td>
            </tr>
            ";
        }
    }else{
        echo "<h1 style='font-size: 27px; font-weight: bold;'>Inventory is Empty!!!</h1>";
    }
    ?>
        </tbody>
    </table>
</div>