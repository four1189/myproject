<?php
session_start();
// ล้างข้อมูลเซสชั่นที่เกี่ยวข้องกับการเข้าสู่ระบบทั้งหมด
unset($_SESSION['staff_login']);
unset($_SESSION['user_login']);
unset($_SESSION['store_login']);
unset($_SESSION['areazone_login']);
unset($_SESSION['operation_login']);
unset($_SESSION['admin_login']);

// ทำลายเซสชั่น
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้าเว็บที่เหมาะสมหลังจากล็อกเอาท์
header('location: signin.php');
exit();
?>
