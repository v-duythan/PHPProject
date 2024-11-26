<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $nhan_vien_id = intval($_GET['id']);

    // Bắt đầu giao dịch
    $conn->begin_transaction();

    try {
        // Lấy ID người dùng từ nhân viên
        $sql_get_user_id = "SELECT nguoi_dung_id FROM nhan_vien WHERE id = ?";
        $stmt_get_user_id = $conn->prepare($sql_get_user_id);
        $stmt_get_user_id->bind_param("i", $nhan_vien_id);
        $stmt_get_user_id->execute();
        $result = $stmt_get_user_id->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Không tìm thấy nhân viên.");
        }
        $nguoi_dung_id = $result->fetch_assoc()['nguoi_dung_id'];
        $stmt_get_user_id->close();

        // Xóa địa chỉ của nhân viên
        $sql_delete_address = "DELETE FROM dia_chi WHERE nhan_vien_id = ?";
        $stmt_delete_address = $conn->prepare($sql_delete_address);
        $stmt_delete_address->bind_param("i", $nhan_vien_id);
        if (!$stmt_delete_address->execute()) {
            throw new Exception("Xóa địa chỉ không thành công.");
        }
        $stmt_delete_address->close();

        // Xóa tài khoản người dùng
        $sql_delete_user = "DELETE FROM nguoi_dung WHERE id = ?";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bind_param("i", $nguoi_dung_id);
        if (!$stmt_delete_user->execute()) {
            throw new Exception("Xóa tài khoản người dùng không thành công.");
        }
        $stmt_delete_user->close();
        // Xóa nhân viên
        $sql_delete_employee = "DELETE FROM nhan_vien WHERE id = ?";
        $stmt_delete_employee = $conn->prepare($sql_delete_employee);
        $stmt_delete_employee->bind_param("i", $nhan_vien_id);
        if (!$stmt_delete_employee->execute()) {
            throw new Exception("Xóa nhân viên không thành công.");
        }
        $stmt_delete_employee->close();


        // Commit giao dịch nếu tất cả đều thành công
        $conn->commit();

        // Thông báo thành công
        echo "Nhân viên và tài khoản người dùng đã được xóa thành công.";

        // Chuyển hướng về trang danh sách nhân viên
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    } finally {
        // Đóng kết nối
        $conn->close();
    }
} else {
    echo "Yêu cầu không hợp lệ.";
    exit;
}
?>