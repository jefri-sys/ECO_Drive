<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'vehicles':
        $query = "SELECT v.id, v.vehicle_number, vl.model, vl.manufacturer 
                  FROM vehicle v 
                  JOIN vehicles_list vl ON v.vehicle_list_id = vl.id 
                  WHERE v.user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicles = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($vehicles);
        break;

    case 'vehicles_list':
        $query = "SELECT v.id, v.vehicle_number, vl.model, vl.manufacturer FROM vehicle v JOIN vehicles_list vl ON v.vehicle_list_id = vl.id 
                    WHERE v.user_id = ? AND v.id NOT IN (SELECT sr.vehicle_id FROM service_rq sr WHERE sr.request_status NOT IN ('Completed', 'Cancelled')
                        AND sr.service_status NOT IN ('Completed', 'Cancelled'))";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicleslist = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($vehicleslist);
        break;

    case 'services':
        $query = "SELECT id, service_name AS name FROM services";
        $result = $conn->query($query);
        $services = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($services);
        break;

    case 'service_plans':
        $query = "SELECT id, plan_name AS name, total_cost_inr AS price, description FROM service_plans";
        $result = $conn->query($query);
        $plans = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($plans);
        break;

    case 'slots':
        $date = $_POST['date'] ?? '';
        if (!$date) {
            echo json_encode(['error' => 'No date provided']);
            exit();
        }
        $query = "SELECT id, slot_time, current_bookings, max_capacity 
                  FROM service_slots 
                  WHERE slot_date = ? 
                  ORDER BY slot_time";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $slots = [];
        while ($row = $result->fetch_assoc()) {
            $slots[] = [
                'id' => $row['id'],
                'time' => date('h:i A', strtotime($row['slot_time'])),
                'available' => $row['current_bookings'] < $row['max_capacity']
            ];
        }
        echo json_encode($slots);
        break;

    case 'vehicle_models':
        $query = "SELECT id, model, manufacturer FROM vehicles_list";
        $result = $conn->query($query);
        $models = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($models);
        break;

    case 'service_request':
        $service_id = $_POST['service_id'] ?? '';
        if (!$service_id) {
            echo json_encode(['error' => 'No service_id provided']);
            exit();
        }
        $query = "SELECT sr.service_id, sr.request_date, sp.plan_name, sr.service_status,GROUP_CONCAT(s.service_name SEPARATOR ', ') as service_names, v.vehicle_number, vl.model
                  FROM service_rq sr
                  JOIN service_rq_services srs ON sr.service_id = srs.service_rq_id
                  JOIN service_plans sp ON sr.plan_id = sp.id
                  JOIN services s ON srs.service_id = s.id
                  JOIN vehicle v ON sr.vehicle_id = v.id
                  JOIN vehicles_list vl ON v.vehicle_list_id = vl.id
                  WHERE sr.service_id = ? AND sr.user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $service_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if ($data) {
            // Ensure service_status is not empty
            $data['service_status'] = $data['service_status'] ?: 'Pending';
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'Service request not found or unauthorized']);
        }
        break;

    case 'service_requests':
        $query = "SELECT sr.service_id, sr.request_date, sp.plan_name, sr.service_status,GROUP_CONCAT(s.service_name SEPARATOR ', ') as service_names, v.vehicle_number, vl.model
                  FROM service_rq sr
                  JOIN service_rq_services srs ON sr.service_id = srs.service_rq_id
                  JOIN service_plans sp ON sr.plan_id = sp.id
                  JOIN services s ON srs.service_id = s.id
                  JOIN vehicle v ON sr.vehicle_id = v.id
                  JOIN vehicles_list vl ON v.vehicle_list_id = vl.id
                  WHERE sr.user_id = ?
                  GROUP BY sr.service_id";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($data);
        break;

    case 'summary_stats':
        $total_vehicles_query = "SELECT COUNT(*) as total FROM vehicle WHERE user_id = ?";
        $stmt = $conn->prepare($total_vehicles_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $total_vehicles = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $pending_services_query = "SELECT COUNT(*) as total FROM service_rq WHERE user_id = ? AND service_status IN ('Assigned', 'Servicing')";
        $stmt = $conn->prepare($pending_services_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $pending_services = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $completed_services_query = "SELECT COUNT(*) as total FROM service_rq WHERE user_id = ? AND service_status = 'Completed'";
        $stmt = $conn->prepare($completed_services_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $completed_services = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        echo json_encode([
            'total_vehicles' => $total_vehicles,
            'pending_services' => $pending_services,
            'completed_services' => $completed_services
        ]);
        break;
    
    case 'servicerq_stats':
        $totalrq_query = "SELECT COUNT(*) as total FROM service_rq WHERE user_id = ?";
        $stmt = $conn->prepare($totalrq_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $totalrq = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $pendingrq_query = "SELECT COUNT(*) as total FROM service_rq WHERE user_id = ? AND service_status IN ('Assigned', 'Servicing')";
        $stmt = $conn->prepare($pendingrq_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $pendingrq = $stmt->get_result()->fetch_assoc()['total'] ?? 0;  

        $completedrq_query = "SELECT COUNT(*) as total FROM service_rq WHERE user_id = ? AND service_status = 'Completed'";
        $stmt = $conn->prepare($completedrq_query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $completedrq = $stmt->get_result()->fetch_assoc()['total'] ?? 0;    

        echo json_encode([
            'totalRequests' => $totalrq,
            'inProgress' => $pendingrq,
            'completed' => $completedrq   
        ]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
$conn->close();
?>