<?php
// Start session and check if user is logged in
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once '../../config/database.php';

// Assuming you have a MySQLi connection instance named $conn
$sql = '
    SELECT nguoi_dung.id, nguoi_dung.ten_dang_nhap, nhan_vien.email, nguoi_dung.trang_thai
    FROM nguoi_dung
    JOIN nhan_vien ON nguoi_dung.id = nhan_vien.nguoi_dung_id
';

$result = $conn->query($sql);

include_once __DIR__ . '/../../config/config.php';
?>



<?php include '../../includes/header.php'; ?>

<?php include '../../includes/admin_sidebar.php'; ?>

<main class="container">
    <h1>Thông tin người dùng</h1>
    <table>
        <tr>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['ten_dang_nhap']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['trang_thai']) . "</td>
                    <td>
                        <a href='edit.php?id=" . htmlspecialchars($row['id']) . "'>Edit</a> |
                        <a href='delete.php?id=" . htmlspecialchars($row['id']) . "' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                    </td>
                  </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>0 results</td></tr>";
        }
        ?>
    </table>
</main>

<!-- Include footer -->
<?php include '../../includes/footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>
