<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(["ok" => false, "error" => "POST only"]);
  exit;
}

require "db.php"; // MySQL DB connection
require 'vendor/autoload.php';
/*
require "PHPMailer/SMTP.php";
require "PHPMailer/Exception.php";*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$comments = trim($_POST["comments"] ?? "");

if (!$name || !$email || !$comments) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Missing fields"]);
  exit;
}

// 1️⃣ Store in database
$stmt = $conn->prepare("INSERT INTO messages (name, email, comments) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $comments);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Database error"]);
  exit;
}
$stmt->close();
$conn->close();

// 2️⃣ Send mail via SMTP
$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host       = 'smtp.gmail.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'rakumar1712005@gmail.com'; // 🔁 Change this
  $mail->Password   = 'jdyi rqyn mckj dzaw';    // 🔁 Use App Password if using Gmail
  $mail->SMTPSecure = 'tls';
  $mail->Port       = 587;

  $mail->setFrom('rakumar1712005@gmail.com', 'Website Form');
  $mail->addAddress('rakumar1712005@gmail.com'); // You will receive the message

  $mail->isHTML(true);
  $mail->Subject = 'New Contact Form Submission';
  $mail->Body    = "
    <h3>New Message</h3>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Comments:</strong> {$comments}</p>
  ";

  $mail->send();
  echo json_encode(["ok" => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Mailer Error: {$mail->ErrorInfo}"]);
}
?>