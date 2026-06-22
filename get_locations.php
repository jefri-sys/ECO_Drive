<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$query = "SELECT id, name, latitude, longitude, address, phone, email FROM locations";
$result = mysqli_query($conn, $query);

$locations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}

echo json_encode($locations);
mysqli_close($conn);
?>