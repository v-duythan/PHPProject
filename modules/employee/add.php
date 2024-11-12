<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
include '../../includes/header.php';
include '../../includes/admin_sidebar.php';
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
                    <select name="phong_ban" id="phong_ban" required>
                        <?php
                        $result = $conn->query("SELECT id, ten_phong_ban FROM phong_ban");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['ten_phong_ban']}</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="chuc_vu">Chức Vụ:</label></th>
                <td>
                    <select name="chuc_vu" id="chuc_vu" required>
                        <?php
                        $result = $conn->query("SELECT id, ten_chuc_vu FROM chuc_vu");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['ten_chuc_vu']}</option>";
                        }
                        ?>
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

<?php include '../../includes/footer.php'; ?>
</body>
</html>
