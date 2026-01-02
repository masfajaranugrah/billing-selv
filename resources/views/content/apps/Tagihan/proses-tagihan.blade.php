@extends('layouts/layoutMaster')

@section('title', 'Proses Verifikasi Tagihan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* MODERN CLEAN STYLES */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #696cff;
  --success-color: #28c76f;
}

.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
  transform: translateY(-2px);
}

.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* Buttons */
.btn {
  border-radius: 8px;
  padding: 0.5rem 1.25rem;
  font-weight: 500;
  transition: var(--transition);
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a5dc9 0%, #4a4db9 100%);
  box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
  transform: translateY(-1px);
}

.btn-success {
  background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%);
  box-shadow: 0 2px 8px rgba(113, 221, 55, 0.3);
}

.btn-success:hover {
  background: linear-gradient(135deg, #5cb82e 0%, #4da326 100%);
  transform: translateY(-1px);
}

.btn-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%);
  box-shadow: 0 2px 8px rgba(255, 171, 0, 0.3);
}

.btn-sm {
  padding: 0.375rem 0.875rem;
  font-size: 0.875rem;
}

.btn-outline-primary {
  border: 1.5px solid #696cff;
  color: #696cff;
  background: transparent;
}

.btn-outline-primary:hover {
  background: #696cff;
  color: #ffffff;
}

.btn-outline-danger {
  border: 1.5px solid #ff3e1d;
  color: #ff3e1d;
  background: transparent;
}

.btn-outline-danger:hover {
  background: #ff3e1d;
  color: #ffffff;
}

.btn-secondary {
  background: #e0e0e0;
  color: #5a5f7d;
}

.btn-outline-secondary {
  border: 1.5px solid #8898aa;
  color: #8898aa;
  background: transparent;
}

.btn-outline-secondary:hover {
  background: #8898aa;
  color: #ffffff;
}

/* Badges */
.badge {
  padding: 0.4rem 0.75rem;
  border-radius: 6px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0.3px;
}

.badge.bg-success {
  background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%) !important;
}

.badge.bg-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%) !important;
}

/* Search Form */
.search-wrapper {
  background: #f8f9fa;
  padding: 1.25rem;
  border-radius: 10px;
  margin-bottom: 1rem;
}

.input-group-text {
  background: white;
  border-right: none;
  border-color: #e0e0e0;
}

.input-group .form-control {
  border-left: none;
  border-color: #e0e0e0;
}

.input-group .form-control:focus {
  border-color: #696cff;
  box-shadow: none;
}

.input-group:focus-within .input-group-text {
  border-color: #696cff;
}

.input-group:focus-within .form-control {
  border-color: #696cff;
}

/* Table */
.table {
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: #f8f9fa;
  border: none;
  padding: 1rem;
  font-weight: 600;
  color: #5a5f7d;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:hover {
  background: #f8f9fa;
  transform: scale(1.001);
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
}

.form-control[readonly] {
  background-color: #f8f9fa;
}

/* Modal */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
}

.modal-header.bg-light {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%) !important;
}

