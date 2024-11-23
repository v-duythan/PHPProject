<?php
session_start();
require_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Set username and role variables
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

    <main class="container">

        <?php
        if ($role == 'Admin') {
            include '../includes/admin_dashboard.php';
        } else {
            $sql = "SELECT nhan_vien.ho_ten, nhan_vien.email, nhan_vien.so_dien_thoai, nhan_vien.ngay_vao_lam, chuc_vu.ten_chuc_vu AS chuc_vu,
    CONCAT_WS(', ', dia_chi.so_nha, wards.full_name, districts.full_name, provinces.full_name) AS dia_chi
FROM nhan_vien
JOIN nguoi_dung ON nhan_vien.nguoi_dung_id = nguoi_dung.id
JOIN chuc_vu ON chuc_vu.id = nhan_vien.chuc_vu_id
LEFT JOIN dia_chi ON nhan_vien.id = dia_chi.nhan_vien_id
LEFT JOIN wards ON dia_chi.phuong_xa_id = wards.code
LEFT JOIN districts ON dia_chi.quan_huyen_id = districts.code
LEFT JOIN provinces ON dia_chi.tinh_thanh_id = provinces.code
WHERE nguoi_dung.ten_dang_nhap = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='employee-info'>";
                    echo "<h2>Thông tin nhân viên</h2>";
                    echo "<p><strong>Tên:</strong> " . htmlspecialchars($row["ho_ten"]) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                    echo "<p><strong>Số điện thoại:</strong> " . htmlspecialchars($row["so_dien_thoai"]) . "</p>";
                    echo "<p><strong>Địa chỉ:</strong> " . htmlspecialchars($row["dia_chi"]) . "</p>";
                    echo "<p><strong>Ngày sinh:</strong> " . htmlspecialchars($row["ngay_vao_lam"]) . "</p>";
                    echo "<p><strong>Chức vụ:</strong> " . htmlspecialchars($row["chuc_vu"]) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Không có thông tin nhân viên.</p>";
            }

            $stmt->close();
            $conn->close();

        }


        ?>
    </main>

<?php include '../includes/footer.php'; ?>