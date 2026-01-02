@php
use Illuminate\Support\Str;

@endphp

@extends('layouts/layoutMaster')

@section('title', 'Kelola Notifikasi')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])

<style>
  .card-header-custom {
    background: white;
    border-bottom: 1px solid #f0f0f0;
    padding: 1.5rem;
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
    color: #6c757d;
    border: none;
    padding: 16px;
  }
  .table-modern tbody tr {
    transition: all 0.2s;
    border-bottom: 1px solid #f1f1f1;
  }
  .table-modern tbody tr:hover {
    background-color: #f8f9ff;
  }
  .notification-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
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
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
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

    // DataTable
    $('.datatables-notifications').DataTable({
        paging: true,
        pageLength: 10,
        searching: true,
        ordering: true,
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ notifikasi",
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            }
        }
    });

    // Kirim Notifikasi
    $(document).on('click', '.btn-send', function(e) {
        e.preventDefault();
        const notifId = $(this).data('id');
        const title = $(this).data('title');

        Swal.fire({
            title: 'Kirim Notifikasi?',
            html: `<p>Kirim "<strong>${title}</strong>" ke semua pelanggan?</p>`,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: '<i class="ri-send-plane-fill me-2"></i>Ya, Kirim!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                fetch(`/notifications/${notifId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `<p><strong>${data.sent}</strong> notifikasi berhasil dikirim</p>`,
                            showCancelButton: false,
                            showDenyButton: false,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-success' },
                            buttonsStyling: false
                        }).then(() => location.reload());
                    }
                })
                .catch(err => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan!',
                        showCancelButton: false,
                        showDenyButton: false,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-danger' },
                        buttonsStyling: false
                    });
                });
            }
        });
    });

    // Delete Notifikasi
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Notifikasi?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;"></div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-notification-3-line me-2"></i>Kelola Notifikasi
                </h4>
                <p class="mb-0 text-muted small">Buat dan kirim notifikasi ke pelanggan</p>
            </div>
            <a href="{{ route('iklan.create')}}" class="btn btn-primary">
                <i class="ri-add-line me-2"></i>Buat Notifikasi
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="datatables-notifications table table-modern table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Terkirim</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($iklans as $notif)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($notif->image)
                            <img src="{{ asset('storage/' . $notif->image) }}" class="notification-image" alt="Image">
                            @else
                            <div class="notification-image bg-light d-flex align-items-center justify-content-center">
                                <i class="ri-image-line text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $notif->title }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($notif->message, 50) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $notif->type_color }}">
                                <i class="{{ $notif->type_icon }} me-1"></i>
                                {{ ucfirst($notif->type) }}
                            </span>
                        </td>
                        <td>
                            @if($notif->status === 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($notif->status === 'sent')
                                <span class="text-success">{{ $notif->total_sent }} orang</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $notif->created_at->format('d M Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $notif->creator->name ?? 'Admin' }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                @if($notif->status === 'draft')
                                <button class="btn btn-sm btn-success btn-send"
                                        data-id="{{ $notif->id }}"
                                        data-title="{{ $notif->title }}">
                                    <i class="ri-send-plane-fill"></i>
                                </button>
                                <a href="{{ route('iklan.edit', $notif->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i>
                                </a>
                                @endif

                                <form action="{{ route('iklan.destroy', $notif->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">
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
@endsection
