<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['signup'])) {
    $username = $_POST['username_id']; // แก้ไขชื่อฟิลด์ให้ตรงกับชื่อที่ใช้ในฟอร์ม
    $password = $_POST['password'];
    $c_password = $_POST['c_password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $branch_id = $_POST['branch_id']; // เพิ่มค่าจากฟอร์ม
    $areazone_id = $_POST['areazone_id']; // เพิ่มค่าจากฟอร์ม
    $department_id = $_POST['department_id']; // เพิ่มค่าจากฟอร์ม
    $role_id = 'user'; // หรือค่าที่ต้องการ

    // ตรวจสอบความถูกต้องของข้อมูล
    if (empty($username)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้';
        header("location: index.php");
        exit();
    } else if (empty($firstname)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อจริง';
        header("location: index.php");
        exit();
    } else if (empty($lastname)) {
        $_SESSION['error'] = 'กรุณากรอกนามสกุล';
        header("location: index.php");
        exit();
    } else if (empty($email)) {
        $_SESSION['error'] = 'กรุณากรอกอีเมล';
        header("location: index.php");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        header("location: index.php");
        exit();
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: index.php");
        exit();
    } else if (strlen($password) > 20 || strlen($password) < 5) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
        header("location: index.php");
        exit();
    } else if (empty($c_password)) {
        $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
        header("location: index.php");
        exit();
    } else if ($password != $c_password) {
        $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
        header("location: index.php");
        exit();
    } else {
        try {
            // ตรวจสอบว่ามีผู้ใช้ที่ใช้ชื่อผู้ใช้แล้วหรือไม่
            $check_username = $conn->prepare("SELECT username_id FROM users WHERE username_id = :username_id");
            $check_username->bindParam(":username_id", $username);
            $check_username->execute();
            $row = $check_username->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $_SESSION['warning'] = "มีผู้ใช้นี้อยู่ในระบบแล้ว <a href='signin.php'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: index.php");
                exit();
            } else {
                // แฮชรหัสผ่าน
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // สั่ง INSERT ข้อมูลผู้ใช้ใหม่
                $stmt = $conn->prepare("INSERT INTO users (username_id, firstname, lastname, email, password, branch_id, areazone_id, department_id, role_id) 
                                        VALUES (:username_id, :firstname, :lastname, :email, :password, :branch_id, :areazone_id, :department_id, :role_id)");
                $stmt->bindParam(":username_id", $username);
                $stmt->bindParam(":firstname", $firstname);
                $stmt->bindParam(":lastname", $lastname);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":branch_id", $branch_id);
                $stmt->bindParam(":areazone_id", $areazone_id);
                $stmt->bindParam(":department_id", $department_id);
                $stmt->bindParam(":password", $passwordHash);
                $stmt->bindParam(":role_id", $role_id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! <a href='signin.php' class='alert-link'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                    header("location: index.php");
                    exit(); // Make sure to exit after redirection
                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: index.php");
                    exit(); // Make sure to exit after redirection
                }
            }
        } catch (PDOException $e) {
            echo "ข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>