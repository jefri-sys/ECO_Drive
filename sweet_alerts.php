<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Handler</title>
    <!-- Include SweetAlert2 from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    /**
     * Display different types of alerts based on the case value
     * param {number} caseValue - 1 for success, 2 for delete confirmation, 3 for error
     * param {string} title - Message or title for the alert (used in success and error)
     * param {string} redirectUrl - URL to redirect to after the alert
     * param {number|string} [id] - Optional ID for delete confirmation (used in case 2)
     */
    function displayAlert(caseValue, title, redirectUrl, id = null, fn = null, callback = null) {
        if (caseValue == 1) {
            // Success Alert
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: title,
                showConfirmButton: false,
                toast: true,
                timer: 2500
            }).then(() => {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        } else if (caseValue == 2) {
            // Delete Confirmation Alert
            Swal.fire({
                title: "Are you sure?",
                text: title ?? "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#338F32",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    if (callback) {
                        callback(result); // Call the provided callback instead of redirecting
                    } else if (id && fn) {
                        // Fallback to redirect if no callback is provided
                        if (fn === 'customer') {
                            window.location.href = "?cdelete_id=" + id;
                        } else if (fn === 'vehicle') {
                            window.location.href = "?vdelete_id=" + id;
                        } else if (fn === 'mechanic') {
                            window.location.href = "?mdelete_id=" + id;
                        } else if (fn === 'inventory') {
                            window.location.href = "?idelete_id=" + id;
                        } else if (fn === 'services') {
                            window.location.href = "?sdelete_id=" + id;
                        } else if (fn === 'service_plans') {
                            window.location.href = "?spdelete_id=" + id;
                        }
                    }
                }
            });
        } else if (caseValue == 3) {
            // Error Alert
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Oops...",
                text: title,
                showConfirmButton: false,
                timer: 2500
            }).then(() => {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            });
        } else {
            console.warn("Invalid case value provided to displayAlert");
        }
    }

    // displayAlert(1, "Your work has been saved", "ECO-drive(UI).php");
    // displayAlert(2, \"\", \"\", " . intval($row["id"]) . ", \"inventory\"); // Example ID for delete
    // displayAlert(3, "Something went wrong", ".php");
</script>
</body>
</html>
