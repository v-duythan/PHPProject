<?php
$sql_departments = "SELECT COUNT(*) as total_departments FROM phong_ban";
$result_departments = $conn->query($sql_departments);
$total_departments = $result_departments->fetch_assoc()['total_departments'];

// Query to get the number of employees
$sql_employees = "SELECT COUNT(*) as total_employees FROM nhan_vien";
$result_employees = $conn->query($sql_employees);
$total_employees = $result_employees->fetch_assoc()['total_employees'];

// Query to get the total salary
$sql_total_salary = "SELECT SUM(tong_luong) as total_salary FROM luong WHERE thang = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)";
$result_total_salary = $conn->query($sql_total_salary);
$total_salary = $result_total_salary->fetch_assoc()['total_salary'];


// Query to get the total salary for each department
$sql_salary_by_department = "
    SELECT phong_ban.ten_phong_ban, SUM(luong.tong_luong) as total_salary
    FROM luong
    JOIN nhan_vien ON luong.nhan_vien_id = nhan_vien.id
        join chuc_vu on nhan_vien.chuc_vu_id = chuc_vu.id
        JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
    WHERE luong.thang = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH)
    GROUP BY phong_ban.ten_phong_ban
";
$result_salary_by_department = $conn->query($sql_salary_by_department);
?>
<style>
    .dashboard-item {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        margin: 10px;
        border-radius: 5px;
    }
</style>

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
    <div class="dashboard-item">
        <h2>Salary by Department</h2>
        <ul>
            <?php while ($row = $result_salary_by_department->fetch_assoc()) { ?>
                <li style="list-style: none"><?php echo htmlspecialchars($row['ten_phong_ban']) . ': ' . number_format($row['total_salary'], 0, ',', '.'); ?> VNĐ</li>
            <?php } ?>
        </ul>
    </div>
</div>