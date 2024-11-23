<?php include_once __DIR__ . '/../config/config.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$ho_ten = isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Quản lý nhân viên và lương</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>includes/css/list_style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>includes/css/form_style.css">


    <style>
        /* Định dạng header chung */
        header {
            position: sticky; /* Giữ cố định khi cuộn */
            top: 0; /* Vị trí cách mép trên */
            z-index: 999; /* Đảm bảo luôn ở trên các phần tử khác */
            background-color: #ffffff; /* Màu nền header */
            padding: 10px 20px; /* Khoảng cách bên trong */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Tạo bóng cho header */
        }

        /* Định dạng tiêu đề trong header */
        header h1 {
            font-size: 1.5rem;
            color: #333;
            margin: 0;
            display: inline-block;
        }

        /* Định dạng cho menu trong header nếu có */
        header nav {
            float: right; /* Đẩy menu sang bên phải */
        }

        header nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        header nav a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <header style="background-color: #f8f9fa; padding: 15px; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
        <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo" style="height: 50px; margin-left: 20px; cursor: pointer"
             onclick="window.location.href='<?php echo BASE_URL; ?>public/index.php'">
        <h1 style="font-size: 24px; color: #343a40; margin: 20px;">Chào
            mừng, <?php echo htmlspecialchars($ho_ten); ?></h1>
        <a href="<?php echo BASE_URL; ?>public/logout.php" style="color: #007bff; text-decoration: none; margin: 20px;">Đăng
            xuất</a>
    </header>
<div class="container">
