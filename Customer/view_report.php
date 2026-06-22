<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}
$service_id = intval($_GET['id'] ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Service Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #10B981;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10B981;
            margin-bottom: 5px;
        }
        .title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #10B981;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .details-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            flex: 0 0 200px;
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            flex: 1;
        }
        .service-list {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }
        .service-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .service-list li:last-child {
            border-bottom: none;
        }
        .service-list li::before {
            content: "✓";
            color: #10B981;
            font-weight: bold;
            display: inline-block;
            width: 20px;
        }
        .notes {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #10B981;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .btn-print {
            display: inline-block;
            background-color: #10B981;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            border: none;
        }
        @media print {
            .btn-print {
                display: none;
            }
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            body {
                padding: 0;
            }
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            color: white;
        }
        .status-completed {
            background-color: #10B981;
        }
        .status-progress {
            background-color: #F59E0B;
        }
        .status-scheduled {
            background-color: #3B82F6;
        }
        .status-cancelled {
            background-color: #EF4444;
        }
        .status-pending {
            background-color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="container" id="reportContainer">
        <h1>Service Report</h1>
        <p>Loading service report...</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceId = <?php echo $service_id; ?>;
            
            fetch(`/S6 PROJECT(TEAM 6)/Customer/generate_document.php?type=report&id=${serviceId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        document.getElementById('reportContainer').innerHTML = `
                            <div style="color: red; text-align: center;">
                                <h2>Error</h2>
                                <p>${data.error}</p>
                            </div>
                        `;
                        return;
                    }

                    // Determine status class
                    let statusClass = 'status-pending';
                    const status = data.status.toLowerCase();
                    if (status.includes('complete')) {
                        statusClass = 'status-completed';
                    } else if (status.includes('progress') || status.includes('servicing')) {
                        statusClass = 'status-progress';
                    } else if (status.includes('cancel')) {
                        statusClass = 'status-cancelled';
                    } else if (status.includes('schedule')) {
                        statusClass = 'status-scheduled';
                    }

                    // Format the report
                    document.getElementById('reportContainer').innerHTML = `
                        <div class="header">
                            <div class="logo">ECO-DRIVE</div>
                            <div class="title">Service Report</div>
                            <div>Report Date: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                        </div>
                        
                        <div class="section">
                            <div class="section-title">Vehicle Information</div>
                            <div class="details-row">
                                <div class="detail-label">Vehicle Model:</div>
                                <div class="detail-value">${data.vehicle}</div>
                            </div>
                            <div class="details-row">
                                <div class="detail-label">Registration Number:</div>
                                <div class="detail-value">${data.vehicle_no}</div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <div class="section-title">Service Information</div>
                            <div class="details-row">
                                <div class="detail-label">Service Plan:</div>
                                <div class="detail-value">${data.plan}</div>
                            </div>
                            <div class="details-row">
                                <div class="detail-label">Description:</div>
                                <div class="detail-value">${data.description || 'Comprehensive service plan for electric vehicles'}</div>
                            </div>
                            <div class="details-row">
                                <div class="detail-label">Request Date:</div>
                                <div class="detail-value">${new Date(data.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                            </div>
                            <div class="details-row">
                                <div class="detail-label">Completion Date:</div>
                                <div class="detail-value">${data.completion_date ? new Date(data.completion_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</div>
                            </div>
                            <div class="details-row">
                                <div class="detail-label">Status:</div>
                                <div class="detail-value">
                                    <span class="status-badge ${statusClass}">
                                        ${data.status}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section">
                            <div class="section-title">Services Performed</div>
                            <ul class="service-list">
                                ${data.services.map(service => `<li>${service}</li>`).join('')}
                            </ul>
                        </div>
                        
                        <div class="section">
                            <div class="section-title">Technician Notes</div>
                            <div class="notes">
                                ${data.notes || 'No additional notes provided.'}
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <button class="btn-print" onclick="window.print()">Print Report</button>
                        </div>
                        
                        <div class="footer">
                            <p>ECO-DRIVE Service Center</p>
                            <p>123 Green Avenue, Chennai, Tamil Nadu - 600001</p>
                            <p>Phone: +91 1234567890 | Email: support@ecodrive.com</p>
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('reportContainer').innerHTML = `
                        <div style="color: red; text-align: center;">
                            <h2>Error Loading Report</h2>
                            <p>${error.message}</p>
                            <p>Please try again later or contact support.</p>
                        </div>
                    `;
                    console.error('Error:', error);
                });
        });
    </script>
</body>
</html>

