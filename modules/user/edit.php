<?php
// Kết nối tới cơ sở dữ liệu
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
session_start();

// Lấy ID người dùng từ URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kiểm tra ID hợp lệ
if ($user_id <= 0) {
    echo "ID người dùng không hợp lệ.";
    exit;
}

// Lấy thông tin người dùng hiện tại
$sql = "SELECT ten_dang_nhap, trang_thai FROM nguoi_dung WHERE id = $user_id";
$result = $conn->query($sql);

// Kiểm tra có dữ liệu không
if ($result->num_rows == 0) {
    echo "Không tìm thấy người dùng.";
    exit;
}

// Lấy dữ liệu người dùng
$user = $result->fetch_assoc();

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten_dang_nhap = isset($_POST['ten_dang_nhap']) ? $_POST['ten_dang_nhap'] : '';
    $trang_thai = isset($_POST['trang_thai']) ? $_POST['trang_thai'] : '';

    // Kiểm tra nếu có giá trị trong form
    if (empty($ten_dang_nhap) || empty($trang_thai)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Cập nhật thông tin người dùng
        $update_sql = "UPDATE nguoi_dung SET ten_dang_nhap = ?, trang_thai = ? WHERE id = ?";

        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare($update_sql);

        // Kiểm tra nếu prepared statement được chuẩn bị thành công
        if ($stmt === false) {
            echo "Lỗi chuẩn bị câu lệnh SQL: " . $conn->error;
        } else {
            $stmt->bind_param('ssi', $ten_dang_nhap, $trang_thai, $user_id);

            // Kiểm tra nếu câu lệnh thực thi thành công
            if ($stmt->execute()) {
                // Chuyển hướng về trang danh sách sau khi cập nhật thành công
                header("Location: list.php");
                exit;
            } else {
                echo "Có lỗi khi cập nhật: " . $stmt->error;
            }
        }
    }
}

// Đóng kết nối
$conn->close();
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/admin_sidebar.php'; ?>

<main class="content">
    <h1>Chỉnh sửa người dùng</h1>

    <form method="POST" action="">
        <label for="ten_dang_nhap">Tên đăng nhập:</label>
        <input type="text" name="ten_dang_nhap" id="ten_dang_nhap" value="<?= htmlspecialchars($user['ten_dang_nhap']) ?>" required>

        <label for="trang_thai">Trạng thái:</label>
        <select name="trang_thai" id="trang_thai" required>
            <option value="active" <?= $user['trang_thai'] == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $user['trang_thai'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit">Cập nhật</button>
    </form>

    <a href="list.php">Quay lại danh sách</a>
</main>

<?php include '../../includes/footer.php'; ?>
</body>
</html>
