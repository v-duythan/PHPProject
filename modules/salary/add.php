<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';


$employees_sql = "SELECT id, ho_ten, luong_thoa_thuan FROM nhan_vien";
$employees_result = $conn->query($employees_sql);



// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nhan_vien_id = intval($_POST['nhan_vien_id']);
    $thang = intval($_POST['thang']);
    $nam_luong = intval($_POST['nam_luong']);
    $luong_cung = floatval($_POST['luong_cung']);
    $phu_cap = floatval($_POST['phu_cap']);
    $khoan_tru = floatval($_POST['khoan_tru']);
    $tien_thuong = floatval($_POST['tien_thuong']);

    // Kiểm tra xem đã có lương cho nhân viên này trong tháng và năm đã chọn chưa
    $check_sql = "SELECT COUNT(*) AS count FROM luong WHERE nhan_vien_id = ? AND thang = ? AND nam_luong = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iii", $nhan_vien_id, $thang, $nam_luong);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Nếu lương đã tồn tại cho tháng và năm này, hiển thị thông báo lỗi
        echo "<script>alert('Lương cho nhân viên này trong tháng và năm đã chọn đã tồn tại. Vui lòng chọn tháng/năm khác.');</script>";
    } else {
        // Thêm lương vào cơ sở dữ liệu nếu không trùng lặp
        $sql = "INSERT INTO luong (nhan_vien_id, thang, nam_luong, luong_cung, phu_cap, khoan_tru, tien_thuong) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidddd", $nhan_vien_id, $thang, $nam_luong, $luong_cung, $phu_cap, $khoan_tru, $tien_thuong);

        if ($stmt->execute()) {
            echo "<script>alert('Lương đã được thêm thành công.'); window.location.href = 'list.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<main class="content">
    <h1>Thêm Lương Nhân Viên</h1>

    <form action="add.php" method="POST">
        <table>
            <tr>
                <td><label for="nhan_vien_id">Nhân Viên:</label></td>
                <td>
                    <select name="nhan_vien_id" id="nhan_vien_id" onchange="updateLuongCoBan()" required>
                        <option value="" data-luong-thoa-thuan="">Chọn nhân viên</option>
                        <?php while ($employee = $employees_result->fetch_assoc()): ?>
                            <option value="<?= $employee['id'] ?>" data-luong-thoa-thuan="<?= $employee['luong_thoa_thuan'] ?>">
                                <?= htmlspecialchars($employee['ho_ten']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="thang">Tháng:</label></td>
                <td>
                    <select name="thang" id="thang" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>"><?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="nam_luong">Năm:</label></td>
                <td><input type="number" name="nam_luong" id="nam_luong" min="2000" max="<?= date('Y') ?>" required></td>
            </tr>
            <tr>
                <td><label for="luong_cung">Lương Cơ Bản:</label></td>
                <td><input type="number" name="luong_cung" id="luong_cung" step="0.01" required></td>
            </tr>
            <tr>
                <td><label for="phu_cap">Phụ Cấp:</label></td>
                <td><input type="number" name="phu_cap" id="phu_cap" step="0.01"></td>
            </tr>
            <tr>
                <td><label for="khoan_tru">Khoản Trừ:</label></td>
                <td><input type="number" name="khoan_tru" id="khoan_tru" step="0.01"></td>
            </tr>
            <tr>
                <td><label for="tien_thuong">Tiền Thưởng:</label></td>
                <td><input type="number" name="tien_thuong" id="tien_thuong" step="0.01"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Thêm Lương</button>
                </td>
            </tr>
        </table>
    </form>
</main>

<script>
    function updateLuongCoBan() {
        const nhanVienSelect = document.getElementById('nhan_vien_id');
        const luongCoBanInput = document.getElementById('luong_cung');
        const selectedOption = nhanVienSelect.options[nhanVienSelect.selectedIndex];
        const luongThoaThuan = selectedOption.getAttribute('data-luong-thoa-thuan');


        if (luongThoaThuan) {
            luongCoBanInput.value = parseFloat(luongThoaThuan).toFixed(2);
        } else {
            luongCoBanInput.value = '';
        }
    }
</script>


<?php include '../../includes/footer.php'; ?>
</body>
</html>
