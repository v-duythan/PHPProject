<?php
require_once '../../config/database.php'; // Kết nối tới cơ sở dữ liệu
session_start();

// Kiểm tra người dùng đã đăng nhập chưa (nếu cần thiết)
if (!isset($_SESSION['nhan_vien_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nhan_vien_id = $_POST['nhan_vien_id'];
    $ngay = $_POST['ngay'];
    $trang_thai = $_POST['trang_thai'];
    $gio_vao = $_POST['gio_vao'] ?: NULL;
    $gio_ra = $_POST['gio_ra'] ?: NULL;
    $ly_do = $_POST['ly_do'] ?: NULL;
    $ghi_chu = $_POST['ghi_chu'] ?: NULL;

    $sql = "INSERT INTO cham_cong (nhan_vien_id, ngay, trang_thai, gio_vao, gio_ra, ly_do_vang_mat, ghi_chu)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issssss", $nhan_vien_id, $ngay, $trang_thai, $gio_vao, $gio_ra, $ly_do, $ghi_chu);
        $stmt->execute();
        header("Location: list.php");
        exit();
    } else {
        $error = "Có lỗi xảy ra khi thêm bản ghi.";
    }
}
$today = date('Y-m-d');
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<!-- Container chính, chiếm phần còn lại -->
<div class="container">
    <h2>Thêm Bản Ghi Chấm Công</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <!-- Form thêm chấm công -->
    <form method="POST" id="attendanceForm">
        <label for="nhan_vien_id">Nhân viên</label>
        <select name="nhan_vien_id" id="nhan_vien_id" required>
            <option value="">-- Chọn nhân viên --</option>
            <?php
            $result = $conn->query("SELECT id, ho_ten FROM nhan_vien ORDER BY ho_ten ASC");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['ho_ten']}</option>";
            }
            ?>
        </select>

        <label for="chuc_vu">Chức vụ</label>
        <input type="text" id="chuc_vu" name="chuc_vu" readonly>

        <label for="phong_ban">Phòng ban</label>
        <input type="text" id="phong_ban" name="phong_ban" readonly>

        <label for="ngay">Ngày</label>
        <input type="date" name="ngay" value="<?php echo $today; ?>" required>

        <label for="trang_thai">Trạng thái</label>
        <select name="trang_thai" id="trang_thai" required>
            <option value="CóMặt">Có mặt</option>
            <option value="VắngMặt">Vắng mặt</option>
            <option value="NghỉPhép">Nghỉ phép</option>
        </select>

        <label for="gio_vao">Giờ vào</label>
        <input type="time" name="gio_vao" id="gio_vao">

        <label for="gio_ra">Giờ ra</label>
        <input type="time" name="gio_ra" id="gio_ra">

        <label for="ly_do">Lý do vắng mặt (nếu có)</label>
        <textarea name="ly_do" id="ly_do"></textarea>

        <label for="ghi_chu">Ghi chú</label>
        <textarea name="ghi_chu"></textarea>

        <button type="submit">Thêm</button>
    </form>
</div>

<script>
    document.getElementById('nhan_vien_id').addEventListener('change', function() {
        var nhanVienId = this.value;
        if (nhanVienId) {
            fetch('get_employee_details.php?nhan_vien_id=' + nhanVienId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.getElementById('chuc_vu').value = '';
                        document.getElementById('phong_ban').value = '';
                    } else {
                        document.getElementById('chuc_vu').value = data.ten_chuc_vu;
                        document.getElementById('phong_ban').value = data.ten_phong_ban;
                    }
                });
        } else {
            document.getElementById('chuc_vu').value = '';
            document.getElementById('phong_ban').value = '';
        }
    });

    document.getElementById('attendanceForm').addEventListener('submit', function(event) {
        var trangThai = document.getElementById('trang_thai').value;
        var gioVao = document.getElementById('gio_vao').value;
        var gioRa = document.getElementById('gio_ra').value;
        var lyDo = document.getElementById('ly_do').value;

        if (
            (trangThai === 'CóMặt' && (!gioVao || !gioRa)) ||
            (trangThai === 'VắngMặt' && !lyDo) ||
            (trangThai === 'CóMặt' && lyDo) ||
            ((trangThai === 'VắngMặt' || trangThai === 'NghỉPhép') && (gioVao || gioRa))
        ) {
            alert(
                trangThai === 'CóMặt' && (!gioVao || !gioRa) ? 'Khi có mặt, phải có thời gian ra vào.' :
                    trangThai === 'VắngMặt' && !lyDo ? 'Khi vắng mặt, phải có lý do.' :
                        trangThai === 'CóMặt' && lyDo ? 'Khi có mặt, không được điền lý do vắng mặt.' :
                            'Khi vắng mặt hoặc nghỉ phép, không được điền giờ ra vào.'
            );
            event.preventDefault();
        }

    });
</script>

</body>
</html>