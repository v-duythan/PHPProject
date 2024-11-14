<?php
$nhan_vien_id = isset($_SESSION['nhan_vien_id']) ? $_SESSION['nhan_vien_id'] : null;
?>
<aside class="sidebar">
    <h2>Menu Người dùng</h2>
    <ul>
        <?php if ($nhan_vien_id): ?>
            <li><a href="<?php echo BASE_URL; ?>modules/employee/profile.php?id=<?php echo $nhan_vien_id; ?>">Xem thông tin cá nhân</a></li>
            <li><a href="<?php echo BASE_URL; ?>modules/salary/details.php?id=<?php echo $nhan_vien_id; ?>">Xem lương</a></li>
        <?php else: ?>
            <li><a href="<?php echo BASE_URL; ?>public/login.php">Đăng nhập để xem thông tin</a></li>
        <?php endif; ?>
    </ul>
</aside>
