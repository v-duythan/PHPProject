<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
session_start();

// Kiểm tra ID nhân viên
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "
        SELECT nhan_vien.id, nhan_vien.ho_ten, nhan_vien.email, nhan_vien.so_dien_thoai, 
               nhan_vien.ngay_vao_lam, nhan_vien.chuc_vu_id, chuc_vu.phong_ban_id, nguoi_dung.ten_dang_nhap, nguoi_dung.id AS nguoi_dung_id,
               dia_chi.so_nha, dia_chi.phuong_xa_id, dia_chi.quan_huyen_id, dia_chi.tinh_thanh_id
        FROM nhan_vien
        JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
        JOIN nguoi_dung ON nhan_vien.nguoi_dung_id = nguoi_dung.id
        LEFT JOIN dia_chi ON nhan_vien.id = dia_chi.nhan_vien_id
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

// Lấy danh sách phòng ban
$sql_departments = "SELECT id, ten_phong_ban FROM phong_ban";
$result_departments = $conn->query($sql_departments);

// Lấy danh sách chức vụ theo phòng ban
$sql_positions = "SELECT id, ten_chuc_vu FROM chuc_vu WHERE phong_ban_id = ?";
$stmt_positions = $conn->prepare($sql_positions);
$stmt_positions->bind_param("i", $row['phong_ban_id']);
$stmt_positions->execute();
$result_positions = $stmt_positions->get_result();

?>
<?php

include '../../includes/header.php';
include '../../includes/admin_sidebar.php';
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



        <label for="tinh_thanh">Tỉnh/Thành:</label>
        <select name="tinh_thanh" id="tinh_thanh" required onchange="loadDistricts(this.value)">
            <option value="">Chọn Tỉnh/Thành</option>
            <?php
            // Lấy danh sách tỉnh thành
            $sql_provinces = "SELECT code, full_name FROM provinces";
            $result_provinces = $conn->query($sql_provinces);
            while ($province = $result_provinces->fetch_assoc()) {
                $selected = ($province['code'] == $row['tinh_thanh']) ? 'selected' : '';
                echo "<option value='{$province['code']}' $selected>{$province['full_name']}</option>";
            }
            ?>
        </select>

        <label for="quan_huyen">Quận/Huyện:</label>
        <select name="quan_huyen" id="quan_huyen" required onchange="loadWards(this.value)">
            <option value="">Chọn Quận/Huyện</option>
            <?php
            // Lấy danh sách quận huyện nếu có sẵn
            $sql_districts = "SELECT code, full_name FROM districts WHERE province_code = ?";
            $stmt_districts = $conn->prepare($sql_districts);
            $stmt_districts->bind_param("i", $row['tinh_thanh']);
            $stmt_districts->execute();
            $result_districts = $stmt_districts->get_result();
            while ($district = $result_districts->fetch_assoc()) {
                $selected = ($district['code'] == $row['quan_huyen']) ? 'selected' : '';
                echo "<option value='{$district['code']}' $selected>{$district['full_name']}</option>";
            }
            ?>
        </select>

        <label for="phuong_xa">Phường/Xã:</label>
        <select name="phuong_xa" id="phuong_xa" required>
            <option value="">Chọn Phường/Xã</option>
            <?php
            // Lấy danh sách phường xã nếu có sẵn
            $sql_wards = "SELECT code, full_name FROM wards WHERE district_code = ?";
            $stmt_wards = $conn->prepare($sql_wards);
            $stmt_wards->bind_param("i", $row['quan_huyen']);
            $stmt_wards->execute();
            $result_wards = $stmt_wards->get_result();
            while ($ward = $result_wards->fetch_assoc()) {
                $selected = ($ward['code'] == $row['phuong_xa']) ? 'selected' : '';
                echo "<option value='{$ward['code']}' $selected>{$ward['full_name']}</option>";
            }
            ?>
        </select>

        <label for="so_nha">Số Nhà:</label>
        <input type="text" name="so_nha" value="<?php echo htmlspecialchars($row['so_nha']); ?>" required>

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

        <label for="ten_dang_nhap">Tên Đăng Nhập:</label>
        <input type="text" name="ten_dang_nhap" value="<?php echo htmlspecialchars($row['ten_dang_nhap']); ?>" required>


        <label for="ngay_vao_lam">Ngày Vào Làm:</label>
        <input type="date" name="ngay_vao_lam" value="<?php echo htmlspecialchars($row['ngay_vao_lam']); ?>" required>

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
    function loadDistricts(provinceId) {
        fetch(`get_districts.php?province_id=${provinceId}`)
            .then(response => response.json())
            .then(data => {
                const districtSelect = document.getElementById('quan_huyen');
                districtSelect.innerHTML = ''; // Clear existing options
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.text = 'Chọn Quận/Huyện';
                districtSelect.appendChild(defaultOption);

                data.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.text = district.name;
                    districtSelect.appendChild(option);
                });

                // Reset Phường/Xã
                loadWards('');
            })
            .catch(error => console.error('Error fetching districts:', error));
    }

    function loadWards(districtId) {
        fetch(`get_wards.php?district_id=${districtId}`)
            .then(response => response.json())
            .then(data => {
                const wardSelect = document.getElementById('phuong_xa');
                wardSelect.innerHTML = ''; // Clear existing options
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.text = 'Chọn Phường/Xã';
                wardSelect.appendChild(defaultOption);

                data.forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.id;
                    option.text = ward.name;
                    wardSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching wards:', error));
    }

    window.onload = function() {
        const provinceId = document.getElementById('tinh_thanh').value;
        if (provinceId) {
            loadDistricts(provinceId);
        }

        const districtId = document.getElementById('quan_huyen').value;
        if (districtId) {
            loadWards(districtId);
        }
    };

</script>

<?php include '../../includes/footer.php'; ?>
</body>
</html>