<?php 

session_start();
require_once 'config/db.php';

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['store_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit(); // ป้องกันการทำงานต่อไปหากไม่เข้าสู่ระบบ
}

// ถ้าผู้ใช้เข้าสู่ระบบแล้ว
$username = $_SESSION['store_login'];

if (!empty($username)) {
    // ใช้ prepared statement เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username_id = :username_id");
    $stmt->bindParam(':username_id', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $_SESSION['error'] = 'ข้อมูลผู้ใช้ไม่ถูกต้อง!';
        header('location: signin.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'ข้อมูลผู้ใช้ไม่ถูกต้อง!';
    header('location: signin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3 class="mt-4">Welcome, <?php echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']); ?></h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
