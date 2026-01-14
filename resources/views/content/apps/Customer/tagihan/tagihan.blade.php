<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Invoice Tagihan</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 24px 0 100px;
    min-height: 100vh;
    color: #0f172a;
}

.container {
    max-width: 680px;
}

.invoice-container {
    display: flex;
    flex-direction: column;
}

/* Header Section */
.header-section {
    margin-bottom: 32px;
}

.header-section h4 {
    color: #0f172a;
    font-weight: 700;
    font-size: 1.75rem;
    margin-bottom: 6px;
}

.header-section p {
    color: #64748b;
    font-size: 0.95rem;
}

/* Card Invoice */
.card-invoice {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 20px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.card-invoice:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}

/* Card Priority untuk belum bayar */
.card-invoice.priority {
    border: 2px solid #fecaca;
    box-shadow: 0 4px 16px rgba(239,68,68,0.12);
    order: -1;
}

.card-invoice.priority:hover {
    box-shadow: 0 6px 20px rgba(239,68,68,0.16);
}

/* Card Header */
.card-header-invoice {
    background: #0f172a;
    padding: 20px 24px;
    color: white;
    border-bottom: 1px solid #1e293b;
}

.card-header-invoice h5 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}

.card-header-invoice small {
    font-size: 0.875rem;
    color: #94a3b8;
}

/* Card Body */
.card-body {
    padding: 24px;
}

/* Info Section */
.info-section {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #f1f5f9;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-item:first-child {
    padding-top: 0;
}

.info-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.info-value {
    font-size: 0.875rem;
    color: #0f172a;
    font-weight: 600;
}

/* PPN Notice */
.ppn-notice {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-left: 3px solid #f59e0b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ppn-notice i {
    color: #f59e0b;
    font-size: 1.1rem;
}

.ppn-notice p {
    margin: 0;
    color: #92400e;
    font-weight: 500;
    font-size: 0.875rem;
}

/* Price Section */
.price-section {
    text-align: center;
    padding: 24px 0;
    margin: 20px 0;
}

.price-section .period-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 16px;
}

.price-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.price-text {
    font-size: 0.8125rem;
    color: #94a3b8;
}

/* Divider */
.divider {
    height: 1px;
    background: #f1f5f9;
    margin: 20px 0;
}

/* Status Badge */
.status-wrapper {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: 100px;
    letter-spacing: 0.02em;
}

.status-lunas {
    background: #f0fdf4;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-verifikasi {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fef3c7;
}

