<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/sweet_alerts.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET["action"])) {
    $action = $_POST["action"] ?? $_GET["action"];

    switch ($action) {
        // 🚗 Mechanics
        case "add_mechanic":
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            $stmt = $conn->prepare("SELECT email FROM user_tbl WHERE email=?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<script>displayAlert(3, 'Email already exists! Try another one.', 'ECO-ADMIN.php');</script>";
            } else {
                $password = $_POST["pass"];
                $fname = trim(htmlspecialchars($_POST["fname"]));
                $lname = trim(htmlspecialchars($_POST['lname']));
                $contact=trim($_POST["contact"]);
                $address = trim(htmlspecialchars($_POST['add']));
                $specialization = htmlspecialchars($_POST['specialization']);
                $exp = $_POST["exp"];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                 // Insert into user_tbl
                $stmt = $conn->prepare("INSERT INTO user_tbl (fname, lname, email, contact_no, address, password, role) VALUES (?, ?, ?, ?, ?, ?, 'mechanic')");
                $stmt->bind_param("ssssss", $fname, $lname, $email, $contact, $address, $hashed_password);
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;
                    $stmt->close();

                    // Insert into mechanic table
                    $stmt = $conn->prepare("INSERT INTO mechanic (specialization, experience_years, user_id) VALUES (?, ?, ?)");
                    $stmt->bind_param("sii", $specialization, $exp, $user_id);
                    echo $stmt->execute() ? "<script>displayAlert(1, 'Mechanic added successfully!', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error Inserting Mechanic Data!!', 'ECO-ADMIN.php');</script>";

                    $stmt->close();
                } else {
                    echo "<script>displayAlert(3, 'Error Inserting Mechanic Data!!', 'ECO-ADMIN.php');</script>";
                }
            }
            break;

        case "update_mechanic":
            $id = $_POST["id"];
            $fname = $_POST["fname"];
            $lname = $_POST["lname"];
            $email = $_POST["email"];
            $contact_no = $_POST["contact_no"];
            $address = $_POST["add"];
            $specialization = $_POST["specialization"];
            $exp = $_POST["exp"];

            // Update user_tbl
            $stmt = $conn->prepare("UPDATE user_tbl SET fname=?, lname=?, email=?, contact_no=?, address=? WHERE id=?");
            $stmt->bind_param("sssssi", $fname, $lname, $email, $contact_no, $address, $id);
            if ($stmt->execute()) {
                $stmt->close();

                // Update mechanic table
                $stmt = $conn->prepare("UPDATE mechanic SET specialization=?, experience_years=? WHERE user_id=?");
                $stmt->bind_param("sii", $specialization, $exp, $id);
                echo $stmt->execute() ? "<script>displayAlert(1, 'Mechanic updated successfully!', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error Updating Mechanic Data!!', 'ECO-ADMIN.php');</script>";

                $stmt->close();
            } else {
                echo "<script>displayAlert(3, 'Error Updating Mechanic Data!!', 'ECO-ADMIN.php');</script>";
            }
            break;

        // 👤 Customers
        case "add_customer":
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            $stmt = $conn->prepare("SELECT email FROM user_tbl WHERE email=?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<script>displayAlert(3, 'Email already exists! Try another one.', 'ECO-ADMIN.php');</script>";
            } else {
                $password = $_POST["pass"];
                $fname = trim(htmlspecialchars($_POST["fname"]));
                $lname = trim(htmlspecialchars($_POST['lname']));
                $contact=trim($_POST["contact"]);
                $address = trim(htmlspecialchars($_POST['add']));
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
                $stmt = $conn->prepare("INSERT INTO user_tbl (fname, lname, email, contact_no,address,password, role) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
                $stmt->bind_param("ssssss", $fname, $lname, $email, $contact, $address, $hashed_password);
                echo $stmt->execute() ? "<script>displayAlert(1, 'Customer added successfully!', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error Inserting Customer Data!!', 'ECO-ADMIN.php');</script>";
                $stmt->close();
            }
            break;

        case "update_customer":
            $id = $_POST["id"];
            $fname = $_POST["fname"];
            $lname = $_POST["lname"];
            $email = $_POST["email"];
            $contact_no = $_POST["contact_no"];
            $address = $_POST["address"];

            $stmt = $conn->prepare("UPDATE user_tbl SET fname=?, lname=?, email=?, contact_no=?, address=? WHERE id=?");
            $stmt->bind_param("sssssi", $fname, $lname, $email, $contact_no, $address, $id);
            echo $stmt->execute() ? "<script>displayAlert(1, 'Customer updated successfully!', 'ECO-ADMIN.php');</script>" : "<script>displayAlert(3, 'Error Updating Customer Data!!', 'ECO-ADMIN.php');</script>";

            $stmt->close();
            break;

        // 🏭 Inventory
        case "add_inventory":
            $item_name = $_POST["item_name"];
            $quantity = $_POST["quantity"];
            $price = $_POST["price"];
            
            $imagePath = null; // Initialize image path as null
            
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
                $image_size = $_FILES['item_image']['size'];
                $image_type = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if ($image_size > 5000000) {
                    die("Error: Image size exceeds 5MB.");
                } elseif (!in_array($image_type, $allowed_types)) {
                    die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
                } else {
                    $unique_name = uniqid() . '.' . $image_type;
                    // Use absolute path based on DOCUMENT_ROOT
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name;
                    $dbImagePath = '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name; // Path to store in DB
                    
                    if (!move_uploaded_file($_FILES['item_image']['tmp_name'], $imagePath)) {
                        die("Error: Failed to move uploaded file. Destination: $imagePath");
                    }
                }
            }
            
            $imagePathParam = $dbImagePath ?? null;

            $stmt = $conn->prepare("INSERT INTO inventory (spare_part_name, quantity, price, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sids", $item_name, $quantity, $price, $imagePathParam);
            
            echo $stmt->execute() 
                ? "<script>displayAlert(1, 'Inventory added successfully!', 'ECO-ADMIN.php');</script>" 
                : "<script>displayAlert(3, 'Error Inserting Inventory Data!!', 'ECO-ADMIN.php');</script>";
            
            $stmt->close();
            
            break;

        case "update_inventory":
            $id = $_POST["id"];
            $item_name = $_POST["item_name"];
            $quantity = $_POST["quantity"];
            $price = $_POST["price"];
        
            // Check if a new image is uploaded
            if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
                $image_size = $_FILES['item_image']['size'];
                $image_type = strtolower(pathinfo($_FILES['item_image']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if ($image_size > 5000000) {
                    die("Error: Image size exceeds 5MB.");
                } elseif (!in_array($image_type, $allowed_types)) {
                    die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
                } else {
                    $unique_name = uniqid() . '.' . $image_type;
                    // Use absolute path based on DOCUMENT_ROOT
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name;
                    $dbImagePath = '/S6 PROJECT(TEAM 6)/uploads/' . $unique_name; // Path to store in DB
                    
                    if (!move_uploaded_file($_FILES['item_image']['tmp_name'], $imagePath)) {
                        die("Error: Failed to move uploaded file. Destination: $imagePath");
                    }
        
                    // Update with new image path
                    $stmt = $conn->prepare("UPDATE inventory SET spare_part_name = ?, quantity = ?, price = ?, image_path = ? WHERE id = ?");
                    $stmt->bind_param("sidss", $item_name, $quantity, $price, $dbImagePath, $id);
                }
            } else {
                // Update without changing the image path
                $stmt = $conn->prepare("UPDATE inventory SET spare_part_name = ?, quantity = ?, price = ? WHERE id = ?");
                $stmt->bind_param("sidi", $item_name, $quantity, $price, $id);
            }
            
            echo $stmt->execute() 
                ? "<script>displayAlert(1, 'Inventory updated successfully!', 'ECO-ADMIN.php');</script>" 
                : "<script>displayAlert(3, 'Error Updating Inventory Data!!', 'ECO-ADMIN.php');</script>";
            
            $stmt->close();
            
            break;
    }
}

$conn->close();
?>