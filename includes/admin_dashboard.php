<?php
$sql_departments = "SELECT COUNT(*) as total_departments FROM phong_ban";
$result_departments = $conn->query($sql_departments);
$total_departments = $result_departments->fetch_assoc()['total_departments'];

// Query to get the number of employees
$sql_employees = "SELECT COUNT(*) as total_employees FROM nhan_vien";
$result_employees = $conn->query($sql_employees);
$total_employees = $result_employees->fetch_assoc()['total_employees'];

// Query to get the total salary
$sql_total_salary = "SELECT SUM(tong_luong) as total_salary FROM luong";
$result_total_salary = $conn->query($sql_total_salary);
$total_salary = $result_total_salary->fetch_assoc()['total_salary'];
?>
<div class="dashboard">
    <h1>Admin Dashboard</h1>
    <div class="dashboard-item">
        <h2>Number of Departments</h2>
        <p><?php echo $total_departments; ?></p>
    </div>
    <div class="dashboard-item">
        <h2>Number of Employees</h2>
        <p><?php echo $total_employees; ?></p>
    </div>
    <div class="dashboard-item">
        <h2>Total Salary</h2>
        <p><?php echo number_format($total_salary, 0, ',', '.'); ?> VNĐ</p>
    </div>
</div>