.modal-header.bg-warning {
  background: linear-gradient(135deg, #ffab00 0%, #e09900 100%) !important;
}

.modal-title {
  font-weight: 600;
  color: #ffffff;
}

.modal-body {
  padding: 2rem;
  max-height: 70vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e8e8e8;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(105, 108, 255, 0.4);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.customer-status {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.detail-section {
  background: #ffffff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #696cff;
}

.detail-section h6 {
  color: #696cff;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #696cff;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  min-width: 150px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a8afc7;
  font-size: 1rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Image Hover */
.table img {
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  transition: var(--transition);
  cursor: pointer;
}

.table img:hover {
  transform: scale(1.5);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 999;
}

/* Pagination Laravel */
.pagination {
  margin: 0;
  gap: 0.5rem;
}

.pagination .page-item .page-link {
  border-radius: 8px !important;
  margin: 0 2px;
  border: 1px solid #e0e0e0;
  color: #696cff;
  padding: 0.5rem 0.875rem;
  font-weight: 500;
  transition: var(--transition);
}

.pagination .page-item .page-link:hover {
  background: #696cff;
  color: white;
  border-color: #696cff;
  transform: translateY(-2px);
}

.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  border-color: #696cff;
  color: white;
  box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.pagination .page-item.disabled .page-link {
  background: #f8f9fa;
  color: #8898aa;
}

@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-header {
    padding: 1.25rem;
  }
  .detail-label {
    min-width: 120px;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // ========================================
    // DETAIL MODAL - MODERN UI
    // ========================================
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        const $tr = $(tr);
        
        // Get visible data from table
        const nomorId = $tr.find('td').eq(1).text().trim() || '-';
        const namaLengkap = $tr.find('td').eq(2).text().trim() || '-';
        const noWhatsapp = $tr.find('td').eq(3).text().trim() || '-';
        const statusPembayaran = $tr.find('td').eq(4).text().trim() || '-';
        
        // Get hidden data from data attributes
        const alamatLengkap = $tr.data('alamat') || '-';
        const kecamatan = $tr.data('kecamatan') || '-';
        const kabupaten = $tr.data('kabupaten') || '-';
        const provinsi = $tr.data('provinsi') || '-';
        const paket = $tr.data('paket') || '-';
        const harga = $tr.data('harga') || '-';
        const kecepatan = $tr.data('kecepatan') || '-';
        const tanggalMulai = $tr.data('tanggal-mulai') || '-';
        const jatuhTempo = $tr.data('jatuh-tempo') || '-';
        const catatan = $tr.data('catatan') || '-';
        const buktiPembayaran = $tr.data('bukti') || '';
        
        // Get tagihan ID and status for button
        const tagihanId = $tr.data('tagihan-id');
        const nama = namaLengkap;
        const status = $tr.find('.btn-konfirmasi').length > 0 ? 'belum_bayar' : 'lunas';
        
        // Build bukti section
        let buktiSection = '<span class="text-muted">-</span>';
        if (buktiPembayaran) {
            buktiSection = `<a href="${buktiPembayaran}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="ri-file-text-line me-1"></i>Lihat Bukti
            </a>`;
        }
        
        const initial = namaLengkap ? namaLengkap.charAt(0).toUpperCase() : '?';
        const statusLower = statusPembayaran.toLowerCase();
        const statusClass = statusLower.includes('lunas') ? 'bg-success' : statusLower.includes('proses') ? 'bg-warning' : 'bg-secondary';
        
        const html = `
            <div class="customer-header-info">
                <div class="customer-avatar mx-auto">${initial}</div>
                <div class="customer-name">${namaLengkap}</div>
                <div class="customer-status ${statusClass}">
                    <i class="ri-checkbox-circle-line me-2"></i>${statusPembayaran}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Pelanggan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-id-card-line"></i>No. ID</span>
                    <span class="detail-value"><strong>${nomorId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-user-line"></i>Nama Lengkap</span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-whatsapp-line"></i>No. WhatsApp</span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <strong>${noWhatsapp}</strong>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-2-line"></i>Alamat</span>
                    <span class="detail-value">${alamatLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-building-line"></i>Kecamatan</span>
                    <span class="detail-value">${kecamatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-community-line"></i>Kabupaten</span>
                    <span class="detail-value">${kabupaten}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-map-pin-2-line"></i>Provinsi</span>
                    <span class="detail-value">${provinsi}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-box-3-line"></i>Informasi Paket</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-box-line"></i>Nama Paket</span>
                    <span class="detail-value"><span class="badge bg-label-info">${paket}</span></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-money-dollar-circle-line"></i>Harga</span>
                    <span class="detail-value"><strong>${harga}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-speed-line"></i>Kecepatan</span>
                    <span class="detail-value">${kecepatan}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Informasi Tagihan</h6>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-line"></i>Tanggal Mulai</span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-calendar-event-line"></i>Jatuh Tempo</span>
                    <span class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-file-list-line"></i>Bukti Pembayaran</span>
                    <span class="detail-value">${buktiSection}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label"><i class="ri-file-text-line"></i>Catatan</span>
                    <span class="detail-value">${catatan}</span>
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').data('tagihan-id', tagihanId);
        $('#detailModal').data('tagihan-nama', nama);
        $('#detailModal').data('tagihan-status', status);
        
        const btnKonfirmasi = $('#btnKonfirmasiDetail');
        if (status === 'lunas') {
            btnKonfirmasi.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary').html('<i class="ri-check-circle-line me-1"></i> Sudah Lunas');
        } else {
            btnKonfirmasi.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success').html('<i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas');
        }
        
        $('#detailModal').modal('show');
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN DARI MODAL DETAIL
    // ========================================
    $(document).on('click', '#btnKonfirmasiDetail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $('#detailModal').data('tagihan-id');
        const nama = $('#detailModal').data('tagihan-nama');
        
        if (!tagihanId) {
            Swal.fire('Error!', 'Data tagihan tidak ditemukan.', 'error');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah melakukan pembayaran?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/bayar`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            $('#detailModal').modal('hide');
                            Swal.fire({
                                title: 'Pembayaran Dikonfirmasi!',
                                html: `
                                    <p>Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary mt-3">
                                        <i class="ri-printer-line me-1"></i> Cetak Kwitansi
                                    </a>
                                `,
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN DARI TABEL
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tagihanId = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah melakukan pembayaran?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                
                $.ajax({
                    url: `/dashboard/admin/tagihan/${tagihanId}/bayar`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.success) {
                            Swal.fire({
                                title: 'Pembayaran Dikonfirmasi!',
                                html: `
                                    <p>Tagihan <strong>${nama}</strong> telah ditandai lunas.</p>
                                    <a href="${response.pdfUrl}" target="_blank" class="btn btn-primary mt-3">
                                        <i class="ri-printer-line me-1"></i> Cetak Kwitansi
                                    </a>
                                `,
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: true,
                                didClose: () => location.reload()
                            });
                        } else {
                            Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire('Gagal!', 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid px-4 py-4">
  <!-- ========================================= -->
  <!-- DAFTAR TAGIHAN PROSES VERIFIKASI -->
  <!-- ========================================= -->
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h5 class="mb-1 fw-bold">
            <i class="ri-file-list-3-line me-2 text-warning"></i>
            Tagihan Proses Verifikasi
          </h5>
          <small class="text-muted">Kelola tagihan yang sedang dalam proses verifikasi pembayaran</small>
        </div>
        
        @if($tagihans->total() > 0)
        <div>
          <span class="badge bg-label-warning" style="padding: 10px 20px; font-size: 0.9rem;">
            <i class="ri-database-2-line me-1"></i>
            {{ $tagihans->total() }} Tagihan
          </span>
        </div>
        @endif
      </div>

      <!-- ========================================= -->
      <!-- FORM SEARCH -->
      <!-- ========================================= -->
      <div class="search-wrapper mt-3">
        <form action="{{ url()->current() }}" method="GET">
          <div class="row g-3 align-items-center">
            <div class="col-md-10">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="ri-search-line"></i>
                </span>
                <input type="text" 
                  class="form-control" 
                  name="search" 
                  placeholder="Cari berdasarkan Nama, No. ID, WhatsApp, Paket, Alamat, Kecamatan, Kabupaten..." 
                  value="{{ request('search') }}"
                  autocomplete="off">
              </div>
            </div>
            <div class="col-md-2">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                  <i class="ri-search-line me-1"></i>Cari
                </button>
                @if(request('search'))
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset">
                  <i class="ri-refresh-line"></i>
                </a>
                @endif
              </div>
            </div>
          </div>
        </form>
      </div>
      <!-- END FORM SEARCH -->

    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th><i class="ri-eye-line me-1"></i>Detail</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>WhatsApp</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-box-3-line me-1"></i>Paket</th>
              <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
              <th><i class="ri-settings-3-line me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $item)
            @php
              $status = strtolower($item->status_pembayaran ?? '');
              $badgeClass = match($status) {
                'lunas' => 'badge bg-success',
                'proses_verifikasi' => 'badge bg-warning',
                default => 'badge bg-secondary',
              };

              $alamatParts = [];
              if($item->pelanggan->alamat_jalan ?? '') $alamatParts[] = $item->pelanggan->alamat_jalan;
              if(($item->pelanggan->rt ?? '') || ($item->pelanggan->rw ?? '')) {
                $alamatParts[] = 'RT '.($item->pelanggan->rt ?? '-').' / RW '.($item->pelanggan->rw ?? '-');
              }
              if($item->pelanggan->desa ?? '') $alamatParts[] = 'Desa '.$item->pelanggan->desa;
              if($item->pelanggan->kecamatan ?? '') $alamatParts[] = 'Kecamatan '.$item->pelanggan->kecamatan;
              if($item->pelanggan->kabupaten ?? '') $alamatParts[] = 'Kabupaten '.$item->pelanggan->kabupaten;
              if($item->pelanggan->provinsi ?? '') $alamatParts[] = $item->pelanggan->provinsi;
              $alamatLengkap = implode(', ', $alamatParts);
            @endphp

            <tr 
              data-tagihan-id="{{ $item->id }}"
              data-alamat="{{ $alamatLengkap }}"
              data-kecamatan="{{ $item->pelanggan->kecamatan ?? '-' }}"
              data-kabupaten="{{ $item->pelanggan->kabupaten ?? '-' }}"
              data-provinsi="{{ $item->pelanggan->provinsi ?? '-' }}"
              data-paket="{{ $item->paket->nama_paket ?? '-' }}"
              data-harga="Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}"
              data-kecepatan="{{ $item->paket->kecepatan ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}"
              data-jatuh-tempo="{{ $item->tanggal_berakhir ? \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') : '-' }}"
              data-catatan="{{ $item->catatan ?? '-' }}"
              data-bukti="{{ !empty($item->bukti_pembayaran) ? asset('storage/' . $item->bukti_pembayaran) : '' }}"
            >
              <td>
                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge bg-label-dark">{{ $item->pelanggan->nomer_id ?? '-' }}</span></td>
              <td><strong>{{ $item->pelanggan->nama_lengkap ?? '-' }}</strong></td>
              <td>
                <a href="https://wa.me/{{ $item->pelanggan->no_whatsapp ?? '' }}" target="_blank" class="text-decoration-none">
                  <code style="background: #f8f9fa; padding: 6px 12px; border-radius: 6px; font-size: 0.875rem; font-weight: 600; color: #25D366;">
                    <i class="ri-whatsapp-line me-1"></i>{{ $item->pelanggan->no_whatsapp ?? '-' }}
                  </code>
                </a>
              </td>
              <td>
                <span class="{{ $badgeClass }}">
                  <i class="ri-time-line me-1"></i>{{ ucfirst(str_replace('_', ' ', $status) ?: 'Belum Bayar') }}
                </span>
              </td>
              <td>
                <span class="badge bg-label-info">
                  <i class="ri-box-line me-1"></i>{{ $item->paket->nama_paket ?? '-' }}
                </span>
              </td>
              <td><strong>Rp {{ number_format($item->paket->harga ?? 0, 0, ',', '.') }}</strong></td>
              <td>
                <div class="d-flex gap-2 flex-wrap">
                  @if($status === 'lunas')
                    <button class="btn btn-sm btn-secondary" disabled>
                      <i class="ri-check-circle-line me-1"></i> Lunas
                    </button>
                  @else
                    <button class="btn btn-sm btn-success btn-konfirmasi" 
                      data-id="{{ $item->id }}" 
                      data-nama="{{ $item->pelanggan->nama_lengkap ?? '-' }}">
                      <i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <div class="mb-3">
                  <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                </div>
                @if(request('search'))
                <h5 class="text-muted mb-2">Tidak Ada Hasil</h5>
                <p class="text-muted">Tidak ditemukan tagihan dengan kata kunci "<strong>{{ request('search') }}</strong>"</p>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-primary mt-2">
                  <i class="ri-refresh-line me-1"></i>Reset Pencarian
                </a>
                @else
                <h5 class="text-muted mb-2">Tidak Ada Tagihan Dalam Proses Verifikasi</h5>
                <p class="text-muted">Saat ini tidak ada tagihan yang sedang dalam proses verifikasi.</p>
                @endif
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- ========================================= -->
      <!-- PAGINATION LARAVEL -->
      <!-- ========================================= -->
      @if($tagihans->hasPages())
      <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
        <div class="text-muted small">
          @if($tagihans->total() > 0)
            Menampilkan <strong>{{ $tagihans->firstItem() }}</strong> - <strong>{{ $tagihans->lastItem() }}</strong> dari <strong>{{ $tagihans->total() }}</strong> tagihan
            @if(request('search'))
              <span class="badge bg-label-primary ms-2">
                <i class="ri-search-line me-1"></i>Hasil pencarian: "{{ request('search') }}"
              </span>
            @endif
          @endif
        </div>
        <div>
          {{ $tagihans->links('pagination::bootstrap-5') }}
        </div>
      </div>
      @elseif(request('search'))
      <div class="px-4 py-3 border-top bg-light">
        <div class="text-muted small">
          <span class="badge bg-label-primary">
            <i class="ri-search-line me-1"></i>Hasil pencarian: "{{ request('search') }}"
          </span>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: DETAIL - MODERN UI -->
<!-- ========================================= -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title text-white">
          <i class="ri-information-line me-2"></i>Detail Pelanggan & Tagihan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
        <button type="button" class="btn btn-success" id="btnKonfirmasiDetail">
          <i class="ri-check-circle-line me-1"></i> Konfirmasi Lunas
        </button>
      </div>
    </div>
  </div>
</div>

@endsection
