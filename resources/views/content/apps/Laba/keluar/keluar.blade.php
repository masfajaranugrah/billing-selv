@extends('layouts/layoutMaster')

@section('title', 'Pengeluaran')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #18181b;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
}

/* ========== CARD ========== */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

/* ========== HEADER SECTION ========== */
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  color: #18181b;
}

.card-header-custom h4 {
  color: #18181b !important;
  font-size: 1.5rem;
}

.card-header-custom p {
  color: #71717a !important;
}

.card-header-custom i {
  color: #18181b !important;
}

/* ========== TABLE STYLES ========== */
.table-modern {
  margin-bottom: 0;
  border-radius: 8px;
  overflow: hidden;
  border-collapse: separate;
  border-spacing: 0;
}

.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #18181b;
  padding: 1rem;
  border: none;
  white-space: nowrap;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e4e4e7;
}

.table-modern tbody tr:hover {
  background-color: #f4f4f5 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #e4e4e7;
  color: #18181b;
}

/* ========== BUTTONS - ALL BLACK ========== */
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

.btn-primary,
.btn.btn-primary,
.btn-add {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}

.btn-primary:hover,
.btn.btn-primary:hover,
.btn-add:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
  transform: translateY(-2px) !important;
}

.btn-add i {
  margin-right: 8px;
}

.btn-success,
.btn.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-success:hover,
.btn.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-danger,
.btn.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-danger:hover,
.btn.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-secondary,
.btn.btn-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-secondary:hover,
.btn.btn-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

.btn-outline-primary,
.btn-outline-danger,
.btn-outline-success {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}

.btn-outline-primary:hover,
.btn-outline-danger:hover,
.btn-outline-success:hover {
  background: #18181b !important;
  color: #fafafa !important;
}

/* ========== BADGES ========== */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  padding: 0.35rem 0.75rem !important;
}

.badge.bg-success,
.badge.bg-label-success {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-secondary {
  background: #f4f4f5 !important;
  color: #71717a !important;
  border: 1px solid #e4e4e7;
}

.badge.bg-label-dark,
.badge.bg-label-primary,
.badge.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
}

/* ========== LOADING OVERLAY ========== */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(24, 24, 27, 0.5);
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

/* ========== MODAL STYLES ========== */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  background: #18181b;
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
}

.modal-body p {
  margin-bottom: 1rem;
  line-height: 1.6;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.modal-body p:last-child {
  border-bottom: none;
}

.modal-body strong {
  color: #18181b;
  font-weight: 600;
  display: inline-block;
  min-width: 140px;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* ========== MODAL BLUR EFFECT ========== */
.modal-backdrop {
  backdrop-filter: blur(8px) !important;
  -webkit-backdrop-filter: blur(8px) !important;
  background-color: rgba(24, 24, 27, 0.5) !important;
}

.modal-backdrop.show {
  opacity: 1 !important;
}

.modal {
  backdrop-filter: none !important;
}

.modal-content {
  backdrop-filter: none !important;
  filter: none !important;
}

/* ========== DAILY SUMMARY CARDS ========== */
.summary-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: all 0.3s;
  overflow: hidden;
}

.summary-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.summary-card .card-body {
  padding: 1.25rem;
}

.summary-card .card-title {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #71717a;
  margin-bottom: 0.5rem;
}

.summary-card .card-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #18181b;
}

.summary-card .card-code {
  font-size: 0.7rem;
  color: #71717a;
  margin-top: 4px;
}

/* Summary Card Avatar/Icon Styling */
.summary-card .avatar {
  width: 48px !important;
  height: 48px !important;
  min-width: 48px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  border-radius: 50% !important;
}

.summary-card .avatar i {
  font-size: 1.25rem !important;
  line-height: 1 !important;
}

.summary-card-total {
  background: #18181b;
}

.summary-card-total .avatar {
  background: rgba(255, 255, 255, 0.15) !important;
}

.summary-card-total .avatar i {
  color: #fafafa !important;
}

.summary-card:not(.summary-card-total) .avatar {
  background: #f4f4f5 !important;
}

.summary-card:not(.summary-card-total) .avatar i {
  color: #18181b !important;
}

.summary-card-total .card-title,
.summary-card-total .card-value,
.summary-card-total .card-code {
  color: #fafafa !important;
}

/* ========== EXPORT BUTTONS ========== */
.btn-export {
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}