.status-belum {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Button Bayar */
.btn-bayar {
    margin-top: 16px;
    border-radius: 8px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 0.9375rem;
    background: #0f172a;
    border: none;
    color: white;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-bayar:hover {
    background: #1e293b;
    transform: translateY(-1px);
}

.btn-bayar:active {
    transform: translateY(0);
}

/* Empty State */
.empty-state {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
    margin-top: 40px;
}

.empty-state i {
    font-size: 3.5rem;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h5 {
    color: #0f172a;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1.125rem;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
    line-height: 1.6;
    font-size: 0.9375rem;
}

.empty-state .btn {
    border-radius: 8px;
    padding: 10px 24px;
    font-weight: 600;
    background: #0f172a;
    border: none;
    transition: all 0.2s ease;
    font-size: 0.9375rem;
}

.empty-state .btn:hover {
    background: #1e293b;
    transform: translateY(-1px);
}

/* Bottom Navbar */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 72px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-around;
    align-items: center;
    z-index: 1000;
}

.bottom-nav button {
    background: none;
    border: none;
    text-align: center;
    color: #94a3b8;
    transition: all 0.2s ease;
    padding: 8px 16px;
    border-radius: 8px;
}

.bottom-nav button:hover,
.bottom-nav button.active {
    color: #0f172a;
}

.bottom-nav button i {
    font-size: 1.5rem;
    display: block;
    margin-bottom: 4px;
}

.bottom-nav button span {
    font-size: 0.6875rem;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 480px) {
    body {
        padding: 16px 0 100px;
    }

    .header-section h4 {
        font-size: 1.5rem;
    }

    .price-amount {
        font-size: 1.75rem;
    }

    .card-body {
        padding: 20px 16px;
    }

    .info-section {
        padding: 16px;
    }
}

.cursor-pointer {
    cursor: pointer;
}

/* Bank selector */
.bank-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.bank-card {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
}

.bank-card:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}

.bank-card.active {
    border-color: #0f172a;
    background: #f1f5f9;
}

.bank-radio {
    display: none;
}

.bank-indicator {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0f172a;
    color: #fff;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.bank-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
    min-width: 0;
}

.bank-name {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.bank-number {
    font-weight: 700;
    color: #334155;
    font-size: 0.95rem;
    font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
    letter-spacing: 0.03em;
}

.bank-owner {
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 500;
}

@media (max-width: 480px) {
    .bank-card {
        padding: 12px;
        gap: 10px;
    }
    .bank-indicator {
        width: 38px;
        height: 38px;
        font-size: 1rem;
        border-radius: 8px;
    }
    .bank-name {
        font-size: 0.85rem;
    }
    .bank-number {
        font-size: 0.88rem;
    }
    .bank-owner {
        font-size: 0.75rem;
    }
}

/* File Upload Styling */
.upload-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
}

.upload-area:hover {
    border-color: #0f172a;
    background: #f1f5f9;
}

.upload-area.has-file {
    border-color: #22c55e;
    background: #f0fdf4;
}

.upload-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 12px;
    background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.5rem;
}

.upload-area.has-file .upload-icon {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.upload-title {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.95rem;
    margin-bottom: 4px;
}

.upload-subtitle {
    color: #64748b;
    font-size: 0.8rem;
}

.upload-filename {
    margin-top: 10px;
    padding: 8px 12px;
    background: #e2e8f0;
    border-radius: 6px;
    font-size: 0.8rem;
    color: #334155;
    font-weight: 500;
    display: none;
    word-break: break-all;
}

.upload-area.has-file .upload-filename {
    display: block;
}

/* SweetAlert Custom Styling */
.swal2-popup {
    border-radius: 16px;
    padding: 24px;
}

.swal2-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
}

.swal2-confirm {
    border-radius: 8px !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
}

.swal2-cancel {
    border-radius: 8px !important;
    font-weight: 600 !important;
    padding: 10px 24px !important;
}

/* Badge Tunggakan dengan Animasi Ring + Shake */
.badge-tunggakan {
    position: relative;
    background: #dc2626;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    z-index: 1;

    /* Gabungan animasi shake dan pulse */
    animation: shake 0.8s ease-in-out infinite, pulse-badge 2s ease-in-out infinite;
}

.badge-tunggakan i {
    font-size: 0.875rem;
}

/* Efek Ring Berdering (Pulse Ring) */
.badge-tunggakan::before {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 8px;
    border: 2px solid #dc2626;
    opacity: 0.7;
    z-index: -1;
    animation: pulse-ring 2s ease-out infinite;
}

/* Ring kedua untuk efek lebih dramatis */
.badge-tunggakan::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 8px;
    border: 2px solid #dc2626;
    opacity: 0;
    z-index: -1;
    animation: pulse-ring 2s 0.5s ease-out infinite;
}

/* Keyframe: Pulse Ring - Efek Berdering */
@keyframes pulse-ring {
    0% {
        transform: scale(1);
        opacity: 0.7;
    }
    50% {
        transform: scale(1.15);
        opacity: 0.4;
    }
    100% {
        transform: scale(1.3);
        opacity: 0;
    }
}

/* Keyframe: Shake - Efek Bergetar */
@keyframes shake {
    0%, 100% {
        transform: translateX(0) rotate(0deg);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-2px) rotate(-1deg);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(2px) rotate(1deg);
    }
}

