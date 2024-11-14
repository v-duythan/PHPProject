<?php
session_start();
require_once '../config/database.php'; // Kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mã hóa mật khẩu bằng SHA-1 để so sánh với cơ sở dữ liệu
    $hashed_password = sha1($password);

    // Kiểm tra xem kết nối có được thiết lập thành công hay không
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Truy vấn người dùng chỉ bằng `ten_dang_nhap`
    $sql = "SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($hashed_password === $user['mat_khau']) {
                // Secure session management
                session_regenerate_id(true);

                $_SESSION['username'] = htmlspecialchars($user['ten_dang_nhap']);
                $_SESSION['role'] = htmlspecialchars($user['vai_tro']);

                $nguoi_dung_id = $user['id'];
                $query = "SELECT id FROM nhan_vien WHERE nguoi_dung_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $nguoi_dung_id);
                $stmt->execute();
                $stmt->bind_result($nhan_vien_id);
                $stmt->fetch();
                $_SESSION['nhan_vien_id'] = $nhan_vien_id;
                $stmt->close();

                header("Location: index.php");
                exit();
            } else {
                $error = "Mật khẩu không đúng!";
            }
        } else {
            $error = "Không tìm thấy tài khoản với tên đăng nhập này.";
        }

        $stmt->close();
    } else {
        $error = "Có lỗi xảy ra trong quá trình chuẩn bị truy vấn.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../includes/css/login_style.css">
</head>
<body>
<div class="login-container">
    <h2>Đăng nhập</h2>
    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
    <form action="login.php" method="POST">
        <label for="username">Tên đăng nhập</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Đăng nhập</button>
    </form>
</div>
</body>
</html>
