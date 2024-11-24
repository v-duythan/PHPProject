<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách chấm công</title>
    <link rel="stylesheet" href="attendance.css"> <!-- Đường dẫn tới tệp CSS -->
</head>
<body>
<?php
require_once '../../config/database.php';
session_start();
// Xử lý tìm kiếm (nếu có)
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;
$filter_employee = $_GET['employee'] ?? null;
$filter_department = $_GET['department'] ?? null;
$filter_status = $_GET['status'] ?? null;

$departments_sql = "SELECT ten_phong_ban FROM phong_ban";
$departments_result = $conn->query($departments_sql);
$departments = [];
if ($departments_result->num_rows > 0) {
    while ($department = $departments_result->fetch_assoc()) {
        $departments[] = $department['ten_phong_ban'];
    }
}

$sql = "SELECT cc.*, nv.ho_ten, pb.ten_phong_ban
        FROM cham_cong cc
        JOIN nhan_vien nv ON cc.nhan_vien_id = nv.id
        join chuc_vu cv on nv.chuc_vu_id = cv.id
        JOIN phong_ban pb ON cv.phong_ban_id = pb.id
        WHERE 1=1";

$params = [];
if ($from_date) {
    $sql .= " AND cc.ngay >= ?";
    $params[] = $from_date;
}
if ($to_date) {
    $sql .= " AND cc.ngay <= ?";
    $params[] = $to_date;
}
if ($filter_employee) {
    $sql .= " AND nv.ho_ten LIKE ?";
    $params[] = "%" . $filter_employee . "%";
}
if ($filter_department) {
    $sql .= " AND pb.ten_phong_ban LIKE ?";
    $params[] = "%" . $filter_department . "%";
}
if ($filter_status) {
    $sql .= " AND cc.trang_thai = ?";
    $params[] = $filter_status;
}
$sql .= " ORDER BY cc.ngay DESC";

// Chuẩn bị statement
$stmt = $conn->prepare($sql);

// Bind tham số nếu có
if ($params) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}

// Thực thi truy vấn
$stmt->execute();
$result = $stmt->get_result();
include_once __DIR__ . '/../../config/config.php';

?>

<?php
include '../../includes/header.php';
if ($vaitro == 'Admin') {
    include '../../includes/admin_sidebar.php';
} else {
    include '../../includes/user_sidebar.php';
}
?>

<!-- Container chính, chiếm phần còn lại -->
<div class="container">
    <h2>Danh sách chấm công</h2>

    <!-- Form nhập (bộ lọc) -->
    <form method="GET" action="" class="filter-form">
        <label for="from_date">Từ ngày:</label>
        <input type="date" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>">
        <label for="to_date">Đến ngày:</label>
        <input type="date" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>">
        <label for="employee">Nhân viên:</label>
        <input type="text" name="employee" id="employee" placeholder="Tên nhân viên" value="<?= htmlspecialchars($filter_employee) ?>">
        <label for="department">Phòng ban:</label>
        <select name="department" id="department">
            <option value="">Chọn phòng ban</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= htmlspecialchars($department) ?>" <?= $filter_department == $department ? 'selected' : '' ?>>
                    <?= htmlspecialchars($department) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="status">Trạng thái:</label>
        <select name="status" id="status">
            <option value="">Chọn trạng thái</option>
            <option value="CóMặt" <?= $filter_status == 'CóMặt' ? 'selected' : '' ?>>Đi làm</option>
            <option value="VắngMặt" <?= $filter_status == 'VắngMặt' ? 'selected' : '' ?>>Vắng mặt</option>
            <option value="NghỉPhép" <?= $filter_status == 'NghỉPhép' ? 'selected' : '' ?>>Nghỉ phép</option>
        </select>
        <button type="submit">Lọc</button>
    </form>
    <!-- Bảng dữ liệu -->
    <table>
        <thead>
        <tr>
            <th>Ngày</th>
            <th>Nhân viên</th>
            <th>Phòng ban</th>
            <th>Trạng thái</th>
            <th>Giờ vào</th>
            <th>Giờ ra</th>
            <th>Lý do</th>
            <th>Ghi chú</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ngay'] ?></td>
                    <td><?= htmlspecialchars($row['ho_ten']) ?></td>
                    <td><?= htmlspecialchars($row['ten_phong_ban']) ?></td>
                    <td>
                        <?php
                        switch ($row['trang_thai']) {
                            case 'CóMặt':
                                echo '<span style="color: green;">Đi làm</span>';
                                break;
                            case 'VắngMặt':
                                echo '<span style="color: red;">Vắng mặt</span>';
                                break;
                            case 'NghỉPhép':
                                echo '<span style="color: blue;">Nghỉ phép</span>';
                                break;
                            default:
                                echo '<span style="color: orange;">Không xác định</span>';
                                break;
                        }
                        ?>
                    </td>
                    <td><?= $row['gio_vao'] ?: '-' ?></td>
                    <td><?= $row['gio_ra'] ?: '-' ?></td>
                    <td><?= $row['ly_do_vang_mat'] ?: '-' ?></td>
                    <td><?= $row['ghi_chu'] ?: '-' ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>">Sửa</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="no-data">Không có dữ liệu</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>