<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';

// Kiểm tra ID phòng ban cần chỉnh sửa
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin phòng ban từ cơ sở dữ liệu
    $sql = "SELECT * FROM phong_ban WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy phòng ban.";
        exit;
    }
} else {
    echo "ID phòng ban không hợp lệ.";
    exit;
}

// Lấy danh sách nhân viên trong cùng phòng ban để chọn làm trưởng phòng
$sql_manager = "SELECT nhan_vien.id, nhan_vien.ho_ten FROM nhan_vien JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id WHERE chuc_vu.phong_ban_id = ?";
$stmt_manager = $conn->prepare($sql_manager);
$stmt_manager->bind_param("i", $id);
$stmt_manager->execute();
$result_manager = $stmt_manager->get_result();

?>

<main class="content">
    <h1>Chỉnh Sửa Phòng Ban</h1>

    <form method="POST" action="process_edit.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" />

        <label for="ten_phong_ban">Tên Phòng Ban:</label>
        <input type="text" name="ten_phong_ban" value="<?php echo htmlspecialchars($row['ten_phong_ban']); ?>" required>

        <label for="mo_ta">Mô Tả:</label>
        <textarea name="mo_ta" required><?php echo htmlspecialchars($row['mo_ta']); ?></textarea>

        <label for="truong_phong_id">Trưởng Phòng:</label>
        <select name="truong_phong_id" required>
            <?php
            while ($manager = $result_manager->fetch_assoc()) {
                $selected = ($manager['id'] == $row['truong_phong_id']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($manager['id']) . "' $selected>" . htmlspecialchars($manager['ho_ten']) . "</option>";
            }
            ?>
        </select>

        <label for="ngay_thanh_lap">Ngày Thành Lập:</label>
        <input type="date" name="ngay_thanh_lap" value="<?php echo htmlspecialchars($row['ngay_thanh_lap']); ?>" required>

        <button type="submit">Cập Nhật</button>
    </form>
</main>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