.btn-export:hover {
  transform: translateY(-2px);
  background: #18181b !important;
  color: #fafafa !important;
}

/* ========== DATE INFO ========== */
.date-info {
  background: #f4f4f5;
  border-radius: 12px;
  padding: 1rem 1.5rem;
  border-left: 4px solid #18181b;
}

.date-info .text-primary {
  color: #18181b !important;
}

/* ========== PAGINATION STYLES ========== */
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
}

.text-primary {
  color: #18181b !important;
}

.text-success {
  color: #18181b !important;
}

/* ========== ANIMATIONS ========== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper function untuk loading overlay
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable
    const dtExpenseTable = $('.datatables-expense').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: true,
        info: true,
        responsive: false,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f><rtip',
        columnDefs: [
            { orderable: false, targets: [-1] }
        ],
        language: {
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            },
            search: "_INPUT_",
            searchPlaceholder: "Cari pengeluaran...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ pengeluaran",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Initialize Flatpickr for date pickers
    if(typeof flatpickr !== 'undefined') {
        flatpickr('#export_start_date', {
            dateFormat: 'Y-m-d',
            maxDate: 'today'
        });
        flatpickr('#export_end_date', {
            dateFormat: 'Y-m-d',
            maxDate: 'today'
        });
    }

    // Event Detail Pengeluaran
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        const kode = tr.data('kode') || '-';
        const kategori = tr.data('kategori') || '-';
        const jumlah = tr.data('jumlah') || '0';
        const keterangan = tr.data('keterangan') || '-';
        const tanggal = tr.data('tanggal') || '-';
        const jam = tr.data('jam') || '-';

        const html = `
            <p><strong>Kode:</strong> ${kode}</p>
            <p><strong>Kategori:</strong> ${kategori}</p>
            <p><strong>Jumlah:</strong> Rp ${parseInt(jumlah).toLocaleString('id-ID')}</p>
            <p><strong>Keterangan:</strong> ${keterangan}</p>
            <p><strong>Tanggal Keluar:</strong> ${tanggal}</p>
            <p><strong>Jam Keluar:</strong> ${jam}</p>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data pengeluaran ini? Data tidak dapat dikembalikan!',
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
                        text: 'Data pengeluaran berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        form.submit();
                    });
                }, 1000);
            }
        });
    });

    // Export Monthly Form Submit
    $('#exportMonthlyForm').on('submit', function(e) {
        e.preventDefault();
        const month = $('#export_month').val();
        const year = $('#export_year').val();
        
        if (!month || !year) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih bulan dan tahun terlebih dahulu.'
            });
            return;
        }
        
        showLoading();
        window.location.href = `{{ route('keluar.export.monthly') }}?month=${month}&year=${year}`;
        setTimeout(() => hideLoading(), 2000);
        $('#exportMonthlyModal').modal('hide');
    });

    // Export Date Range Form Submit
    $('#exportDateRangeForm').on('submit', function(e) {
        e.preventDefault();
        const startDate = $('#export_start_date').val();
        const endDate = $('#export_end_date').val();
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan pilih tanggal awal dan akhir terlebih dahulu.'
            });
            return;
        }
        
        showLoading();
        window.location.href = `{{ route('keluar.export.daterange') }}?start_date=${startDate}&end_date=${endDate}`;
        setTimeout(() => hideLoading(), 2000);
        $('#exportDateRangeModal').modal('hide');
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

<!-- Date Info -->
<div class="date-info mb-4">
    <div class="d-flex align-items-center">
        <i class="ri-calendar-check-line fs-4 me-3 text-primary"></i>
        <div>
            <h6 class="mb-0 fw-bold">Pengeluaran Hari Ini</h6>
            <small class="text-muted">{{ \Carbon\Carbon::parse($today)->locale('id')->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>
</div>

<!-- Daily Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Total Hari Ini -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card summary-card summary-card-total h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-md bg-white bg-opacity-25 rounded-circle">
                            <i class="ri-money-dollar-circle-line fs-4 text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">Total Hari Ini</h6>
                        <span class="card-value">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($todayTotals as $kategori => $total)
    @if($total > 0)
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card summary-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-md bg-light rounded-circle">
                            <i class="ri-wallet-3-line fs-5 text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="card-title mb-1">{{ $kategori }}</h6>
                        <span class="card-value text-success">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        <p class="card-code mb-0">Kode: {{ $kategori_list[$kategori] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<!-- Pengeluaran Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-money-dollar-circle-line me-2"></i>Data Pengeluaran
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor data pengeluaran</p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <!-- Export Buttons -->
                <div class="dropdown">
                    <button class="btn btn-outline-success btn-export dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ri-file-excel-2-line me-1"></i> Export Excel
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportMonthlyModal">
                                <i class="ri-calendar-line me-2"></i>Export Bulanan (Per Hari)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportDateRangeModal">
                                <i class="ri-calendar-2-line me-2"></i>Export Rentang Tanggal
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('keluar.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-add-line"></i>
                    Tambah Pengeluaran
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-expense table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-eye-line me-1"></i>Detail</th>
                        <th><i class="ri-barcode-line me-1"></i>Kode</th>
                        <th><i class="ri-folder-line me-1"></i>Kategori</th>
                        <th><i class="ri-money-dollar-circle-line me-1"></i>Jumlah</th>
                        <th><i class="ri-file-text-line me-1"></i>Keterangan</th>
                        <th><i class="ri-calendar-line me-1"></i>Tanggal</th>
                        <th><i class="ri-time-line me-1"></i>Jam</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $e)
                    <tr
                        data-kode="{{ $e->kode }}"
                        data-kategori="{{ $e->kategori }}"
                        data-jumlah="{{ $e->jumlah }}"
                        data-keterangan="{{ $e->keterangan ?? '-' }}"
                        data-tanggal="{{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('d M Y') }}"
                        data-jam="{{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('H:i') }}"
                    >
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        </td>
                        
                        <td>
                            <span class="badge bg-label-dark">{{ $e->kode }}</span>
                        </td>
                        
                        <td>
                            <span class="fw-semibold">{{ $e->kategori }}</span>
                        </td>
                        
                        <td>
                            <span class="badge bg-label-success">Rp {{ number_format($e->jumlah, 0, ',', '.') }}</span>
                        </td>
                        
                        <td>{{ $e->keterangan ?? '-' }}</td>
                        
                        <td>
                            <i class="ri-calendar-line me-1 text-muted"></i>
                            {{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('d M Y') }}
                        </td>
                        
                        <td>
                            <i class="ri-time-line me-1 text-muted"></i>
                            {{ \Carbon\Carbon::parse($e->tanggal_keluar)->format('H:i') }}
                        </td>
                        
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('keluar.edit', $e->id) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                <form action="{{ route('keluar.destroy', $e->id) }}" method="POST" class="d-inline">
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
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail Pengeluaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Content will be inserted via JavaScript -->
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Export Monthly Modal -->
<div class="modal fade" id="exportMonthlyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-file-excel-2-line me-2"></i>Export Laporan Bulanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="exportMonthlyForm">
                <div class="modal-body">
                    <p class="text-muted mb-4">
                        Export laporan pengeluaran per bulan. Laporan akan menampilkan total pengeluaran per hari untuk setiap kategori, beserta akumulasi (sum) dari tanggal 1 sampai akhir bulan.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select class="form-select" id="export_month" name="month" required>
                                <option value="">Pilih Bulan</option>
                                @php
                                    $bulanList = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
                                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach($bulanList as $num => $nama)
                                    <option value="{{ $num }}" {{ \Carbon\Carbon::now()->month == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun</label>
                            <select class="form-select" id="export_year" name="year" required>
                                <option value="">Pilih Tahun</option>
                                @for($y = \Carbon\Carbon::now()->year; $y >= \Carbon\Carbon::now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ \Carbon\Carbon::now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-download-2-line me-1"></i>Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Date Range Modal -->
<div class="modal fade" id="exportDateRangeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-file-excel-2-line me-2"></i>Export Rentang Tanggal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="exportDateRangeForm">
                <div class="modal-body">
                    <p class="text-muted mb-4">
                        Export detail pengeluaran untuk rentang tanggal tertentu. Laporan akan menampilkan setiap transaksi pengeluaran secara detail.
                    </p>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Awal</label>
                            <input type="text" class="form-control" id="export_start_date" name="start_date" placeholder="Pilih tanggal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="text" class="form-control" id="export_end_date" name="end_date" placeholder="Pilih tanggal" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-download-2-line me-1"></i>Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
