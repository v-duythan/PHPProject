<?php
// Kết nối tới cơ sở dữ liệu
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
session_start();

// Lấy id nhân viên từ URL
$nhan_vien_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra id hợp lệ
if ($nhan_vien_id <= 0) {
    echo "ID không hợp lệ.";
    exit;
}

// Lấy năm hiện tại và tạo danh sách năm
$current_year = date('Y');
$years = range($current_year - 1, $current_year); // Danh sách năm từ 5 năm trước đến hiện tại
$months = range(1, 12); // Danh sách tháng từ 1 đến 12

// Mặc định là tháng 10, năm 2024
$month = isset($_POST['month']) ? intval($_POST['month']) : 10;
$year = isset($_POST['year']) ? intval($_POST['year']) : 2024;

// Truy vấn chi tiết lương của nhân viên theo id và tháng/năm
$sql = "
SELECT
    nhan_vien.ho_ten,
    nhan_vien.hinh_anh,
    phong_ban.ten_phong_ban,
    chuc_vu.ten_chuc_vu,
    CONCAT(luong.thang, ' - ', luong.nam_luong) AS thang_nam_luong,
    luong.luong_cung,
    luong.phu_cap,
    luong.khoan_tru,
    luong.tien_thuong
FROM nhan_vien
JOIN luong ON nhan_vien.id = luong.nhan_vien_id
JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
WHERE nhan_vien.id = $nhan_vien_id
AND luong.thang = $month
AND luong.nam_luong = $year
";

$result = $conn->query($sql);

// Kiểm tra có dữ liệu hay không
if ($result->num_rows == 0) {
    echo "Không tìm thấy dữ liệu.";
    exit;
}

$row = $result->fetch_assoc(); // Lấy dữ liệu chi tiết
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<main class="container">
    <h1>Chi tiết Lương Nhân Viên</h1>

    <!-- Form lọc tháng và năm -->
    <form method="POST" action="">
        <label for="month">Tháng:</label>
        <select name="month" id="month">
            <?php foreach ($months as $m): ?>
                <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>

        <label for="year">Năm:</label>
        <select name="year" id="year">
            <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Lọc</button>
    </form>

    <!-- Hiển thị ảnh nhân viên -->
    <div>
        <img src="<?php echo BASE_URL . 'assets/images/' . htmlspecialchars($row['hinh_anh']); ?>" alt="Ảnh nhân viên" style="width: 150px; height: auto;">
    </div>

    <table>
        <tr>
            <th>Tên nhân viên:</th>
            <td><?= htmlspecialchars($row['ho_ten']) ?></td>
        </tr>
        <tr>
            <th>Phòng ban:</th>
            <td><?= htmlspecialchars($row['ten_phong_ban']) ?></td>
        </tr>
        <tr>
            <th>Chức vụ:</th>
            <td><?= htmlspecialchars($row['ten_chuc_vu']) ?></td>
        </tr>
        <tr>
            <th>Tháng - Năm lương:</th>
            <td><?= htmlspecialchars($row['thang_nam_luong']) ?></td>
        </tr>
        <tr>
            <th>Lương cơ bản:</th>
            <td><?= number_format($row['luong_cung'], 0, ',', '.') ?> VNĐ</td>
        </tr>
        <tr>
            <th>Phụ cấp:</th>
            <td><?= number_format($row['phu_cap'], 0, ',', '.') ?> VNĐ</td>
        </tr>
        <tr>
            <th>Khoản trừ:</th>
            <td><?= number_format($row['khoan_tru'], 0, ',', '.') ?> VNĐ</td>
        </tr>
        <tr>
            <th>Lương thưởng:</th>
            <td><?= number_format($row['tien_thuong'], 0, ',', '.') ?> VNĐ</td>
        </tr>
        <tr>
            <th>Tổng thu nhập:</th>
            <td>
                <?php
                $tong_thu_nhap = $row['luong_cung'] + $row['phu_cap'] + $row['tien_thuong'] - $row['khoan_tru'];
                echo number_format($tong_thu_nhap, 0, ',', '.') . " VNĐ";
                ?>
            </td>
        </tr>
    </table>

    <a href="list.php">Quay lại danh sách</a>
</main>

<?php include '../../includes/footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
