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

// Internal Notification HTML Email Body
$email_content = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background-color: #f1f5f9; padding: 20px; margin: 0;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
    <div style="background: #0B111E; padding: 24px; text-align: center; border-bottom: 3px solid #62C6C2;">
      <img src="https://acebeamel.com/assets/images/acebeam_logo.png" alt="Acebeam Engineering Limited" width="180" style="display: block; margin: 0 auto 10px;">
      <h2 style="color: #62C6C2; margin: 0; font-size: 18px; letter-spacing: 0.5px;">NEW WEB INQUIRY & RFQ</h2>
      <p style="color: #94a3b8; font-size: 12px; margin: 4px 0 0;">Acebeam Engineering Limited (RC 1885530)</p>
    </div>
    <div style="padding: 28px;">
      <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; font-weight: bold; color: #475569; width: 35%;">Client Name:</td><td style="padding: 10px 0; color: #0B111E; font-weight: 600;">' . htmlspecialchars($name) . '</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; font-weight: bold; color: #475569;">Email:</td><td style="padding: 10px 0;"><a href="mailto:' . htmlspecialchars($email) . '" style="color: #0284c7; text-decoration: none;">' . htmlspecialchars($email) . '</a></td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; font-weight: bold; color: #475569;">Phone:</td><td style="padding: 10px 0; color: #0B111E;">' . htmlspecialchars($phone) . '</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; font-weight: bold; color: #475569;">Company/Org:</td><td style="padding: 10px 0; color: #0B111E;">' . htmlspecialchars($company) . '</td></tr>
        <tr style="border-bottom: 1px solid #f1f5f9;"><td style="padding: 10px 0; font-weight: bold; color: #475569;">Division/Service:</td><td style="padding: 10px 0; color: #0B111E; font-weight: 600; color: #0284c7;">' . htmlspecialchars($service) . '</td></tr>
      </table>
      <div style="margin-top: 20px;">
        <h4 style="color: #0B111E; margin-bottom: 8px;">Scope & Specifications:</h4>
        <div style="background: #f8fafc; padding: 16px; border-radius: 6px; border-left: 3px solid #62C6C2; color: #334155; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">' . htmlspecialchars($message) . '</div>
      </div>
      <div style="margin-top: 24px; font-size: 11px; color: #94a3b8; text-align: center;">
        Submitted on ' . date("Y-m-d H:i:s") . ' &bull; IP: ' . $_SERVER['REMOTE_ADDR'] . '
      </div>
    </div>
  </div>
</body>
</html>';

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Acebeam Web Portal <info@acebeamel.com>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (@mail($recipient, $subject, $email_content, $headers, "-finfo@acebeamel.com")) {
    // Send polite branded auto-acknowledgement to user
    $ack_subject = "Acknowledgment: Your Engineering Request with Acebeam Engineering Limited";
    $ack_body = '<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background-color: #f1f5f9; padding: 20px; margin: 0;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
    <div style="background: #0B111E; padding: 24px; text-align: center; border-bottom: 3px solid #62C6C2;">
      <img src="https://acebeamel.com/assets/images/acebeam_logo.png" alt="Acebeam Engineering Limited" width="180" style="display: block; margin: 0 auto 8px;">
      <p style="color: #62C6C2; font-size: 12px; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Turnkey Engineering &bull; Industrial Automation &bull; RC 1885530</p>
    </div>
    <div style="padding: 30px; font-size: 14px; line-height: 1.6; color: #334155;">
      <h3 style="color: #0B111E; margin-top: 0;">Dear ' . htmlspecialchars($name) . ',</h3>
      <p>Thank you for contacting <strong>Acebeam Engineering Limited (AEL)</strong>.</p>
      <p>We have successfully received your inquiry regarding <strong>"' . htmlspecialchars($service) . '"</strong>. Our technical engineering desk is currently reviewing your project scope and specifications.</p>
      <p>A specialized project engineer will be in touch with you promptly to discuss your technical deliverables.</p>
      <div style="background: #e6f9f8; border: 1px solid #62C6C2; padding: 14px 18px; border-radius: 6px; margin: 20px 0;">
        <strong style="color: #0B111E;">Need immediate technical clarification?</strong><br>
        Call our direct engineering line: <a href="tel:+2348037646371" style="color: #0284c7; font-weight: bold; text-decoration: none;">+234 803 764 6371</a> or chat with us on <a href="https://wa.me/2348037646371" style="color: #25D366; font-weight: bold; text-decoration: none;">WhatsApp</a>.
      </div>

      <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">

      <!-- Official Executive Signature Block -->
      <table cellpadding="0" cellspacing="0" border="0" style="font-size: 12px; color: #475569;">
        <tr>
          <td style="padding-right: 16px; border-right: 2px solid #62C6C2; vertical-align: top;">
            <img src="https://acebeamel.com/assets/images/acebeam_logo.png" width="120" style="display: block;" alt="Acebeam">
          </td>
          <td style="padding-left: 16px; vertical-align: top;">
            <strong style="color: #0B111E; font-size: 13px;">Acebeam Engineering Limited</strong><br>
            <span style="color: #64748b; font-size: 11px;">CAC Registration: RC 1885530</span><br>
            <span style="color: #64748b; font-size: 11px;">KM 32, Lagos-Badagry Expressway, Morogbo / Agbara, Lagos</span><br>
            <a href="https://acebeamel.com" style="color: #0284c7; text-decoration: none; font-size: 11px;">https://acebeamel.com</a>
          </td>
        </tr>
      </table>

    </div>
  </div>
</body>
</html>';

    $ack_headers  = "MIME-Version: 1.0\r\n";
    $ack_headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $ack_headers .= "From: Acebeam Engineering <info@acebeamel.com>\r\n";
    @mail($email, $ack_subject, $ack_body, $ack_headers, "-finfo@acebeamel.com");

    echo json_encode(['status' => 'success', 'message' => 'Inquiry dispatched successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Unable to dispatch mail via server.']);
}
