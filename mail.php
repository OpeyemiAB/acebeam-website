<?php
/**
 * ACEBEAM ENGINEERING LIMITED - Server-side RFQ & Contact Mailer
 * Compatible with cPanel PHP 7.4 / 8.x
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Honeypot spam check
if (!empty($_POST['website_hp'])) {
    // Silent success for bots
    echo json_encode(['status' => 'success', 'message' => 'Processed']);
    exit;
}

// Sanitize inputs
$name    = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email   = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$phone   = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : 'Not provided';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : 'General Engineering Inquiry';
$company = isset($_POST['company']) ? trim(strip_tags($_POST['company'])) : 'Not provided';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields (Name, Email, Message).']);
    exit;
}

$recipient = "info@acebeamel.com";
$subject   = "New Engineering Request: " . $service . " - " . $name;

// Email Body
$email_content  = "====================================================\n";
$email_content .= "ACEBEAM ENGINEERING LIMITED - NEW WEBSITE INQUIRY\n";
$email_content .= "====================================================\n\n";
$email_content .= "Client Name:  " . $name . "\n";
$email_content .= "Email:        " . $email . "\n";
$email_content .= "Phone:        " . $phone . "\n";
$email_content .= "Company/Org:  " . $company . "\n";
$email_content .= "Division:     " . $service . "\n\n";
$email_content .= "Scope & Message:\n";
$email_content .= "----------------------------------------------------\n";
$email_content .= $message . "\n";
$email_content .= "----------------------------------------------------\n";
$email_content .= "Submitted on: " . date("Y-m-d H:i:s") . "\n";
$email_content .= "IP Address:   " . $_SERVER['REMOTE_ADDR'] . "\n";

$headers  = "From: Acebeam Web Portal <info@acebeamel.com>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (@mail($recipient, $subject, $email_content, $headers, "-finfo@acebeamel.com")) {
    // Send polite auto-acknowledgement to user
    $ack_subject = "Acknowledgment: Your Engineering Request with Acebeam Engineering Limited";
    $ack_body = "Dear " . $name . ",\n\n"
              . "Thank you for contacting Acebeam Engineering Limited (RC 1885530).\n\n"
              . "We have received your inquiry regarding '" . $service . "'. Our technical engineering team is reviewing your project requirements and will contact you promptly.\n\n"
              . "For urgent inquiries, reach our direct line: +234 803 764 6371\n\n"
              . "Best regards,\n"
              . "Technical Operations Team\n"
              . "Acebeam Engineering Limited\n"
              . "KM 32, Lagos-Badagry Expressway, Lagos, Nigeria\n"
              . "https://acebeamel.com\n";
    $ack_headers = "From: Acebeam Engineering <info@acebeamel.com>\r\n";
    @mail($email, $ack_subject, $ack_body, $ack_headers, "-finfo@acebeamel.com");

    echo json_encode(['status' => 'success', 'message' => 'Inquiry dispatched successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to dispatch mail via server.']);
}
