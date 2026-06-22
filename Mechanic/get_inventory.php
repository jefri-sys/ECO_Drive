<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$query = "SELECT id, spare_part_name, quantity FROM inventory WHERE quantity > 0";
$result = $conn->query($query);
$parts = [];
while ($row = $result->fetch_assoc()) {
    $parts[] = $row;
}
$conn->close();

echo json_encode($parts);
?>