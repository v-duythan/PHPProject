<?php
$nhan_vien_id = isset($_SESSION['nhan_vien_id']) ? $_SESSION['nhan_vien_id'] : null;
$ho_ten = isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : null;
?>
<aside class="sidebar">
    <h2>Menu Người dùng</h2>
    <ul style="list-style-type:none; padding:0;">
        <?php if ($nhan_vien_id): ?>
            <li style="margin-bottom:10px;">
                <a href="<?php echo BASE_URL; ?>public/profile.php?id=<?php echo $nhan_vien_id; ?>" style="color:#333; text-decoration:none;">Xem thông tin cá nhân</a>
            </li>
            <li style="margin-bottom:10px;">
                <a href="<?php echo BASE_URL; ?>modules/salary/details.php?id=<?php echo $nhan_vien_id; ?>" style="color:#333; text-decoration:none;">Xem lương</a>
            </li>
            <li style="margin-bottom:10px;">
                <a href="<?php echo BASE_URL; ?>modules/attendance/user_add.php" style="color:#333; text-decoration:none;">Chấm công</a>
            </li>
            <li style="margin-bottom:10px;">
                <a href="<?php echo BASE_URL; ?>modules/attendance/list.php?employee=<?php echo $ho_ten; ?>" style="color:#333; text-decoration:none;">Xem bảng chấm công</a>
            </li>
        <?php else: ?>
            <li style="margin-bottom:10px;">
                <a href="<?php echo BASE_URL; ?>public/login.php" style="color:#333; text-decoration:none;">Đăng nhập để xem thông tin</a>
            </li>
        <?php endif; ?>
    </ul>
</aside>