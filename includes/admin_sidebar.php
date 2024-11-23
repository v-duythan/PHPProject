<?php include_once __DIR__ . '/../config/config.php'; ?>
<aside class="sidebar">
    <h2>Menu Admin</h2>
    <ul style="list-style-type:none; padding:0;">
        <li style="margin-bottom:10px;">
            <a href="<?php echo BASE_URL; ?>modules/employee/list.php" style="color:#333; text-decoration:none;">Danh
                sách nhân viên</a>
            <ul style="list-style-type:none; padding-left:20px;">
                <li><a href="<?php echo BASE_URL; ?>modules/employee/add.php" style="color:#333; text-decoration:none;">Thêm nhân viên</a>
                </li>
            </ul>
        </li>
        <li style="margin-bottom:10px;">
            <a href="<?php echo BASE_URL; ?>modules/salary/list.php" style="color:#333; text-decoration:none;">Quản lý
                lương</a>
        </li>
        <li style="margin-bottom:10px;">
            <a href="<?php echo BASE_URL; ?>modules/user/list.php" style="color:#333; text-decoration:none;">Quản lý
                người dùng</a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/department/list.php" style="color:#333; text-decoration:none;">Quản
                lý phòng ban</a>
        </li>
        <li>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/attendance/list.php" style="color:#333; text-decoration:none;">Quản lý Chấm công</a></li>
        <ul style="list-style-type:none; padding-left:20px;">
            <li><a href="<?php echo BASE_URL; ?>modules/attendance/add.php" style="color:#333; text-decoration:none;">Thêm bản ghi chấm công</a>
            </li>
        </ul>
        </li>
    </ul>
</aside>