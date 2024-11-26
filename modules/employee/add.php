<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';

session_start();
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';

// Lấy danh sách phòng ban
$sql_departments = "SELECT id, ten_phong_ban FROM phong_ban";
$result_departments = $conn->query($sql_departments);
if ($conn->error) {
    die("Lỗi truy vấn phòng ban: " . $conn->error);
}

// Lấy danh sách tỉnh/thành
$sql_provinces = "SELECT code, full_name FROM provinces";
$result_provinces = $conn->query($sql_provinces);
if ($conn->error) {
    die("Lỗi truy vấn tỉnh/thành: " . $conn->error);
}
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
                        <option value="">Chọn Phòng Ban</option>
                        <?php while ($department = $result_departments->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($department['id']); ?>">
                                <?= htmlspecialchars($department['ten_phong_ban']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="chuc_vu">Chức Vụ:</label></th>
                <td>
                    <select name="chuc_vu" id="chuc_vu" required>
                        <option value="">Chọn Chức Vụ</option>
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
                <th><label for="ngay_vao_lam">Ngày Vào Làm:</label></th>
                <td><input type="date" name="ngay_vao_lam" id="ngay_vao_lam" required></td>
            </tr>
            <tr>
                <th><label for="tinh_thanh">Tỉnh/Thành:</label></th>
                <td>
                    <select name="tinh_thanh" id="tinh_thanh" required onchange="loadDistricts(this.value)">
                        <option value="">Chọn Tỉnh/Thành</option>
                        <?php while ($province = $result_provinces->fetch_assoc()) { ?>
                            <option value="<?= htmlspecialchars($province['code']); ?>">
                                <?= htmlspecialchars($province['full_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="quan_huyen">Quận/Huyện:</label></th>
                <td>
                    <select name="quan_huyen" id="quan_huyen" required onchange="loadWards(this.value)">
                        <option value="">Chọn Quận/Huyện</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="phuong_xa">Phường/Xã:</label></th>
                <td>
                    <select name="phuong_xa" id="phuong_xa" required>
                        <option value="">Chọn Phường/Xã</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="so_nha">Số Nhà:</label></th>
                <td><input type="text" name="so_nha" id="so_nha" required></td>
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
<?php include '../../includes/footer.php'; ?>
<script>
    async function loadPositions(departmentId) {
        const positionSelect = document.getElementById('chuc_vu');
        positionSelect.innerHTML = '<option value="">Chọn Chức Vụ</option>'; // Reset options

        if (!departmentId) return;

        try {
            const response = await fetch(`get_positions.php?phong_ban_id=${departmentId}`);
            const data = await response.json();

            data.forEach(position => {
                const option = document.createElement('option');
                option.value = position.id;
                option.text = position.ten_chuc_vu;
                positionSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Lỗi khi tải chức vụ:', error);
        }
    }

    async function loadDistricts(provinceId) {
        const districtSelect = document.getElementById('quan_huyen');
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>'; // Reset options

        if (!provinceId) return;

        try {
            const response = await fetch(`get_districts.php?province_id=${provinceId}`);
            const data = await response.json();

            data.forEach(district => {
                const option = document.createElement('option');
                option.value = district.id;
                option.text = district.name;
                districtSelect.appendChild(option);
            });

// Reset wards
            loadWards('');
        } catch (error) {
            console.error('Lỗi khi tải quận/huyện:', error);
        }
    }

    async function loadWards(districtId) {
        const wardSelect = document.getElementById('phuong_xa');
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>'; // Reset options

        if (!districtId) return;

        try {
            const response = await fetch(`get_wards.php?district_id=${districtId}`);
            const data = await response.json();

            data.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.id;
                option.text = ward.name;
                wardSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Lỗi khi tải phường/xã:', error);
        }
    }
</script>
