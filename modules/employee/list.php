<?php
// Start session and check if user is logged in
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once '../../config/database.php';

// SQL query to fetch employee data
$sql = '
    SELECT nhan_vien.id, nhan_vien.ho_ten, nhan_vien.email, nhan_vien.so_dien_thoai, nhan_vien.dia_chi, 
           nhan_vien.ngay_vao_lam, nguoi_dung.ten_dang_nhap, 
           phong_ban.ten_phong_ban AS phong_ban, chuc_vu.ten_chuc_vu AS chuc_vu
    FROM nhan_vien
    JOIN nguoi_dung ON nhan_vien.nguoi_dung_id = nguoi_dung.id
    JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
    JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
';


$result = $conn->query($sql);

include_once __DIR__ . '/../../config/config.php';
?>

<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>

<main class="content">
    <h1>Quản lý nhân viên</h1>

    <table>
        <tr>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Địa chỉ</th>
            <th>Phòng ban</th>
            <th>Chức vụ</th>
            <th>Ngày vào làm</th>
            <th>Tên đăng nhập</th>
            <th>Hành động</th>
            <th>Chi tiết lương</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['ho_ten']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['so_dien_thoai']) . "</td>
                    <td>" . htmlspecialchars($row['dia_chi']) . "</td>
                    <td>" . htmlspecialchars($row['phong_ban']) . "</td>
                    <td>" . htmlspecialchars($row['chuc_vu']) . "</td>
                    <td>" . htmlspecialchars($row['ngay_vao_lam']) . "</td>
                    <td>" . htmlspecialchars($row['ten_dang_nhap']) . "</td>
                    <td>
                        <a href='edit.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a> |
                        <a href='delete.php?id=" . htmlspecialchars($row['id']) . "' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                    </td>
                    <td>
                        <a href='../salary/details.php?id=" . htmlspecialchars($row['id']) . "'>View Salary</a>
                    </td>
                  </tr>";
            }
        } else {
            echo "<tr><td colspan='10'>0 results</td></tr>";
        }
        ?>
    </table>
</main>

<!-- Include footer -->
<?php include '../../includes/footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
