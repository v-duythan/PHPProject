<?php
require_once '../config/database.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['nhan_vien_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['nhan_vien_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
    } else {
        $sql = "SELECT nguoi_dung.mat_khau FROM nhan_vien JOIN nguoi_dung ON nguoi_dung.id = nhan_vien.nguoi_dung_id WHERE nhan_vien.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($current_password_hash);
        $stmt->fetch();
        $stmt->close();

        if (sha1($old_password) !== $current_password_hash) {
            $error = 'Mật khẩu cũ không đúng.';
        } else {
            $new_password_hash = sha1($new_password);
            $sql = "UPDATE nguoi_dung SET mat_khau = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password_hash, $user_id);
            if ($stmt->execute()) {
                $success = 'Đổi mật khẩu thành công.';
            } else {
                $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
            $stmt->close();
        }
    }
}
?>

<?php include '../includes/header.php';
if ($_SESSION['role'] == 'Admin') {
    include '../includes/admin_sidebar.php';
} else {
    include '../includes/user_sidebar.php';
}
?>
<style>
    .profile-content {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .profile-content .form-group {
        margin-bottom: 15px;
    }

    .profile-content label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .profile-content input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .profile-content .btn-submit {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .profile-content .btn-submit:hover {
        background-color: #0056b3;
    }

    .profile-content .error {
        color: #dc3545;
        margin-bottom: 15px;
    }

    .profile-content .success {
        color: #28a745;
        margin-bottom: 15px;
    }
</style>
<body>

<div class="container">
        <h2>Đổi Mật Khẩu</h2>
    <div class="profile-content">
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="change_password.php" method="post">
        <div class="form-group">
            <label for="old_password">Mật khẩu cũ:</label>
            <input type="password" id="old_password" name="old_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">Mật khẩu mới:</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Xác nhận mật khẩu mới:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-submit">Đổi Mật Khẩu</button>
    </form>
</div>
</div>
</body>
</html>