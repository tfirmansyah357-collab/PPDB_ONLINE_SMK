<?php
// File: cetak_bukti_siswa.php
session_start();
require('includes/fpdf.php');
include('includes/koneksi.php');

// 1. Cek Login Siswa
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_mode'] !== 'siswa') {
    header("Location: login_siswa.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Ambil Data Siswa
$sql = "SELECT 
            p.id as pendaftar_id,
            p.no_pendaftaran,
            p.tanggal_daftar,
            pds.nama_lengkap,
            pds.nis,
            pds.no_handphone,
            rwa.nama_wilayah as alamat,
            pas.nama_sekolah,
            j.nama as jurusan
        FROM pendaftar p
        JOIN pendaftar_data_siswa pds ON p.id = pds.pendaftar_id
        JOIN pendaftar_asal_sekolah pas ON p.id = pas.pendaftar_id
        JOIN jurusan j ON p.jurusan_id = j.id
        JOIN ref_wilayah_asal rwa ON pds.wilayah_asal_id = rwa.id
        WHERE p.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data pendaftaran tidak ditemukan.");
}

// 3. Cek Kelengkapan Berkas (Rapor, Akta, KK)
$pendaftar_id = $data['pendaftar_id'];
$berkas_ada = []; // Array untuk menyimpan berkas apa saja yang ada

$sql_berkas = "SELECT jenis FROM pendaftar_berkas WHERE pendaftar_id = ?";
$stmt_b = $conn->prepare($sql_berkas);
$stmt_b->bind_param("i", $pendaftar_id);
$stmt_b->execute();
$res_b = $stmt_b->get_result();
while ($row = $res_b->fetch_assoc()) {
    $berkas_ada[] = $row['jenis']; // Simpan jenis berkas ke array
}

// Fungsi Helper untuk Cek Ada/Tidak
function cekStatus($jenis, $array_berkas) {
    return in_array($jenis, $array_berkas) ? "ADA (Sudah Diupload)" : "TIDAK ADA";
}

// 4. Generate PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,7,'BUKTI PENDAFTARAN PPDB ONLINE',0,1,'C');
        $this->SetFont('Arial','B',16);
        $this->Cell(0,7,'SMK BAKTI NUSANTARA 666',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,5,'Tahun Ajaran 2025/2026',0,1,'C');
        $this->Ln(5);
        $this->SetLineWidth(0.5);
        $this->Line(10, 32, 200, 32);
        $this->Ln(10);
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AddPage();

// Info Pendaftaran
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,10,'A. DATA PENDAFTAR',0,1);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,8,'No. Pendaftaran',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['no_pendaftaran'],0,1);

$pdf->Cell(50,8,'Tanggal Daftar',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,date('d F Y', strtotime($data['tanggal_daftar'])),0,1);

$pdf->Cell(50,8,'Nama Lengkap',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['nama_lengkap'],0,1);

$pdf->Cell(50,8,'NIS / NISN',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['nis'] ? $data['nis'] : '-',0,1);

$pdf->Cell(50,8,'Asal Sekolah (SMP)',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['nama_sekolah'],0,1);

$pdf->Cell(50,8,'Jurusan Pilihan',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['jurusan'],0,1);

$pdf->Cell(50,8,'Alamat / Wilayah',0,0);
$pdf->Cell(5,8,':',0,0);
$pdf->Cell(0,8,$data['alamat'],0,1);

$pdf->Ln(10);

// Info Kelengkapan Berkas
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,10,'B. KELENGKAPAN BERKAS PERSYARATAN',0,1);

// Header Tabel
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(230,230,230);
$pdf->Cell(10,10,'No',1,0,'C',true);
$pdf->Cell(90,10,'Jenis Dokumen',1,0,'L',true);
$pdf->Cell(90,10,'Keterangan',1,1,'C',true);

// Isi Tabel
$pdf->SetFont('Arial','',10);

// 1. Rapor
$pdf->Cell(10,10,'1',1,0,'C');
$pdf->Cell(90,10,'Rapor Semester Terakhir',1,0,'L');
$pdf->Cell(90,10, cekStatus('RAPOR', $berkas_ada),1,1,'C');

// 2. Akta
$pdf->Cell(10,10,'2',1,0,'C');
$pdf->Cell(90,10,'Akta Kelahiran',1,0,'L');
$pdf->Cell(90,10, cekStatus('AKTA', $berkas_ada),1,1,'C');

// 3. KK
$pdf->Cell(10,10,'3',1,0,'C');
$pdf->Cell(90,10,'Kartu Keluarga (KK)',1,0,'L');
$pdf->Cell(90,10, cekStatus('KK', $berkas_ada),1,1,'C');


// Footer Tanda Tangan
$pdf->Ln(20);
$pdf->SetFont('Arial','',10);
$pdf->Cell(120); // Geser kanan
$pdf->Cell(70,5,'Dicetak pada: ' . date('d-m-Y H:i'),0,1,'C');
$pdf->Cell(120);
$pdf->Cell(70,5,'Calon Siswa,',0,1,'C');
$pdf->Ln(20);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(120);
$pdf->Cell(70,5,'( ' . $data['nama_lengkap'] . ' )',0,1,'C');

$pdf->Output('I', 'Bukti_Pendaftaran_' . $data['no_pendaftaran'] . '.pdf');
?>