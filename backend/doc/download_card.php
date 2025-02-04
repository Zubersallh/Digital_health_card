<?php
session_start();
require('fpdf/fpdf.php');
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();


$patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
if (!$patient_id) {
    die("Invalid patient ID.");
}


$ret = "SELECT * FROM patient WHERE patient_id = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('i', $patient_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Patient not found.");
}
$row = $res->fetch_object();


$blood_type = isset($row->blood_type) ? $row->blood_type : 'N/A';


$logoPath = 'uploads/photo2.jpg';

$qrPath   = !empty($row->qr_code_image_path) && file_exists($row->qr_code_image_path)
    ? $row->qr_code_image_path
    : '';



$pdf = new FPDF('L', 'mm', array(54, 86));
$pdf->AddPage();


$pdf->SetDrawColor(0, 0, 0);
$pdf->Rect(1, 1, 84, 52, 'D');


$pdf->SetFillColor(255, 193, 7);
$pdf->Rect(1, 1, 84, 15, 'F');


$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(0, 5);
$pdf->Cell(86, 6, 'DIGITAL HEALTH CARD', 0, 0, 'C');



if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 3, 17, 18, 18);
}


$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);



$pdf->SetXY(25, 18);
$pdf->Cell(0, 5, 'Name: ' . $row->first_name . ' ' . $row->last_name, 0, 1);


$pdf->SetXY(25, 23);
$pdf->Cell(0, 5, 'Phone: ' . ($row->contact_information ?? 'N/A'), 0, 1);


$pdf->SetXY(25, 28);
$pdf->Cell(0, 5, 'Blood Group: ' . $blood_type, 0, 1);


if (!empty($qrPath) && file_exists($qrPath)) {

    $pdf->Image($qrPath, 60, 17, 20, 20);
} else {

    $pdf->SetXY(60, 25);
    $pdf->Cell(20, 5, 'QR Code not available', 0, 1, 'C');
}


$pdfFileName = 'patient_card_' . $patient_id . '.pdf';
$pdf->Output('D', $pdfFileName);
exit;
