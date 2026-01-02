@extends('layouts/layoutMaster')

@section('title', 'Daftar Paket')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ambil semua tombol delete
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
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
});
</script>
@endsection

@section('content')
{{-- Header dengan Tombol Add Paket --}}
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1 fw-bold text-dark">Daftar Paket Internet</h4>
        <p class="text-muted mb-0 small">Kelola semua paket layanan internet Anda</p>
      </div>
      <a href="{{route('paket.add')}}" class="btn btn-primary px-4 py-2 shadow-sm hover-lift">
        <i class="ri-add-line ri-18px me-1"></i>
        <span class="fw-semibold">Tambah Paket</span>
      </a>
    </div>
  </div>
</div>

<div class="row g-4">
  @if($pakets->isEmpty())
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <div class="avatar avatar-xl mb-3 mx-auto">
            <div class="avatar-initial bg-label-primary rounded-circle">
              <i class="ri-inbox-line ri-36px"></i>
            </div>
          </div>
          <h5 class="mb-2">Belum Ada Paket</h5>
          <p class="text-muted mb-4">Mulai dengan menambahkan paket internet pertama Anda</p>
      <a href="{{route('paket.add')}}" class="btn btn-primary">
    <i class="ri-add-line me-1"></i> Tambah Paket Baru
</a>
        </div>
      </div>
    </div>
  @else
    @foreach($pakets as $paket)
      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
        <div class="card h-100 border-0 shadow-sm hover-card position-relative overflow-hidden">
          {{-- Accent Border Top --}}
          <div class="card-accent"></div>
          
          <div class="card-body p-4 d-flex flex-column">
            {{-- Header Card --}}
            <div class="d-flex align-items-start mb-4">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-lg">
                  <div class="avatar-initial bg-gradient-primary rounded-3 shadow-sm">
                    <i class="ri-wifi-line ri-24px"></i>
                  </div>
                </div>
              </div>
              <div class="flex-grow-1 overflow-hidden">
                <h5 class="card-title mb-2 fw-bold text-truncate">{{ $paket->nama_paket }}</h5>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge bg-success text-white px-3 py-2 fw-semibold rounded-pill shadow-sm">
                    Rp {{ number_format($paket->harga, 0, ',', '.') }}
                  </span>
                </div>
              </div>
            </div>

            {{-- Info Section --}}
            <div class="mb-4 pb-3 border-bottom">
              <div class="d-flex align-items-center text-muted">
                <i class="ri-speed-line ri-18px me-2 text-primary"></i>
                <span class="small">Kecepatan:</span>
                <strong class="ms-auto text-dark">{{ $paket->kecepatan ?? '-' }} Mbps</strong>
              </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 mt-auto">
              <a href="{{ route('paket.edit', $paket->id) }}" 
                 class="btn btn-warning btn-sm flex-fill d-flex align-items-center justify-content-center gap-1 py-2 shadow-sm hover-lift">
                <i class="ri-edit-line ri-16px"></i>
                <span class="fw-medium">Edit</span>
              </a>

              <form action="{{ route('paket.destroy', $paket->id) }}" method="POST" class="flex-fill">
                @csrf
                @method('DELETE')
                <button type="button" 
                        class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1 py-2 shadow-sm hover-lift btn-delete">
                  <i class="ri-delete-bin-line ri-16px"></i>
                  <span class="fw-medium">Hapus</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @endif
</div>

<style>
/* Card Hover Effects */
.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
}

.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
}

/* Card Accent Border */
.card-accent {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
  border-radius: 12px 12px 0 0;
}

/* Avatar Gradient */
.avatar-initial.bg-gradient-primary {
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Button Hover Effects */
.hover-lift {
  transition: all 0.2s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Warning Button Styling */
.btn-warning {
  background: #ffc107;
  border: none;
  color: #000;
}

.btn-warning:hover {
  background: #ffb300;
  color: #000;
}

/* Danger Button Styling */
.btn-danger {
  background: #dc3545;
  border: none;
}

.btn-danger:hover {
  background: #c82333;
}

/* Badge Styling */
.badge.bg-success {
  background: #28a745 !important;
  font-size: 0.875rem;
  letter-spacing: 0.3px;
}

/* Card Title */
.card-title {
  font-size: 1.125rem;
  line-height: 1.4;
  color: #2c3e50;
}

/* Empty State */
.avatar-xl {
  width: 80px;
  height: 80px;
}

.bg-label-primary {
  background: rgba(106, 17, 203, 0.1) !important;
  color: #6a11cb !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .hover-card:hover {
    transform: translateY(-4px);
  }
}

/* Card Body Spacing */
.card-body {
  border-radius: 12px;
}

/* Icon Sizing */
.ri-24px {
  font-size: 24px;
}

.ri-18px {
  font-size: 18px;
}

.ri-16px {
  font-size: 16px;
}

.ri-36px {
  font-size: 36px;
}
</style>
@endsection
