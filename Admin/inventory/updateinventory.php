<!-- Update Inventory Form -->
<?php if (isset($_GET['iedit_id'])) {
    $id = $_GET['iedit_id'];
    $result = $conn->query("SELECT * FROM inventory WHERE id = $id");
    $item = $result->fetch_assoc();
?>
<div class="content active" style="margin: 50px;">
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Update Inventory Item</h3>
        <form method="POST" action="backend.php" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_inventory">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <input type="text" name="item_name" value="<?php echo $item['spare_part_name']; ?>" required class="w-full p-2 border rounded">
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" required class="w-full p-2 border rounded">
            <input type="number" step="0.01" name="price" value="<?php echo $item['price']; ?>" required class="w-full p-2 border rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Image (optional)</label>
                <input type="file" name="item_image" accept="image/*" class="w-full p-2 border rounded">
                <p class="text-sm text-gray-500">Max file size: 5MB. Formats: JPG, PNG, GIF</p>
            </div>
            <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Item</button>
        </form>
    </div>
</div>
<?php } ?>