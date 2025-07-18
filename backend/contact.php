<?php
header("Content-Type: application/json");

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405); // Method Not Allowed
  echo json_encode(["ok" => false, "error" => "POST only"]);
  exit;
}

// Autoload dependencies
require 'vendor/autoload.php';
require "db.php"; // Include your DB connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sanitize inputs
$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$comments = trim($_POST["comments"] ?? "");

// Check for required fields
if (!$name || !$email || !$comments) {
  http_response_code(400); // Bad Request
  echo json_encode(["ok" => false, "error" => "Missing fields"]);
  exit;
}

// ✅ Insert into database
try {
  $stmt = $conn->prepare("INSERT INTO messages (name, email, comments) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $comments);

  if (!$stmt->execute()) {
    throw new Exception("Failed to insert into database.");
  }

  $stmt->close();
  $conn->close();
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Database error: " . $e->getMessage()]);
  exit;
}

// ✅ Send email
$mail = new PHPMailer(true);

try {
  // Server settings
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'rakumar1712005@gmail.com'; // Replace with your Gmail
  $mail->Password   = 'jdyi rqyn mckj dzaw';       // Use Gmail App Password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  // Recipients
  $mail->setFrom('rakumar1712005@gmail.com', 'Website Form');
  $mail->addAddress('rakumar1712005@gmail.com'); // Receiver address

  // Content
  $mail->isHTML(true);
  $mail->Subject = 'New Contact Form Submission';
  $mail->Body    = "
    <h3>New Contact Message</h3>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Message:</strong><br>{$comments}</p>
  ";

  $mail->send();

  // ✅ Success response
  echo json_encode(["ok" => true, "message" => "Message sent and stored successfully."]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Mailer Error: " . $mail->ErrorInfo]);
}
