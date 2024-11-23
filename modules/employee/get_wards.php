<?php
include '../../config/database.php';

if (isset($_GET['district_id']) && is_numeric($_GET['district_id'])) {
    $district_id = $_GET['district_id'];

    $sql = "SELECT code, full_name FROM wards WHERE district_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $district_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $wards = [];
    while ($row = $result->fetch_assoc()) {
        $wards[] = [
            'id' => $row['code'],
            'name' => $row['full_name']
        ];
    }

    echo json_encode($wards);
} else {
    echo json_encode([]);
}
?>
