<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$service_id = $_GET['id'] ?? 0;
$type = $_GET['type'] ?? '';

if ($service_id == 0) {
    die('Invalid service ID');
}

// Fetch service request details including username and email
$query = "SELECT sr.service_id, sr.request_date, sr.service_status, sr.notes, sr.service_date, completion_date,
                 sp.plan_name, sp.description, sp.total_cost_inr as price, 
                 v.vehicle_number, vl.model, vl.manufacturer,
                 ut.fname, ut.lname, ut.email
          FROM service_rq sr
          JOIN service_plans sp ON sr.plan_id = sp.id
          JOIN vehicle v ON sr.vehicle_id = v.id
          JOIN vehicles_list vl ON v.vehicle_list_id = vl.id
          JOIN user_tbl ut ON sr.user_id = ut.id
          WHERE sr.service_id = ? AND sr.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $service_id, $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die('Service request not found or unauthorized access');
}

// Fetch services and their prices associated with the service request from service_rq_services
$services_query = "SELECT s.service_name, s.price
                   FROM service_rq_services srs
                   JOIN services s ON srs.service_id = s.id
                   WHERE srs.service_rq_id = ?";
$services_stmt = $conn->prepare($services_query);
$services_stmt->bind_param('i', $service_id);
$services_stmt->execute();
$services_result = $services_stmt->get_result();
$services = [];
$total_services_cost = 0;
while ($row = $services_result->fetch_assoc()) {
    $services[] = [
        'name' => $row['service_name'],
        'price' => $row['price']
    ];
    $total_services_cost += $row['price']; // Sum the service prices
}

// Fetch spare parts used in the service from service_spare_parts
$spare_parts_query = "SELECT i.spare_part_name as name, ssp.quantity_used as quantity, i.price
                      FROM service_spare_parts ssp
                      JOIN inventory i ON ssp.spare_part_id = i.id
                      WHERE ssp.service_id = ?";
$spare_parts_stmt = $conn->prepare($spare_parts_query);
$spare_parts_stmt->bind_param('i', $service_id);
$spare_parts_stmt->execute();
$spare_parts_result = $spare_parts_stmt->get_result();
$spare_parts = [];
$total_spare_parts_cost = 0;
while ($row = $spare_parts_result->fetch_assoc()) {
    $spare_parts[] = [
        'name' => $row['name'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
    $total_spare_parts_cost += $row['quantity'] * $row['price']; // Sum the spare parts costs
}

switch ($type) {
    case 'report':
        // For the report, only service names are needed
        $service_names = array_map(fn($service) => $service['name'], $services);
        echo json_encode([
            'id' => $data['service_id'],
            'vehicle' => $data['manufacturer'] . ' ' . $data['model'],
            'vehicle_no' => $data['vehicle_number'],
            'plan' => $data['plan_name'],
            'description' => $data['description'],
            'status' => $data['service_status'],
            'date' => $data['request_date'],
            'completion_date' => $data['completion_date'],
            'notes' => $data['notes'] ?: 'Service in progress.',
            'services' => $service_names, 
        ]);
        break;

    case 'bill':
        // Calculate the bill: plan cost + total services cost + total spare parts cost
        $subtotal = $data['price'] + $total_services_cost + $total_spare_parts_cost;
        $tax = $subtotal * 0.18; // 18% tax
        $total = $subtotal + $tax;

        echo json_encode([
            'id' => $data['service_id'],
            'vehicle' => $data['manufacturer'] . ' ' . $data['model'] . ' (' . $data['vehicle_number'] . ')',
            'plan' => $data['plan_name'],
            'date' => $data['service_date'] ?: $data['request_date'], // Use service_date if available, else request_date
            'price' => $data['price'], // Base plan cost
            'services' => $services, // Services with prices
            'spare_parts' => $spare_parts, // Spare parts with prices
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'username' => $data['fname'] . ' ' . $data['lname'], // Full name
            'email' => $data['email']
        ]);
        break;

    default:
        die('Invalid type');
}

// Clean up
$services_stmt->close();
$spare_parts_stmt->close();
$stmt->close();
$conn->close();
?>