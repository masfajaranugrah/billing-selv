@extends('layouts/layoutMaster')

@section('title', 'Backup Database')

@php
use Illuminate\Support\Facades\File;
@endphp

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #18181b;
  --gray-border: #e4e4e7;
}
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
  transition: var(--transition);
}
.card:hover {
  box-shadow: var(--card-hover-shadow);
}
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  color: #18181b;
}
.card-header-custom h4 { color: #18181b !important; }
.card-header-custom p { color: #71717a !important; }
.card-header-custom i { color: #18181b !important; }
.btn-primary, .btn.btn-primary, .btn-add {
  background: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}
.btn-primary:hover, .btn-add:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  transform: translateY(-2px) !important;
}
.btn-add i { margin-right: 8px; }
.btn-danger { background: #18181b !important; color: #fafafa !important; border: 1px solid #18181b !important; }
.btn-danger:hover { background: #27272a !important; }
.btn-secondary { background: transparent !important; border: 1px solid #e4e4e7 !important; color: #18181b !important; }
.btn-secondary:hover { background: #f4f4f5 !important; border-color: #18181b !important; }
.btn-outline-success, .btn-outline-danger {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}
.btn-outline-success:hover, .btn-outline-danger:hover {
  background: #18181b !important;
  color: #fafafa !important;
}
.table-modern { border-radius: 8px; overflow: hidden; }
.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  color: #18181b;
  padding: 1rem;
  border: none;
}
.table-modern tbody tr { border-bottom: 1px solid #e4e4e7; }
.table-modern tbody tr:hover { background-color: #f4f4f5 !important; }
.table-modern tbody td { padding: 1rem; color: #18181b; }
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
.spinner-border-custom { width: 3rem; height: 3rem; border-width: 0.3rem; }
.badge.bg-label-info { background: #18181b !important; color: #fafafa !important; }
.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  background: #fafafa;
  border-radius: 12px;
  border: 2px dashed #e4e4e7;
}
.empty-state i { font-size: 4rem; color: #a1a1aa; margin-bottom: 1rem; }
.empty-state p { color: #71717a; }
.file-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #18181b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fafafa;
  margin-right: 12px;
}
</style>
@endsection

@section('vendor-script')
@vite([
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

    // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');
        const filename = form.data('filename');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus backup database ini? Data tidak dapat dikembalikan!',
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
                        text: 'Backup database berhasil dihapus.',
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
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Backup Database Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header-custom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="ri-database-2-line me-2"></i>Backup Database
                            </h4>
                            <p class="mb-0 opacity-75 small">Kelola backup database sistem</p>
                        </div>
                        <div class="d-flex mt-3 mt-md-0">
                            <a href="{{ route('backup.create') }}" class="btn btn-primary btn-add">
                                <i class="ri-database-2-line"></i>
                                Buat Backup Baru
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success mx-3 mt-3 mb-0">
                            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mx-3 mt-3 mb-0">
                            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(count($files) > 0)
                        <div class="table-responsive p-3">
                            <table class="table table-modern table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="ri-hashtag me-1"></i>No</th>
                                        <th><i class="ri-file-line me-1"></i>Nama File</th>
                                        <th><i class="ri-file-info-line me-1"></i>Ukuran</th>
                                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $index => $file)
                                        @php
                                            $size = round($file->getSize() / 1024 / 1024, 2) . ' MB';
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="file-icon">
                                                        <i class="ri-file-zip-line" style="font-size: 1.25rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold d-block">{{ $file->getFilename() }}</span>
                                                        <small class="text-muted">
                                                            <i class="ri-time-line me-1"></i>
                                                            {{ date('d M Y H:i', $file->getMTime()) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td>
                                                <span class="badge bg-label-info" style="padding: 8px 16px; font-size: 0.8rem;">
                                                    <i class="ri-hard-drive-line me-1"></i>{{ $size }}
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('backup.download', $file->getFilename()) }}" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Download">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>

                                                    <form action="{{ route('backup.delete', $file->getFilename()) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          data-filename="{{ $file->getFilename() }}">
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
                    @else
                        <div class="p-4">
                            <div class="empty-state">
                                <i class="ri-database-2-line d-block"></i>
                                <p class="mb-0">Belum ada backup database</p>
                                <small class="text-muted">Klik tombol "Buat Backup Baru" untuk membuat backup pertama</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection