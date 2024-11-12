<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';

// Kiểm tra nếu có dữ liệu từ form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // ID của phòng ban
    $ten_phong_ban = trim($_POST['ten_phong_ban']);
    $mo_ta = trim($_POST['mo_ta']);
    $truong_phong = intval($_POST['truong_phong_id']); // ID của trưởng phòng mới
    $ngay_thanh_lap = $_POST['ngay_thanh_lap'];

    // Bắt đầu giao dịch
    $conn->begin_transaction();

    try {
        // 1. Cập nhật thông tin phòng ban và trường trưởng phòng
        $sql_update_department = "
            UPDATE phong_ban 
            SET ten_phong_ban = ?, mo_ta = ?, ngay_thanh_lap = ?, truong_phong_id = ? 
            WHERE id = ?";
        $stmt_update_department = $conn->prepare($sql_update_department);
        $stmt_update_department->bind_param("sssii", $ten_phong_ban, $mo_ta, $ngay_thanh_lap, $truong_phong, $id);

        if (!$stmt_update_department->execute()) {
            throw new Exception("Lỗi cập nhật phòng ban: " . $stmt_update_department->error);
        }

        // Commit giao dịch nếu tất cả đều thành công
        $conn->commit();

        // Thông báo thành công
        echo "Thông tin phòng ban đã được cập nhật thành công.";
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    } finally {
        // Đóng statement và kết nối
        if (isset($stmt_update_department)) $stmt_update_department->close();
        $conn->close();
    }

    // Chuyển hướng về trang danh sách phòng ban
    header("Location: list.php");
    exit;
}
?>
