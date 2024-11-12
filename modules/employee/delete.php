<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';

// Kiểm tra xem có ID nhân viên để xóa không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Kiểm tra xem nhân viên có phải là trưởng phòng của bất kỳ phòng ban nào không
    $check_sql = "SELECT COUNT(*) AS count FROM phong_ban WHERE truong_phong_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Hiển thị thông báo nếu nhân viên là trưởng phòng
        echo "<script>alert('Không thể xóa nhân viên vì họ đang là trưởng phòng của một phòng ban.'); window.location.href = 'list.php';</script>";
    } else {
        // Xóa nhân viên nếu không là trưởng phòng
        $sql = "DELETE FROM nhan_vien WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Nhân viên đã được xóa thành công.'); window.location.href = 'list.php';</script>";
        } else {
            echo "<script>alert('Lỗi: " . $stmt->error . "'); window.location.href = 'list.php';</script>";
        }

        $stmt->close();
    }
} else {
    echo "<script>alert('ID nhân viên không hợp lệ.'); window.location.href = 'list.php';</script>";
}

$conn->close();
?>
