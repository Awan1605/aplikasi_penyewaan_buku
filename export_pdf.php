<?php
require_once('fpdf/fpdf.php');
include 'koneksi2.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 0, 255);  // Blue color
        $this->Cell(90, 10, 'De', 0, 0, 'R');  // 'De' aligned to right
        $this->SetTextColor(0, 0, 0);  // Black color
        $this->Cell(90, 10, 'Book', 0, 1, 'L');  // 'Book' aligned to left

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 5, 'Nota Tanda Penyewaan', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 3, '"Tunjukan kepada admin jika ingin mengambil buku"', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Ln(10); // Adjusted space after subtitle
    }

    public function displayRentalDetails($data)
    {
        global $koneksi;
        static $recordCount = 0;

        if ($recordCount >= 4) {
            $this->AddPage();
            $recordCount = 0;
        }

        $currentY = (float) $this->GetY();
        $query_sampul = mysqli_query($koneksi, "SELECT sampul FROM kelola_data_buku WHERE judul = '" . $data['nama_buku'] . "'");
        $sampul_data = mysqli_fetch_assoc($query_sampul);

        if (!empty($sampul_data['sampul']) && file_exists($sampul_data['sampul'])) {
            $this->Image($sampul_data['sampul'], 10, $currentY, 30, 40);
            $this->SetY($currentY);
        } else {
            $this->Rect(10, $currentY, 30, 40);
        }

        // Format currency values
        $harga_sewa = 'Rp ' . number_format((float) $data['harga_sewa'], 0, ',', '.');
        $total_harga = 'Rp ' . number_format((float) $data['total_harga'], 0, ',', '.');

        // Format dates
        $tanggal_pinjam = date('d-m-Y', strtotime($data['tanggal_pinjam']));
        $tanggal_kembali = date('d-m-Y', strtotime($data['tanggal_kembali']));

        // Sanitize text data
        $nama_peminjam = htmlspecialchars($data['nama_peminjam']);
        $nama_buku = htmlspecialchars($data['nama_buku']);

        $details = [
            'Nama' => $nama_peminjam,
            'Judul Buku' => $nama_buku,
            'Harga Sewa' => $harga_sewa,
            'Total Harga' => $total_harga,
            'Tanggal Pinjam' => $tanggal_pinjam,
            'Tanggal Kembali' => $tanggal_kembali
        ];

        $this->SetTextColor(0, 0, 0);
        foreach ($details as $label => $value) {
            $this->SetX(45);
            $this->Cell(50, 8, $label, 1, 0, 'L');
            $this->Cell(100, 8, $value, 1, 1, 'L');
        }

        $this->Ln(5);
        $recordCount++;
    }

    public function displayTotalPayment($total)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Total Pembayaran: ' . 'Rp ' . number_format($total, 0, ',', '.'), 1, 1, 'C');
        $this->Ln(10);
    }
}

$pdf = new PDF();
$pdf->AddPage();

$totalPayment = 0;

$query = mysqli_query($koneksi, "SELECT * FROM status_sewa");
while ($data = mysqli_fetch_assoc($query)) {
    $pdf->SetFont('Arial', '', 12);
    $pdf->displayRentalDetails($data);

    // Add total price
    $totalPayment += $data['total_harga'];
}

// Display total payment
$pdf->displayTotalPayment($totalPayment);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="kartu_sewa.pdf"');
$pdf->Output('D', 'kartu_sewa.pdf');
exit;
?>