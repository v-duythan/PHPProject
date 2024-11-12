<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = trim($_POST['ho_ten']);
    $phong_ban = intval($_POST['phong_ban']);
    $chuc_vu = intval($_POST['chuc_vu']);
    $email = trim($_POST['email']);
    $so_dien_thoai = trim($_POST['so_dien_thoai']);
    $dia_chi = trim($_POST['dia_chi']);
    $ngay_vao_lam = $_POST['ngay_vao_lam'];
    $luong_thoa_thuan = floatval($_POST['luong_thoa_thuan']);

    // Kiểm tra email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Lỗi: Email không hợp lệ.");
    }

    // Kiểm tra email đã tồn tại
    $sql_check_email = "SELECT COUNT(*) FROM nhan_vien WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->bind_result($email_count);
    $stmt_check_email->fetch();
    $stmt_check_email->close();

    if ($email_count > 0) {
        die("Lỗi: Email đã được sử dụng.");
    }

    // Chuyển tên thành không dấu để tạo tên đăng nhập
    $ten_dang_nhap = strtolower(str_replace(' ', '_', convert_to_no_accent($ho_ten)));

    // Kiểm tra và tạo tài khoản người dùng
    $mat_khau = sha1('password'); // Mật khẩu mặc định được mã hóa
    $vai_tro = 'user';
    $trang_thai = 'Active';

    $sql_user = "INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, vai_tro, trang_thai) 
                 VALUES (?, ?, ?, ?)";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("ssss", $ten_dang_nhap, $mat_khau, $vai_tro, $trang_thai);

    if ($stmt_user->execute()) {
        // Lấy ID của người dùng mới để liên kết với bảng nhân viên
        $user_id = $stmt_user->insert_id;

        // Kiểm tra file ảnh và đặt tên file ảnh
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['hinh_anh']['tmp_name'];
            $file_ext = pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('nhanvien_', true) . '.' . $file_ext; // Tạo tên file duy nhất
            $upload_dir = __DIR__ . '/../../assets/images/';
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Thêm nhân viên vào cơ sở dữ liệu
                $sql_employee = "INSERT INTO nhan_vien (
                    ho_ten, chuc_vu_id, email, so_dien_thoai, dia_chi, 
                    ngay_vao_lam, luong_thoa_thuan, hinh_anh, nguoi_dung_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt_employee = $conn->prepare($sql_employee);

                if ($stmt_employee === false) {
                    die('Prepare failed: ' . $conn->error);
                }

                // Correct the bind_param string
                $stmt_employee->bind_param(
                    "sisssssds",  // Correct number of parameters
                    $ho_ten,        // s - string
                    $chuc_vu,       // i - integer
                    $email,         // s - string
                    $so_dien_thoai, // s - string
                    $dia_chi,       // s - string
                    $ngay_vao_lam,  // s - string (YYYY-MM-DD)
                    $luong_thoa_thuan, // d - decimal/float
                    $file_name,     // s - string
                    $user_id        // i - integer
                );

                if ($stmt_employee->execute()) {
                    echo "Nhân viên và tài khoản đã được thêm thành công.";
                } else {
                    echo "Lỗi: " . $stmt_employee->error;
                }

                $stmt_employee->close();
            } else {
                echo "Không thể tải lên file ảnh.";
            }
        } else {
            echo "File ảnh không hợp lệ.";
        }
    } else {
        echo "Lỗi khi tạo tài khoản người dùng: " . $stmt_user->error;
    }

    $stmt_user->close();
}

$conn->close();

?>
