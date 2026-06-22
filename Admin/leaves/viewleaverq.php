<?php include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php'; ?>

<div style="text-align: center;">
    <h2 class="text-2xl font-bold mb-8">Leave Requests List</h2>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border border-gray-300">ID</th>
                <th class="p-2 border border-gray-300">Mechanic</th>
                <th class="p-2 border border-gray-300">Start Date</th>
                <th class="p-2 border border-gray-300">End Date</th>
                <th class="p-2 border border-gray-300">Reason</th>
                <th class="p-2 border border-gray-300">Status</th>
                <th class="p-2 border border-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to fetch leave requests with details
            $query = "SELECT ml.id, CONCAT(u.fname, ' ', u.lname) AS mechanic_name, 
                            DATE_FORMAT(ml.start_date, '%Y-%m-%d') as start_date, 
                            DATE_FORMAT(ml.end_date, '%Y-%m-%d') as end_date, 
                            ml.reason, ml.status
                     FROM mechanic_leave ml
                     JOIN mechanic m ON ml.mechanic_id = m.id
                     JOIN user_tbl u ON m.user_id = u.id
                     ORDER BY ml.id DESC";
            
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td class='p-2 border border-gray-300'>{$row['id']}</td>
                        <td class='p-2 border border-gray-300'>{$row['mechanic_name']}</td>
                        <td class='p-2 border border-gray-300'>{$row['start_date']}</td>
                        <td class='p-2 border border-gray-300'>{$row['end_date']}</td>
                        <td class='p-2 border border-gray-300'>{$row['reason']}</td>
                        <td class='p-2 border border-gray-300'>
                            <span id='status_text-{$row['id']}'>{$row['status']}</span>
                        </td>
                        <td class='p-2 border border-gray-300'>
                            <select class='status-select p-2 border rounded' onchange='updateLeaveStatus(this, {$row['id']})' ". ($row['status'] == 'Approved' || $row['status'] == 'Rejected' ? 'style="display:none;"' : '') . ">
                                <option value=''>Select Status</option>
                                <option value='Approved' " . ($row['status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                                <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                            </select>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='p-2 border border-gray-300 text-center'>No leave requests found.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script>
function updateLeaveStatus(selectElement, leaveId) {
    const status = selectElement.value;
    if (!status) return; // Don't proceed if no status is selected

    const formData = new FormData();
    formData.append('leave_id', leaveId);
    formData.append('status', status);

    fetch('leaves/update_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status text
            document.getElementById(`status_text-${leaveId}`).textContent = status;
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: 'Status updated successfully!',
                toast: true,
                showConfirmButton: false,
                timer: 2500
            })
            if (status === 'Approved'|| status === 'Rejected') {
                selectElement.style.display = 'none';  
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to update status'));
            // Reset the select to the current status
            selectElement.value = document.getElementById(`status_text-${leaveId}`).textContent;
    }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Oops...",
                    text: 'Error: ' + data.message, // Use error param if provided, otherwise title
                    toast: true,
                    showConfirmButton: false,
                    timer: 2500
        })
        // Reset the select to the current status
        selectElement.value = document.getElementById(`status_text-${leaveId}`).textContent;
    });
}
</script>
