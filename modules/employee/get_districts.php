<?php
include '../../config/database.php';

if (isset($_GET['province_id']) && is_numeric($_GET['province_id'])) {
    $province_id = $_GET['province_id'];

    $sql = "SELECT code, full_name FROM districts WHERE province_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $province_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $districts = [];
    while ($row = $result->fetch_assoc()) {
        $districts[] = [
            'id' => $row['code'],
            'name' => $row['full_name']
        ];
    }

    echo json_encode($districts);
} else {
    echo json_encode([]);
}
?>
