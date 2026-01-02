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
  .badge.bg-label-info {
    background: rgba(3, 195, 236, 0.12) !important;
    color: #03c3ec !important;
    font-weight: 600;
  }
  .empty-state {
    padding: 4rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 12px;
    border: 2px dashed #e8e8e8;
  }
  .empty-state i {
    font-size: 4rem;
    color: #a8afc7;
    margin-bottom: 1rem;
  }
  .empty-state p {
    color: #6c757d;
    font-size: 1rem;
  }
  .file-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
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