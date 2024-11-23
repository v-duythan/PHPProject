<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';

// Kiểm tra nếu có dữ liệu từ form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $id = $_POST['id']; // ID của nhân viên
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $phong_ban_id = intval($_POST['phong_ban']);
    $chuc_vu_id = intval($_POST['chuc_vu']);
    $ngay_vao_lam = $_POST['ngay_vao_lam'];
    $nguoi_dung_id = intval($_POST['nguoi_dung_id']);
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);

    // Địa chỉ (nếu có)
    $so_nha = trim($_POST['so_nha']);
    $phuong_xa_id = trim($_POST['phuong_xa']);
    $quan_huyen_id = trim($_POST['quan_huyen']);
    $tinh_thanh_id = trim($_POST['tinh_thanh']);

    // Kiểm tra định dạng ngày hợp lệ
    $date = DateTime::createFromFormat('Y-m-d', $ngay_vao_lam);
    if (!$date || $date->format('Y-m-d') !== $ngay_vao_lam) {
        die("Định dạng ngày không hợp lệ.");
    }

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Địa chỉ email không hợp lệ.");
    }

    // Bắt đầu giao dịch
    $conn->begin_transaction();

    try {
        // Kiểm tra tên đăng nhập có bị trùng không
        $sql_check_username = "SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ? AND id != ?";
        $stmt_check_username = $conn->prepare($sql_check_username);
        $stmt_check_username->bind_param("si", $ten_dang_nhap, $nguoi_dung_id);
        $stmt_check_username->execute();
        $stmt_check_username->store_result();
        if ($stmt_check_username->num_rows > 0) {
            throw new Exception("Tên đăng nhập đã tồn tại.");
        }
        $stmt_check_username->close();

        // Cập nhật tên đăng nhập nếu có thay đổi
        $sql_check = "SELECT ten_dang_nhap FROM nguoi_dung WHERE id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $nguoi_dung_id);
        $stmt_check->execute();
        $stmt_check->bind_result($existing_ten_dang_nhap);
        $stmt_check->fetch();
        if ($existing_ten_dang_nhap !== $ten_dang_nhap) {
            $stmt_check->close();
            $sql_user = "UPDATE nguoi_dung SET ten_dang_nhap = ? WHERE id = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("si", $ten_dang_nhap, $nguoi_dung_id);
            $stmt_user->execute();
            if ($stmt_user->affected_rows == 0) {
                throw new Exception("Cập nhật tên đăng nhập không thành công.");
            }
            $stmt_user->close();
        } else {
            $stmt_check->close();
        }

        // Cập nhật thông tin nhân viên
        $sql_nhan_vien = "UPDATE nhan_vien SET ho_ten = ?, email = ?, so_dien_thoai = ?, chuc_vu_id = ?, ngay_vao_lam = ? WHERE id = ?";
        $stmt_nhan_vien = $conn->prepare($sql_nhan_vien);
        $stmt_nhan_vien->bind_param("sssisi", $ho_ten, $email, $so_dien_thoai, $chuc_vu_id, $ngay_vao_lam, $id);
        if (!$stmt_nhan_vien->execute()) {
            throw new Exception("Cập nhật thông tin nhân viên không thành công.");
        }
        $stmt_nhan_vien->close();

        // Cập nhật địa chỉ nếu có sự thay đổi
        $sql_address_check = "SELECT so_nha, phuong_xa_id, quan_huyen_id, tinh_thanh_id FROM dia_chi WHERE nhan_vien_id = ?";
        $stmt_address_check = $conn->prepare($sql_address_check);
        $stmt_address_check->bind_param("i", $id);
        $stmt_address_check->execute();
        $stmt_address_check->bind_result($existing_so_nha, $existing_phuong_xa, $existing_quan_huyen, $existing_tinh_thanh);
        $stmt_address_check->fetch();
        if ($so_nha !== $existing_so_nha || $phuong_xa_id !== $existing_phuong_xa || $quan_huyen_id !== $existing_quan_huyen || $tinh_thanh_id !== $existing_tinh_thanh) {
            $stmt_address_check->close();
            $sql_dia_chi = "UPDATE dia_chi SET so_nha = ?, phuong_xa_id = ?, quan_huyen_id = ?, tinh_thanh_id = ? WHERE nhan_vien_id = ?";
            $stmt_dia_chi = $conn->prepare($sql_dia_chi);
            $stmt_dia_chi->bind_param("ssssi", $so_nha, $phuong_xa_id, $quan_huyen_id, $tinh_thanh_id, $id);
            if (!$stmt_dia_chi->execute()) {
                throw new Exception("Cập nhật địa chỉ không thành công.");
            }
            $stmt_dia_chi->close();
        } else {
            $stmt_address_check->close();
        }

        // Commit giao dịch nếu tất cả đều thành công
        $conn->commit();

        // Thông báo thành công
        echo "Thông tin nhân viên đã được cập nhật thành công.";

        // Chuyển hướng về trang danh sách nhân viên
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    } finally {
        // Đóng statement và kết nối
        if (isset($stmt_nhan_vien)) $stmt_nhan_vien->close();
        if (isset($stmt_address_check)) $stmt_address_check->close();
        $conn->close();
    }
}
?>
