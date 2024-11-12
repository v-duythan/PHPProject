<?php
session_start();
require_once '../config/database.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    header("Location: login.php");
    exit();
}

// Đặt tên người dùng và vai trò vào biến
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<?php include '../includes/header.php'; ?>


<?php
if ($role == 'Admin') {
    include '../includes/admin_sidebar.php';
} else {
    include '../includes/user_sidebar.php';
}
?>

    <main class="content">
        <?php
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $sql = "SELECT nhan_vien.ho_ten, nhan_vien.email, nhan_vien.so_dien_thoai 
            FROM nhan_vien
            JOIN nguoi_dung ON nhan_vien.nguoi_dung_id = nguoi_dung.id 
            WHERE nguoi_dung.ten_dang_nhap = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<p>Tên: " . $row["ho_ten"] . "</p>";
                echo "<p>Email: " . $row["email"] . "</p>";
                echo "<p>Số điện thoại: " . $row["so_dien_thoai"] . "</p>";
            }
        } else {
            echo "<p>Không có thông tin nhân viên.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </main>

<?php include '../includes/footer.php'; ?>