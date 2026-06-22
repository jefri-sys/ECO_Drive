<?php
if (isset($_GET['spdelete_id'])) {
    $id = $_GET["spdelete_id"];
    $stmt = $conn->prepare("DELETE FROM service_plans WHERE id=?");
    $stmt->bind_param("i", $id);
    echo $stmt->execute() ? "<script>displayAlert(1, 'Service Plan deleted successfully', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error deleting service plan', 'ADMIN-2.php');</script>";
    exit;
}
?>
<div style="text-align: center;">
    <?php
    $result = $conn->query("SELECT sp.id, sp.plan_name, sp.description, sp.total_cost_inr, sp.duration_months, 
                GROUP_CONCAT(s.service_name SEPARATOR ', ') AS service_names
                FROM service_plans sp
                LEFT JOIN service_plans_services sps ON sp.id = sps.plan_id
                LEFT JOIN services s ON sps.service_id = s.id
                GROUP BY sp.id
                ORDER BY sp.id DESC");
    if ($result->num_rows > 0) { ?>
    <h1 style='font-size: 27px; font-weight: bold; margin-bottom: 20px;'>Service Plans List</h1>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">ID</th>
                <th class="p-2 border border-gray-300">Plan Name</th>
                <th class="p-2 border border-gray-300">Description</th>
                <th class="p-2 border border-gray-300">Total Cost (INR)</th>
                <th class="p-2 border border-gray-300">Duration (Months)</th>
                <th class="p-2 border border-gray-300">Services</th>
                <th class="p-2 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td class='p-2 border border-gray-300'>{$row['id']}</td>
                    <td class='p-2 border border-gray-300'>{$row['plan_name']}</td>
                    <td class='p-2 border border-gray-300'>{$row['description']}</td>
                    <td class='p-2 border border-gray-300'>{$row['total_cost_inr']}</td>
                    <td class='p-2 border border-gray-300'>{$row['duration_months']}</td>
                    <td class='p-2 border border-gray-300'>{$row['service_names']}</td>
                    <td class='p-2 border border-gray-300'>
                        <a href='#' onclick='displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"service_plans\");' class='text-red-500 hover:text-red-700'>Delete</a>                        </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='p-2 border border-gray-300 text-center'>No service plans found.</td></tr>";
        }?>
        </tbody>
    </table>
</div>