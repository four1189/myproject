<?php 
session_start();
require_once 'config/db.php';

// ตรวจสอบการเข้าสู่ระบบและบทบาทของผู้ใช้
if (!isset($_SESSION['areazone_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit();
}

$user_id = $_SESSION['areazone_login'];
try {
    // ป้องกัน SQL Injection โดยใช้ prepared statements
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id AND urole = :urole");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':urole', 'areazone', PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลของผู้ใช้หรือไม่
    if (!$row) {
        $_SESSION['error'] = 'ไม่มีข้อมูลในระบบหรือไม่อนุญาตให้เข้าถึงหน้านี้!';
        header('location: signin.php');
        exit();
    }
} catch (PDOException $e) {
    // แสดงข้อผิดพลาดหากมีข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล
    echo 'Error: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AreaZone Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h3 class="mt-4">Welcome, <?php echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']); ?></h3>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
