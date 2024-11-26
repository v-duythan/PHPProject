<?php
include '../../config/database.php';
include '../../includes/functions.php';
include_once __DIR__ . '/../../config/config.php';

// Kiểm tra nếu có dữ liệu từ form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $ho_ten = trim($_POST['ho_ten']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $phong_ban_id = intval($_POST['phong_ban']);
    $chuc_vu_id = intval($_POST['chuc_vu']);
    $ngay_vao_lam = $_POST['ngay_vao_lam'];
    $hinh_anh = convert_to_ascii($ho_ten) . pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION);
move_uploaded_file($_FILES['hinh_anh']['tmp_name'], '../../assets/images/' . $hinh_anh);


    $ten_dang_nhap = convert_to_ascii($ho_ten); // Tạo tên đăng nhập từ họ tên
    $mat_khau = sha1('password'); // Hash mật khẩu mặc định
    $vai_tro = 'user'; // Vai trò mặc định

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
        $sql_check_username = "SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ?";
        $stmt_check_username = $conn->prepare($sql_check_username);
        $stmt_check_username->bind_param("s", $ten_dang_nhap);
        $stmt_check_username->execute();
        $stmt_check_username->store_result();
        if ($stmt_check_username->num_rows > 0) {
            throw new Exception("Tên đăng nhập đã tồn tại.");
        }
        $stmt_check_username->close();

        // Thêm người dùng mới vào bảng `nguoi_dung`
        $sql_user = "INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, vai_tro) VALUES (?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("sss", $ten_dang_nhap, $mat_khau, $vai_tro);
        if (!$stmt_user->execute()) {
            throw new Exception("Thêm người dùng không thành công.");
        }
        $nguoi_dung_id = $stmt_user->insert_id; // Lấy ID của người dùng vừa thêm
        $stmt_user->close();


        // Thêm nhân viên mới vào bảng `nhan_vien`
        $sql_nhan_vien = "INSERT INTO nhan_vien (ho_ten, email, so_dien_thoai, chuc_vu_id, ngay_vao_lam, nguoi_dung_id, hinh_anh) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_nhan_vien = $conn->prepare($sql_nhan_vien);
        $stmt_nhan_vien->bind_param("sssisis", $ho_ten, $email, $so_dien_thoai, $chuc_vu_id, $ngay_vao_lam, $nguoi_dung_id, $hinh_anh);
        if (!$stmt_nhan_vien->execute()) {
            throw new Exception("Thêm nhân viên không thành công.");
        }
        $nhan_vien_id = $stmt_nhan_vien->insert_id;
        $stmt_nhan_vien->close();

        // Thêm địa chỉ vào bảng `dia_chi` nếu có
        $sql_dia_chi = "INSERT INTO dia_chi (so_nha, phuong_xa_id, quan_huyen_id, tinh_thanh_id, nhan_vien_id) VALUES (?, ?, ?, ?, ?)";
        $stmt_dia_chi = $conn->prepare($sql_dia_chi);
        $stmt_dia_chi->bind_param("ssssi", $so_nha, $phuong_xa_id, $quan_huyen_id, $tinh_thanh_id, $nhan_vien_id);
        if (!$stmt_dia_chi->execute()) {
            throw new Exception("Thêm địa chỉ không thành công.");
        }
        $stmt_dia_chi->close();

        // Commit giao dịch nếu tất cả đều thành công
        $conn->commit();

        // Thông báo thành công
        echo "Nhân viên mới đã được thêm thành công.";

        // Chuyển hướng về trang danh sách nhân viên
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    } finally {
        // Đóng statement và kết nối
        if (isset($stmt_user)) $stmt_user->close();
        if (isset($stmt_nhan_vien)) $stmt_nhan_vien->close();
        if (isset($stmt_dia_chi)) $stmt_dia_chi->close();
        $conn->close();
    }
}
?>
