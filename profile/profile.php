<!-- profile_modal.php -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Fetch user data
$stmt = $conn->prepare("SELECT u.*, m.specialization, m.experience_years, m.availability_status 
                       FROM user_tbl u 
                       LEFT JOIN mechanic m ON u.id = m.user_id 
                       WHERE u.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile data update
    if (isset($_POST['fname'])) {
        $updates = [
            'fname' => $_POST['fname'] ?? $user['fname'],
            'lname' => $_POST['lname'] ?? $user['lname'],
            'email' => $_POST['email'] ?? $user['email'],
            'contact_no' => $_POST['contact_no'] ?? $user['contact_no'],
            'address' => $_POST['address'] ?? $user['address']
        ];
        
        $stmt = $conn->prepare("UPDATE user_tbl SET fname = ?, lname = ?, email = ?, contact_no = ?, address = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $updates['fname'], $updates['lname'], $updates['email'], $updates['contact_no'], $updates['address'], $user_id);
        
        if ($stmt->execute()) {
            // Update session username
            $_SESSION['username'] = $updates['fname'] . ' ' . $updates['lname'];
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT u.*, m.specialization, m.experience_years, m.availability_status 
                                   FROM user_tbl u 
                                   LEFT JOIN mechanic m ON u.id = m.user_id 
                                   WHERE u.id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        }
        $stmt->close();
    }

    // Handle image upload with deletion of existing image
    if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] == 0) {
        $image_size = $_FILES['imageUpload']['size'];
        $image_type = strtolower(pathinfo($_FILES['imageUpload']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if ($image_size > 5000000) {
            die("Error: Image size exceeds 5MB.");
        } elseif (!in_array($image_type, $allowed_types)) {
            die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
        } else {
            $unique_name = uniqid() . '.' . $image_type;
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/uploads/profile_images/';
            
            // Ensure directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $imagePath = $uploadDir . $unique_name;
            $dbImagePath = '/S6 PROJECT(TEAM 6)/uploads/profile_images/' . $unique_name;
    
            // Delete existing image if it exists
            if (!empty($user['image_path']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['image_path'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $user['image_path']);
            }
    
            if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $imagePath)) {
                $stmt = $conn->prepare("UPDATE user_tbl SET image_path = ? WHERE id = ?");
                $stmt->bind_param("si", $dbImagePath, $user_id);
                $stmt->execute();
                $stmt->close();
                
                // Refresh user data
                $stmt = $conn->prepare("SELECT u.*, m.specialization, m.experience_years, m.availability_status 
                                       FROM user_tbl u 
                                       LEFT JOIN mechanic m ON u.id = m.user_id 
                                       WHERE u.id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                // Update the displayed image immediately
                echo '<script>document.getElementById("profileImage").src = "' . $dbImagePath . '?t=' . time() . '";</script>';
            } else {
                $error = error_get_last();
                die("Error: Failed to move uploaded file. Error: " . $error['message']);
            }
        }
       
    }
    echo "<script>displayAlert(1, 'Profile updated successfully!', '');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4CAF50',
                        secondary: '#81C784'
                    },
                    borderRadius: {
                        'button': '8px'
                    }
                }
            }
        }
    </script>
    <style>
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 35rem;
            max-height: 105vh;
            overflow-y: auto;
            position: relative;
        }
        .field-container {
            padding: 0.75rem;
        }
        .field-container textarea {
            min-height: 2.5rem;
        }
        .avatar-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: #4CAF50;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div id="profileModal" class="modal">
        <div class="modal-content p-4">
            <button onclick="closeProfileModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <i class="ri-close-line text-xl"></i>
            </button>
            <div class="relative w-24 h-24 mx-auto mb-4">
                <?php if ($user['image_path']): ?>
                    <img id="profileImage" src="<?php echo htmlspecialchars($user['image_path']); ?>" 
                         class="w-full h-full rounded-full object-cover" alt="Profile Picture">
                <?php else: ?>
                    <div id="profileImage" class="avatar-circle">
                        <?php echo htmlspecialchars(substr($user['fname'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <label for="imageUpload" class="absolute bottom-0 right-0 bg-primary text-white p-1 rounded-full cursor-pointer">
                    <div class="w-5 h-5 flex items-center justify-center">
                        <i class="ri-camera-line"></i>
                    </div>
                </label>
                <input type="file" id="imageUpload" name="imageUpload" class="hidden" accept="image/*">
            </div>

            <form method="POST" id="profileForm" enctype="multipart/form-data">
                <div class="space-y-2">
                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">First Name:</strong>
                                <input type="text" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" 
                                       class="w-full bg-transparent border-none focus:outline-none text-gray-700 text-sm" readonly>
                            </div>
                        </div>
                        <button type="button" class="text-primary edit-btn" data-field="fname">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="ri-edit-line"></i>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Last Name:</strong>
                                <input type="text" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" 
                                       class="w-full bg-transparent border-none focus:outline-none text-gray-700 text-sm" readonly>
                            </div>
                        </div>
                        <button type="button" class="text-primary edit-btn" data-field="lname">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="ri-edit-line"></i>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Email:</strong>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       class="w-full bg-transparent border-none focus:outline-none text-gray-700 text-sm" readonly>
                            </div>
                        </div>
                        <button type="button" class="text-primary edit-btn" data-field="email">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="ri-edit-line"></i>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Contact:</strong>
                                <input type="tel" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no']); ?>" 
                                       class="w-full bg-transparent border-none focus:outline-none text-gray-700 text-sm" readonly>
                            </div>
                        </div>
                        <button type="button" class="text-primary edit-btn" data-field="contact_no">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="ri-edit-line"></i>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-home-2-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Address:</strong>
                                <textarea name="address" class="w-full bg-transparent border-none focus:outline-none text-gray-700 text-sm resize-none" 
                                          readonly><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <button type="button" class="text-primary edit-btn" data-field="address">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <i class="ri-edit-line"></i>
                            </div>
                        </button>
                    </div>

                    <?php if ($user['role'] === 'mechanic' && $user['specialization']): ?>
                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-tools-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Specialization:</strong>
                                <span class="text-gray-700 text-sm"><?php echo htmlspecialchars($user['specialization']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-time-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Experience:</strong>
                                <span class="text-gray-700 text-sm"><?php echo htmlspecialchars($user['experience_years']); ?> years</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Availability:</strong>
                                <span class="text-gray-700 text-sm"><?php echo htmlspecialchars($user['availability_status']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex items-center justify-between bg-gray-50 rounded field-container">
                        <div class="flex items-center gap-2 w-full">
                            <div class="w-5 h-5 flex items-center justify-center text-primary">
                                <i class="ri-shield-user-line"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-gray-700 text-sm">Role:</strong>
                                <span class="text-gray-700 text-sm"><?php echo htmlspecialchars($user['role']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="closeProfileModal()" class="px-4 py-1 border border-gray-300 rounded-button text-gray-700 text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-1 bg-primary text-white rounded-button text-sm hover:bg-primary/90">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Edit button functionality
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const field = this.getAttribute('data-field');
                const input = this.parentElement.querySelector(`[name="${field}"]`);
                input.readOnly = !input.readOnly;
                input.focus();
                
                if (!input.readOnly) {
                    input.classList.add('border', 'border-gray-300', 'rounded', 'p-1');
                } else {
                    input.classList.remove('border', 'border-gray-300', 'rounded', 'p-1');
                }
            });
        });

        // Modal control functions
        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'flex';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        // Image handling
     

        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageElement = document.getElementById('profileImage');
                    imageElement.outerHTML = `<img id="profileImage" src="${e.target.result}" class="w-full h-full rounded-full object-cover" alt="Profile Picture">`;
                }
                reader.readAsDataURL(file);
            }
        });

    </script>
</body>
</html>