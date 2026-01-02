@extends('layouts/layoutMaster')
@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Laporan Tagihan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])

<style>
  .stats-card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  .badge-status {
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
  }
  .action-buttons {
    gap: 12px;
  }
  .card-header-custom {
    color: black;
    border-radius: 12px 12px 0 0 !important;
    padding: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
  }
  .btn-export {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(40, 199, 111, 0.3);
  }
  .btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 199, 111, 0.4);
  }
  .btn-export i {
    margin-right: 8px;
  }
  .table-modern {
    border-radius: 8px;
    overflow: hidden;
  }
  .table-modern thead th {
    background: #f8f9fa;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #6c757d;
    border: none;
    padding: 16px;
  }
  .table-modern tbody tr {
    transition: all 0.2s;
    border-bottom: 1px solid #f1f1f1;
  }
  .table-modern tbody tr:hover {
    background-color: #f8f9ff !important;
    transform: scale(1.001);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
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
  .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
  }
  .modal-header {
    background: linear-gradient(135deg, #28c76f 0%, #1f9d57 100%);
    border-radius: 16px 16px 0 0;
    padding: 1.5rem;
    border-bottom: none;
  }
  .modal-title {
    font-weight: 600;
    font-size: 1.125rem;
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
    border-color: #28c76f;
  }
  .detail-section h6 {
    color: #28c76f;
    font-weight: 700;
    margin-bottom: 1.25rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #28c76f;
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
  .tagihan-header-info {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
  }
  .tagihan-id {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background: linear-gradient(135deg, #28c76f 0%, #1f9d57 100%);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px rgba(40, 199, 111, 0.3);
  }
  .bukti-preview {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
    border: 2px solid #e8e8e8;
    margin-top: 0.5rem;
    cursor: pointer;
    transition: transform 0.3s;
  }
  .bukti-preview:hover {
    transform: scale(1.02);
  }
  .filter-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
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

  // Inisialisasi DataTables
  const dtTagihan = $('.datatables-tagihan').DataTable({
      paging: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      searching: true,
      ordering: true,
      responsive: false,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
      columnDefs: [
          { orderable: false, targets: 0 } // Kolom detail button
      ],
      language: {
          search: "_INPUT_",
          searchPlaceholder: "Cari tagihan...",
          lengthMenu: "Tampilkan _MENU_ data",
          info: "Menampilkan _START_ - _END_ dari _TOTAL_ tagihan",
          paginate: {
              previous: '<i class="ri-arrow-left-s-line"></i>',
              next: '<i class="ri-arrow-right-s-line"></i>'
          },
          zeroRecords: "Tidak ada data yang ditemukan",
          infoEmpty: "Tidak ada data",
          infoFiltered: "(difilter dari _MAX_ total data)"
      }
  });

  // Custom filter function untuk filter berdasarkan data attributes
  $.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
        if (settings.nTable.id !== 'tagihanTable') {
            return true;
        }

        const row = dtTagihan.row(dataIndex).node();
        const kecamatanFilter = $('#filterKecamatan').val().toLowerCase();
        const kabupatenFilter = $('#filterKabupaten').val().toLowerCase();
        const statusFilter = $('#filterStatus').val().toLowerCase();

        const kecamatan = $(row).data('kecamatan') ? $(row).data('kecamatan').toString().toLowerCase() : '';
        const kabupaten = $(row).data('kabupaten') ? $(row).data('kabupaten').toString().toLowerCase() : '';
        const status = $(row).data('status') ? $(row).data('status').toString().toLowerCase() : '';

        // Check filters
        if (kecamatanFilter && kecamatan.indexOf(kecamatanFilter) === -1) {
            return false;
        }
        if (kabupatenFilter && kabupaten.indexOf(kabupatenFilter) === -1) {
            return false;
        }
        if (statusFilter && status.indexOf(statusFilter) === -1) {
            return false;
        }

        return true;
    }
  );

  // Filter event
  $('#filterKecamatan, #filterKabupaten, #filterStatus').on('change', function() {
      dtTagihan.draw();
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

      const kecamatan = $('#filterKecamatan').val();
      const kabupaten = $('#filterKabupaten').val();
      const status = $('#filterStatus').val();

      let url = "{{ route('laporan.tagihan.export') }}";
      const params = new URLSearchParams();
      if (kecamatan) params.append('kecamatan', kecamatan);
      if (kabupaten) params.append('kabupaten', kabupaten);
      if (status) params.append('status', status);

      if (params.toString()) url += '?' + params.toString();

      setTimeout(() => {
          hideLoading();
          window.location.href = url;
      }, 1000);
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
      <div class="row g-3">
        <div class="col-md-4">
          <label for="filterKecamatan" class="form-label fw-semibold">
            <i class="ri-building-line me-1"></i>Filter Kecamatan
          </label>
          <select id="filterKecamatan" class="form-select">
            <option value="">-- Semua Kecamatan --</option>
            @foreach($kecamatans as $kecamatan)
              <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label for="filterKabupaten" class="form-label fw-semibold">
            <i class="ri-map-2-line me-1"></i>Filter Kabupaten
          </label>
          <select id="filterKabupaten" class="form-select">
            <option value="">-- Semua Kabupaten --</option>
            @foreach($kabupatens as $kabupaten)
              <option value="{{ $kabupaten }}">{{ $kabupaten }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label for="filterStatus" class="form-label fw-semibold">
            <i class="ri-shield-check-line me-1"></i>Filter Status Pembayaran
          </label>
          <select id="filterStatus" class="form-select">
            <option value="">-- Semua Status --</option>
            <option value="lunas">Lunas</option>
            <option value="belum bayar">Belum Bayar</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="card-datatable table-responsive">
      <table id="tagihanTable" class="datatables-tagihan table table-modern table-hover">
        <thead>
          <tr>
            <th><i class="ri-eye-line me-1"></i>Detail</th>
            <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
            <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
            <th><i class="ri-rocket-line me-1"></i>Nama Paket</th>
            <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
            <th><i class="ri-speed-line me-1"></i>Kecepatan</th>
            <th><i class="ri-shield-check-line me-1"></i>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tagihans as $tagihan)
          <tr
            data-id="{{ $tagihan->id }}"
            data-nama="{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}"
            data-alamat="{{ $tagihan->pelanggan->alamat_jalan ?? '-' }}"
            data-paket="{{ $tagihan->paket->nama_paket ?? '-' }}"
            data-harga="Rp {{ number_format($tagihan->harga, 0, ',', '.') }}"
            data-kecepatan="{{ $tagihan->paket->kecepatan ?? '-' }}"
            data-status="{{ $tagihan->status_pembayaran }}"
            data-kabupaten="{{ $tagihan->pelanggan->kabupaten ?? '-' }}"
            data-kecamatan="{{ $tagihan->pelanggan->kecamatan ?? '-' }}"
            data-tanggal-mulai="{{ $tagihan->tanggal_mulai ?? '-' }}"
            data-tanggal-berakhir="{{ $tagihan->tanggal_berakhir ?? '-' }}"
            data-catatan="{{ $tagihan->catatan ?? '-' }}"
            data-bukti="{{ !empty($tagihan->bukti_pembayaran) ? asset('storage/' . $tagihan->bukti_pembayaran) : '' }}"
          >
            <td>
              <button class="btn btn-sm btn-icon btn-outline-success btn-detail" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-2">
                  <span class="avatar-initial rounded-circle bg-label-primary">
                    {{ substr($tagihan->pelanggan->nama_lengkap ?? 'U', 0, 1) }}
                  </span>
                </div>
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
          @endforeach
        </tbody>
      </table>
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
