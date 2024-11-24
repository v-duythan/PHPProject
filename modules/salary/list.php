<?php
include '../../config/database.php';
include_once __DIR__ . '/../../config/config.php';
require '../../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();


$current_month = date('n'); // Tháng hiện tại (1-12)
$current_year = date('Y'); // Năm hiện tại

// Tính default_month và default_year
$default_month = $current_month - 1;
$default_year = $current_year;

if ($default_month == 0) { // Nếu tháng hiện tại là 1, thì tháng trước là 12, và năm giảm 1
    $default_month = 12;
    $default_year--;
}

// Nhận giá trị từ GET hoặc gán mặc định
$thang = isset($_GET['thang']) && is_numeric($_GET['thang']) ? (int)$_GET['thang'] : $default_month;
$nam = isset($_GET['nam']) && is_numeric($_GET['nam']) ? (int)$_GET['nam'] : $default_year;

// Đảm bảo giá trị hợp lệ
if ($thang < 1 || $thang > 12) {
    $thang = $default_month;
}
if ($nam < 1900 || $nam > $current_year) {
    $nam = $default_year;
}

// Kiểm tra xem nút "Xuất Excel" có được nhấn không
if (isset($_POST['export_excel'])) {
    // Ensure no output is sent before headers
    ob_clean();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tiêu đề cột
    $sheet->setCellValue('A1', 'Nhân Viên');
    $sheet->setCellValue('B1', 'Số Ngày Công');
    $sheet->setCellValue('C1', 'Số Ngày Nghỉ Phép');
    $sheet->setCellValue('D1', 'Số Ngày Vắng');
    $sheet->setCellValue('E1', 'Số Giờ Làm Thêm');
    $sheet->setCellValue('F1', 'Lương Cơ Bản');
    $sheet->setCellValue('G1', 'Lương Ngày Công');
    $sheet->setCellValue('H1', 'Lương Làm Thêm');
    $sheet->setCellValue('I1', 'Phụ Cấp');
    $sheet->setCellValue('J1', 'Thưởng');
    $sheet->setCellValue('K1', 'Bảo Hiểm');
    $sheet->setCellValue('L1', 'Khấu Trừ Khác');
    $sheet->setCellValue('M1', 'Tổng Lương');

    // Truy vấn dữ liệu
    $sql = "SELECT luong.*, nhan_vien.ho_ten FROM luong
            JOIN nhan_vien ON luong.nhan_vien_id = nhan_vien.id
            WHERE nam = ? AND thang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nam, $thang);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ghi dữ liệu vào Excel
    $rowNumber = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValueExplicit('A' . $rowNumber, $row['ho_ten'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('B' . $rowNumber, $row['so_ngay_cong']);
        $sheet->setCellValue('C' . $rowNumber, $row['so_ngay_nghi_phep']);
        $sheet->setCellValue('D' . $rowNumber, $row['so_ngay_vang']);
        $sheet->setCellValue('E' . $rowNumber, $row['so_gio_lam_them']);
        $sheet->setCellValue('F' . $rowNumber, $row['luong_co_ban']);
        $sheet->setCellValue('G' . $rowNumber, $row['luong_ngay_cong']);
        $sheet->setCellValue('H' . $rowNumber, $row['luong_lam_them']);
        $sheet->setCellValue('I' . $rowNumber, $row['phu_cap']);
        $sheet->setCellValue('J' . $rowNumber, $row['thuong']);
        $sheet->setCellValue('K' . $rowNumber, $row['bao_hiem']);
        $sheet->setCellValue('L' . $rowNumber, $row['khoan_tru_khac']);
        $sheet->setCellValue('M' . $rowNumber, $row['tong_luong']);
        $rowNumber++;
    }

    foreach (range('A', 'M') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    // Cấu hình tải file
    $filename = 'luong_thang_' . $thang . '_' . $nam . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Xuất file
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Truy vấn dữ liệu cho bảng HTML
$sql = "SELECT luong.*, nhan_vien.ho_ten FROM luong
        JOIN nhan_vien ON luong.nhan_vien_id = nhan_vien.id
        WHERE nam = ? AND thang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $nam, $thang);
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>

<?php include '../../includes/header.php';
if ($vaitro !== 'Admin') {
    include '../../includes/user_sidebar.php';
} else {
    include '../../includes/admin_sidebar.php';
    exit;
}
?>
<div class="container">
    <form action="calculate.php" method="GET" style="background-color: #f5f5f5; padding: 20px; border-radius: 5px;">
        <label for="month" style="font-weight: bold;">Tháng:</label>
        <select name="month" id="month" required style="margin-bottom: 10px; padding: 10px; height: 40px;">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= $i == $default_month ? 'selected' : '' ?>>Tháng <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <br><br>
        <label for="year" style="font-weight: bold;">Năm:</label>
        <input type="number" id="year" name="year" min="1900" max="<?= $current_year ?>" value="<?= $current_year ?>" required
               style="margin-bottom: 10px; padding: 10px; height: 40px;">
        <br><br>
        <input type="submit" value="Gửi"
               style="background-color: #4CAF50; color: white; height: 40px; width: 10%; border: none; border-radius: 3px; cursor: pointer; display: block; margin: 0 auto;">
    </form>
    <h1>Tính Lương Nhân Viên Tháng <?php echo $thang . '/' . $nam; ?></h1>
    <form action="list.php" method="post" style="margin-top: 20px;">
        <input type="hidden" name="export_excel" value="1" />
        <input type="submit" value="Xuất Excel" style="background-color: #f44336; color: white; height: 40px; width: 10%; border: none; border-radius: 3px; cursor: pointer;">
    </form>
    <table>
        <thead>
        <tr>
            <th>Nhân Viên</th>
            <th>Số Ngày Công</th>
            <th>Số Ngày Nghỉ Phép</th>
            <th>Số Ngày Vắng</th>
            <th>Số Giờ Làm Thêm</th>
            <th>Tổng Lương</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) { ?>
            <tr>
                <td><?php echo $row['ho_ten']; ?></td>
                <td><?php echo $row['so_ngay_cong']; ?></td>
                <td><?php echo $row['so_ngay_nghi_phep']; ?></td>
                <td><?php echo $row['so_ngay_vang']; ?></td>
                <td><?php echo $row['so_gio_lam_them']; ?></td>
                <td><?php echo number_format($row['tong_luong'], 0, ',', '.'); ?> VNĐ</td>
            </tr>
            <tr>
                <td colspan="6">
                    <details>
                        <summary>Chi tiết</summary>
                        <p>Lương Cơ Bản: <?php echo number_format($row['luong_co_ban'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Lương Ngày Công: <?php echo number_format($row['luong_ngay_cong'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Lương Làm Thêm: <?php echo number_format($row['luong_lam_them'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Phụ Cấp: <?php echo number_format($row['phu_cap'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Thưởng: <?php echo number_format($row['thuong'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Bảo Hiểm: <?php echo number_format($row['bao_hiem'], 0, ',', '.'); ?> VNĐ</p>
                        <p>Khấu Trừ Khác: <?php echo number_format($row['khoan_tru_khac'], 0, ',', '.'); ?> VNĐ</p>
                    </details>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>