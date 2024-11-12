<?php
session_start(); // Bắt đầu session
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';

// Kiểm tra xem có ID phòng ban để xóa không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Kiểm tra xem có nhân viên nào thuộc phòng ban này không
    $check_sql = "SELECT COUNT(*) AS count FROM nhan_vien INNER JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id WHERE chuc_vu.phong_ban_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    // Nếu có nhân viên thuộc phòng ban, không cho phép xóa
    if ($count > 0) {
        $_SESSION['message'] = "Không thể xóa phòng ban vì vẫn còn nhân viên thuộc phòng ban này.";
    } else {
        // Xóa phòng ban nếu không có nhân viên liên kết
        $sql = "DELETE FROM phong_ban WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Phòng ban đã được xóa thành công.";
        } else {
            $_SESSION['message'] = "Lỗi: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    $_SESSION['message'] = "ID phòng ban không hợp lệ.";
}

$conn->close();

// Quay lại trang danh sách phòng ban
header("Location: list.php");
exit;
?>