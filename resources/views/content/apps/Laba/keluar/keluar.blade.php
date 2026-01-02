@extends('layouts/layoutMaster')

@section('title', 'Pengeluaran')

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
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
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
    color: #5a5f7d;
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
  .badge.bg-label-success {
    background: rgba(40, 199, 111, 0.12) !important;
    color: #28c76f !important;
    font-weight: 600;
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
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
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
            <div class="d-flex mt-3 mt-md-0">
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
@endsection
