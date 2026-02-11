<?php
// File: check_events.php (place in OpenCart root)
// Visit: yourstore.com/check_events.php

require_once('config.php');

// Database connection
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "<h1>PDF Invoice Events Check</h1>";

// Check if events table exists
$table_check = $db->query("SHOW TABLES LIKE '" . DB_PREFIX . "event'");
if ($table_check->num_rows == 0) {
    echo "❌ Events table does not exist!<br>";
    exit;
}

echo "✅ Events table exists<br><br>";

// Check for PDF invoice events
$events = $db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `code` LIKE '%pdf_invoice%'");

echo "<h3>PDF Invoice Events:</h3>";

if ($events->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Code</th><th>Trigger</th><th>Action</th><th>Status</th></tr>";
    
    while ($event = $events->fetch_assoc()) {
        $status_color = $event['status'] ? '#28a745' : '#dc3545';
        $status_text = $event['status'] ? 'Enabled' : 'Disabled';
        
        echo "<tr>";
        echo "<td>" . $event['code'] . "</td>";
        echo "<td>" . $event['trigger'] . "</td>";
        echo "<td>" . $event['action'] . "</td>";
        echo "<td style='color: $status_color; font-weight: bold;'>$status_text</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    $enabled_events = $db->query("SELECT COUNT(*) as count FROM `" . DB_PREFIX . "event` WHERE `code` LIKE '%pdf_invoice%' AND `status` = 1");
    $enabled_count = $enabled_events->fetch_assoc()['count'];
    
    if ($enabled_count >= 1) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
        echo "✅ <strong>Events are registered and enabled!</strong><br>";
        echo "PDF invoices should be generated automatically for new orders.";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
        echo "⚠️ <strong>Events exist but are disabled!</strong><br>";
        echo "Enable them in Admin > Extensions > Events";
        echo "</div>";
    }
    
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "❌ <strong>No PDF invoice events found!</strong><br>";
    echo "Events need to be registered. Use the installation script below.";
    echo "</div>";
    
    echo "<h3>Quick Installation:</h3>";
    echo "<p>Run these SQL commands in your database:</p>";
    echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; font-family: monospace;'>";
    echo "INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`, `sort_order`) VALUES<br>";
    echo "('pdf_invoice_generation', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/pdf_invoice/generateOrderInvoice', 1, 0),<br>";
    echo "('pdf_invoice_email_attachment', 'mail/before', 'extension/module/pdf_invoice/attachInvoiceToEmail', 1, 0);";
    echo "</div>";
}

echo "<hr>";

// Check if frontend controller exists
$frontend_controller = DIR_OPENCART . 'catalog/controller/extension/module/pdf_invoice.php';
echo "<h3>Frontend Files Check:</h3>";
if (file_exists($frontend_controller)) {
    echo "✅ Frontend controller exists<br>";
} else {
    echo "❌ Frontend controller missing: $frontend_controller<br>";
}

// Check if frontend model exists
$frontend_model = DIR_OPENCART . 'catalog/model/extension/total/pdf_invoice.php';
if (file_exists($frontend_model)) {
    echo "✅ Frontend model exists<br>";
} else {
    echo "❌ Frontend model missing: $frontend_model<br>";
}

$db->close();

echo "<hr>";
echo "<p><em>Delete this file after checking.</em></p>";
?>