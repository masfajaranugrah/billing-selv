@extends('layouts/layoutMaster')

@section('title', 'User List - Pages')

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
  .icon-wrapper {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 24px;
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
  .dropdown-menu {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    padding: 0.5rem;
  }
  .dropdown-item {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    transition: all 0.2s;
    font-size: 0.875rem;
  }
  .dropdown-item:hover {
    background: #f8f9fa;
    transform: translateX(4px);
  }
  .dropdown-item i {
    width: 20px;
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
    min-width: 100px;
  }
  .modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
  }
  .btn-close-white {
    filter: brightness(0) invert(1);
  }
  .badge.bg-label-primary {
    background: rgba(105, 108, 255, 0.12) !important;
    color: #696cff !important;
    font-weight: 600;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    margin-right: 12px;
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
    // Helper function untuk loading overlay
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable dengan styling modern
    const dtUserTable = $('.datatables-users').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
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
            searchPlaceholder: "Cari user...",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ user",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event Detail User
    $(document).on('click', '.btn-detail', function(e) {
        e.stopPropagation();
        const tr = $(this).closest('tr');
        const row = dtUserTable.row(tr).data();
        if (!row) return;

        const html = `
            <div class="row g-3">
                <div class="col-12 text-center mb-3">
                    <div class="user-avatar mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        ${row[1].charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="col-12">
                    <p><strong>Nama:</strong> ${row[1]}</p>
                </div>
                <div class="col-12">
                    <p><strong>Email:</strong> ${row[2]}</p>
                </div>
                <div class="col-12">
                    <p><strong>Role:</strong> <span class="badge bg-label-primary">${row[3]}</span></p>
                </div>
            </div>
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
        text: 'Yakin ingin menghapus data karyawan ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: false,  // Pastikan deny button tidak muncul
        confirmButtonText: '<i class="ri-delete-bin-line me-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
        confirmButtonColor: '#f5365c',
        cancelButtonColor: '#8898aa',
        reverseButtons: true,  // Cancel di kiri, Confirm di kanan
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $(form).find('.btn-delete');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');
            showLoading();
            
            setTimeout(() => {
                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    text: 'Data karyawan berhasil dihapus dari sistem.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false
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
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Users List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-group-line me-2"></i>Data Users
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor data pengguna sistem</p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line"></i>
                    Tambah User Baru
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
 
                        <th><i class="ri-user-3-line me-1"></i>Nama</th>
                        <th><i class="ri-mail-line me-1"></i>Email</th>
                        <th><i class="ri-shield-user-line me-1"></i>Role</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                    
                        
                        <td>
                            <div class="d-flex align-items-center">
                                                          <div>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <td>
                            <i class="ri-mail-line me-1 text-muted"></i>
                            {{ $user->email }}
                        </td>
                        
                        <td>
                            <span class="badge bg-label-primary">
                                <i class="ri-shield-user-line me-1"></i>{{ ucfirst($user->role) }}
                            </span>
                        </td>
                        
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit User">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus User">
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
                    <i class="ri-information-line me-2"></i>Detail User
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
