<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

$query = "
SELECT 
    sr.service_id AS id,
    COALESCE(
        sp.plan_name,
        GROUP_CONCAT(s.service_name SEPARATOR ', '),
        'No Services Assigned'
    ) AS type,
    CONCAT(u.fname, ' ', u.lname) AS customer,
    sr.request_date AS date,
    sr.service_status AS status,
    vl.model AS vehicle, -- Get model from vehicles_list
    COALESCE(
        GROUP_CONCAT(s.description SEPARATOR '; '),
        'Plan-based service'
    ) AS description,
    'High' AS priority, -- Placeholder; adjust as needed
    'TBD' AS location   -- Placeholder; adjust as needed
FROM service_rq sr
JOIN user_tbl u ON sr.user_id = u.id
JOIN vehicle v ON sr.vehicle_id = v.id
JOIN vehicles_list vl ON v.vehicle_list_id = vl.id -- Join to get vehicle model
LEFT JOIN service_plans sp ON sr.plan_id = sp.id
LEFT JOIN service_rq_services srs ON sr.service_id = srs.service_rq_id -- Link to service_rq_services
LEFT JOIN services s ON srs.service_id = s.id -- Link to services table
WHERE sr.service_id = ?
GROUP BY sr.service_id, u.fname, u.lname, sr.request_date, sr.service_status, vl.model, sp.plan_name";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

$conn->close();

echo json_encode($request);
?>