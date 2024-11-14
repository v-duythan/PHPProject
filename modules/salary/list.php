<?php
// Kết nối tới cơ sở dữ liệu
include '../../config/database.php';
session_start();

// Mặc định chọn năm và tháng nếu không có giá trị tìm kiếm
$nam_luong = isset($_GET['nam_luong']) ? $_GET['nam_luong'] : '2024';
$thang = isset($_GET['thang']) ? $_GET['thang'] : '10';

// Truy vấn danh sách lương của nhân viên với bộ lọc năm và tháng
$sql = "
    SELECT 
        nhan_vien.id,
        nhan_vien.ho_ten,
        phong_ban.ten_phong_ban,
        chuc_vu.ten_chuc_vu,
        luong.luong_cung,
        luong.phu_cap,
        luong.khoan_tru,
        luong.tien_thuong
    FROM nhan_vien
    JOIN luong ON nhan_vien.id = luong.nhan_vien_id
    JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
    JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
    WHERE luong.nam_luong = '$nam_luong' AND luong.thang = '$thang'
";

$result = $conn->query($sql);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<main class="content">
    <h1>Danh sách Lương</h1>

    <!-- Hiển thị thông tin tháng/năm lương đang hiển thị -->
    <h2>Tháng <?= $thang ?>, Năm <?= $nam_luong ?></h2>

    <!-- Form tìm kiếm -->
    <form method="GET" action="">
        <label for="nam_luong">Chọn năm:</label>
        <select id="nam_luong" name="nam_luong">
            <option value="2024" <?= ($nam_luong == '2024') ? 'selected' : '' ?>>2024</option>
            <option value="2023" <?= ($nam_luong == '2023') ? 'selected' : '' ?>>2023</option>
            <option value="2022" <?= ($nam_luong == '2022') ? 'selected' : '' ?>>2022</option>
        </select>

        <label for="thang">Chọn tháng:</label>
        <select id="thang" name="thang">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= ($thang == $i) ? 'selected' : '' ?>>Tháng <?= $i ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Tìm kiếm</button>
    </form>

    <table>
        <tr>
            <th>Tên nhân viên</th>
            <th>Chức vụ</th>
            <th>Phòng ban</th>
            <th>Tổng thu nhập</th>
            <th>Thao tác</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Tính tổng thu nhập từ các thành phần lương
                $tong_thu_nhap = $row['luong_cung'] + $row['phu_cap'] + $row['tien_thuong'] - $row['khoan_tru'];

                echo "<tr>
                    <td>" . htmlspecialchars($row['ho_ten']) . "</td>
                    <td>" . htmlspecialchars($row['ten_chuc_vu']) . "</td>
                    <td>" . htmlspecialchars($row['ten_phong_ban']) . "</td>
                    <td>" . number_format($tong_thu_nhap, 0, ',', '.') . " VNĐ</td>
                    <td>
                        <a href='details.php?id=" . htmlspecialchars($row['id']) . "'>Xem chi tiết</a>
                    </td>
                  </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
        }
        ?>
    </table>
</main>

<?php include '../../includes/footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
