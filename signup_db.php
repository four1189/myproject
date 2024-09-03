<?php 

session_start();
require_once 'config/db.php';

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $c_password = $_POST['c_password'];
    $urole = 'user';

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
            $check_username = $conn->prepare("SELECT username FROM users WHERE username = :username");
            $check_username->bindParam(":username", $username);
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
                $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, email, password, urole) 
                                        VALUES (:username, :firstname, :lastname, :email, :password, :urole)");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":firstname", $firstname);
                $stmt->bindParam(":lastname", $lastname);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $passwordHash);
                $stmt->bindParam(":urole", $urole);
                $stmt->execute();

                $_SESSION['success'] = "สมัครสมาชิกเรียบร้อยแล้ว! <a href='signin.php' class='alert-link'>คลิ๊กที่นี่</a> เพื่อเข้าสู่ระบบ";
                header("location: index.php");
                exit();
            }
        } catch(PDOException $e) {
            echo "ข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>
