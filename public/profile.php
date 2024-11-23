<?php
session_start();
require_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['nhan_vien_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Get the id from the URL
$nhan_vien_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['nhan_vien_id'];

// Fetch employee details from the database
$sql_basic = "SELECT 
    nhan_vien.ho_ten, 
    nhan_vien.email, 
    nhan_vien.so_dien_thoai, 
    nhan_vien.ngay_vao_lam, 
    nhan_vien.hinh_anh,
    CONCAT_WS(', ', dia_chi.so_nha, wards.full_name, districts.full_name, provinces.full_name) AS dia_chi, 
    chuc_vu.ten_chuc_vu, 
    phong_ban.ten_phong_ban
FROM nhan_vien
    LEFT JOIN dia_chi ON nhan_vien.id = dia_chi.nhan_vien_id
    LEFT JOIN wards ON dia_chi.phuong_xa_id = wards.code
    LEFT JOIN districts ON dia_chi.quan_huyen_id = districts.code
    LEFT JOIN provinces ON dia_chi.tinh_thanh_id = provinces.code
    LEFT JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
    LEFT JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
WHERE nhan_vien.id = ?;
";

$stmt = $conn->prepare($sql_basic);
$stmt->bind_param("i", $nhan_vien_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$sql_history = "SELECT 
    lich_su_chuyen.chuc_vu_cu, 
    chuc_vu_cu.ten_chuc_vu AS chuc_vu_cu_ten,
    phong_ban_cu.ten_phong_ban AS phong_ban_cu,
    lich_su_chuyen.chuc_vu_moi, 
    chuc_vu_moi.ten_chuc_vu AS chuc_vu_moi_ten,
    phong_ban_moi.ten_phong_ban AS phong_ban_moi,
    lich_su_chuyen.ngay_chuyen
FROM lich_su_chuyen
    LEFT JOIN chuc_vu AS chuc_vu_cu ON lich_su_chuyen.chuc_vu_cu = chuc_vu_cu.id
    LEFT JOIN phong_ban AS phong_ban_cu ON chuc_vu_cu.phong_ban_id = phong_ban_cu.id
    LEFT JOIN chuc_vu AS chuc_vu_moi ON lich_su_chuyen.chuc_vu_moi = chuc_vu_moi.id
    LEFT JOIN phong_ban AS phong_ban_moi ON chuc_vu_moi.phong_ban_id = phong_ban_moi.id
WHERE lich_su_chuyen.nhan_vien_id = ?";

$stmt = $conn->prepare($sql_history);
$stmt->bind_param("i", $nhan_vien_id);
$stmt->execute();
$result = $stmt->get_result();
$employee_history = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/user_sidebar.php'; ?>
<link rel="stylesheet" href="../includes/css/profile_style.css">
<div class="profile-container">
    <div class="profile-header">
        <h2>Thông tin cá nhân</h2>
    </div>
    <div class="profile-content">
        <?php if ($employee): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($employee['hinh_anh']); ?>" alt="Ảnh đại diện" class="profile-image">
            <table class="profile-info">
                <tr>
                    <th>Tên:</th>
                    <td><?php echo htmlspecialchars($employee['ho_ten']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                </tr>
                <tr>
                    <th>Số điện thoại:</th>
                    <td><?php echo htmlspecialchars($employee['so_dien_thoai']); ?></td>
                </tr>
                <tr>
                    <th>Địa chỉ:</th>
                    <td><?php echo htmlspecialchars($employee['dia_chi']); ?></td>
                </tr>
                <tr>
                    <th>Ngày vào làm:</th>
                    <td><?php echo htmlspecialchars($employee['ngay_vao_lam']); ?></td>
                </tr>
                <tr>
                    <th>Chức vụ:</th>
                    <td><?php echo htmlspecialchars($employee['ten_chuc_vu']); ?></td>
                </tr>
                <tr>
                    <th>Phòng ban:</th>
                    <td><?php echo htmlspecialchars($employee['ten_phong_ban']); ?></td>
                </tr>
                <tr>
                    <th>Lịch sử chuyển chức vụ:</th>
                    <td>
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Chức vụ cũ</th>
                                <th>Phòng ban cũ</th>
                                <th>Chức vụ mới</th>
                                <th>Phòng ban mới</th>
                                <th>Ngày chuyển</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($employee_history)): ?>
                                <tr>
                                    <td> -</td>
                                    <td> -</td>
                                    <td><?php echo htmlspecialchars($employee_history[0]['chuc_vu_cu_ten'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($employee_history[0]['phong_ban_cu'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['ngay_vao_lam']); ?></td>
                                </tr>
                                <?php foreach ($employee_history as $index => $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['chuc_vu_cu_ten'] ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['phong_ban_cu'] ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['chuc_vu_moi_ten'] ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['phong_ban_moi'] ?: '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['ngay_chuyen'] ?: '-'); ?></td>
                                        </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Không có lịch sử chuyển -->
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo htmlspecialchars($employee['ten_chuc_vu']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['ten_phong_ban']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['ngay_vao_lam']); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <p>Không tìm thấy thông tin nhân viên.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>