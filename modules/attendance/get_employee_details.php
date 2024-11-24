<?php
require_once '../../config/database.php';

if (isset($_GET['nhan_vien_id'])) {
    $nhan_vien_id = $_GET['nhan_vien_id'];

    $sql = "SELECT chuc_vu.ten_chuc_vu, phong_ban.ten_phong_ban
            FROM nhan_vien
            JOIN chuc_vu ON nhan_vien.chuc_vu_id = chuc_vu.id
            JOIN phong_ban ON chuc_vu.phong_ban_id = phong_ban.id
            WHERE nhan_vien.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $nhan_vien_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode($employee);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
}
?>