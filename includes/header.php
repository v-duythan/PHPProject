<?php include_once __DIR__ . '/../config/config.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Quản lý nhân viên và lương</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>includes/css/list_style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>includes/css/form_style.css">


</head>
<body>
<header style="background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
    <h1 style="font-size: 24px; color: #343a40; margin: 20px;">Chào mừng, <?php echo htmlspecialchars($username); ?></h1>
    <a href="<?php echo BASE_URL; ?>public/logout.php" style="color: #007bff; text-decoration: none; margin: 20px;">Đăng xuất</a>
</header>
<div class="container">
