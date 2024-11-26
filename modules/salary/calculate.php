<?php
require_once '../../config/database.php';
require_once '../../config/config.php';

$nam = $_GET['year'];
$thang = $_GET['month'];

$current_month = date('n'); // Tháng hiện tại (1-12)
$current_year = date('Y'); // Năm hiện tại

if ($nam > $current_year || ($nam == $current_year && $thang >= $current_month)) {
    header("Location: list.php?error=Không thể tính lương cho tháng hiện tại hoặc tương lai");
    exit();
}
// Lấy thông tin từ bảng cham_cong
$sql = "SELECT cham_cong.nhan_vien_id, cham_cong.ngay, cham_cong.trang_thai, cham_cong.gio_lam_them, YEAR(cham_cong.ngay) as nam, MONTH(cham_cong.ngay) as thang
        FROM cham_cong 
        WHERE YEAR(cham_cong.ngay) = ? AND MONTH(cham_cong.ngay) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $nam, $thang);  // Thay nam, thang từ form hoặc mặc định
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $nhan_vien_id = $row['nhan_vien_id'];
    $so_ngay_cong = 0;
    $so_ngay_nghi_phep = 0;
    $so_ngay_vang = 0;
    $so_gio_lam_them = 0;

    // Đếm số ngày công (trạng thái "CóMặt")
    $so_ngay_cong = $conn->query("SELECT COUNT(*) as so_ngay_cong 
                                  FROM cham_cong 
                                  WHERE nhan_vien_id = '$nhan_vien_id' 
                                  AND YEAR(ngay) = '$nam' 
                                  AND MONTH(ngay) = '$thang' 
                                  AND trang_thai = 'CóMặt'")->fetch_assoc()['so_ngay_cong'];

    // Đếm số ngày nghỉ phép (trạng thái "NghỉPhép")
    $so_ngay_nghi_phep = $conn->query("SELECT COUNT(*) as so_ngay_nghi_phep
                                       FROM cham_cong 
                                       WHERE nhan_vien_id = '$nhan_vien_id' 
                                       AND YEAR(ngay) = '$nam' 
                                       AND MONTH(ngay) = '$thang' 
                                       AND trang_thai = 'NghỉPhép'")->fetch_assoc()['so_ngay_nghi_phep'];

    // Đếm số ngày vắng mặt (trạng thái "VắngMặt")
    $so_ngay_vang = $conn->query("SELECT COUNT(*) as so_ngay_vang 
                                  FROM cham_cong 
                                  WHERE nhan_vien_id = '$nhan_vien_id' 
                                  AND YEAR(ngay) = '$nam' 
                                  AND MONTH(ngay) = '$thang' 
                                  AND trang_thai = 'VắngMặt'")->fetch_assoc()['so_ngay_vang'];

    // Đếm số lần vắng mặt không phép (vượt quá 2 lần)
    $so_lan_vang = $so_ngay_vang > 2 ? $so_ngay_vang - 2 : 0;  // Trừ 2 lần vắng mặt không tính khấu trừ

    $so_gio_lam_them = $conn->query("SELECT COALESCE(SUM(gio_lam_them), 0) as so_gio_lam_them
                                 FROM cham_cong 
                                 WHERE nhan_vien_id = '$nhan_vien_id' 
                                 AND YEAR(ngay) = '$nam' 
                                 AND MONTH(ngay) = '$thang' 
                                 AND trang_thai = 'CóMặt'")->fetch_assoc()['so_gio_lam_them'];


    // Tính lương cơ bản và các khoản khác
    $sql_luong = "SELECT * FROM luong WHERE nhan_vien_id = ? AND nam = ? AND thang = ?";
    $stmt_luong = $conn->prepare($sql_luong);
    $stmt_luong->bind_param("iii", $nhan_vien_id, $nam, $thang);
    $stmt_luong->execute();
    $stmt_luong_result = $stmt_luong->get_result()->fetch_assoc();

    $luong_co_ban = $stmt_luong_result['luong_co_ban'];
    $phu_cap = $stmt_luong_result['phu_cap'];
    $thuong = $stmt_luong_result['thuong'];

    // Tính lương ngày công (dựa vào số ngày công); làm tròn số hàng nghìn
    $luong_ngay_cong = round(($luong_co_ban / 22) * $so_ngay_cong, -3); 
    

    // Tính lương làm thêm (dựa vào số giờ làm thêm)
    $luong_lam_them = round($so_gio_lam_them * ($luong_co_ban / 22 / 8) * 1.5, -3);

    // Tính bảo hiểm
    $bao_hiem = ($luong_co_ban * 0.08) + ($luong_co_ban * 0.015) + ($luong_co_ban * 0.01);

    // Khấu trừ khác (nếu có)
    $khoan_tru_khac = $so_lan_vang * 100000;

    // Tính tổng lương
    $tong_luong = $luong_ngay_cong + $phu_cap + $thuong + $luong_lam_them - $bao_hiem - $khoan_tru_khac;


    $stmt_update = $conn->prepare("UPDATE luong
                               SET so_ngay_cong = ?, so_ngay_nghi_phep = ?, so_ngay_vang = ?,
                                   luong_ngay_cong = ?, luong_lam_them = ?, bao_hiem = ?,
                                   khoan_tru_khac = ?, tong_luong = ?, phu_cap = ?, so_gio_lam_them = ?
                               WHERE nhan_vien_id = ? AND nam = ? AND thang = ?");
    $stmt_update->bind_param("iiiddddddiiii", $so_ngay_cong, $so_ngay_nghi_phep, $so_ngay_vang, $luong_ngay_cong, $luong_lam_them, $bao_hiem, $khoan_tru_khac, $tong_luong, $phu_cap, $so_gio_lam_them, $nhan_vien_id, $nam, $thang);
    $stmt_update->execute();
}

header("Location: list.php?nam=$nam&thang=$thang");
exit();
?>