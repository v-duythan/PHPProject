<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';

// Kiểm tra xem ID nhân viên có hợp lệ hay không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "
        SELECT nhan_vien.id, nhan_vien.ho_ten, nhan_vien.email, nhan_vien.so_dien_thoai, nhan_vien.dia_chi, 
               nhan_vien.ngay_vao_lam, nhan_vien.chuc_vu_id, chuc_vu.phong_ban_id, nguoi_dung.ten_dang_nhap, nguoi_dung.id AS nguoi_dung_id
        FROM nhan_vien
        JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
        JOIN nguoi_dung ON nhan_vien.nguoi_dung_id = nguoi_dung.id
        WHERE nhan_vien.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy nhân viên.";
        exit;
    }
} else {
    echo "ID nhân viên không hợp lệ.";
    exit;
}

// Đảm bảo phòng ban ID được gán giá trị
$phong_ban_id = $row['phong_ban_id'];

$sql_departments = "SELECT id, ten_phong_ban FROM phong_ban";
$result_departments = $conn->query($sql_departments);
if ($conn->error) {
    die("Query failed: " . $conn->error);
}

// Lấy chức vụ cho từng phòng ban
$sql_positions = "SELECT id, ten_chuc_vu FROM chuc_vu WHERE phong_ban_id = ?";
$stmt_positions = $conn->prepare($sql_positions);
$stmt_positions->bind_param("i", $phong_ban_id);
$stmt_positions->execute();
$result_positions = $stmt_positions->get_result();

?>
<main class="content">
    <h1>Chỉnh Sửa Thông Tin Nhân Viên</h1>
    <form method="POST" action="process_edit.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" />
        <input type="hidden" name="nguoi_dung_id" value="<?php echo htmlspecialchars($row['nguoi_dung_id']); ?>" />

        <label for="ho_ten">Họ Tên:</label>
        <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($row['ho_ten']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>

        <label for="so_dien_thoai">Số Điện Thoại:</label>
        <input type="text" name="so_dien_thoai" value="<?php echo htmlspecialchars($row['so_dien_thoai']); ?>" required>

        <label for="dia_chi">Địa Chỉ:</label>
        <input type="text" name="dia_chi" value="<?php echo htmlspecialchars($row['dia_chi']); ?>" required>

        <label for="phong_ban">Phòng Ban:</label>
        <select name="phong_ban" id="phong_ban" required onchange="loadPositions(this.value)">
            <?php while ($department = $result_departments->fetch_assoc()) {
                $selected = ($department['id'] == $row['phong_ban_id']) ? 'selected' : '';
                echo "<option value='{$department['id']}' $selected>{$department['ten_phong_ban']}</option>";
            } ?>
        </select>

        <label for="chuc_vu">Chức Vụ:</label>
        <select name="chuc_vu" id="chuc_vu" required>
            <?php while ($position = $result_positions->fetch_assoc()) {
                $selected = ($position['id'] == $row['chuc_vu_id']) ? 'selected' : '';
                echo "<option value='{$position['id']}' $selected>{$position['ten_chuc_vu']}</option>";
            } ?>
        </select>

        <label for="ngay_vao_lam">Ngày Vào Làm:</label>
        <input type="date" name="ngay_vao_lam" value="<?php echo htmlspecialchars($row['ngay_vao_lam']); ?>" required>

        <label for="ten_dang_nhap">Tên Đăng Nhập:</label>
        <input type="text" name="ten_dang_nhap" value="<?php echo htmlspecialchars($row['ten_dang_nhap']); ?>" required>

        <button type="submit">Cập Nhật</button>
    </form>
</main>

<script>
    function loadPositions(departmentId) {
        fetch(`get_positions.php?phong_ban_id=${departmentId}`)
            .then(response => response.json())
            .then(data => {
                const positionSelect = document.getElementById('chuc_vu');
                positionSelect.innerHTML = ''; // Clear existing options
                data.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.text = position.ten_chuc_vu;
                    positionSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching positions:', error));
    }
    // Tự động tải chức vụ cho phòng ban hiện tại khi tải trang
    window.onload = function() {
        loadPositions(document.getElementById('phong_ban').value);
    };
</script>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
