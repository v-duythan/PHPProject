<?php include_once __DIR__ . '/../config/config.php'; ?>
<aside class="sidebar">
    <h2>Menu Admin</h2>
    <ul>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/employee/list.php">Danh sách nhân viên</a>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>modules/employee/add.php">Add</a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/salary/list.php">Quản lý lương</a>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>modules/salary/add.php">Add</a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/user/list.php">Quản lý người dùng</a>
        </li>
        <li>
            <a href="<?php echo BASE_URL; ?>modules/department/list.php">Quản lý phòng ban</a>

        </li>
    </ul>
</aside>