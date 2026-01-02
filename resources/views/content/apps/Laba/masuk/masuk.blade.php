@extends('layouts/layoutMaster')

@section('title', 'Data Laba Masuk')

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
  .btn-add {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  }
  .btn-add i {
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
    background: linear-gradient(135deg, #666cff 0%, #5a5dc9 100%);
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
  .income-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #666cff 0%, #5a5dc9 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 16px rgba(102, 108, 255, 0.4);
    border: 4px solid white;
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
    border-color: #666cff;
  }
  .detail-section h6 {
    color: #666cff;
    font-weight: 700;
    margin-bottom: 1.25rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #666cff;
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
  .income-header-info {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f5f5ff 0%, #ffffff 100%);
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
  }
  .income-amount {
    font-size: 1.75rem;
    font-weight: 700;
    color: #666cff;
    margin-bottom: 0.5rem;
  }
  .income-category {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background: linear-gradient(135deg, #666cff 0%, #5a5dc9 100%);
    color: white;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px rgba(102, 108, 255, 0.3);
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable
    const dtIncomeTable = $('.datatables-income').DataTable({
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
            searchPlaceholder: "Cari laba masuk...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ pemasukan",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event Detail - gunakan event delegation
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const tr = $(this).closest('tr');
        const kode = tr.data('kode') || '-';
        const kategori = tr.data('kategori') || '-';
        const jumlah = tr.data('jumlah') || 0;
        const keterangan = tr.data('keterangan') || '-';
        const tanggalMasuk = tr.data('tanggal-masuk') || '-';
        const jamMasuk = tr.data('jam-masuk') || '-';

        const html = `
            <div class="income-header-info">
                <div class="income-icon mx-auto">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
                <div class="income-amount">Rp ${parseInt(jumlah).toLocaleString('id-ID')}</div>
                <div class="income-category">
                    <i class="ri-bookmark-line me-2"></i>${kategori}
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-information-line"></i>Informasi Pemasukan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-barcode-line"></i>Kode Transaksi
                    </span>
                    <span class="detail-value"><strong>${kode}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-bookmark-3-line"></i>Kategori
                    </span>
                    <span class="detail-value">
                        <span class="badge bg-label-primary">${kategori}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-money-dollar-box-line"></i>Jumlah Laba
                    </span>
                    <span class="detail-value">
                        <strong style="color: #666cff; font-size: 1.1rem;">Rp ${parseInt(jumlah).toLocaleString('id-ID')}</strong>
                    </span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calendar-event-line"></i>Waktu Pemasukan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-check-line"></i>Tanggal Masuk
                    </span>
                    <span class="detail-value">${tanggalMasuk}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Jam Masuk
                    </span>
                    <span class="detail-value">${jamMasuk}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-file-text-line"></i>Keterangan</h6>
                <div class="detail-item">
                    <span class="detail-value">${keterangan}</span>
                </div>
            </div>
        `;
        
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // Event DELETE - gunakan event delegation
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const form = $(this).closest('form');

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Konfirmasi Penghapusan',
        text: 'Yakin ingin menghapus data laba masuk ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Ya, Hapus!',
        cancelButtonText: '<i class="ri-close-line me-1"></i>Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            
            setTimeout(() => {
                hideLoading();
                swalWithBootstrapButtons.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data laba masuk berhasil dihapus.',
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
                    <i class="ri-money-dollar-circle-line me-2"></i>Data Laba Masuk
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor pemasukan laba perusahaan</p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0">
                <a href="{{ route('income.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-add-circle-line"></i>
                    Tambah Laba Masuk
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-income table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-eye-line me-1"></i>Detail</th>
                        <th><i class="ri-barcode-line me-1"></i>Kode</th>
                        <th><i class="ri-bookmark-line me-1"></i>Kategori</th>
                        <th><i class="ri-money-dollar-box-line me-1"></i>Jumlah</th>
                        <th><i class="ri-file-text-line me-1"></i>Keterangan</th>
                        <th><i class="ri-calendar-line me-1"></i>Tanggal Masuk</th>
                        <th><i class="ri-time-line me-1"></i>Jam Masuk</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $i)
                    <tr
                        data-kode="{{ $i->kode }}"
                        data-kategori="{{ $i->kategori }}"
                        data-jumlah="{{ $i->jumlah }}"
                        data-keterangan="{{ $i->keterangan ?? '-' }}"
                        data-tanggal-masuk="{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('d M Y') }}"
                        data-jam-masuk="{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('H:i') }}"
                    >
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        </td>
                        
                        <td>
                            <span class="badge bg-label-dark">{{ $i->kode }}</span>
                        </td>
                        
                        <td>
                            <span class="badge bg-label-primary">{{ $i->kategori }}</span>
                        </td>
                        
                        <td>
                            <strong style="color: #666cff;">Rp {{ number_format($i->jumlah, 0, ',', '.') }}</strong>
                        </td>
                        
                        <td>{{ \Illuminate\Support\Str::limit($i->keterangan ?? '-', 40) }}</td>
                        
                        <td>{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('d M Y') }}</td>
                        
                        <td>{{ \Carbon\Carbon::parse($i->tanggal_masuk)->format('H:i') }}</td>
                        
                    <td>
    <div class="d-flex gap-2 justify-content-center">
        <!-- Button Edit -->
        <a href="{{ route('income.edit', $i->id) }}" 
           class="btn btn-sm btn-outline-primary"
           title="Edit">
            <i class="ri-edit-2-line"></i>
        </a>

        <!-- Button Delete -->
        <form action="{{ route('income.delete', $i->id) }}" method="POST" class="d-inline">
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

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail Laba Masuk
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
