<?php include_once __DIR__ . '/../config/config.php'; ?>

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
<header>
    <style>
        .alert {
            padding: 15px;
            background-color: #f44336; /* Màu nền đỏ cho thông báo lỗi */
            color: white; /* Màu chữ trắng */
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .alert-success {
            background-color: #4CAF50; /* Màu nền xanh cho thông báo thành công */
        }

        /* Button đóng thông báo */
        .alert .close-btn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 20px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .alert .close-btn:hover {
            color: black;
        }
    </style>

    <h1>Chào mừng, <?php echo htmlspecialchars($username); ?></h1>
    <a href="<?php echo BASE_URL; ?>public/logout.php">Đăng xuất</a>
</header>
<div class="container">
