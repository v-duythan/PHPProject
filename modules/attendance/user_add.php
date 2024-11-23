<?php
require_once '../../config/database.php'; // Kết nối tới cơ sở dữ liệu
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['nhan_vien_id'])) {
    header("Location: ../../login.php");
    exit();
}

$nhan_vien_id = $_SESSION['nhan_vien_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/user_sidebar.php'; ?>

<!-- Container chính, chiếm phần còn lại -->
<div class="container">
    <h2>Thêm Bản Ghi Chấm Công</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>

    <!-- Form thêm chấm công -->
    <form method="POST">
        <label for="ngay">Ngày</label>
        <input type="date" name="ngay" required>

        <label for="trang_thai">Trạng thái</label>
        <select name="trang_thai" required>
            <option value="CóMặt">Có mặt</option>
            <option value="VắngMặt">Vắng mặt</option>
            <option value="NghỉPhép">Nghỉ phép</option>
        </select>

        <label for="gio_vao">Giờ vào</label>
        <input type="time" name="gio_vao">

        <label for="gio_ra">Giờ ra</label>
        <input type="time" name="gio_ra">

        <label for="ly_do">Lý do vắng mặt (nếu có)</label>
        <textarea name="ly_do"></textarea>

        <label for="ghi_chu">Ghi chú</label>
        <textarea name="ghi_chu"></textarea>

        <button type="submit">Thêm</button>
    </form>
</div>

</body>
</html>