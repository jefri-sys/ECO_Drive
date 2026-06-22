<?php 
if (isset($_GET['sedit_id'])) { 
    $id = $_GET['sedit_id'];
    $result = $conn->query("SELECT * FROM services WHERE id = $id");
    $service = $result->fetch_assoc();
?>
<div id="updateservice" class="content active">
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Update Service</h3>
        <form method="POST" action="backend2.php" class="space-y-4">
            <input type="hidden" name="action" value="update_services">
            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
            <input type='text' name='service_name' value="<?php echo $service['service_name']; ?>" required class='w-full p-2 border rounded'>
            <textarea name='description' class='w-full p-2 border rounded' required><?php echo $service['description']; ?></textarea>
            <input type='number' step='0.01' name='price' value="<?php echo $service['price']; ?>" required class='w-full p-2 border rounded'>
            <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Service</button>
        </form>
    </div>
</div>
<?php } ?>