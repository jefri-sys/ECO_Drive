<?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php'; 

// Function to return the correct class for status colors
function getStatusColor($status) {
    $colors = [
        'Pending' => 'bg-yellow-100 text-yellow-800',
        'Approved' => 'bg-blue-100 text-blue-800',
        'Rejected' => 'bg-red-100 text-red-800'
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800'; // Default color if status is unknown
}
?>

<div style="text-align: center;">
    <h2 class="text-2xl font-bold mb-8">Service Requests List</h2>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">ID</th>
                <th class="p-2 border border-gray-300">Customer</th>
                <th class="p-2 border border-gray-300">Vehicle</th>
                <th class="p-2 border border-gray-300">Services/Plan</th>
                <th class="p-2 border border-gray-300">Request Status</th>
                <th class="p-2 border border-gray-300">Assigned Mechanic</th>
                <th class="p-2 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to fetch service requests with slot_id
            $query = "
                SELECT sr.service_id, sr.slot_id, CONCAT(u.fname, ' ', u.lname) AS customer_name, v.vehicle_number, 
                       sr.request_status, sr.assigned_mechanic_id, 
                       COALESCE(m.specialization, 'Not Assigned') AS mechanic_specialization, 
                       COALESCE(sp.plan_name, GROUP_CONCAT(s.service_name SEPARATOR ', ')) AS servicesOrPlan
                FROM service_rq sr
                JOIN user_tbl u ON sr.user_id = u.id
                JOIN vehicle v ON sr.vehicle_id = v.id
                LEFT JOIN mechanic m ON sr.assigned_mechanic_id = m.id
                LEFT JOIN service_plans sp ON sr.plan_id = sp.id
                LEFT JOIN service_rq_services srs ON sr.service_id = srs.service_rq_id
                LEFT JOIN services s ON srs.service_id = s.id
                GROUP BY sr.service_id
            ";

            $result = $conn->query($query);
            if (!$result) {
                die("Query failed: " . $conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td class='p-2 border border-gray-300'>{$row['service_id']}</td>
                    <td class='p-2 border border-gray-300'>{$row['customer_name']}</td>
                    <td class='p-2 border border-gray-300'>{$row['vehicle_number']}</td>
                    <td class='p-2 border border-gray-300'>{$row['servicesOrPlan']}</td>
                    <td class='p-2 border border-gray-300'>
                        <span id='status-text-{$row['service_id']}' class='px-3 py-1 rounded-full text-xs font-medium " . getStatusColor($row['request_status']) . "'>{$row['request_status']}</span>
                    </td>
                    <td class='p-2 border border-gray-300'>
                        <span id='mechanic-text-{$row['service_id']}'>{$row['mechanic_specialization']}</span>
                    </td>
                    <td class='p-2 border border-gray-300'>
                        <div class='flex flex-col gap-2'>
                            <select class='mechanic-select p-2 border rounded w-full' data-service-id='{$row['service_id']}' onchange='assignMechanic(this, {$row['service_id']})' " . 
                            (($row['assigned_mechanic_id'] != null && $row['request_status'] === 'Approved' || $row['request_status'] === 'Rejected') ? "style='display:none;'" : "") . ">
                                <option value=''>Select Mechanic</option>";

                                // Fetch available mechanics: check availability_status first, then slot availability
                                $slot_id = $row['slot_id'];
                                $mech_query = "
                                    SELECT m.id, m.specialization 
                                    FROM mechanic m
                                    LEFT JOIN service_rq sr ON m.id = sr.assigned_mechanic_id AND sr.slot_id = ?
                                    WHERE m.availability_status = 'Available' 
                                    AND (sr.service_id IS NULL OR sr.service_id = ?)
                                ";
                                $stmt = $conn->prepare($mech_query);
                                $stmt->bind_param('ii', $slot_id, $row['service_id']);
                                $stmt->execute();
                                $mech_result = $stmt->get_result();

                                while ($mech = $mech_result->fetch_assoc()) {
                                    $selected = ($row['assigned_mechanic_id'] == $mech['id']) ? 'selected' : '';
                                    echo "<option value='{$mech['id']}' $selected>{$mech['specialization']}</option>";
                                }
                                $stmt->close();

                            echo "</select>
                            
                            <select class='status-select p-2 border rounded w-full' data-service-id='{$row['service_id']}' onchange='updateServiceStatus(this, {$row['service_id']})' " . 
                            (($row['request_status'] === 'Approved' || $row['request_status'] === 'Rejected') ? "style='display:none;'" : "") . ">
                                <option value='Pending' " . ($row['request_status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                <option value='Approved' " . ($row['request_status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                                <option value='Rejected' " . ($row['request_status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                            </select>
                        </div>
                    </td>
                </tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
    function assignMechanic(selectElement, serviceId) {
        const mechanicId = selectElement.value;
        const mechanicSpecialization = selectElement.options[selectElement.selectedIndex].text;
        
        fetch('/S6 PROJECT(TEAM 6)/Admin/service/update_mechanic.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `service_id=${serviceId}&mechanic_id=${mechanicId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`mechanic-text-${serviceId}`).textContent = mechanicSpecialization;
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: 'Mechanic assigned successfully!',
                    toast: true,
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                alert('Error: ' + data.message);
                selectElement.value = ''; // Reset if failed
            }
        })
        .catch(error => {
            console.error('Error:', error);
            selectElement.value = ''; // Reset on error
        });
    }

    function updateServiceStatus(selectElement, serviceId) {
        const status = selectElement.value;
        const mechanicSelect = document.querySelector(`.mechanic-select[data-service-id='${serviceId}']`);
        const mechanicId = mechanicSelect ? mechanicSelect.value : null;

        if (status === 'Approved' && !mechanicId) {
            Swal.fire({
                position: "top-end",
                icon: "error",
                title: "Oops...",
                text: "Cannot approve request without assigning a mechanic!",
                toast: true,
                showConfirmButton: false,
                timer: 2500
            })
            selectElement.value = 'Pending'; // Revert to Pending
            return;
        }

        fetch('/S6 PROJECT(TEAM 6)/Admin/service/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `service_id=${serviceId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`status-text-${serviceId}`).textContent = status;

                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: 'Status updated successfully!',
                    toast: true,
                    showConfirmButton: false,
                    timer: 2500
                });
                // Hide dropdown if status is Approved or Rejected
                if (status === 'Approved' || status === 'Rejected') {
                    selectElement.style.display = 'none';
                    mechanicSelect.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            selectElement.value = 'Pending'; // Revert on error
        });
    }
</script>