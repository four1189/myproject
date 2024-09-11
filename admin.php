<?php 

session_start();
require_once 'config/db.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ตรวจสอบว่าผู้ใช้ได้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['admin_login'])) {
    $_SESSION['error'] = 'กรุณาเข้าสู่ระบบ!';
    header('location: signin.php');
    exit(); // ป้องกันการทำงานต่อไปหากไม่เข้าสู่ระบบ
}

// ถ้าผู้ใช้เข้าสู่ระบบแล้ว
$username = $_SESSION['admin_login'];


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
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        body {
            font-family: 'Kanit', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f0f0f0;
        }

        .sidebar {
            width: 250px;
            background-color: #0066cc;
            color: white;
            padding: 20px;
            height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            background-color: white;
            border-radius: 15px;
            margin: 0 auto 20px;
        }

        .user-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .menu-item {
            display: block;
            padding: 12px 15px;
            margin-bottom: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container {
            width: 70%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        input[type="number"] {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
            /* Allow decimal input */
            step: 0.01;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 48%;
        }

        button:hover {
            background-color: #45a049;
        }

        .hidden {
            display: none;
        }

        .total {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .modal-overlay {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            overflow: hidden; /* Prevent scrolling */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto; /* Center it horizontally */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 600px; /* Optional: Adjust width as needed */
            position: fixed; /* Fix position */
            top: 50%; /* Center vertically */
            left: 50%; /* Center horizontally */
            transform: translate(-50%, -50%); /* Center the modal */
        }

        /* Close Button */
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .profile-container {
        max-width: 500px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-align: center;
        }
        .profile-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .profile-position {
            font-size: 1.2rem;
            color: #555;
        }
    </style>
<body>
<div class="sidebar">
    <div class="container">
        <h3 class="mt-4">
            <?php 
            if (isset($row['firstname']) && isset($row['lastname'])) {
                echo htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']);
            } else {
                echo "ชื่อและนามสกุลไม่ระบุ";
            }
            ?>
        </h3>
        <p>
            <?php 
            if (isset($row['position'])) {
                echo htmlspecialchars($row['position']);
            } else {
                echo "ตำแหน่งไม่ระบุ";
            }
            ?>
        </p>
    </div>
        <a href="index.html" class="menu-item">หน้าแรก</a>
        <a href="Add information.html" class="menu-item">เพิ่มข้อมูล</a>
        <a href="setting.html" class="menu-item">การตั้งค่าสิทธิ์</a>
        <a href="setting2.html" class="menu-item">ตั้งค่า2</a>
        <a href="averaging.html" class="menu-item">การเฉลี่ยยอด</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <div class="main-content">
        <div class="container">
            <h1>ตั้งค่าเป้ายอดขาย</h1>
            <!-- Step 1: Method Selection -->
            <div id="method-selection" class="step">
                <h2>เลือกวิธีการตั้งค่าเป้าหมาย</h2>
                <form>
                    <label><input type="radio" name="method" value="manual" checked> กรอกเป้าหมายยอดขาย</label><br>
                    <label><input type="radio" name="method" value="last_year_target"> ใช้เป้าหมายจากปีที่แล้ว</label><br>
                    <label><input type="radio" name="method" value="actual_sales"> คำนวณจากยอดขายจริงของปีที่แล้ว</label><br>
                    <div class="button-group">
                        <button type="button" id="cancel-button" onclick="goBack()">ยกเลิก</button>
                        <button type="button" onclick="goToNextStep()">ถัดไป</button>
                    </div>
                </form>
            </div>
    
    <div id="data-entry" class="step hidden">
                <h2>กรอกข้อมูล</h2>
                <form method="POST" action="">
                    <label for="year">ปี:</label>
                    <select id="year" name="year">
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                    </select><br><br>

                    <label for="areazone">เลือก AreaZone:</label>
                    <select name="areazone_id" id="areazone">
                        <option value="">-- เลือก AreaZone --</option>
                        <?php while ($row = mysqli_fetch_assoc($areazones)) { ?>
                            <option value="<?php echo $row['areazone_id']; ?>">
                                <?php echo $row['areazone_name']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label for="branch_id">เลือกสาขา:</label>
                    <select id="branch_id" name="branch_id">
                        <option value="">-- เลือกสาขา --</option>
                    </select>

                    <div id="manual-entry" class="hidden">
                        <h3>กรอกเป้าหมายยอดขาย</h3>
                        <label for="annual_target">เป้าหมายยอดขายรายปี:</label>
                        <input type="number" id="annual_target" name="annual_target" step="0.01" value="12000" oninput="updateMonthlyTargets()"><br><br>

                        <h3>เป้าหมายยอดขายรายเดือน:</h3>
                        <table>
                            <tr>
                                <th>เดือน</th>
                                <th>เป้าหมายยอดขาย</th>
                            </tr>
                            <tr>
                                <td>มกราคม</td>
                                <td><input type="number" id="target_1" name="target_1" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>กุมภาพันธ์</td>
                                <td><input type="number" id="target_2" name="target_2" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>มีนาคม</td>
                                <td><input type="number" id="target_3" name="target_3" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>เมษายน</td>
                                <td><input type="number" id="target_4" name="target_4" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>พฤษภาคม</td>
                                <td><input type="number" id="target_5" name="target_5" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>มิถุนายน</td>
                                <td><input type="number" id="target_6" name="target_6" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>กรกฎาคม</td>
                                <td><input type="number" id="target_7" name="target_7" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>สิงหาคม</td>
                                <td><input type="number" id="target_8" name="target_8" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>กันยายน</td>
                                <td><input type="number" id="target_9" name="target_9" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>ตุลาคม</td>
                                <td><input type="number" id="target_10" name="target_10" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>พฤศจิกายน</td>
                                <td><input type="number" id="target_11" name="target_11" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                            <tr>
                                <td>ธันวาคม</td>
                                <td><input type="number" id="target_12" name="target_12" step="0.01" oninput="updateTotalTarget()" disabled></td>
                            </tr>
                        </table>
                        <div class="total">
                            <p>ยอดรวมเป้าหมายยอดขายรายปี: <span id="total-target">0</span></p>
                        </div>
                    </div>

                    <!-- Additional sections for other methods can be added here -->

                    <div class="button-group">
                        <button type="submit">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        
        function goToNextStep() {
            document.getElementById('method-selection').classList.add('hidden');
            document.getElementById('data-entry').classList.remove('hidden');

            const selectedMethod = document.querySelector('input[name="method"]:checked').value;

            document.getElementById('manual-entry').classList.toggle('hidden', selectedMethod !== 'manual');
        }

    
        function updateMonthlyTargets() {
            const annualTarget = parseFloat(document.getElementById('annual_target').value) || 0;
            const monthlyTargets = annualTarget / 12;
            const remainder = annualTarget % 12;

            for (let month = 1; month <= 12; month++) {
                const input = document.getElementById(`target_${month}`);
                if (input) {
                    // const value = (monthlyTargets);
                    const value = Math.floor((monthlyTargets + (month <= remainder ? +0.01 : 0)) * 100) / 100;
                    input.value = value.toFixed(2);
                    input.disabled = false;
                }
            }
            updateTotalTarget();
        }

        function updateTotalTarget() {
            let totalSum = 0;

            for (let month = 1; month <= 12; month++) {
                const input = document.getElementById(`target_${month}`);
                if (input) {
                    totalSum += parseFloat(input.value) || 0;
                }
            }

            document.getElementById('total-target').textContent = totalSum.toFixed(2);
        }

        function goBack() {
            window.history.back();
        }

        function monthToStr(month) {
            const months = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
            return months[month - 1];
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('areazone').addEventListener('change', function() {
                var areazone_id = this.value;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetch_branches.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('branch_id').innerHTML = xhr.responseText;
                    }
                };
                xhr.send('areazone_id=' + encodeURIComponent(areazone_id));
            });
        });

        document.getElementById('date').addEventListener('change', function() {
            var dateValue = new Date(this.value);
            var year = dateValue.getFullYear();
            document.getElementById('year').value = year;
        });
    </script>
</body>
</body>
</html>
