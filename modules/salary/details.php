<?php
// Kết nối đến cơ sở dữ liệu
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
session_start();
$vaitro = $_SESSION['role'];
// Lấy ID nhân viên từ URL
$nhan_vien_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra xem ID có hợp lệ không
if ($nhan_vien_id <= 0) {
    echo "ID không hợp lệ.";
    exit;
}

// Lấy năm hiện tại và tạo danh sách các năm
$current_year = date('Y');
$years = range($current_year - 1, $current_year); // Danh sách các năm từ năm trước đến năm hiện tại
$months = range(1, 12); // Danh sách các tháng từ 1 đến 12

// Mặc định là tháng 10 năm 2024
$month = isset($_POST['month']) ? intval($_POST['month']) : 10;
$year = isset($_POST['year']) ? intval($_POST['year']) : 2024;

// Truy vấn chi tiết lương nhân viên theo ID và tháng/năm
$sql = "
SELECT
    nhan_vien.ho_ten,
    nhan_vien.hinh_anh,
    phong_ban.ten_phong_ban,
    chuc_vu.ten_chuc_vu,
    CONCAT(luong.thang, ' - ', luong.nam) AS thang_nam_luong,
    luong.luong_co_ban,
    luong.phu_cap,
    luong.khoan_tru_khac AS khoan_tru,
    luong.thuong,
    luong.so_ngay_cong,
    luong.so_ngay_nghi_phep,
    luong.so_ngay_vang,
    luong.so_gio_lam_them,
    luong.luong_ngay_cong,
    luong.luong_lam_them,
    luong.bao_hiem,
    luong.tong_luong
FROM nhan_vien
JOIN luong ON nhan_vien.id = luong.nhan_vien_id
JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
WHERE nhan_vien.id = $nhan_vien_id
AND luong.thang = $month
AND luong.nam = $year
";

$result = $conn->query($sql);

// Kiểm tra xem có dữ liệu không
if ($result->num_rows == 0) {
    echo "Không tìm thấy dữ liệu.";
    exit;
}

$row = $result->fetch_assoc(); // Lấy dữ liệu chi tiết
?>
<?php include '../../includes/header.php'; ?>


<?php
if ($vaitro == 'Admin') {
    include '../../includes/admin_sidebar.php';
} else {
    include '../../includes/user_sidebar.php';
}
?>

    <main class="container">
        <h1>Chi Tiết Lương Nhân Viên</h1>

        <!-- Form để lọc theo tháng và năm -->
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

        <!-- Hiển thị hình ảnh nhân viên -->
        <div>
            <img src="<?php echo BASE_URL . 'assets/images/' . htmlspecialchars($row['hinh_anh']); ?>" alt="Hình Ảnh Nhân Viên" style="width: 150px; height: auto;">
        </div>

        <table>
            <tr>
                <th>Tên Nhân Viên:</th>
                <td><?= htmlspecialchars($row['ho_ten']) ?></td>
            </tr>
            <tr>
                <th>Phòng Ban:</th>
                <td><?= htmlspecialchars($row['ten_phong_ban']) ?></td>
            </tr>
            <tr>
                <th>Chức Vụ:</th>
                <td><?= htmlspecialchars($row['ten_chuc_vu']) ?></td>
            </tr>
            <tr>
                <th>Tháng - Năm Lương:</th>
                <td><?= htmlspecialchars($row['thang_nam_luong']) ?></td>
            </tr>
            <tr>
                <th>Lương Cơ Bản:</th>
                <td><?= number_format($row['luong_co_ban'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Phụ Cấp:</th>
                <td><?= number_format($row['phu_cap'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Khấu Trừ:</th>
                <td><?= number_format($row['khoan_tru'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Thưởng:</th>
                <td><?= number_format($row['thuong'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Số Ngày Công:</th>
                <td><?= $row['so_ngay_cong'] ?></td>
            </tr>
            <tr>
                <th>Số Ngày Nghỉ Phép:</th>
                <td><?= $row['so_ngay_nghi_phep'] ?></td>
            </tr>
            <tr>
                <th>Số Ngày Vắng:</th>
                <td><?= $row['so_ngay_vang'] ?></td>
            </tr>
            <tr>
                <th>Số Giờ Làm Thêm:</th>
                <td><?= $row['so_gio_lam_them'] ?></td>
            </tr>
            <tr>
                <th>Lương Ngày Công:</th>
                <td><?= number_format($row['luong_ngay_cong'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Lương Làm Thêm:</th>
                <td><?= number_format($row['luong_lam_them'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Bảo Hiểm:</th>
                <td><?= number_format($row['bao_hiem'], 0, ',', '.') ?> VND</td>
            </tr>
            <tr>
                <th>Tổng Thu Nhập:</th>
                <td><?= number_format($row['tong_luong'], 0, ',', '.') ?> VND</td>
            </tr>
        </table>
        <a href="list.php" class="button" style="margin-top: 20px; background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 5px;">Quay lại danh sách</a>
    </main>

<?php include '../../includes/footer.php'; ?>

    </body>
    </html>

<?php
$conn->close();
?>