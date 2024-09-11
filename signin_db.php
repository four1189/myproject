<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        header("location: signin.php");
        exit();
    }

    try {
        $check_data = $conn->prepare("SELECT * FROM users WHERE username_id = :username_id");
        $check_data->bindParam(":username_id", $username);
        $check_data->execute();
        $row = $check_data->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // ตรวจสอบรหัสผ่าน
            if (password_verify($password, $row['password'])) {
                if ($row['role_id'] == 'admin') {
                    $_SESSION['admin_login'] = $row['username_id'];
                    header("location: admin.php");
                } else if ($row['role_id'] == 'user') {
                    $_SESSION['user_login'] = $row['username_id'];
                    header("location: user.php");
                } else if ($row['role_id'] == 'staff') {
                    $_SESSION['staff_login'] = $row['username_id'];
                    header("location: staff.php");
                } else if ($row['role_id'] == 'store') {
                    $_SESSION['store_login'] = $row['username_id'];
                    header("location: store.php");
                } else if ($row['role_id'] == 'operation') {
                    $_SESSION['operation_login'] = $row['username_id'];
                    header("location: operation.php");
                } else if ($row['role_id'] == 'areazone') {
                    $_SESSION['areazone_login'] = $row['username_id'];
                    header("location: areazone.php");
                } 
                exit();
            } else {
                $_SESSION['error'] = 'รหัสผ่านผิด';
                header("location: signin.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "ไม่มีข้อมูลในระบบ";
            header("location: signin.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "ข้อผิดพลาด: " . $e->getMessage();
        exit();
    }
}
?>