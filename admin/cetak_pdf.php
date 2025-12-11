<?php
// File: admin/cetak_pdf.php
// 1. Panggil Library FPDF
require('../includes/fpdf.php');
include('../includes/koneksi.php');

// 2. Buat Class PDF Custom untuk Header/Footer yang rapi
class PDF extends FPDF {
    // Header Halaman
    function Header() {
        // Logo (Sesuaikan path gambar logo Anda)
        // $this->Image('../assets/images/logo_bn1.jpeg',10,6,30);
        
        // Font Arial Bold 14
        $this->SetFont('Arial','B',14);
        // Judul
        $this->Cell(0,10,'LAPORAN PENERIMAAN PESERTA DIDIK BARU',0,1,'C');
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'SMK BAKTI NUSANTARA 666',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'Jl. Percobaan Km. 17 No. 65 Cileunyi, Kab. Bandung',0,1,'C');
        $this->Cell(0,5,'Tahun Ajaran 2025/2026',0,1,'C');
        
        // Garis bawah
        $this->Ln(5);
        $this->SetLineWidth(1);
        $this->Line(10, 42, 200, 42);
        $this->SetLineWidth(0);
        $this->Ln(10);
    }

    // Footer Halaman
    function Footer() {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Nomor Halaman
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// 3. Instansiasi Objek PDF
$pdf = new PDF('P','mm','A4'); // P = Potrait, mm = milimeter, A4
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Judul Laporan
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'DAFTAR SISWA DITERIMA',0,1,'L');
$pdf->Ln(2);

// 4. Header Tabel
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,220,255); // Warna Background Header
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(35,10,'No Pendaftaran',1,0,'C',true);
$pdf->Cell(60,10,'Nama Lengkap',1,0,'C',true);
$pdf->Cell(45,10,'Asal Sekolah',1,0,'C',true);
$pdf->Cell(40,10,'Jurusan',1,1,'C',true); // 1 di akhir artinya ganti baris

// 5. Isi Tabel dari Database
$pdf->SetFont('Arial','',10);

$sql = "SELECT 
            p.no_pendaftaran,
            pds.nama_lengkap,
            pas.nama_sekolah,
            j.kode as jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        WHERE p.status_pembayaran = 'Lunas' AND p.status_administrasi = 'Diverifikasi'
        ORDER BY j.nama ASC, pds.nama_lengkap ASC";

$result = $conn->query($sql);
$no = 1;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pdf->Cell(10,8,$no++,1,0,'C');
        $pdf->Cell(35,8,$row['no_pendaftaran'],1,0,'C');
        $pdf->Cell(60,8,$row['nama_lengkap'],1,0,'L');
        $pdf->Cell(45,8,$row['nama_sekolah'],1,0,'L');
        $pdf->Cell(40,8,$row['jurusan'],1,1,'C');
    }
} else {
    $pdf->Cell(190,10,'Belum ada data siswa diterima.',1,1,'C');
}

// 6. Tanda Tangan (Opsional - Biar Keren)
$pdf->Ln(20);
$pdf->SetFont('Arial','',11);
// Geser ke kanan
$pdf->Cell(120); 
$pdf->Cell(70,5,'Bandung, ' . date('d F Y'),0,1,'C');
$pdf->Cell(120);
$pdf->Cell(70,5,'Kepala Sekolah,',0,1,'C');
$pdf->Ln(20);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120);
$pdf->Cell(70,5,'( ...................................... )',0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(120);
$pdf->Cell(70,5,'NIP. 12345678 123456 1 001',0,1,'C');

// 7. Output PDF
$pdf->Output('I', 'Laporan_PPDB_Siswa_Diterima.pdf'); // I = Tampil di browser, D = Download langsung
?>