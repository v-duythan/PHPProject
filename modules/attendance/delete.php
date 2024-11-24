<?php
require_once '../../config/database.php';
session_start();

// Kiểm tra xem có ID chấm công để xóa không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Xóa bản ghi chấm công
    $sql = "DELETE FROM cham_cong WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Bản ghi chấm công đã được xóa thành công.'); window.location.href = 'list.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . $stmt->error . "'); window.location.href = 'list.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID chấm công không hợp lệ.'); window.location.href = 'list.php';</script>";
}

$conn->close();
?>