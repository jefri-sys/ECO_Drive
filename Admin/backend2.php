<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET["action"])) {
    $action = $_POST["action"] ?? $_GET["action"];

    switch ($action) {
        // 🚗 Mechanics
        case "add_services":
            $service_name = $_POST['service_name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $result= $conn->query("select * from services where service_name = '$service_name'");
            if ($result->num_rows > 0) {
                echo "<script>displayAlert(3, 'Service already exists! Try another one.', 'ADMIN-2.php');</script>";
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO services (service_name, description, price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $service_name, $description, $price);

            echo $stmt->execute() ? "<script>displayAlert(1, 'Services added successfully!', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error Inserting Services Data!!', 'ADMIN-2.php');</script>";
            $stmt->close();
            
            break;

        case "update_services":
            $id = $_POST['id'];
            $service_name = $_POST['service_name'];
            $description = $_POST['description'];
            $price = $_POST['price'];

            $stmt = $conn->prepare("UPDATE services SET service_name = ?, description = ?, price = ? WHERE id = ?");
            $stmt->bind_param("ssdi", $service_name, $description, $price, $id);

            echo $stmt->execute() ? "<script>displayAlert(1, 'Services updated successfully!', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error Updating Services Data!!', 'ADMIN-2.php');</script>";
            $stmt->close();

            break;

        // 👤 Customers
        case "add_service_plans":
            $plan_name = $_POST['plan_name'];
            $description = $_POST['description'];
            $total_cost_inr = floatval($_POST['total_cost_inr']);
            $duration_months = intval($_POST['duration_months']);
            $service_ids = $_POST['service_ids'] ?? [];

            $stmt = $conn->prepare("INSERT INTO service_plans (plan_name, description, total_cost_inr, duration_months) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssdi", $plan_name, $description, $total_cost_inr, $duration_months);

            if ($stmt->execute()) {
                $plan_id = $conn->insert_id;
                if (!empty($service_ids)) {
                    $values = array_map(fn($sid) => "($plan_id, $sid)", array_map('intval', $service_ids));
                    $conn->query("INSERT INTO service_plans_services (plan_id, service_id) VALUES " . implode(',', $values));
                }
                echo "<script>displayAlert(1, 'Service Plan added successfully!', 'ADMIN-2.php');</script>"; 

            } else {
                echo "<script>displayAlert(3, 'Error Inserting Service Plan Data!!', 'ADMIN-2.php');</script>";
            }
            $stmt->close();
            break;


        // 🏭 Inventory
        case "add_vehicles":
            $model = $_POST['model'];
            $manufacturer = $_POST['manufacturer'];
            $launch_year = $_POST['launch_year'];
            $notes = $_POST['notes'];
            $result= $conn->query("select * from vehicles_list where model = '$model'");
            if ($result->num_rows > 0) {
                echo "<script>showAlert('Vehicle already exists! Try another one.', 'ADMIN-2.php');</script>";
                exit;
            }

            $stmt = $conn->prepare("INSERT INTO vehicles_list (model, manufacturer, launch_year, notes) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $model, $manufacturer, $launch_year, $notes);

            echo $stmt->execute() ? "<script>displayAlert(1, 'Vehicle added successfully!', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error Inserting Vehicle Data!!', 'ADMIN-2.php');</script>";
            $stmt->close();

            break;

        case "update_vehicles":
            $id = $_POST['id'] ?? 0;
            $model = $_POST['model'];
            $manufacturer = $_POST['manufacturer'];
            $launch_year = $_POST['launch_year'];
            $notes = $_POST['notes'];

            $stmt = $conn->prepare("UPDATE vehicles_list SET model = ?, manufacturer = ?, launch_year = ?, notes = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $model, $manufacturer, $launch_year, $notes, $id);

            echo $stmt->execute() ? "<script>displayAlert(1, 'Vehicle updated successfully!', 'ADMIN-2.php');</script>" : "<script>displayAlert(3, 'Error Updating Vehicle Data!!', 'ADMIN-2.php');</script>";
            $stmt->close();

            break;
    }
}

$conn->close();
?>