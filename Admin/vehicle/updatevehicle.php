<?php
if (isset($_GET['vedit_id'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
    $id = $_GET['vedit_id'];
    $result = $conn->query("SELECT * FROM vehicles_list WHERE id = $id");
    $vehicle = $result->fetch_assoc();
    $result->close();
?>

<div id="updatevehicle" class="content active">
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Update Vehicle Model</h3>
        <form method="POST" action="backend2.php" class="space-y-4">
            <input type="hidden" name="action" value="update_vehicles">
            <input type="hidden" name="id" value="<?php echo $vehicle['id']; ?>">
            <input type="text" name="model" value="<?php echo $vehicle['model']; ?>" required class="w-full p-2 border rounded">
            <input type="text" name="manufacturer" value="<?php echo $vehicle['manufacturer']; ?>" required class="w-full p-2 border rounded">
            <input type="number" name="launch_year" value="<?php echo $vehicle['launch_year']; ?>" required class="w-full p-2 border rounded">
            <textarea name="notes" class="w-full p-2 border rounded"><?php echo $vehicle['notes']; ?></textarea>
            <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Vehicle</button>
        </form>
    </div>
</div>
<?php } ?>