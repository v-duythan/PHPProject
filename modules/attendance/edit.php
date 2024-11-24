<?php
require_once '../../config/database.php';
session_start();

// Kiểm tra xem có ID chấm công để sửa không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin chấm công hiện tại
    $sql = "SELECT * FROM cham_cong WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();
    $stmt->close();

    if (!$attendance) {
        echo "<script>alert('Không tìm thấy bản ghi chấm công.'); window.location.href = 'list.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID chấm công không hợp lệ.'); window.location.href = 'list.php';</script>";
    exit();
}

// Xử lý cập nhật chấm công
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ngay = $_POST['ngay'];
    $gio_vao = $_POST['gio_vao'];
    $gio_ra = $_POST['gio_ra'];
    $trang_thai = $_POST['trang_thai'];
    $ly_do_vang_mat = $_POST['ly_do_vang_mat'];
    $ghi_chu = $_POST['ghi_chu'];

    $update_sql = "UPDATE cham_cong SET ngay = ?, gio_vao = ?, gio_ra = ?, trang_thai = ?, ly_do_vang_mat = ?, ghi_chu = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssi", $ngay, $gio_vao, $gio_ra, $trang_thai, $ly_do_vang_mat, $ghi_chu, $id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Cập nhật chấm công thành công.'); window.location.href = 'list.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $update_stmt->error . "'); window.location.href = 'edit.php?id=$id';</script>";
    }

    $update_stmt->close();
}

include_once __DIR__ . '/../../config/config.php';
?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chỉnh sửa chấm công</title>
        <link rel="stylesheet" href="attendance.css"> <!-- Đường dẫn tới tệp CSS -->
    </head>
    <body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/admin_sidebar.php'; ?>

    <div class="container">
        <h2>Chỉnh sửa chấm công</h2>
        <form method="POST" action="">
            <label for="ngay">Ngày:</label>
            <input type="date" name="ngay" id="ngay" value="<?= htmlspecialchars($attendance['ngay']) ?>" required>

            <label for="gio_vao">Giờ vào:</label>
            <input type="time" name="gio_vao" id="gio_vao" value="<?= htmlspecialchars($attendance['gio_vao']) ?>">

            <label for="gio_ra">Giờ ra:</label>
            <input type="time" name="gio_ra" id="gio_ra" value="<?= htmlspecialchars($attendance['gio_ra']) ?>">

            <label for="trang_thai">Trạng thái:</label>
            <select name="trang_thai" id="trang_thai" required>
                <option value="CóMặt" <?= $attendance['trang_thai'] == 'CóMặt' ? 'selected' : '' ?>>Đi làm</option>
                <option value="VắngMặt" <?= $attendance['trang_thai'] == 'VắngMặt' ? 'selected' : '' ?>>Vắng mặt</option>
                <option value="NghỉPhép" <?= $attendance['trang_thai'] == 'NghỉPhép' ? 'selected' : '' ?>>Nghỉ phép</option>
            </select>

            <label for="ly_do_vang_mat">Lý do vắng mặt:</label>
            <input type="text" name="ly_do_vang_mat" id="ly_do_vang_mat" value="<?= htmlspecialchars($attendance['ly_do_vang_mat']) ?>">

            <label for="ghi_chu">Ghi chú:</label>
            <textarea name="ghi_chu" id="ghi_chu"><?= htmlspecialchars($attendance['ghi_chu']) ?></textarea>

            <button type="submit">Cập nhật</button>
        </form>
    </div>
    </body>
    </html>

<?php
$conn->close();
?>