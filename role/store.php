<?php 

session_start();
require_once 'config/db.php';

if (isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูล
    if (empty($username)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้';
        header("location: signin.php");
        exit();
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: signin.php");
        exit();
    } else if (strlen($password) > 20 || strlen($password) < 5) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
        header("location: signin.php");
        exit();
    } else {
        try {
            // ตรวจสอบข้อมูลผู้ใช้
            $check_data = $conn->prepare("SELECT * FROM users WHERE urole = :urole");
            $check_data->bindParam(":urole", $urole);
            $check_data->execute();
            $row = $check_data->fetch(PDO::FETCH_ASSOC);
    
            if ($row) {
                // ตรวจสอบรหัสผ่าน
                if (password_verify($password, $row['password'])) {
                    // ตรวจสอบบทบาทของผู้ใช้
                    switch ($row['urole']) {
                        case 'admin':
                            $_SESSION['admin_login'] = $row['id'];
                            header("location: admin.php");
                            break;
                        case 'user':
                            $_SESSION['user_login'] = $row['id'];
                            header("location: user.php");
                            break;
                        case 'staff':
                            $_SESSION['staff_login'] = $row['id'];
                            header("location: staff.php");
                            break;
                        case 'store':
                            $_SESSION['store_login'] = $row['id'];
                            header("location: store.php");
                            break;
                        case 'operation':
                            $_SESSION['operation_login'] = $row['id'];
                            header("location: operation.php");
                            break;
                        case 'areazone':
                            $_SESSION['areazone_login'] = $row['id'];
                            header("location: areazone.php");
                            break;
                        default:
                            $_SESSION['error'] = 'บทบาทไม่รู้จัก';
                            header("location: signin.php");
                            break;
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
        } catch(PDOException $e) {
            echo "ข้อผิดพลาด: " . $e->getMessage();
        }
    }    
}
?>
