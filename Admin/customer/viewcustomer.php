<?php
    include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
    
    if (isset($_GET['cdelete_id'])) {
        $id = $_GET["cdelete_id"];
        $stmt = $conn->prepare("DELETE FROM user_tbl WHERE id=?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? "<script>displayAlert(1, 'Customer Data deleted', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error deleting customer', 'ECO-ADMIN.php');</script>";

        $stmt->close();
    }
?>
<div style="text-align: center;">
    <?php 
    $result = $conn->query("SELECT * FROM user_tbl WHERE role = 'customer'");
    if ($result->num_rows > 0) { ?>
    <h3 style='font-size: 27px; font-weight: bold; margin-bottom: 20px;'>Customers List</h3>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">ID</th>
                <th class="p-2 border border-gray-300">Name</th>
                <th class="p-2 border border-gray-300">Email</th>
                <th class="p-2 border border-gray-300">Phone</th>
                <th class="p-2 border border-gray-300">Address</th>
                <th class="p-2 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td class='p-2 border border-gray-300'>{$row['id']}</td>
                    <td class='p-2 border border-gray-300'>{$row['fname']} {$row['lname']}</td>
                    <td class='p-2 border border-gray-300'>{$row['email']}</td>
                    <td class='p-2 border border-gray-300'>+91 {$row['contact_no']}</td>
                    <td class='p-2 border border-gray-300'>{$row['address']}</td>
                    <td class='p-2 border border-gray-300'>
                        <a href='?cedit_id={$row['id']}' class='text-blue-500 hover:text-blue-700'>Edit</a>
                        <a href='#' onclick='displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"customer\");' class='text-red-500 hover:text-red-700'>Delete</a>                    </td>
                </tr>
                ";
            }
        }else{
            echo "<h1 style='font-size: 27px; font-weight: bold;'>Customer List is Empty!!!</h1>";
        }
        ?>
        </tbody>
    </table>
</div>