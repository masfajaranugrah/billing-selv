@extends('layouts/layoutMaster')

@section('title', 'Status Pelanggan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
  :root {
    --primary-color: #696cff;
    --success-color: #28c76f;
    --secondary-color: #82868b;
    --border-radius: 12px;
    --transition: all 0.3s ease;
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: var(--transition);
  }

  .card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  }

  .card-header-custom {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .stats-card {
    border-radius: var(--border-radius);
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #f0f0f0;
    transition: var(--transition);
  }

  .stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  }

  .stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
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

  .table-modern tbody tr:not(.empty-state-row):hover {
    background-color: #f8f9ff !important;
    transform: scale(1.001);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .table-modern tbody td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 16px;
  }

  .status-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .badge {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
  }

  .badge.bg-success {
    background: rgba(40, 199, 111, 0.12) !important;
    color: #28c76f !important;
  }

  .badge.bg-secondary {
    background: rgba(130, 134, 139, 0.12) !important;
    color: #82868b !important;
  }

  /* Form & button */
  .form-control,
  .form-select {
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    padding: 0.625rem 1rem;
    transition: var(--transition);
  }

  .form-control:focus,
  .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
  }

  .btn {
    border-radius: 8px;
    padding: 0.625rem 1.25rem;
    font-weight: 600;
    transition: var(--transition);
  }

  .btn-primary {
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, #5a5dc9 0%, #4a4db9 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(105, 108, 255, 0.4);
  }

  .btn-secondary {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    color: #6c757d;
  }

  .btn-secondary:hover {
    background: #e9ecef;
    border-color: #ccc;
  }

  /* Empty state */
  .empty-state-row td {
    background: #fafbfc !important;
    border: none !important;
  }

  .empty-state-content {
    padding: 3rem 1rem;
  }

  table.dataTable tbody tr.empty-state-row,
  table.dataTable tbody tr.empty-state-row:hover {
    background: #fafbfc !important;
  }

  /* Pagination (Laravel) */
  .pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
  }

  .pagination {
    margin: 0;
    gap: 0.5rem;
  }

  .pagination .page-item .page-link {
    border-radius: 50% !important;
    width: 40px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
    color: #6c757d;
    font-weight: 600;
    background-color: #fff;
    margin: 0 2px;
    transition: all 0.3s ease;
  }

  .pagination .page-item .page-link:hover {
    background-color: #f3f2ff;
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(105, 108, 255, 0.2);
  }

  .pagination .page-item.active .page-link {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
  }

  .pagination .page-item.disabled .page-link {
    background-color: #f8f9fa;
    border-color: #e0e0e0;
    color: #adb5bd;
    cursor: not-allowed;
  }

  .pagination-info {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
  }

  /* Loading overlay */
  .loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
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

  /* Scrollbar */
  .table-responsive::-webkit-scrollbar {
    height: 8px;
  }

  .table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  .table-responsive::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
  }

  .table-responsive::-webkit-scrollbar-thumb:hover {
    background: #5a5de8;
  }

  /* Hide DataTables default controls */
  .dataTables_info,
  .dataTables_paginate,
  .dataTables_length {
    display: none !important;
  }

  @media (max-width: 768px) {
    .pagination-wrapper {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }

    .stats-card {
      margin-bottom: 1rem;
    }

    .table-responsive {
      margin-bottom: 1rem;
    }
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
  document.addEventListener("DOMContentLoaded", function () {
    const loadScript = (src) => new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });

    const ensureJquery = () => {
      if (window.jQuery) return Promise.resolve();
      return loadScript('https://code.jquery.com/jquery-3.7.1.min.js');
    };

    const ensureDataTables = () => {
      if (window.jQuery && $.fn.DataTable) return Promise.resolve();
      const css = document.createElement('link');
      css.rel = 'stylesheet';
      css.href = 'https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css';
      document.head.appendChild(css);

      const jsCore = loadScript('https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js');
      const jsBs = loadScript('https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js');
      return Promise.all([jsCore, jsBs]);
    };

    ensureJquery()
      .then(ensureDataTables)
      .then(() => {
        const $table = $('.datatables-status');
        if (!$table.length) return;

        const hasData = $table.find('tbody tr').not(':has(td[colspan])').length > 0;

        if (hasData) {
          try {
            $table.DataTable({
              paging: false,
              lengthChange: false,
              searching: false,
              ordering: true,
              info: false,
              scrollX: true,
              autoWidth: false,
              dom: 'rt',
              language: {
                zeroRecords: "Tidak ada data yang sesuai",
                emptyTable: "Tidak ada data tersedia"
              },
              columnDefs: [
                { orderable: false, targets: [0, 6, 7] },
                { width: '5%', targets: 0 },
                { width: '20%', targets: 1 },
                { width: '12%', targets: 2 },
                { width: '20%', targets: 3 },
                { width: '10%', targets: 4 },
                { width: '12%', targets: 5 },
                { width: '10%', targets: 6 },
                { width: '11%', targets: 7 }
              ]
            });
          } catch (error) {
            console.warn('DataTables initialization error:', error);
          }
        }
      })
      .catch((error) => {
        console.warn('DataTables gagal dimuat:', error);
      });

    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
      statusFilter.addEventListener('change', function () {
        this.form.submit();
      });
    }

    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
      filterForm.addEventListener('submit', function () {
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) overlay.style.display = 'flex';
      });
    }
  });
