<?php include_once __DIR__ . '/../config/config.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$ho_ten = isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Guest';
$vaitro = isset($_SESSION['vaitro']) ? $_SESSION['vaitro'] : 'Guest';
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
        .logout-button {
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            text-align: center;
        }

        .logout-button:hover {
            background-color: #ececec;
        }
    </style>
</head>
<body>
    <header style="background-color: #f8f9fa; padding: 15px; text-align: center; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
        <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Logo" style="height: 50px; margin-left: 20px; cursor: pointer"
             onclick="window.location.href='<?php echo BASE_URL; ?>public/index.php'">
        <h1 style="font-size: 24px; color: #343a40; margin: 20px;">Chào
            mừng, <?php echo htmlspecialchars($ho_ten); ?></h1>

        <h1 style="margin: 20px;">Hôm nay là ngày: <?php echo date('d/m/Y'); ?></h1>

            <nav>
                <a href="<?php echo BASE_URL; ?>public/index.php">Trang chủ</a>
                <a href="<?php echo BASE_URL; ?>public/contact.php">Liên hệ</a>
                <a href="<?php echo BASE_URL; ?>public/about.php">Giới thiệu</a>

                <a href="<?php echo BASE_URL; ?>public/logout.php" class="logout-button">Đăng xuất</a>
                <a href="<?php echo BASE_URL; ?>public/change_password.php" class="logout-button"
                style="margin-right: 20px">Đổi mật khẩu</a>
            </nav>

    </header>
<div class="container">
