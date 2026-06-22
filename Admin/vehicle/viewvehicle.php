<?php
    include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
    if (isset($_GET['vdelete_id'])) {
        $id = $_GET['vdelete_id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM vehicles_list WHERE id = ?");
        $stmt->bind_param("i", $id);

        echo $stmt->execute() ? "<script>displayAlert(1, 'Vehicle Data deleted successfully', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error deleting vehicle Data', 'ADMIN-2.php');</script>";
        $stmt->close();
    }
?>
<div style="text-align: center;">
    <?php 
    $result = $conn->query("SELECT * FROM vehicles_list ORDER BY id DESC");
    if ($result->num_rows > 0) { ?>
        <h3 class="text-lg font-semibold mb-4">Vehicle Models List</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border border-gray-300">ID</th>
                    <th class="p-2 border border-gray-300">model</th>
                    <th class="p-2 border border-gray-300">Manufacturer</th>
                    <th class="p-2 border border-gray-300">Launch Year</th>
                    <th class="p-2 border border-gray-300">Notes</th>
                    <th class="p-2 border border-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td class='p-2 border border-gray-300'>{$row['id']}</td>
                        <td class='p-2 border border-gray-300'>{$row['model']}</td>
                        <td class='p-2 border border-gray-300'>{$row['manufacturer']}</td>
                        <td class='p-2 border border-gray-300'>{$row['launch_year']}</td>
                        <td class='p-2 border border-gray-300'>{$row['notes']}</td>
                        <td class='p-2 border border-gray-300'>
                            <a href='?vedit_id={$row['id']}' class='text-blue-500 hover:text-blue-700 mr-2'>Edit</a>
                            <a href='#' onclick='displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"vehicle\");' class='text-red-500 hover:text-red-700'>Delete</a>                        </td>
                    </tr>";
                }
                $conn->close();
    }else{
        echo "<h3 class='text-lg font-semibold mb-4'>No Vehicle Models Found</h3>";
    }
                ?>
            </tbody>
        </table>
    </div>