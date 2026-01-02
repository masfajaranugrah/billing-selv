@extends('layouts/layoutMaster')

@section('title', 'Data Pelanggan')

@php
use Illuminate\Support\Str;
@endphp

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

{{-- PAGE STYLE --}}
@section('page-style')
<style>
  :root {
    --primary: #696cff;
    --primary-hover: #5a5dc9;
    --success: #71dd37;
    --danger: #ff3e1d;
    --warning: #ffab00;
    --gray-bg: #f8f9fa;
    --gray-border: #e8e8e8;
  }

  * {
    box-sizing: border-box;
  }

  body {
    background: #f5f5f9;
  }

  .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    background: white;
  }

  /* ========== HEADER SECTION ========== */
  .card-header-custom {
    background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
    border-bottom: 2px solid #f0f0f0;
    padding: 1.75rem 2rem;
    border-radius: 12px 12px 0 0;
  }

  .card-header-custom h4 {
    color: #2c3e50;
    font-size: 1.5rem;
  }

  .btn-add {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.25);
    transition: all 0.3s ease;
  }

  .btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(105, 108, 255, 0.35);
  }

  /* ========== SEARCH SECTION ========== */
  .search-section {
    background: var(--gray-bg);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-border);
  }

  .search-input-group {
    max-width: 900px;
    margin: 0 auto;
  }

  .search-input-group .input-group {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-radius: 10px;
    overflow: hidden;
  }

  .search-input-group .input-group-text {
    background: white;
    border: 2px solid #e0e0e0;
    border-right: 0;
    padding: 0.75rem 1rem;
  }

  .search-input-group .form-control {
    border: 2px solid #e0e0e0;
    border-left: 0;
    border-right: 0;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
  }

  .search-input-group .form-control:focus {
    border-color: var(--primary);
    box-shadow: none;
  }

  .search-input-group .btn {
    border: 2px solid #e0e0e0;
    border-left: 0;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    white-space: nowrap;
  }

  .btn-clear-search {
    background: white;
    border: 2px solid #e0e0e0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    color: #6c757d;
    padding: 0.75rem 1rem;
  }

  .btn-clear-search:hover {
    background: #f8f9fa;
    color: #dc3545;
  }

  .search-info-box {
    max-width: 900px;
    margin: 1rem auto 0;
    padding: 0.75rem 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .search-keyword {
    background: #f0f0ff;
    color: var(--primary);
    padding: 2px 10px;
    border-radius: 4px;
    font-weight: 600;
  }

  /* ========== TABLE SECTION ========== */
  .table-modern {
    margin-bottom: 0;
  }

  .table-modern thead th {
    background: #f8f9fa;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #6c757d;
    padding: 1rem;
    border: none;
    white-space: nowrap;
  }

  .table-modern tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
  }

  .table-modern tbody tr:hover {
    background: #f8f9ff !important;
  }

  .table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
  }

  .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* ========== PAGINATION SECTION ========== */
  .pagination-section {
    background: var(--gray-bg);
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--gray-border);
    border-radius: 0 0 12px 12px;
  }

  .pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .pagination-info {
    color: #6c757d;
    font-size: 0.9rem;
  }

  .pagination-info i {
    color: var(--primary);
    margin-right: 0.5rem;
  }

  .pagination {
    margin: 0;
  }

  .pagination .page-link {
    border-radius: 6px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
    color: #495057;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    min-width: 38px;
    text-align: center;
    transition: all 0.2s ease;
  }

  .pagination .page-link:hover {
    background: #f0f0ff;
    border-color: var(--primary);
    color: var(--primary);
  }

  .pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
  }

  .pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #adb5bd;
    cursor: not-allowed;
  }

  /* ========== EMPTY STATE ========== */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
  }

  .empty-state-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
  }

  .empty-state h5 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }

  .empty-state p {
    color: #6c757d;
    margin-bottom: 1.5rem;
  }

  /* ========== LOADING OVERLAY ========== */
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
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

  /* ========== MODAL STYLING ========== */
  .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  }

  .modal-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
    border-radius: 16px 16px 0 0;
    color: white;
    padding: 1.5rem 2rem;
    border: none;
  }

  .modal-title {
    font-weight: 600;
    font-size: 1.125rem;
  }

  .btn-close-white {
    filter: brightness(0) invert(1);
    opacity: 0.9;
  }

  .btn-close-white:hover {
    opacity: 1;
  }

  .modal-body {
    padding: 2rem;
    max-height: 70vh;
    overflow-y: auto;
  }

  .modal-footer {
    padding: 1.5rem 2rem;
    border-top: 1px solid #e8e8e8;
    background: #fafafa;
    border-radius: 0 0 16px 16px;
  }

  .customer-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
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

  .detail-section {
    background: white;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    transition: all 0.2s ease;
  }

  .detail-section:hover {
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.1);
  }

  .detail-section h6 {
    color: var(--primary);
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--primary);
    display: flex;
    align-items: center;
  }

  .detail-section h6 i {
    margin-right: 0.5rem;
    font-size: 1.1rem;
  }

  .detail-item {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .detail-label {
    color: #6c757d;
    font-weight: 600;
    min-width: 180px;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
  }

  .detail-label i {
    margin-right: 0.5rem;
    color: var(--primary);
    font-size: 1rem;
  }

  .detail-value {
    color: #495057;
    font-size: 0.875rem;
    flex: 1;
    word-break: break-word;
  }

  .customer-header-info {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 10px;
    margin-bottom: 1.5rem;
    border: 2px solid #e8e8e8;
  }

  .customer-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
  }

  .customer-id {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
  }

  .ktp-preview {
    max-width: 100%;
    border-radius: 8px;
    border: 2px solid #e8e8e8;
    margin-top: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  /* ========== RESPONSIVE ========== */
  @media (max-width: 768px) {
    .card-header-custom,
    .search-section,
    .pagination-section {
      padding: 1rem 1.25rem;
    }

    .pagination-wrapper {
      flex-direction: column;
      text-align: center;
    }

    .btn-add {
      width: 100%;
    }

    .search-input-group .input-group {
      flex-wrap: wrap;
    }

    .search-input-group .btn {
      flex: 1 1 100%;
      border-radius: 0 0 8px 8px !important;
      border: 2px solid #e0e0e0 !important;
      margin-top: -2px;
    }

    .detail-label {
      min-width: 120px;
      font-size: 0.8rem;
    }

    .detail-value {
      font-size: 0.8rem;
    }
  }

  @media (max-width: 576px) {
    .table-modern {
      font-size: 0.85rem;
    }

    .table-modern thead th,
    .table-modern tbody td {
      padding: 0.75rem 0.5rem;
    }

    .empty-state {
      padding: 3rem 1rem;
    }
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
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "{{ env('ONESIGNAL_APP_ID') }}",
            safari_web_id: "",
            allowLocalhostAsSecureOrigin: true,
        });

        OneSignal.on('subscriptionChange', function (isSubscribed) {
            if (isSubscribed) {
                OneSignal.getUserId(function(player_id) {
                    fetch('/pelanggan/save-player-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ player_id })
                    });
                });
            }
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // ? HANYA INISIALISASI DATATABLES JIKA ADA DATA
    @if($pelanggan->count() > 0)
        const dtUserTable = $('.datatables-users').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            responsive: false,
            dom: 'rt',
            columnDefs: [
                { orderable: false, targets: [0, -1] }
            ],
            language: {
                emptyTable: "Tidak ada data pelanggan tersedia",
                zeroRecords: "Tidak ditemukan data yang sesuai"
            }
        });
    @endif

    // LIVE SEARCH dengan debounce
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val();
        
        searchTimeout = setTimeout(function() {
            if (query.length >= 2 || query.length === 0) {
                showLoading();
                $('#searchForm').submit();
            }
        }, 600);
    });

    // Clear search button
    $('#clearSearch').on('click', function(e) {
        e.preventDefault();
        showLoading();
        window.location.href = "{{ route('pelanggan') }}";
    });

    // Show loading saat submit form
    $('#searchForm').on('submit', function() {
        showLoading();
    });

    // EVENT DETAIL MODAL
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tr = $(this).closest('tr');

        const nomerId = tr.data('nomer-id') || '-';
        const namaLengkap = tr.data('nama') || '-';
        const noWhatsapp = tr.data('whatsapp') || '-';
        const alamatJalan = tr.data('alamat') || '-';
        const rt = tr.data('rt') || '-';
        const rw = tr.data('rw') || '-';
        const kecamatan = tr.data('kecamatan') || '-';
        const kabupaten = tr.data('kabupaten') || '-';
        const tanggalMulai = tr.data('tanggal-mulai') || '-';
        const fotoKtp = tr.data('foto-ktp') || '';
        const status = tr.data('status') || '-';
        const marketingName = tr.data('marketing-name') || 'Sistem';
        const marketingEmail = tr.data('marketing-email') || '-';
        const createdAt = tr.data('created-at') || '-';
        const initial = namaLengkap ? namaLengkap.charAt(0).toUpperCase() : '?';

        let statusBadge = '';
        if (status.toLowerCase() === 'approve') {
            statusBadge = '<span class="badge bg-success">Approve</span>';
        } else if (status.toLowerCase() === 'pending') {
            statusBadge = '<span class="badge bg-warning">Pending</span>';
        } else if (status.toLowerCase() === 'reject') {
            statusBadge = '<span class="badge bg-danger">Reject</span>';
        } else {
            statusBadge = '<span class="badge bg-secondary">' + status + '</span>';
        }

        const html = `
            <div class="customer-header-info">
                <div class="customer-avatar mx-auto">
                    ${initial}
                </div>
                <div class="customer-name">${namaLengkap}</div>
                <div class="customer-id">
                    <i class="ri-barcode-line me-2"></i>ID: ${nomerId}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Pribadi</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-id-card-line"></i>No. ID
                    </span>
                    <span class="detail-value"><strong>${nomerId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-line"></i>Nama Lengkap
                    </span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-whatsapp-line"></i>No. WhatsApp
                    </span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <strong>${noWhatsapp}</strong> <i class="ri-external-link-line"></i>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-road-map-line"></i>Jalan
                    </span>
                    <span class="detail-value">${alamatJalan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-community-line"></i>RT / RW
                    </span>
                    <span class="detail-value">${rt} / ${rw}</span>
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
                <h6><i class="ri-calendar-check-line"></i>Informasi Langganan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-line"></i>Tanggal Mulai
                    </span>
                    <span class="detail-value">${tanggalMulai}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-shield-check-line"></i>Status
                    </span>
                    <span class="detail-value">${statusBadge}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-user-settings-line"></i>Ditambahkan Oleh</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-star-line"></i>Marketing
                    </span>
                    <span class="detail-value">
                        <strong>${marketingName}</strong>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-mail-line"></i>Email
                    </span>
                    <span class="detail-value">${marketingEmail}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Tanggal Input
                    </span>
                    <span class="detail-value">${createdAt}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-image-line"></i>Foto KTP</h6>
                <div class="text-center">
                    ${fotoKtp ? '<img src="' + fotoKtp + '" class="ktp-preview" alt="Foto KTP">' : '<p class="text-muted">Tidak ada foto KTP</p>'}
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // EVENT DELETE
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data pelanggan ini? Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
            reverseButtons: false,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(form).find('.btn-delete');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');
                showLoading();

                setTimeout(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pelanggan berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        form.submit();
                    });
                }, 1000);
            }
        });
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

<div class="card">
    {{-- HEADER --}}
    <div class="card-header-custom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-user-star-line me-2 text-primary"></i>Data Pelanggan
                </h4>
                <p class="mb-0 text-muted small">Kelola dan monitor data pelanggan</p>
            </div>
            <div>
                <a href="{{ route('add-pelanggan') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line me-2"></i>Tambah Pelanggan
                </a>
            </div>
        </div>
    </div>

    {{-- SEARCH SECTION --}}
    <div class="search-section">
        <form action="{{ route('pelanggan') }}" method="GET" id="searchForm">
            <div class="search-input-group">
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="ri-search-line text-primary"></i>
                    </span>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Cari berdasarkan ID, Nama, No. WA, Alamat, RT/RW, Kecamatan, Kabupaten, atau Status..." 
                        value="{{ request('search') }}"
                        id="searchInput"
                        autocomplete="off"
                    >
                    @if(request('search'))
                    <button type="button" class="btn btn-clear-search" id="clearSearch" title="Hapus Pencarian">
                        <i class="ri-close-line"></i>
                    </button>
                    @endif
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-2-line me-1"></i>Cari
                    </button>
                </div>
            </div>

            @if(request('search'))
            <div class="search-info-box">
                <div>
                    <i class="ri-filter-3-line text-primary me-2"></i>
                    <small class="text-muted">
                        Hasil pencarian: <span class="search-keyword">"{{ request('search') }}"</span>
                    </small>
                </div>
                <a href="{{ route('pelanggan') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ri-refresh-line me-1"></i>Reset
                </a>
            </div>
            @endif
        </form>
    </div>

    {{-- TABLE SECTION --}}
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            @if($pelanggan->count() > 0)
                <table class="datatables-users table table-modern table-hover">
                    <thead>
                        <tr>
                            <th><i class="ri-eye-line me-1"></i>Detail</th>
                            <th><i class="ri-barcode-line me-1"></i>No. ID</th>
                            <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
                            <th><i class="ri-whatsapp-line me-1"></i>No. WhatsApp</th>
                            <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
                            <th><i class="ri-calendar-line me-1"></i>Tanggal</th>
                            <th><i class="ri-shield-check-line me-1"></i>Status</th>
                            <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pelanggan as $p)
                        <tr
                            data-nomer-id="{{ $p->nomer_id }}"
                            data-nama="{{ $p->nama_lengkap }}"
                            data-whatsapp="{{ $p->no_whatsapp }}"
                            data-alamat="{{ $p->alamat_jalan }}"
                            data-rt="{{ $p->rt }}"
                            data-rw="{{ $p->rw }}"
                            data-kecamatan="{{ $p->kecamatan }}"
                            data-kabupaten="{{ $p->kabupaten }}"
                            data-tanggal-mulai="{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}"
                            data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                            data-status="{{ ucfirst($p->status ?? '-') }}"
                            data-marketing-name="{{ $p->user->name ?? 'Admin' }}"
                            data-marketing-email="{{ $p->user->email ?? '-' }}"
                            data-created-at="{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}"
                        >
                            <td>
                                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>

                            <td>
                                <span class="badge bg-label-dark">{{ $p->nomer_id }}</span>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $p->nama_lengkap }}</span>
                            </td>

                            <td>
                                <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="text-success text-decoration-none">
                                    <i class="ri-whatsapp-line me-1"></i>{{ $p->no_whatsapp }}
                                </a>
                            </td>

                            <td>
                                {{ Str::limit($p->alamat_jalan, 30) }}<br>
                                <small class="text-muted">RT {{ $p->rt }}/RW {{ $p->rw }}, {{ $p->kecamatan }}</small>
                            </td>

                            <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</td>

                            <td>
                                @php
                                  $statusClass = match(strtolower($p->status ?? '')) {
                                      'reject' => 'badge bg-danger',
                                      'pending' => 'badge bg-warning',
                                      'approve' => 'badge bg-success',
                                      default => 'badge bg-secondary',
                                  };
                                @endphp
                                <span class="{{ $statusClass }}">{{ ucfirst($p->status ?? '-') }}</span>
                            </td>

                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('pelanggan.edit', $p->id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">
                                        <i class="ri-edit-2-line"></i>
                                    </a>

                                    <form action="{{ route('pelanggan.delete', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                {{-- EMPTY STATE --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ri-inbox-line"></i>
                    </div>
                    @if(request('search'))
                        <h5>Tidak ada hasil untuk "{{ request('search') }}"</h5>
                        <p>Coba gunakan kata kunci lain atau ubah filter pencarian</p>
                        <a href="{{ route('pelanggan') }}" class="btn btn-outline-primary">
                            <i class="ri-refresh-line me-2"></i>Reset Pencarian
                        </a>
                    @else
                        <h5>Belum ada data pelanggan</h5>
                        <p>Mulai tambahkan pelanggan baru untuk mengelola data</p>
                        <a href="{{ route('add-pelanggan') }}" class="btn btn-primary">
                            <i class="ri-user-add-line me-2"></i>Tambah Pelanggan Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if($pelanggan->hasPages())
        <div class="pagination-section">
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <i class="ri-file-list-3-line"></i>
                    @if(request('search'))
                        <span>Ditemukan <strong>{{ $pelanggan->total() }}</strong> hasil 
                        (<strong>{{ $pelanggan->firstItem() }}</strong>-<strong>{{ $pelanggan->lastItem() }}</strong>)</span>
                    @else
                        <span>Menampilkan <strong>{{ $pelanggan->firstItem() ?? 0 }}</strong>-<strong>{{ $pelanggan->lastItem() ?? 0 }}</strong> 
                        dari <strong>{{ $pelanggan->total() }}</strong> pelanggan</span>
                    @endif
                </div>
                <div>
                    {{ $pelanggan->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail Pelanggan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Content will be inserted via JavaScript -->
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
