<?php
$servername = "localhost";
$username = "root";
$password = ""; // Use the appropriate password
$dbname = "sas"; // Use your database name

try {
    // Create connection to the MySQL server
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create a users table if it does not exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL AUTO_INCREMENT,
        email VARCHAR(100) NOT NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        PRIMARY KEY(id)
    )";
    $conn->exec($sql);

    // Insert new user registration entry
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check if the username already exists
        $checkSql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            // Username already exists
            header("Location: register.php?status=fail");
            exit;
        }

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, username, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email, $username, $hashed_password]);

        // Redirect back to the login page with a success message
        header("Location: login.php?registration=success");
        exit;
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$conn = null;
?>

