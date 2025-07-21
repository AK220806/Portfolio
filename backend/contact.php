<?php
header("Content-Type: application/json");

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405); // Method Not Allowed
  echo json_encode(["ok" => false, "error" => "Only POST requests are allowed"]);
  exit;
}

// Include your DB connection file
require "db.php";

// Sanitize inputs
$name     = trim($_POST["name"] ?? "");
$email    = trim($_POST["email"] ?? "");
$comments = trim($_POST["comments"] ?? "");

// Check for required fields
if (!$name || !$email || !$comments) {
  http_response_code(400); // Bad Request
  echo json_encode(["ok" => false, "error" => "Missing required fields"]);
  exit;
}

// Insert into database
try {
  $stmt = $conn->prepare("INSERT INTO messages (name, email, comments) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $comments);

  if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Message stored successfully."]);
  } else {
    throw new Exception("Database insert failed");
  }

  $stmt->close();
  $conn->close();
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Database Error: " . $e->getMessage()]);
}
?>
