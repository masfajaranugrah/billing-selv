@extends('layouts/layoutMaster')
@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Laporan Tagihan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])

<style>
/* ========================================= */
/* MODERN CLEAN STYLES - SHADCN UI */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
}

/* Card Design */
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

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e5e7eb;
  transition: var(--transition);
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
}

.badge-status {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 0.75rem;
}

.action-buttons {
  gap: 12px;
}

.card-header-custom {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  color: #18181b;
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* ========================================= */
.btn {
  border-radius: 6px !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  font-size: 0.875rem !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.5rem !important;
}

/* Primary Button - Black */
.btn.btn-primary,
.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-primary:hover,
.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Success Button - Black */
.btn.btn-success,
.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-success:hover,
.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Secondary Button - Black */
.btn.btn-secondary,
.btn-secondary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-secondary:hover,
.btn-secondary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Buttons - Light background, black text */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn-outline-primary,
.btn-outline-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

/* Export Button */
.btn-export {
  padding: 10px 24px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: all 0.3s !important;
}

.btn-export:hover {
  transform: translateY(-2px);
}

.btn-export i {
  margin-right: 8px;
}

/* Small Button */
.btn.btn-sm,
.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* Icon Button */
.btn-icon {
  width: 2rem;
  height: 2rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES */
/* ========================================= */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

/* Neutralize accent labels - shadcn style */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* Badge Paket - Black background */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Lunas - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Belum Bayar - Red */
.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Table Styles */
.table-modern {
  border-radius: 8px;
  overflow: hidden;
}

.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #0f172a;
  border: none;
  padding: 1rem;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e5e7eb;
}

.table-modern tbody tr:hover {
  background-color: #f1f5f9 !important;
  transform: scale(1.001);
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
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

.spinner-border-custom {
  width: 3rem;
  height: 3rem;
  border-width: 0.3rem;
}

/* ========================================= */
/* SHADCN UI STYLE MODAL */
/* ========================================= */
.modal-content {
  border-radius: 12px;
  border: 1px solid #e4e4e7;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #18181b;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #fafafa;
  margin: 0;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 65vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Detail Section */
.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:first-child {
  margin-top: 0.5rem;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #18181b;
}

.detail-section h6 {
  color: #18181b;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
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
  min-width: 180px;
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

/* Tagihan Header Info */
.tagihan-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

.tagihan-id {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  background: #18181b;
  color: #fafafa;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
}

/* Bukti Preview */
.bukti-preview {
  max-width: 100%;
  max-height: 400px;
  border-radius: 8px;
  border: 2px solid #e4e4e7;
  margin-top: 0.5rem;
  cursor: pointer;
  transition: transform 0.3s;
}

.bukti-preview:hover {
  transform: scale(1.02);
}

/* Filter Section */
.filter-section {
  background: #fafafa;
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.12);
}

/* ========================================= */
/* PAGINATION STYLES - BLACK ACTIVE */
/* ========================================= */
.pagination {
  margin: 0;
  gap: 0.5rem;
  justify-content: flex-end;
}

.pagination .page-item .page-link {
  border-radius: 50% !important;
  width: 40px;
  height: 40px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e4e4e7;
  color: #18181b;
  font-weight: 600;
  background-color: #fff;
  margin: 0 4px;
  transition: all 0.3s ease;
}

.pagination .page-item .page-link:hover {
  background-color: #f4f4f5;
  border-color: #18181b;
  color: #18181b;
}

.pagination .page-item.active .page-link {
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
  box-shadow: none;
}

.pagination .page-item.disabled .page-link {
  background-color: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
}

/* Text Colors - Black Theme */
.text-primary {
  color: #18181b !important;
}

body, p, span, div, td, th, label, h1, h2, h3, h4, h5, h6, strong {
  color: #18181b;
}

.text-muted {
  color: #71717a !important;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.avatar-initial.bg-label-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
  function showLoading() {
    $('.loading-overlay').css('display', 'flex');
  }

  function hideLoading() {
    $('.loading-overlay').fadeOut(300);
  }

  const filterForm = $('#filterForm');

  filterForm.on('submit', function() {
    showLoading();
  });

  // Event tombol detail
  $(document).on('click', '.btn-detail', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const tr = $(this).closest('tr');

      // Ambil data dari data attributes
      const id = tr.data('id') || '-';
      const namaLengkap = tr.data('nama') || '-';
      const alamat = tr.data('alamat') || '-';
      const namaPaket = tr.data('paket') || '-';
      const hargaPaket = tr.data('harga') || '-';
      const kecepatan = tr.data('kecepatan') || '-';
      const statusPembayaran = tr.data('status') || '-';
      const bank = tr.data('bank') || '-';
      const kabupaten = tr.data('kabupaten') || '-';
      const kecamatan = tr.data('kecamatan') || '-';
      const tanggalMulai = tr.data('tanggal-mulai') || '-';
      const tanggalBerakhir = tr.data('tanggal-berakhir') || '-';
      const catatan = tr.data('catatan') || '-';
      const buktiBayar = tr.data('bukti') || '';

      // Format status badge
      let statusBadge = '';
      if (statusPembayaran.toLowerCase() === 'lunas') {
          statusBadge = '<span class="badge bg-success"><i class="ri-checkbox-circle-line me-1"></i>Lunas</span>';
      } else if (statusPembayaran.toLowerCase() === 'belum bayar') {
          statusBadge = '<span class="badge bg-danger"><i class="ri-close-circle-line me-1"></i>Belum Bayar</span>';
      } else {
          statusBadge = '<span class="badge bg-secondary">' + statusPembayaran + '</span>';
      }

      // Build modal HTML
      const html = `
          <div class="tagihan-header-info">
              <div class="tagihan-id">
                  <i class="ri-file-list-3-line me-2"></i>ID Tagihan: ${id}
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-user-3-line"></i>Informasi Pelanggan</h6>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-user-line"></i>Nama Lengkap
                  </span>
                  <span class="detail-value"><strong>${namaLengkap}</strong></span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-map-pin-line"></i>Alamat Lengkap
                  </span>
                  <span class="detail-value">${alamat}</span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-building-line"></i>Kecamatan
                  </span>
                  <span class="detail-value">${kecamatan}</span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-map-2-line"></i>Kabupaten
                  </span>
                  <span class="detail-value">${kabupaten}</span>
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-wifi-line"></i>Informasi Paket Internet</h6>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-rocket-line"></i>Nama Paket
                  </span>
                  <span class="detail-value"><strong>${namaPaket}</strong></span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-money-dollar-circle-line"></i>Harga Paket
                  </span>
                  <span class="detail-value"><strong class="text-primary">${hargaPaket}</strong></span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-speed-line"></i>Kecepatan
                  </span>
                  <span class="detail-value"><span class="badge bg-label-info">${kecepatan}</span></span>
              </div>
                  <div class="detail-item">
                    <span class="detail-label">
                      <i class="ri-bank-line"></i>Metode Pembayaran
                    </span>
                    <span class="detail-value"><span class="badge bg-label-dark">${bank}</span></span>
                  </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-calendar-check-line"></i>Periode & Status Pembayaran</h6>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-calendar-line"></i>Tanggal Mulai
                  </span>
                  <span class="detail-value">${tanggalMulai}</span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-calendar-event-line"></i>Tanggal Berakhir
                  </span>
                  <span class="detail-value">${tanggalBerakhir}</span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-shield-check-line"></i>Status Pembayaran
                  </span>
                  <span class="detail-value">${statusBadge}</span>
              </div>
              <div class="detail-item">
                  <span class="detail-label">
                      <i class="ri-file-text-line"></i>Catatan
                  </span>
                  <span class="detail-value">${catatan}</span>
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-image-line"></i>Bukti Pembayaran</h6>
              <div class="text-center">
                  ${buktiBayar ?
                      '<a href="' + buktiBayar + '" target="_blank"><img src="' + buktiBayar + '" class="bukti-preview" alt="Bukti Pembayaran"></a>' :
                      '<div class="alert alert-warning mb-0"><i class="ri-error-warning-line me-2"></i>Tidak ada bukti pembayaran</div>'}
              </div>
          </div>
      `;

      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
  });

  // Export Excel dengan loading
  $('#btnExportExcel').on('click', function(e) {
      e.preventDefault();
      showLoading();

      const formData = new FormData(document.getElementById('filterForm'));
      const params = new URLSearchParams();

      for (const [key, value] of formData.entries()) {
        if (value) {
          params.append(key, value);
        }
      }

      let url = "{{ route('laporan.tagihan.export') }}";
      const queryString = params.toString();
      if (queryString) {
        url += '?' + queryString;
      }

      setTimeout(() => {
        hideLoading();
        window.location.href = url;
      }, 500);
  });
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header-custom">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h4 class="mb-1 fw-bold">
          <i class="ri-file-list-3-line me-2"></i>Laporan Tagihan
        </h4>
        <p class="mb-0 opacity-75 small">Kelola dan monitor data tagihan pelanggan</p>
      </div>
      <div class="d-flex action-buttons mt-3 mt-md-0">
        <button type="button" id="btnExportExcel" class="btn btn-success btn-export">
          <i class="ri-file-excel-2-line"></i>
          Export Excel
        </button>
      </div>
    </div>
  </div>

  <div class="card-body">
    <!-- Filter Section -->
    <div class="filter-section">
      <form id="filterForm" method="GET" action="{{ route('laporan.tagihan') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="filterKecamatan" class="form-label fw-semibold">
              <i class="ri-building-line me-1"></i>Filter Kecamatan
            </label>
            <select id="filterKecamatan" name="kecamatan" class="form-select">
              <option value="">-- Semua Kecamatan --</option>
              @foreach($kecamatans as $kecamatan)
                <option value="{{ $kecamatan }}" {{ request('kecamatan') === $kecamatan ? 'selected' : '' }}>{{ $kecamatan }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterKabupaten" class="form-label fw-semibold">
              <i class="ri-map-2-line me-1"></i>Filter Kabupaten
            </label>
            <select id="filterKabupaten" name="kabupaten" class="form-select">
              <option value="">-- Semua Kabupaten --</option>
              @foreach($kabupatens as $kabupaten)
                <option value="{{ $kabupaten }}" {{ request('kabupaten') === $kabupaten ? 'selected' : '' }}>{{ $kabupaten }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label for="filterStatus" class="form-label fw-semibold">
              <i class="ri-shield-check-line me-1"></i>Filter Status Pembayaran
            </label>
            <select id="filterStatus" name="status" class="form-select">
              <option value="">-- Semua Status --</option>
              <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
              <option value="belum bayar" {{ request('status') === 'belum bayar' ? 'selected' : '' }}>Belum Bayar</option>
            </select>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <button type="submit" class="btn btn-primary">
            <i class="ri-filter-3-line me-1"></i>Terapkan Filter
          </button>
          <a href="{{ route('laporan.tagihan') }}" class="btn btn-outline-secondary">
            <i class="ri-refresh-line me-1"></i>Reset
          </a>
        </div>
      </form>
    </div>

    <!-- Table Section -->
    <div class="table-responsive">
      <table class="table table-modern table-hover align-middle">
        <thead>
          <tr>
            <th class="text-center" style="width: 60px;">No</th>
            <th><i class="ri-eye-line me-1"></i>Detail</th>
            <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
            <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
            <th><i class="ri-rocket-line me-1"></i>Nama Paket</th>
            <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
            <th><i class="ri-speed-line me-1"></i>Kecepatan</th>
            <th><i class="ri-bank-line me-1"></i>Bank</th>
            <th><i class="ri-shield-check-line me-1"></i>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tagihans as $tagihan)
          <tr
            data-id="{{ $tagihan->id }}"
            data-nama="{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}"
            data-alamat="{{ $tagihan->pelanggan->alamat_jalan ?? '-' }}"
            data-paket="{{ $tagihan->paket->nama_paket ?? '-' }}"
            data-harga="Rp {{ number_format($tagihan->harga, 0, ',', '.') }}"
            data-kecepatan="{{ $tagihan->paket->kecepatan ?? '-' }}"
            data-status="{{ $tagihan->status_pembayaran }}"
            data-bank="{{ $tagihan->rekening->nama_bank ?? 'Lainnya' }}"
            data-kabupaten="{{ $tagihan->pelanggan->kabupaten ?? '-' }}"
            data-kecamatan="{{ $tagihan->pelanggan->kecamatan ?? '-' }}"
            data-tanggal-mulai="{{ $tagihan->tanggal_mulai ?? '-' }}"
            data-tanggal-berakhir="{{ $tagihan->tanggal_berakhir ?? '-' }}"
            data-catatan="{{ $tagihan->catatan ?? '-' }}"
            data-bukti="{{ !empty($tagihan->bukti_pembayaran) ? asset('storage/' . $tagihan->bukti_pembayaran) : '' }}"
          >
            <td class="text-center fw-semibold">
              {{ ($tagihans->firstItem() ?? 0) + $loop->index }}
            </td>
            <td>
              <button class="btn btn-sm btn-icon btn-outline-secondary btn-detail" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
            </td>
            <td>
              <div class="d-flex align-items-center">
                 
                <span class="fw-semibold">{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</span>
              </div>
            </td>
            <td>
              <div>
                {{ Str::limit($tagihan->pelanggan->alamat_jalan ?? '-', 35) }}
                <br>
                <small class="text-muted">
                  <i class="ri-map-pin-2-line"></i>
                  {{ $tagihan->pelanggan->kecamatan ?? '-' }}, {{ $tagihan->pelanggan->kabupaten ?? '-' }}
                </small>
              </div>
            </td>
            <td>
              <span class="badge bg-label-info">{{ $tagihan->paket->nama_paket ?? '-' }}</span>
            </td>
            <td>
              <span class="fw-bold text-primary">Rp {{ number_format($tagihan->harga, 0, ',', '.') }}</span>
            </td>
            <td>
              <span class="badge bg-label-dark">
                <i class="ri-speed-line me-1"></i>{{ $tagihan->paket->kecepatan ?? '-' }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-info">{{ $tagihan->rekening->nama_bank ?? 'Lainnya' }}</span>
            </td>
            <td>
              @php
                $statusClass = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'badge bg-success',
                    'belum bayar' => 'badge bg-danger',
                    default => 'badge bg-secondary',
                };
                $statusIcon = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'ri-checkbox-circle-line',
                    'belum bayar' => 'ri-close-circle-line',
                    default => 'ri-information-line',
                };
              @endphp
              <span class="{{ $statusClass }}">
                <i class="{{ $statusIcon }} me-1"></i>{{ ucfirst($tagihan->status_pembayaran) }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-4">
              <div class="text-muted fw-semibold"><i class="ri-inbox-2-line me-1"></i>Tidak ada data tagihan.</div>
              <div class="small text-secondary">Silakan ubah filter atau reset untuk melihat semua data.</div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
      <div class="text-muted small">
        Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }} dari {{ $tagihans->total() }} tagihan
      </div>
      <div>
        {{ $tagihans->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">
          <i class="ri-information-line me-2"></i>Detail Tagihan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
