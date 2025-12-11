<?php 
session_start(); 
include('includes/koneksi.php'); 
include('includes/header.php'); 
?>

<h2 class="text-primary mb-4">Tentang Sekolah Kami</h2>

<div class="card p-4 shadow-sm">
    <p>SMK Bakti Nusantara 666 didirikan pada tahun 2007. Sekolah ini telah terakreditasi A dengan Nomor SK Akreditasi 02.00/203/SK/BAN-SM/XII/2018 pada tanggal 4 Desember 2018.</p>
    <h3 class="mt-4 text-secondary">Visi & Misi</h3>
    <p>
        <strong>Visi:</strong>Menjadi lembaga pendidikan kejuruan yang profesional, mandiri, dan unggul dalam bidang teknologi dan rekayasa, dengan lulusan yang kompetitif di dunia kerja.
    </p>
    <p>
        <strong>Misi:</strong> 
        Mewujudkan lingkungan pendidikan yang Islami dan berakhlak mulia.
        <br>
        Menumbuhkan budaya disiplin sekolah dan semangat keunggulan yang kreatif dan inovatif.
        <br>
        Menyelenggarakan pembelajaran yang berorientasi pada kebutuhan industri dan menciptakan lulusan yang siap berkarier.
        <br>
        Mengembangkan kompetensi siswa di bidang keahliannya agar mampu bersaing dan beradaptasi di era globalisasi. 
    </p>

    <h3 class="mt-5 text-secondary">Jurusan yang Dibuka (TA 2025/2026)</h3>
    <ul class="list-group list-group-flush mb-4">
        <?php if (empty($jurusan_options_db)): ?>
            <li class="list-group-item">Jurusan belum ditentukan.</li>
        <?php else: ?>
            <?php foreach($jurusan_options_db as $jurusan): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($jurusan['kode']); ?></strong> - <?php echo htmlspecialchars($jurusan['nama']); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    
    <h3 class="mt-5 mb-4 text-secondary">Fasilitas Unggulan Kami</h3>
    
    <div class="row g-3">
        
        <div class="col-lg-3 col-md-6 text-center">
            <div class="facility-box shadow-sm border p-2 h-100">
                <img src="<?php echo $base_url; ?>assets/images/lab_komputer.jpeg" class="img-fluid rounded" alt="Laboratorium Komputer">
                <p class="mt-2 mb-0 fw-bold">Laboratorium Komputer Modern</p>
                <small class="text-muted">Untuk menunjang kegiatan pembelajaran RPL & DKV.</small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 text-center">
            <div class="facility-box shadow-sm border p-2 h-100">
                <img src="<?php echo $base_url; ?>assets/images/images.jpeg" class="img-fluid rounded" alt="Studio Kreatif Multimedia">
                <p class="mt-2 mb-0 fw-bold">Studio Kreatif Multimedia</p>
                <small class="text-muted">Fokus pada Animasi (ANM) dan Desain (DKV).</small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 text-center">
            <div class="facility-box shadow-sm border p-2 h-100">
                <img src="<?php echo $base_url; ?>assets/images/perpustakaan.jpeg" class="img-fluid rounded" alt="Perpustakaan">
                <p class="mt-2 mb-0 fw-bold">Perpustakaan Digital dan Konvensional</p>
                <small class="text-muted">Tempat belajar dan riset yang nyaman.</small>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 text-center">
            <div class="facility-box shadow-sm border p-2 h-100">
                <img src="<?php echo $base_url; ?>assets/images/lapang.jpeg" class="img-fluid rounded" alt="Sarana Olahraga">
                <p class="mt-2 mb-0 fw-bold">Sarana Olahraga Lengkap</p>
                <small class="text-muted">Lapangan Futsal, Basket, dan Area Parkir.</small>
            </div>
        </div>

    </div>
    <p class="mt-4 text-muted fst-italic">Foto-foto fasilitas di atas adalah yang terbaru dan siap digunakan oleh siswa baru.</p>
    </div>

<?php 
if (isset($conn)) {
    $conn->close();
}
include('includes/footer.php'); 
?>