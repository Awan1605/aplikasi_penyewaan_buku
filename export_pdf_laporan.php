<?php
require('fpdf/fpdf.php');
include 'koneksi2.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 0, 255);  // Blue color

        // Combine 'De' and 'Book' into one Cell with centered alignment
        $this->Cell(0, 10, 'De Book', 0, 1, 'C');  // Center align the text

        $this->SetTextColor(0, 0, 0);  // Reset to black color for subsequent text
        $this->Ln(5); // Add some space below the header

        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 5, 'Laporan Penyewaan Buku', 0, 1, 'C');  // Center align the text
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 3, '"Laporan ini dibuat untuk keperluan internal"', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Ln(10); // Adjusted space after subtitle

        // Table header
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(200, 220, 255);  // Light blue background
        $this->Cell(10, 10, 'No', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Nama', 1, 0, 'C', true);
        $this->Cell(50, 10, 'Judul Buku', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Harga Sewa', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Total Harga', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Denda', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Tanggal Pinjam', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Tanggal Kembali', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }

    public function displayTableRow($no, $data)
    {
        $this->SetFont('Arial', '', 12);

        // Format currency values
        $harga_sewa = 'Rp ' . number_format((float) $data['harga_sewa'], 0, ',', '.');
        $total_harga = 'Rp ' . number_format((float) $data['total_harga'], 0, ',', '.');
        $denda = 'Rp ' . number_format((float) $data['denda'], 0, ',', '.');

        // Format dates
        $tanggal_pinjam = date('d-m-Y', strtotime($data['tanggal_pinjam']));
        $tanggal_kembali = date('d-m-Y', strtotime($data['tanggal_kembali']));

        // Sanitize text data
        $nama_peminjam = htmlspecialchars($data['nama_peminjam']);
        $nama_buku = htmlspecialchars($data['nama_buku']);

        // Check for page overflow
        if ($this->GetY() + 10 > $this->PageBreakTrigger) {
            $this->AddPage();
        }

        // Populate row
        $this->Cell(10, 10, $no, 1, 0, 'C');
        $this->Cell(40, 10, $nama_peminjam, 1, 0, 'L');
        $this->Cell(50, 10, $nama_buku, 1, 0, 'L');
        $this->Cell(30, 10, $harga_sewa, 1, 0, 'R');
        $this->Cell(30, 10, $total_harga, 1, 0, 'R');
        $this->Cell(30, 10, $denda, 1, 0, 'R');
        $this->Cell(40, 10, $tanggal_pinjam, 1, 0, 'C');
        $this->Cell(40, 10, $tanggal_kembali, 1, 1, 'C');
    }

    public function displayTotalRow($total, $totalDenda)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(130, 10, 'Total Keseluruhan', 1, 0, 'C');
        $this->Cell(30, 10, 'Rp ' . number_format($total, 0, ',', '.'), 1, 0, 'R');
        $this->Cell(30, 10, 'Rp ' . number_format($totalDenda, 0, ',', '.'), 1, 0, 'R');
        $this->Cell(80, 10, '', 1, 1, 'C');
    }
}

$pdf = new PDF('L');  // Set orientation to Landscape
$pdf->AddPage();

$totalPayment = 0;
$totalDenda = 0;
$no = 1;

// Fetch data from database and populate table
$query = mysqli_query($koneksi, "SELECT * FROM sewa_selesai");
while ($data = mysqli_fetch_assoc($query)) {
    $pdf->displayTableRow($no++, $data);

    // Calculate totals
    $totalPayment += $data['total_harga'];
    $totalDenda += $data['denda'];
}

// Display total row
$pdf->displayTotalRow($totalPayment, $totalDenda);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="laporan_penyewaan.pdf"');
$pdf->Output('D', 'laporan_penyewaan.pdf');
exit;
?>