</script>
@endsection

@section('content')
<div class="loading-overlay">
  <div class="spinner-border spinner-border-custom text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<div class="container-fluid px-4 py-4">

  {{-- Statistik --}}
  <div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-primary me-3">
            <i class="ri-group-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Total Pelanggan</p>
            <h2 class="mb-0 fw-bold">{{ number_format($statistics['total']) }}</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-success me-3">
            <i class="ri-checkbox-circle-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Status Active</p>
            <h2 class="mb-0 fw-bold text-success">{{ number_format($statistics['active']) }}</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-secondary me-3">
            <i class="ri-close-circle-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Status Inactive</p>
            <h2 class="mb-0 fw-bold text-secondary">{{ number_format($statistics['inactive']) }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

 

  {{-- Filter & Search --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('pelanggan.status.active') }}" id="filterForm">
        <div class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-search-line me-1"></i>Pencarian
            </label>
            <input
              type="text"
              name="search"
              class="form-control"
              placeholder="Cari nama, No. ID, WhatsApp, alamat, paket..."
              value="{{ request('search') }}">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-filter-3-line me-1"></i>Filter Status
            </label>
            <select
              name="status_filter"
              id="statusFilter"
              class="form-select">
              <option value="">Semua Status</option>
              <option value="Active" {{ request('status_filter') == 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status_filter') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="col-md-4">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="ri-search-line me-1"></i>Cari
              </button>

              @if(request('status_filter') || request('search'))
                <a href="{{ route('pelanggan.status.active') }}" class="btn btn-secondary">
                  <i class="ri-refresh-line me-1"></i>Reset
                </a>
              @endif
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Data Table --}}
<div class="card border-0 shadow-sm">
  <div class="card-header-custom">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h4 class="mb-1 fw-bold">
          <i class="ri-user-follow-line me-2"></i>Status Pelanggan
        </h4>
        <p class="mb-0 opacity-75 small">Monitor status login dan aktivitas pelanggan secara real-time.</p>
      </div>

      <div class="d-flex align-items-center gap-2">
        {{-- Badge total data --}}
        @if($pelanggan->total() > 0)
          <span class="badge bg-label-primary" style="padding: 10px 20px; font-size: 0.9rem;">
            <i class="ri-database-2-line me-1"></i>
            {{ $pelanggan->total() }} Data Total
          </span>
        @endif

        {{-- Export Excel di kanan header --}}
        <a href="{{ url('/pelanggan/export') }}" class="btn btn-success">
          <i class="ri-file-excel-2-line me-1"></i> Export Excel
        </a>
      </div>
    </div>
  </div>
    <div class="card-body p-0">
      <div class="table-responsive p-3">
        <table class="datatables-status table table-modern table-hover nowrap" style="width: 100%;">
          <thead>
            <tr>
              <th><i class="ri-hashtag me-1"></i>No</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>No. WhatsApp</th>
              <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-box-3-line me-1"></i>Paket</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-time-line me-1"></i>Login Terakhir</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pelanggan as $index => $item)
              @php
                $isActive    = optional($item->loginStatus)->is_active;
                $loggedInAt  = optional($item->loginStatus)->logged_in_at;
                $no          = ($pelanggan->currentPage() - 1) * $pelanggan->perPage() + $index + 1;
              @endphp
              <tr>
                <td class="fw-bold text-center">{{ $no }}</td>

                <td>
                  <div class="d-flex align-items-center">
                    <div class="status-icon bg-label-primary me-2">
                      <i class="ri-user-line" style="font-size: 1.25rem;"></i>
                    </div>
                    <span class="fw-semibold">{{ $item->nama_lengkap }}</span>
                  </div>
                </td>

                <td>
                  <a
                    href="https://wa.me/{{ $item->no_whatsapp }}"
                    target="_blank"
                    class="text-decoration-none">
                    <code style="background: #f8f9fa; padding: 6px 12px; border-radius: 6px; font-size: 0.875rem; font-weight: 600; color: #25D366;">
                      <i class="ri-whatsapp-line me-1"></i>{{ $item->no_whatsapp }}
                    </code>
                  </a>
                </td>

                <td>
                  <div style="min-width: 200px; max-width: 250px;">
                    <div class="text-truncate">{{ $item->alamat_jalan ?? '-' }}</div>
                    <small class="text-muted">
                      RT {{ $item->rt ?? '-' }}/RW {{ $item->rw ?? '-' }}, {{ $item->kecamatan ?? '-' }}
                    </small>
                  </div>
                </td>

                <td>
                  <span class="badge bg-label-dark" style="padding: 8px 12px; font-size: 0.85rem; font-family: monospace;">
                    {{ $item->nomer_id ?? '-' }}
                  </span>
                </td>

                <td>
                  <span class="badge bg-label-info">
                    <i class="ri-box-line me-1"></i>{{ optional($item->paket)->nama_paket ?? '-' }}
                  </span>
                </td>

                <td>
                  @if($isActive)
                    <span class="badge bg-success">
                      <i class="ri-checkbox-circle-line me-1"></i>Active
                    </span>
                  @else
                    <span class="badge bg-secondary">
                      <i class="ri-close-circle-line me-1"></i>Inactive
                    </span>
                  @endif
                </td>

                <td>
                  @if($loggedInAt)
                    <div>
                      <small class="d-block fw-semibold">
                        {{ $loggedInAt->timezone(config('app.timezone'))->format('d M Y') }}
                      </small>
                      <small class="text-muted">
                        {{ $loggedInAt->timezone(config('app.timezone'))->format('H:i') }} WIB
                      </small>
                    </div>
                  @else
                    <span class="text-muted small">Belum pernah login</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr class="empty-state-row">
                <td colspan="8" class="text-center">
                  <div class="empty-state-content">
                    <div class="mb-3">
                      <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                    </div>

                    @if(request('search') || request('status_filter'))
                      <h5 class="text-muted mb-2">
                        <i class="ri-search-eye-line me-2"></i>Data Tidak Ditemukan
                      </h5>
                      <p class="text-muted mb-3">
                        Tidak ada data yang sesuai dengan pencarian atau filter yang Anda pilih.
                      </p>

                      <div class="mb-3">
                        @if(request('search'))
                          <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                            <i class="ri-search-line me-1"></i>
                            Pencarian: "{{ request('search') }}"
                          </span>
                        @endif

                        @if(request('status_filter'))
                          <span class="badge bg-label-info" style="padding: 8px 16px;">
                            <i class="ri-filter-line me-1"></i>
                            Status: {{ request('status_filter') }}
                          </span>
                        @endif
                      </div>

                      <a href="{{ route('pelanggan.status.active') }}" class="btn btn-primary mt-2">
                        <i class="ri-refresh-line me-1"></i>Reset Filter &amp; Tampilkan Semua Data
                      </a>
                    @else
                      <h5 class="text-muted mb-2">
                        <i class="ri-user-unfollow-line me-2"></i>Belum Ada Data Pelanggan
                      </h5>
                      <p class="text-muted">
                        Saat ini belum ada data pelanggan yang terdaftar dalam sistem.
                      </p>
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($pelanggan->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $pelanggan->firstItem() ?? 0 }}</strong> - <strong>{{ $pelanggan->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $pelanggan->total() }}</strong> data
        </div>
        <div>
          {{ $pelanggan->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>

</div>
@endsection
