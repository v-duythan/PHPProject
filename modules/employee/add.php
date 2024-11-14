<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';

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
    <h1>Thêm Nhân Viên Mới</h1>

    <form action="process_add.php" method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <th><label for="ho_ten">Họ Tên:</label></th>
                <td><input type="text" name="ho_ten" id="ho_ten" required></td>
            </tr>
            <tr>
                <th><label for="phong_ban">Phòng Ban:</label></th>
                <td>
                    <select name="phong_ban" id="phong_ban" required onchange="loadPositions(this.value)">
                        <?php while ($department = $result_departments->fetch_assoc()) {
                            $selected = ($department['id'] == $row['phong_ban_id']) ? 'selected' : '';
                            echo "<option value='{$department['id']}' $selected>{$department['ten_phong_ban']}</option>";
                        } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="chuc_vu">Chức Vụ:</label></th>
                <td>
                    <select name="chuc_vu" id="chuc_vu" required>
                        <?php while ($position = $result_positions->fetch_assoc()) {
                            $selected = ($position['id'] == $row['chuc_vu_id']) ? 'selected' : '';
                            echo "<option value='{$position['id']}' $selected>{$position['ten_chuc_vu']}</option>";
                        } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="email">Email:</label></th>
                <td><input type="email" name="email" id="email" required></td>
            </tr>
            <tr>
                <th><label for="so_dien_thoai">Số Điện Thoại:</label></th>
                <td><input type="tel" name="so_dien_thoai" id="so_dien_thoai" required></td>
            </tr>
            <tr>
                <th><label for="dia_chi">Địa Chỉ:</label></th>
                <td><input type="text" name="dia_chi" id="dia_chi" required></td>
            </tr>
            <tr>
                <th><label for="ngay_vao_lam">Ngày Vào Làm:</label></th>
                <td><input type="date" name="ngay_vao_lam" id="ngay_vao_lam" required></td>
            </tr>
            <tr>
                <th><label for="luong_thoa_thuan">Lương Thỏa Thuận:</label></th>
                <td><input type="number" name="luong_thoa_thuan" id="luong_thoa_thuan" required></td>
            </tr>
            <tr>
                <th><label for="hinh_anh">Ảnh Nhân Viên:</label></th>
                <td><input type="file" name="hinh_anh" id="hinh_anh" accept="image/*" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit">Thêm Nhân Viên</button>
                </td>
            </tr>
        </table>
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
