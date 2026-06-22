<?php if (isset($_GET['medit_id'])) {
    $id = $_GET['medit_id'];
    $result = $conn->query("SELECT u.*, m.specialization, m.experience_years 
                        FROM user_tbl u 
                        JOIN mechanic m ON u.id = m.user_id 
                        WHERE u.id = $id");
    $mechanic = $result->fetch_assoc();
?>
<div class="content active" style="margin: 50px;">
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Update Mechanic</h3>
        <form method="POST" action="backend.php" class="space-y-4">
            <input type="hidden" name="action" value="update_mechanic">
            <input type="hidden" name="id" value="<?php echo $mechanic['id']; ?>">
            <input type="text" name="fname" value="<?php echo $mechanic['fname']; ?>" required class="w-full p-2 border rounded">
            <input type="text" name="lname" value="<?php echo $mechanic['lname']; ?>" required class="w-full p-2 border rounded">
            <input type="email" name="email" value="<?php echo $mechanic['email']; ?>" required class="w-full p-2 border rounded">
            <input type="text" name="contact_no" value="<?php echo $mechanic['contact_no']; ?>" required class="w-full p-2 border rounded">
            <input type="text" name="add" value="<?php echo $mechanic['address']; ?>" required class="w-full p-2 border rounded">
            <input type="text" name="specialization" value="<?php echo $mechanic['specialization']; ?>" required class="w-full p-2 border rounded">
            <input type="number" name="exp" value="<?php echo $mechanic['experience_years']; ?>" required class="w-full p-2 border rounded">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Mechanic</button>
        </form>
    </div>
</div>
<?php } ?>