/* Keyframe: Pulse Badge - Efek Zoom Halus */
@keyframes pulse-badge {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Hover: Stop semua animasi */
.badge-tunggakan:hover {
    animation: none;
}

.badge-tunggakan:hover::before,
.badge-tunggakan:hover::after {
    animation: none;
    opacity: 0;
}

/* Responsive untuk mobile */
@media (max-width: 480px) {
    .badge-tunggakan {
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .badge-tunggakan::before,
    .badge-tunggakan::after {
        inset: -3px;
        border-width: 1.5px;
    }
}

</style>
</head>

<body>

<div class="container">
    <div class="header-section">
        <h4>Tagihan</h4>
        <p>Kelola pembayaran tagihan Anda</p>
    </div>

    <div class="invoice-container">
    @forelse($tagihans as $tagihan)
    @php
        $pelanggan = $tagihan->pelanggan ?? null;
        $paket = $tagihan->paket ?? null;
        $isPriority = $tagihan->status_pembayaran !== 'lunas' && $tagihan->status_pembayaran !== 'proses_verifikasi';
    @endphp

    <div class="card card-invoice {{ $isPriority ? 'priority' : '' }}">


@php
    $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_berakhir);
    $sekarang = \Carbon\Carbon::now();

    // Cek apakah sudah lewat bulan (bukan hanya tanggal)
    // Tunggakan = jatuh tempo di bulan sebelumnya atau lebih lama
    $isPastMonth = $jatuhTempo->format('Y-m') < $sekarang->format('Y-m');

    $isUnpaid = $tagihan->status_pembayaran !== 'lunas' && $tagihan->status_pembayaran !== 'proses_verifikasi';

    // Tunggakan muncul hanya jika: belum bayar DAN sudah lewat bulan
    $isTunggakan = $isUnpaid && $isPastMonth;
@endphp

<div class="card-header-invoice d-flex justify-content-between align-items-center">
    <div>
        <h5>Invoice Tagihan</h5>
        <small>PT. Jernih Multi Komunikasi</small>
    </div>

    @if($isTunggakan)
        <span class="badge-tunggakan">
            <i class="bi bi-exclamation-triangle-fill"></i> Tunggakan
        </span>
    @endif
</div>

        <div class="card-body">

          <div class="info-section">
            <div class="info-item">
                <span class="info-label">No. ID</span>
                <span class="info-value">{{ $pelanggan->nomer_id ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Nama</span>
                <span class="info-value">{{ $pelanggan->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Invoice</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jatuh Tempo</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->format('d M Y') }}</span>
            </div>
          </div>



          <div class="price-section">
   <!-- <p class="period-label">
                  Periode: {{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('F Y') }}
              </p> -->
              <p class="period-label">
                  Periode: Desember 2025              </p>

              <div class="price-amount">
                  Rp {{ number_format($paket->harga ?? 0, 0, ',', '.') }}
              </div>

              <p class="price-text">
                  {{ ucwords(\NumberFormatter::create('id_ID', \NumberFormatter::SPELLOUT)->format($paket->harga ?? 0)) }} rupiah
              </p>
          </div>

          <div class="status-wrapper">
              @if($tagihan->status_pembayaran === 'lunas')
                  <span class="status-badge status-lunas">
                      <i class="bi bi-check-circle-fill"></i> Lunas
                  </span>
              @elseif($tagihan->status_pembayaran === 'proses_verifikasi')
                  <span class="status-badge status-verifikasi">
                      <i class="bi bi-clock-fill"></i> Menunggu Verifikasi
                  </span>
              @else
                  <span class="status-badge status-belum">
                      <i class="bi bi-cloud-arrow-up-fill"></i> Menunggu Upload
                  </span>
                  <div>
                      <button class="btn btn-bayar bayar-btn" data-id="{{ $tagihan->id }}">
                          <i class="bi bi-upload"></i> Upload Bukti Pembayaran
                      </button>
                  </div>
              @endif
          </div>

        </div>
    </div>

    @empty
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>Tidak Ada Tagihan</h5>
        <p>Saat ini tidak ada tagihan yang perlu dibayar.<br>Untuk melihat riwayat pembayaran, klik tombol di bawah.</p>
        <a href="https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai">
            <button class="btn btn-primary">
                <i class="bi bi-receipt"></i> Lihat Kwitansi
            </button>
        </a>
    </div>
    @endforelse
    </div>
</div>

<div class="bottom-nav">
    @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'invoice'])
</div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

/* ================================
   FUNGSI KOMPRES GAMBAR (iOS/Android)
================================== */
function compressImage(file, maxWidth = 1280, quality = 0.7) {
    return new Promise((resolve, reject) => {
        // Skip PDF files
        if (file.type === "application/pdf") {
            resolve(file);
            return;
        }

        // Check if file is an image
        if (!file.type.startsWith("image/")) {
            resolve(file);
            return;
        }

        const reader = new FileReader();

        reader.onload = (event) => {
            const img = new Image();

            img.onload = () => {
                try {
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    // Calculate new dimensions
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Fill white background for JPG (handles transparency)
                    ctx.fillStyle = "#FFFFFF";
                    ctx.fillRect(0, 0, width, height);

                    // Draw image
                    ctx.drawImage(img, 0, 0, width, height);

                    // Determine output type - keep original format or use JPEG
                    let outputType = "image/jpeg";
                    let outputQuality = quality;

                    // For PNG with transparency, keep as PNG
                    if (file.type === "image/png") {
                        outputType = "image/jpeg"; // Convert PNG to JPEG for smaller size
                    }

                    canvas.toBlob(
                        (blob) => {
                            if (!blob) {
                                console.warn("Blob creation failed, using original file");
                                resolve(file);
                                return;
                            }

                            // Create new filename with jpg extension
                            let newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            const compressedFile = new File([blob], newName, {
                                type: outputType,
                                lastModified: Date.now()
                            });

                            console.log(`Compressed: ${file.size} -> ${compressedFile.size} bytes`);
                            resolve(compressedFile);
                        },
                        outputType,
                        outputQuality
                    );
                } catch (err) {
                    console.error("Canvas error:", err);
                    resolve(file); // Return original on error
                }
            };

            img.onerror = () => {
                console.warn("Image load failed, using original file");
                resolve(file);
            };

            img.src = event.target.result;
        };

        reader.onerror = () => {
            console.warn("FileReader error, using original file");
            resolve(file);
        };

        reader.readAsDataURL(file);
    });
}

/* ================================
   EVENT BAYAR
================================== */
document.querySelectorAll('.bayar-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const tagihanId = btn.dataset.id;
        const rekenings = @json($rekenings);

        let htmlRekening = '<div class="bank-list">';
        rekenings.forEach(r => {
            htmlRekening += `
            <label class="bank-card">
                <input type="radio" class="bank-radio" name="type_pembayaran" value="${r.id}">
                <div class="bank-indicator"><i class="bi bi-bank"></i></div>
                <div class="bank-content">
                    <div class="bank-name">${r.nama_bank}</div>
                    <div class="bank-number">${r.nomor_rekening}</div>
                    <div class="bank-owner">a.n ${r.nama_pemilik}</div>
                </div>
            </label>`;
        });
        htmlRekening += '</div>';

        Swal.fire({
            title: 'Pilih Rekening Tujuan',
            html: htmlRekening,
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#94a3b8',
            preConfirm: () => {
                const selected = document.querySelector('input[name="type_pembayaran"]:checked');
                if (!selected) Swal.showValidationMessage('Pilih salah satu rekening!');
                return selected ? selected.value : null;
            }
        }).then(result => {
            if (!result.isConfirmed) return;
            const selectedRekening = rekenings.find(r => r.id == result.value);

            Swal.fire({
                title: 'Upload Bukti Pembayaran',
                html: `
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; margin-bottom: 16px; text-align: left; border: 1px solid #e2e8f0;">
                        <p style="margin: 0; color: #0f172a; font-weight: 600; font-size: 0.9rem;">${selectedRekening.nama_bank}</p>
                        <p style="margin: 3px 0 0 0; color: #334155; font-size: 0.85rem; font-family: 'SF Mono', monospace; font-weight: 600;">${selectedRekening.nomor_rekening}</p>
                        <p style="margin: 3px 0 0 0; color: #64748b; font-size: 0.8rem;">a.n ${selectedRekening.nama_pemilik}</p>
                    </div>
                    <div class="upload-area" id="upload-area">
                        <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="upload-title">Pilih atau drop file di sini</div>
                        <div class="upload-subtitle">JPG, PNG atau PDF (maks 5MB)</div>
                        <div class="upload-filename" id="upload-filename"></div>
                        <input type="file" id="bukti-pembayaran" accept="image/*,application/pdf" style="display: none;">
                    </div>
                `,
                didOpen: () => {
                    const uploadArea = document.getElementById('upload-area');
                    const fileInput = document.getElementById('bukti-pembayaran');
                    const filenameEl = document.getElementById('upload-filename');

                    uploadArea.addEventListener('click', () => fileInput.click());

                    uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        uploadArea.style.borderColor = '#0f172a';
                        uploadArea.style.background = '#f1f5f9';
                    });

                    uploadArea.addEventListener('dragleave', () => {
                        if (!uploadArea.classList.contains('has-file')) {
                            uploadArea.style.borderColor = '#cbd5e1';
                            uploadArea.style.background = '#f8fafc';
                        }
                    });

                    uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        if (e.dataTransfer.files.length) {
                            fileInput.files = e.dataTransfer.files;
                            updateFileDisplay(e.dataTransfer.files[0]);
                        }
                    });

                    fileInput.addEventListener('change', () => {
                        if (fileInput.files.length) {
                            updateFileDisplay(fileInput.files[0]);
                        }
                    });

                    function updateFileDisplay(file) {
                        uploadArea.classList.add('has-file');
                        filenameEl.textContent = file.name;
                        uploadArea.querySelector('.upload-title').textContent = 'File terpilih';
                        uploadArea.querySelector('.upload-icon i').className = 'bi bi-check-lg';
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0f172a',
                cancelButtonColor: '#94a3b8',
                showLoaderOnConfirm: true,

                preConfirm: async () => {
                    const fileInput = document.getElementById('bukti-pembayaran');
                    if (!fileInput.files.length) return Swal.showValidationMessage('Pilih file bukti pembayaran!');

                    let file = fileInput.files[0];

                    try { file = await compressImage(file); }
                    catch (e) { return Swal.showValidationMessage("Gagal kompres gambar: " + e); }

                    const formData = new FormData();
                    formData.append('bukti_pembayaran', file);
                    formData.append('type_pembayaran', selectedRekening.id);
                    formData.append('_method', 'PUT');

                    return fetch(`/dashboard/customer/tagihan/${tagihanId}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => { if (!data.success) throw new Error(data.message); return data; })
                    .catch(err => Swal.showValidationMessage(`Gagal upload: ${err.message}`));
                }
            }).then(uploadResult => {
                if (uploadResult.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Bukti pembayaran telah dikirim',
                        icon: 'success',
                        confirmButtonColor: '#0f172a'
                    }).then(() => location.reload());
                }
            });
        });
    });
});

// Hover & active effect untuk rekening cards
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('bank-radio')) {
            document.querySelectorAll('.bank-card').forEach(card => {
                const radio = card.querySelector('.bank-radio');
                card.classList.toggle('active', radio && radio.checked);
            });
        }
    });
});
</script>

</body>
</html>
