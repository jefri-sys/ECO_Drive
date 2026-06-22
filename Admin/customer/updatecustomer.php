<?php 
if (isset($_GET['cedit_id'])) {
    $id = $_GET['cedit_id'];
    $result = $conn->query("SELECT * FROM user_tbl WHERE id = $id AND role = 'customer'");
    $customer = $result->fetch_assoc();
?>
    <div class="content active" style="margin: 50px;">
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Update Customer</h3>
            <form method="POST" action="backend.php" class="space-y-4">
                <input type="hidden" name="action" value="update_customer">
                <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                <input type="text" name="fname" value="<?php echo $customer['fname']; ?>" required class="w-full p-2 border rounded">
                <input type="text" name="lname" value="<?php echo $customer['lname']; ?>" required class="w-full p-2 border rounded">
                <input type="email" name="email" value="<?php echo $customer['email']; ?>" required class="w-full p-2 border rounded">
                <input type="text" name="contact_no" value="<?php echo $customer['contact_no']; ?>" required class="w-full p-2 border rounded">
                <input type="text" name="address" value="<?php echo $customer['address']; ?>" required class="w-full p-2 border rounded">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Customer</button>
            </form>
        </div>
    </div>
<?php } ?>