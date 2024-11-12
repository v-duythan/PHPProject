<?php
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $phong_ban_id = intval($_POST['phong_ban']);
    $chuc_vu_id = intval($_POST['chuc_vu']);
    $ngay_vao_lam = $_POST['ngay_vao_lam'];
    $nguoi_dung_id = $_POST['nguoi_dung_id'];
    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);

    // Cập nhật thông tin người dùng (tên đăng nhập)
    $sql_user = "UPDATE nguoi_dung SET ten_dang_nhap = ? WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("si", $ten_dang_nhap, $nguoi_dung_id);

    // Cập nhật thông tin nhân viên
    $sql_nhan_vien = "
        UPDATE nhan_vien 
        SET ho_ten = ?, email = ?, so_dien_thoai = ?, dia_chi = ?, chuc_vu_id = ?, ngay_vao_lam = ? 
        WHERE id = ?";
    $stmt_nhan_vien = $conn->prepare($sql_nhan_vien);
    $stmt_nhan_vien->bind_param("ssssisi", $ho_ten, $email, $so_dien_thoai, $dia_chi, $chuc_vu_id, $ngay_vao_lam, $id);

    // Thực thi các câu lệnh SQL
    if ($stmt_user->execute() && $stmt_nhan_vien->execute()) {
        echo "Thông tin nhân viên và tên đăng nhập đã được cập nhật thành công.";
        header("Location: list.php");
        exit;
    } else {
        echo "Lỗi khi cập nhật thông tin: " . $stmt_user->error . " | " . $stmt_nhan_vien->error;
    }

    // Đóng statement và kết nối
    $stmt_user->close();
    $stmt_nhan_vien->close();
    $conn->close();
}
?>
