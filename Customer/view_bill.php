<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /S6 PROJECT(TEAM 6)/ECO-drive(UI).php?login=open');
    exit();
}
$service_id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
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
            padding: 20px;
        }
        .header {
            padding-bottom: 20px;
            border-bottom: 2px solid #10B981;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #10B981;
        }
        .invoice-details {
            text-align: right;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #10B981;
        }
        .section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #10B981;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f5f5f5;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .summary {
            width: 300px;
            margin-left: auto;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .summary-total {
            font-weight: bold;
            border-top: 2px solid #ddd;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .thank-you {
            font-size: 18px;
            text-align: center;
            margin: 30px 0;
            color: #10B981;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="billContainer">
        <h1>Invoice</h1>
        <p>Loading...</p>
    </div>
    <script>
        const serviceId = <?php echo $service_id; ?>;
        fetch(`/S6 PROJECT(TEAM 6)/Customer/generate_document.php?type=bill&id=${serviceId}`)
            .then(response => response.json())
            .then(data => {
                // Prepare services list for display
                const servicesList = data.services.map(service => service.name);
                const servicesSubtotal = Number(data.price) + data.services.reduce((sum, service) => sum + Number(service.price), 0);
                const spareParts = data.spare_parts.map(part => ({
                    name: part.name,
                    quantity: part.quantity,
                    unit_price: part.price,
                    total: part.quantity * part.price
                }));
                const sparePartsTotal = spareParts.reduce((sum, part) => sum + Number(part.total), 0);
                const subtotal = Number(data.subtotal);
                const taxAmount = Number(data.tax);
                const total = Number(data.total);

                document.getElementById('billContainer').innerHTML = `
                    <div class="header">
                        <div class="logo">ECO-DRIVE</div>
                        <div class="invoice-details">
                            <div class="title">INVOICE</div>
                            <div><span class="label">Invoice Number:</span> ${data.id}</div>
                            <div><span class="label">Date:</span> ${new Date(data.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
                        </div>
                    </div>
                    
                    <div class="section">
                        <div class="info-grid">
                            <div>
                                <div class="section-title">Bill To:</div>
                                <div>${data.username || 'Customer'}</div>
                                <div>${data.email || ''}</div>
                            </div>
                            <div>
                                <div class="section-title">Vehicle Details:</div>
                                <div>${data.vehicle}</div>
                                <div>Service Date: ${new Date(data.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section">
                        <div class="section-title">Service Details</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th style="text-align: right;">Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>${data.plan}</strong>
                                        <ul style="margin: 5px 0 0; padding-left: 20px; color: #666; font-size: 14px;">
                                            ${servicesList.map(item => `<li>${item}</li>`).join('')}
                                        </ul>
                                    </td>
                                    <td style="text-align: right; vertical-align: top;">
                                        ${servicesSubtotal.toFixed(2)}
                                    </td>
                                </tr>
                                ${spareParts.length > 0 ? `
                                <tr>
                                    <td colspan="2">
                                        <strong style="color: #10B981;">Spare Parts Used</strong>
                                        <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                                            <thead>
                                                <tr style="background-color: #f5f5f5;">
                                                    <th style="text-align: left; padding: 8px; border-bottom: 1px solid #eee;">Part Name</th>
                                                    <th style="text-align: center; padding: 8px; border-bottom: 1px solid #eee;">Quantity</th>
                                                    <th style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">Unit Price (₹)</th>
                                                    <th style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">Total (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${spareParts.map(part => `
                                                <tr>
                                                    <td style="padding: 8px; border-bottom: 1px solid #eee;">${part.name}</td>
                                                    <td style="text-align: center; padding: 8px; border-bottom: 1px solid #eee;">${part.quantity}</td>
                                                    <td style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">${part.unit_price}</td>
                                                    <td style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">${part.total.toFixed(2)}</td>
                                                </tr>
                                                `).join('')}
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">Spare Parts Subtotal:</td>
                                                    <td style="text-align: right; padding: 8px; font-weight: bold;">${sparePartsTotal.toFixed(2)}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                                ` : ''}
                            </tbody>
                        </table>
                        
                        <div class="summary">
                            <div class="summary-row">
                                <div>Services Subtotal</div>
                                <div>₹${servicesSubtotal.toFixed(2)}</div>
                            </div>
                            ${spareParts.length > 0 ? `
                            <div class="summary-row">
                                <div>Spare Parts Subtotal</div>
                                <div>₹${sparePartsTotal.toFixed(2)}</div>
                            </div>
                            ` : ''}
                            <div class="summary-row">
                                <div>Subtotal</div>
                                <div>₹${subtotal.toFixed(2)}</div>
                            </div>
                            <div class="summary-row">
                                <div>GST (18%)</div>
                                <div>₹${taxAmount.toFixed(2)}</div>
                            </div>
                            <div class="summary-row summary-total">
                                <div>Total</div>
                                <div>₹${total.toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="thank-you">
                        Thank you for choosing ECO-DRIVE!
                    </div>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <button class="print-btn" onclick="window.print()" style="padding: 10px 20px; background-color: #10B981; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Print Invoice
                        </button>
                    </div>
                    
                    <div class="footer">
                        <p>ECO-DRIVE Service Center</p>
                        <p>123 Green Avenue, Chennai, Tamil Nadu - 600001</p>
                        <p>GSTIN: 33AABCE9603R1ZX</p>
                        <p>Phone: +91 1234567890 | Email: support@ecodrive.com</p>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('billContainer').innerHTML = `<p>Error loading invoice: ${error.message}</p>`;
            });
    </script>
</body>
</html>