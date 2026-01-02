@extends('layouts/layoutMaster')

@section('title', 'Push Notification')

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
  }
  .btn-broadcast {
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-broadcast:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  }
  .btn-broadcast i {
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
    transform: scale(1.01);
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

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-bill-line me-2"></i>Daftar Tagihan Belum Bayar ya
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan kirim notifikasi tagihan ke pelanggan</p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0">
                
                <button id="send-broadcast-push" class="btn btn-success btn-broadcast">
                    <i class="ri-notification-3-fill"></i>
                    Kirim Notifikasi ke Semua
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-rekenings table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama Pelanggan</th>
                        <th><i class="ri-shopping-bag-line me-1"></i>Paket</th>
                        <th><i class="ri-checkbox-circle-line me-1"></i>Status</th>
                        <th><i class="ri-calendar-line me-1"></i>Tanggal Mulai</th>
                        <th><i class="ri-calendar-check-line me-1"></i>Tanggal Berakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tagihans as $tagihan)
                    <tr id="row-{{ $tagihan['id'] }}">
                        <td class="fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold">{{ $tagihan['nama_lengkap'] }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $tagihan['paket']['nama_paket'] }}</span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($tagihan['status_pembayaran']) {
                                    'Lunas' => 'bg-success',
                                    'Belum Bayar' => 'bg-warning',
                                    'Jatuh Tempo' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge badge-status {{ $statusClass }}">
                                {{ $tagihan['status_pembayaran'] }}
                            </span>
                        </td>
                        <td>{{ $tagihan['tanggal_mulai'] ?? '-' }}</td>
                        <td>{{ $tagihan['tanggal_berakhir'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi DataTable
  const dtTagihan = $('.datatables-rekenings').DataTable({
        paging: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50,400], [5, 10, 25, 50,400]],
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        // ? TAMBAHKAN INI - Huruf 'l' untuk length menu
        dom: 'lfrtip',
        columnDefs: [
            { orderable: false, targets: [] }
        ],
        language: {
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            search: "Cari:",
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            }
        }
    });
    // Fungsi helper untuk menampilkan loading
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').css('display', 'none');
    }

    // ========================================
    // TOMBOL BROADCAST PUSH NOTIFICATION - 2 BUTTON KONFIRMASI
    // ========================================
    $('#send-broadcast-push').on('click', function() {
        const allTagihanIds = [];
        $('.datatables-rekenings tbody tr').each(function() {
            const rowId = $(this).attr('id');
            if(rowId) {
                allTagihanIds.push(rowId.replace('row-', ''));
            }
        });

        if(allTagihanIds.length === 0){
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Data',
                text: 'Tidak ada tagihan yang bisa dikirim.',
                showCancelButton: false,
                showDenyButton: false,
                showCloseButton: false,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-warning'
                },
                buttonsStyling: false
            });
            return;
        }

        // MODAL KONFIRMASI - 2 BUTTON: YA & BATAL
        Swal.fire({
            title: 'Apakah Anda yakin?',
            html: `
                <p>Kirim notifikasi tagihan ke <strong>${allTagihanIds.length}</strong> pelanggan?</p>
                <p class="text-muted small mt-2">
                    <i class="ri-information-line"></i> Notifikasi akan dikirim ke semua pelanggan yang belum bayar
                </p>
            `,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: '<i class="ri-checkbox-circle-line me-2"></i>Ya, Kirim!',
            cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            reverseButtons: false,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $('#send-broadcast-push');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');
                showLoading();

                fetch("{{ route('tagihan.push') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ tagihan_ids: allTagihanIds })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);
                    
                    console.log('Response data:', data);
                    
                    if(data.success && data.sent > 0){
                        // ? MODAL SUCCESS - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Terkirim!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2"><strong class="text-success fs-4">${data.sent}</strong> notifikasi berhasil dikirim</p>
                                    ${data.ignored > 0 ? `<p class="text-muted small mb-0"><i class="ri-information-line"></i> ${data.ignored} pelanggan diabaikan (SID kosong)</p>` : ''}
                                </div>
                            `,
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        // ? MODAL WARNING - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Yang Terkirim',
                            text: data.message || 'Tidak ada notifikasi yang berhasil dikirim. Pastikan pelanggan memiliki SID yang valid.',
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(err => {
                    console.error('Error detail:', err);
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);
                    // ? MODAL ERROR - HANYA 1 BUTTON
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengirim notifikasi. Silakan coba lagi atau hubungi administrator.',
                        showCancelButton: false,
                        showDenyButton: false,
                        showCloseButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
    });

    // ========================================
    // TOMBOL BROADCAST INFO/IKLAN - 2 BUTTON KONFIRMASI
    // ========================================
    $('#send-broadcast-info').on('click', function() {
        // MODAL INPUT - 2 BUTTON: KIRIM & BATAL
        Swal.fire({
            title: '<i class="ri-megaphone-line me-2"></i>Kirim Info/Iklan',
            html: `
                <div class="text-start">
                    <label for="swal-input-message" class="form-label fw-bold">Pesan yang akan dikirim:</label>
                    <textarea 
                        id="swal-input-message" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Contoh: Promo spesial bulan ini! Diskon 50% untuk semua paket internet"
                        maxlength="500"
                    ></textarea>
                    <small class="text-muted d-block mt-2">
                        <i class="ri-information-line"></i> Maksimal 500 karakter
                    </small>
                </div>
            `,
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: '<i class="ri-send-plane-fill me-2"></i>Kirim Sekarang',
            cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            reverseButtons: false,
            allowOutsideClick: false,
            focusConfirm: false,
            customClass: {
                confirmButton: 'btn btn-info me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            preConfirm: () => {
                const message = document.getElementById('swal-input-message').value.trim();
                if (!message) {
                    Swal.showValidationMessage('Pesan tidak boleh kosong!');
                    return false;
                }
                if (message.length < 10) {
                    Swal.showValidationMessage('Pesan minimal 10 karakter!');
                    return false;
                }
                return message;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const message = result.value;
                const btn = $('#send-broadcast-info');
                const originalText = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');
                showLoading();

                fetch("", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);
                    
                    console.log('Response data:', data);
                    
                    if(data.success && data.sent > 0){
                        // ? MODAL SUCCESS - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Terkirim!',
                            html: `
                                <div class="text-center">
                                    <p class="mb-2"><strong class="text-info fs-4">${data.sent}</strong> notifikasi info berhasil dikirim</p>
                                    ${data.ignored > 0 ? `<p class="text-muted small mb-0"><i class="ri-information-line"></i> ${data.ignored} pelanggan diabaikan (SID kosong)</p>` : ''}
                                </div>
                            `,
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-info'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        // ? MODAL WARNING - HANYA 1 BUTTON
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada Yang Terkirim',
                            text: data.message || 'Tidak ada notifikasi yang berhasil dikirim. Pastikan pelanggan memiliki SID yang valid.',
                            showCancelButton: false,
                            showDenyButton: false,
                            showCloseButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-warning'
                            },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(err => {
                    console.error('Error detail:', err);
                    hideLoading();
                    btn.prop('disabled', false).html(originalText);
                    // ? MODAL ERROR - HANYA 1 BUTTON
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal mengirim notifikasi. Silakan coba lagi atau hubungi administrator.',
                        showCancelButton: false,
                        showDenyButton: false,
                        showCloseButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                        buttonsStyling: false
                    });
                });
            }
        });
    });
});
</script>

<style>
/* Custom SweetAlert2 Styling */
.swal2-input, 
.swal2-textarea {
    border: 2px solid #e0e0e0 !important;
    border-radius: 8px !important;
    padding: 12px !important;
    font-size: 14px !important;
}

.swal2-input:focus, 
.swal2-textarea:focus {
    border-color: #17a2b8 !important;
    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25) !important;
}

.swal2-validation-message {
    background: #fef2f2 !important;
    color: #dc3545 !important;
    border: 1px solid #fecaca !important;
    border-radius: 6px !important;
    padding: 10px !important;
    margin-top: 10px !important;
}

/* Spinning icon animation */
.ri-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}
</style>
@endsection
