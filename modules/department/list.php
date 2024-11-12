<?php
// Bắt đầu session và kiểm tra xem người dùng đã đăng nhập hay chưa
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION['message'])) {
    echo "<div class='alert'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
}
// Kết nối tới cơ sở dữ liệu
include '../../config/database.php';

$sql = "
    SELECT 
        phong_ban.id,
        phong_ban.ten_phong_ban,
        phong_ban.mo_ta,
        phong_ban.ngay_thanh_lap,
        nhan_vien_truong.ho_ten AS truong_phong,
        COUNT(nhan_vien_phong.id) AS so_luong_nhan_vien
    FROM phong_ban
    -- Lấy tên trưởng phòng từ truong_phong_id
    LEFT JOIN nhan_vien AS nhan_vien_truong ON phong_ban.truong_phong_id = nhan_vien_truong.id
    -- Đếm tất cả nhân viên trong phòng ban thông qua bảng chuc_vu
    LEFT JOIN chuc_vu ON chuc_vu.phong_ban_id = phong_ban.id
    LEFT JOIN nhan_vien AS nhan_vien_phong ON nhan_vien_phong.chuc_vu_id = chuc_vu.id
    GROUP BY phong_ban.id
";

$result = $conn->query($sql);

include_once __DIR__ . '/../../config/config.php';
?>

<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>

<main class="content">
    <h1>Danh Sách Phòng Ban</h1>

    <?php
    // Hiển thị danh sách phòng ban
    if ($result->num_rows > 0) {
        echo "<table border='1'>
            <tr>
                <th>Tên phòng ban</th>
                <th>Mô tả</th>
                <th>Ngày thành lập</th>
                <th>Trưởng phòng</th>
                <th>Số lượng nhân viên</th>
                <th>Actions</th>
            </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . htmlspecialchars($row["ten_phong_ban"]) . "</td>
                <td>" . htmlspecialchars($row["mo_ta"]) . "</td>
                <td>" . htmlspecialchars($row["ngay_thanh_lap"]) . "</td>
                <td>" . htmlspecialchars($row["truong_phong"]) . "</td>
                <td>" . htmlspecialchars($row["so_luong_nhan_vien"]) . "</td>
                <td>
                    <a href='edit.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a> |
                    <a href='delete.php?id=" . htmlspecialchars($row['id']) . "' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                </td>
              </tr>";
        }
        echo "</table>";
    } else {
        echo "Không có phòng ban nào";
    }
    ?>
</main>

<!-- Bao gồm footer -->
<?php include '../../includes/footer.php'; ?>

</body>
</html>

<?php
// Đóng kết nối
$conn->close();
?>
