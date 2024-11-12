<?php
include '../../config/database.php';

if (isset($_GET['phong_ban_id']) && is_numeric($_GET['phong_ban_id'])) {
    $phong_ban_id = $_GET['phong_ban_id'];

    $sql_positions = "SELECT id, ten_chuc_vu FROM chuc_vu WHERE phong_ban_id = ?";
    $stmt = $conn->prepare($sql_positions);
    $stmt->bind_param("i", $phong_ban_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $positions = [];
    while ($position = $result->fetch_assoc()) {
        $positions[] = $position;
    }

    echo json_encode($positions);
} else {
    echo json_encode([]);
}
?>
