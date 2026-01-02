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
<style>
  /* ========== ROOT VARIABLES ========== */
  :root {
    --primary-gradient: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    --card-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ========== GLOBAL STYLES ========== */
  body {
    background: #f8f9fa;
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    overflow: hidden;
  }

  .card:hover {
    box-shadow: var(--card-shadow-hover);
  }

  /* ========== HEADER STYLES ========== */
  .card-header-custom {
    color: black;
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    padding: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
    background: white;
  }

  .page-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .page-subtitle {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
  }

  /* ========== BUTTON STYLES ========== */
  .btn-add {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
    white-space: nowrap;
    border: none;
    background: var(--primary-gradient);
  }

  .btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
  }

  .btn-add i {
    font-size: 1.125rem;
  }

  /* ========== MOBILE CARD VIEW ========== */
  .mobile-card {
    display: none;
    background: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    border-left: 4px solid #696cff;
  }

  .mobile-card:active {
    transform: scale(0.98);
  }

  .mobile-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .mobile-card-id {
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    color: white;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
  }

  .mobile-card-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
  }

  .mobile-card-info {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
  }

  .mobile-info-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.875rem;
  }

  .mobile-info-item i {
    width: 20px;
    color: #696cff;
    font-size: 1rem;
  }

  .mobile-card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.875rem;
    padding-top: 0.875rem;
    border-top: 1px solid #f0f0f0;
  }

  .mobile-card-actions .btn {
    flex: 1;
    padding: 0.625rem;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
  }

  /* ========== TABLE STYLES (Desktop) ========== */
  .table-container {
    display: block;
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
    white-space: nowrap;
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

  .table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
  }

  /* ========== MODAL STYLES ========== */
  .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  }

  .modal-header {
    background: var(--primary-gradient);
    border-radius: 20px 20px 0 0;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
  }

  .modal-title {
    font-weight: 700;
    font-size: 1.125rem;
    color: #ffffff;
  }

  .modal-body {
    padding: 1.5rem;
    max-height: 70vh;
    overflow-y: auto;
  }

  .modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
    border-radius: 0 0 20px 20px;
  }

  .btn-close-white {
    filter: brightness(0) invert(1);
  }

  /* ========== DETAIL SECTION ========== */
  .customer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 4px 16px rgba(105, 108, 255, 0.4);
    border: 3px solid white;
  }

  .detail-section {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .detail-section h6 {
    color: #696cff;
    font-weight: 700;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    padding-bottom: 0.625rem;
    border-bottom: 2px solid #696cff;
  }

  .detail-section h6 i {
    margin-right: 0.5rem;
    font-size: 1rem;
  }

  .detail-item {
    display: flex;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
    align-items: flex-start;
    gap: 1rem;
  }

  .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .detail-label {
    color: #6c757d;
    font-weight: 600;
    min-width: 120px;
    font-size: 0.8125rem;
    display: flex;
    align-items: center;
  }

  .detail-label i {
    margin-right: 0.5rem;
    color: #a8afc7;
    font-size: 0.875rem;
  }

  .detail-value {
    color: #2c3e50;
    font-size: 0.875rem;
    flex: 1;
    word-break: break-word;
  }

  .customer-header-info {
    text-align: center;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    border: 1px solid #e8e8e8;
  }

  .customer-name {
    font-size: 1.375rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
  }

  .customer-id {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    background: var(--primary-gradient);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8125rem;
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
  }

  .ktp-preview {
    max-width: 100%;
    border-radius: 8px;
    border: 2px solid #e8e8e8;
    margin-top: 0.5rem;
  }

  /* ========== LOADING OVERLAY ========== */
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
  }

  .spinner-border-custom {
    width: 3.5rem;
    height: 3.5rem;
    border-width: 0.35rem;
  }

  /* ========== FLOATING ACTION BUTTON (Mobile) ========== */
  .fab-container {
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1000;
  }

  .fab {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-gradient);
    color: white;
    border: none;
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: var(--transition);
  }

  .fab:active {
    transform: scale(0.9);
  }

  .fab i {
    font-size: 1.75rem;
  }

  /* ========== RESPONSIVE DESIGN ========== */
  @media (max-width: 768px) {
    /* Hide desktop table */
    .table-container {
      display: none !important;
    }

    /* Show mobile cards */
    .mobile-card {
      display: block;
    }

    /* Show FAB */
    .fab-container {
      display: block;
    }

    /* Hide desktop add button */
    .btn-add {
      display: none;
    }

    /* Adjust header */
    .card-header-custom {
      padding: 1.25rem;
    }

    .page-title {
      font-size: 1.25rem;
    }

    .page-subtitle {
      font-size: 0.8125rem;
    }

    /* Modal adjustments */
    .modal-dialog {
      margin: 0.5rem;
    }

    .modal-body {
      padding: 1.25rem;
      max-height: 75vh;
    }

    .customer-avatar {
      width: 70px;
      height: 70px;
      font-size: 1.75rem;
    }

    .customer-name {
      font-size: 1.25rem;
    }

    .detail-section {
      padding: 0.875rem;
    }

    .detail-item {
      flex-direction: column;
      gap: 0.375rem;
      padding: 0.625rem 0;
    }

    .detail-label {
      min-width: auto;
      font-weight: 700;
    }

    .detail-value {
      padding-left: 1.5rem;
    }

    /* Card body padding */
    .card-body {
      padding: 1rem !important;
    }

    /* Swal adjustments */
    .swal2-popup {
      width: 90% !important;
      padding: 1.5rem !important;
    }

    .swal2-title {
      font-size: 1.25rem !important;
    }

    .swal2-html-container {
      font-size: 0.9375rem !important;
    }
  }

  @media (max-width: 480px) {
    .mobile-card {
      border-radius: 10px;
      padding: 0.875rem;
    }

    .mobile-card-name {
      font-size: 1rem;
    }

    .mobile-info-item {
      font-size: 0.8125rem;
    }

    .fab {
      width: 56px;
      height: 56px;
    }

    .fab i {
      font-size: 1.5rem;
    }

    .fab-container {
      bottom: 20px;
      right: 20px;
    }
  }

  /* ========== BADGE IMPROVEMENTS ========== */
  .badge {
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.3px;
  }

  .badge.bg-success {
    background: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%) !important;
  }

  .badge.bg-warning {
    background: linear-gradient(135deg, #ffab00 0%, #e09900 100%) !important;
  }

  .badge.bg-danger {
    background: linear-gradient(135deg, #ff3e1d 0%, #d32f2f 100%) !important;
  }

  /* ========== SCROLLBAR STYLING ========== */
  .modal-body::-webkit-scrollbar,
  .card-datatable::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }

  .modal-body::-webkit-scrollbar-thumb,
  .card-datatable::-webkit-scrollbar-thumb {
    background: #696cff;
    border-radius: 10px;
  }

  .modal-body::-webkit-scrollbar-track,
  .card-datatable::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 10px;
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

    // Inisialisasi DataTable (hanya untuk desktop)
    if (window.innerWidth > 768) {
        const dtUserTable = $('.datatables-users').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            searching: true,
            ordering: true,
            info: true,
            responsive: false,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
            columnDefs: [
                { orderable: false, targets: [0, -1] }
            ],
            language: {
                paginate: {
                    previous: '<i class="ri-arrow-left-s-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>'
                },
                search: "_INPUT_",
                searchPlaceholder: "Cari pelanggan...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ pelanggan",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang sesuai"
            }
        });
    }

    // Event Detail - untuk desktop table dan mobile cards
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const target = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.mobile-card');
        
        const nomerId = target.data('nomer-id') || '-';
        const namaLengkap = target.data('nama') || '-';
        const noWhatsapp = target.data('whatsapp') || '-';
        const alamatJalan = target.data('alamat') || '-';
        const rt = target.data('rt') || '-';
        const rw = target.data('rw') || '-';
        const kecamatan = target.data('kecamatan') || '-';
        const kabupaten = target.data('kabupaten') || '-';
        const tanggalMulai = target.data('tanggal-mulai') || '-';
        const fotoKtp = target.data('foto-ktp') || '';
        const status = target.data('status') || '-';
        const marketingName = target.data('marketing-name') || 'Admin';
        const marketingEmail = target.data('marketing-email') || '-';
        const createdAt = target.data('created-at') || '-';
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
                <h6><i class="ri-user-3-line"></i>Info Pribadi</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-id-card-line"></i>No. ID
                    </span>
                    <span class="detail-value"><strong>${nomerId}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-line"></i>Nama
                    </span>
                    <span class="detail-value">${namaLengkap}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-whatsapp-line"></i>WhatsApp
                    </span>
                    <span class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <strong>${noWhatsapp}</strong> <i class="ri-external-link-line"></i>
                        </a>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat</h6>
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
                <h6><i class="ri-calendar-check-line"></i>Langganan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-line"></i>Mulai
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
                <h6><i class="ri-user-settings-line"></i>Marketing</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-user-star-line"></i>Nama
                    </span>
                    <span class="detail-value"><strong>${marketingName}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-mail-line"></i>Email
                    </span>
                    <span class="detail-value">${marketingEmail}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Input
                    </span>
                    <span class="detail-value">${createdAt}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-image-line"></i>Foto KTP</h6>
                <div class="text-center">
                    ${fotoKtp ? '<img src="' + fotoKtp + '" class="ktp-preview" alt="Foto KTP">' : '<p class="text-muted">Tidak ada foto</p>'}
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // Event DELETE
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Data?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ff3e1d',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                setTimeout(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        form.submit();
                    });
                }, 800);
            }
        });
    });
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Main Card -->
<div class="card border-0 shadow-sm">
    <!-- Header -->
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="page-title">
                    <i class="ri-user-star-line"></i>
                    Data Pelanggan
                </h4>
                <p class="page-subtitle">Kelola data pelanggan Anda</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('marketing.add-pelanggan') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line me-2"></i>
                    Tambah Pelanggan
                </a>
            </div>
        </div>
    </div>
    
    <!-- Body -->
    <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="card-datatable table-responsive p-3 table-container">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-eye-line me-1"></i>Detail</th>
                        <th><i class="ri-barcode-line me-1"></i>No. ID</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama</th>
                        <th><i class="ri-whatsapp-line me-1"></i>WhatsApp</th>
                        <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
                        <th><i class="ri-calendar-line me-1"></i>Tanggal</th>
                        <th><i class="ri-shield-check-line me-1"></i>Status</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pelanggan as $p)
                    <tr
                        data-nomer-id="{{ $p->nomer_id ?? '' }}"
                        data-nama="{{ $p->nama_lengkap ?? '' }}"
                        data-whatsapp="{{ $p->no_whatsapp ?? '' }}"
                        data-alamat="{{ $p->alamat_jalan ?? '' }}"
                        data-rt="{{ $p->rt ?? '' }}"
                        data-rw="{{ $p->rw ?? '' }}"
                        data-kecamatan="{{ $p->kecamatan ?? '' }}"
                        data-kabupaten="{{ $p->kabupaten ?? '' }}"
                        data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                        data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                        data-status="{{ $p->status ?? '' }}"
                        data-marketing-name="{{ optional($p->user)->name ?? 'Admin' }}"
                        data-marketing-email="{{ optional($p->user)->email ?? '' }}"
                        data-created-at="{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') : '' }}"
                    >
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        </td>
                        <td><span class="badge bg-label-dark">{{ $p->nomer_id ?? '-' }}</span></td>
                        <td><span class="fw-semibold">{{ $p->nama_lengkap ?? '-' }}</span></td>
                        <td>
                            @if($p->no_whatsapp)
                            <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="text-success text-decoration-none">
                                <i class="ri-whatsapp-line me-1"></i>{{ $p->no_whatsapp }}
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ Str::limit($p->alamat_jalan ?? '-', 25) }}<br>
                            <small class="text-muted">RT {{ $p->rt ?? '-' }}/RW {{ $p->rw ?? '-' }}, {{ $p->kecamatan ?? '-' }}</small>
                        </td>
                        <td>{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</td>
                        <td>
                            @php
                              $statusClass = match(strtolower($p->status ?? 'pending')) {
                                  'reject' => 'badge bg-danger',
                                  'pending' => 'badge bg-warning text-dark',
                                  'approve' => 'badge bg-success',
                                  default => 'badge bg-secondary',
                              };
                            @endphp
                            <span class="{{ $statusClass }}">{{ ucfirst($p->status ?? 'Pending') }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" class="d-inline">
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
        </div>

        <!-- Mobile Card View -->
        <div class="p-3">
            @foreach($pelanggan as $p)
            <div class="mobile-card"
                data-nomer-id="{{ $p->nomer_id ?? '' }}"
                data-nama="{{ $p->nama_lengkap ?? '' }}"
                data-whatsapp="{{ $p->no_whatsapp ?? '' }}"
                data-alamat="{{ $p->alamat_jalan ?? '' }}"
                data-rt="{{ $p->rt ?? '' }}"
                data-rw="{{ $p->rw ?? '' }}"
                data-kecamatan="{{ $p->kecamatan ?? '' }}"
                data-kabupaten="{{ $p->kabupaten ?? '' }}"
                data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                data-status="{{ $p->status ?? '' }}"
                data-marketing-name="{{ optional($p->user)->name ?? 'Admin' }}"
                data-marketing-email="{{ optional($p->user)->email ?? '' }}"
                data-created-at="{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') : '' }}"
            >
                <div class="mobile-card-header">
                    <div>
                        <div class="mobile-card-id">{{ $p->nomer_id ?? '-' }}</div>
                    </div>
                    <div>
                        @php
                          $statusClass = match(strtolower($p->status ?? 'pending')) {
                              'reject' => 'badge bg-danger',
                              'pending' => 'badge bg-warning text-dark',
                              'approve' => 'badge bg-success',
                              default => 'badge bg-secondary',
                          };
                        @endphp
                        <span class="{{ $statusClass }}">{{ ucfirst($p->status ?? 'Pending') }}</span>
                    </div>
                </div>
                
                <div class="mobile-card-name">{{ $p->nama_lengkap ?? '-' }}</div>
                
                <div class="mobile-card-info">
                    <div class="mobile-info-item">
                        <i class="ri-whatsapp-line"></i>
                        <span>{{ $p->no_whatsapp ?? '-' }}</span>
                    </div>
                    <div class="mobile-info-item">
                        <i class="ri-map-pin-line"></i>
                        <span>{{ Str::limit($p->alamat_jalan ?? '-', 40) }}</span>
                    </div>
                    <div class="mobile-info-item">
                        <i class="ri-calendar-line"></i>
                        <span>{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                <div class="mobile-card-actions">
                    <button class="btn btn-outline-primary btn-sm btn-detail">
                        <i class="ri-eye-line"></i>Detail
                    </button>
                    <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn btn-outline-warning btn-sm">
                        <i class="ri-edit-2-line"></i>Edit
                    </a>
                    <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" class="d-inline" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete" style="width: 100%;">
                            <i class="ri-delete-bin-line"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Floating Action Button (Mobile Only) -->
<div class="fab-container">
    <a href="{{ route('marketing.add-pelanggan') }}" class="fab">
        <i class="ri-add-line"></i>
    </a>
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
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
