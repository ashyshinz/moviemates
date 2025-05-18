<?php
/* ---------- CONFIG ---------- */
$dbhost = "localhost";   // Usually localhost
$dbuser = "root";  // Replace with your MySQL username, e.g., "root"
$dbpass = "";  // Replace with your MySQL password (often empty for XAMPP)
$dbname = "moviemate";
/* ---------------------------- */

header("Content-Type: application/json");

// 1) SERVER‑SIDE validation
$name  = trim($_POST["name"]  ?? "");
$email = trim($_POST["email"] ?? "");
$genre = trim($_POST["genre"] ?? "");

if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(["ok" => false, "msg" => "Invalid input"]);
    exit;
}

try {
    // 2) CONNECT + INSERT safely with prepared statement
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4",
                   $dbuser, $dbpass,
                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare(
        "INSERT INTO signups (name, email, genre) VALUES (:n, :e, :g)"
    );
    $stmt->execute([":n" => $name, ":e" => $email, ":g" => $genre]);

    echo json_encode(["ok" => true]);
} catch (PDOException $ex) {
    // duplicate e‑mail or other DB error
    http_response_code(500);
    echo json_encode(["ok" => false, "msg" => "Database error"]);
}
?>
