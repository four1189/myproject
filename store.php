<?php 

    session_start();
    require_once 'config/db.php';

    if (!isset($_SESSION['store_login'])) {
        $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
        header('location: signin.php');
        exit();
    }

    $store_id = $_SESSION['store_login'];

    try {
    // ตรวจสอบข้อมูลผู้ใช้ในฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id AND urole = :urole");
        $stmt->bindParam(':id', $store_id, PDO::PARAM_INT);
        $stmt->bindValue(':urole', 'store', PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงหน้านี้หรือไม่
        if (!$row) {
            $_SESSION['error'] = 'ไม่มีข้อมูลในระบบหรือไม่อนุญาตให้เข้าถึงหน้านี้!';
            header('location: signin.php');
            exit();
        }
    } catch (PDOException $e) {
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
