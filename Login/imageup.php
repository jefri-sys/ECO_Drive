<?php 
$fname = $_SESSION['fname'];
$dbImagePath = null; // Default to null

    
if (isset($_FILES['rimage']) && $_FILES['rimage']['error'] == 0) {
    $image_size = $_FILES['rimage']['size'];
    $image_type = strtolower(pathinfo($_FILES['rimage']['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    
    if ($image_size > 5000000) {
        die("Error: Image size exceeds 5MB.");
    } elseif (!in_array($image_type, $allowed_types)) {
        die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
    } else {
        $unique_name = uniqid() . '.' . $image_type;
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name;
        $dbImagePath = '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name;
        if (!move_uploaded_file($_FILES['rimage']['tmp_name'], $imagePath)) {
            die("Error: Failed to move uploaded file. Destination: $imagePath");
        }
    }
} else {
    // Set image_path to the first letter of item_name if no image is uploaded
    $dbImagePath = strtoupper(substr(trim($fname), 0, 1)); // e.g., "Brake" → "B"
}
?>