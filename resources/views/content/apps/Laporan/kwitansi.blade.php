@extends('layouts/layoutMaster')

@section('title', 'Laporan Kwitansi')

@section('vendor-style')
<style>
/* ========================================= */
/* MODERN CLEAN STYLES - SHADCN UI */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
}

/* Card Design */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

.card-header {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  color: #18181b;
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.card-header h5 {
  font-weight: 700;
  color: #18181b;
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* ========================================= */
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

/* Primary Button - Black */
.btn.btn-primary,
.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-primary:hover,
.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Success Button - Black */
.btn.btn-success,
.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-success:hover,
.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Buttons */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn-outline-primary,
.btn-outline-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES */
/* ========================================= */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* ========================================= */
/* TABLE STYLES */
/* ========================================= */
.table-modern {
  border-radius: 8px;
  overflow: hidden;
}

.table-modern thead th,
.table-light th {
  background: #f8fafc !important;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #0f172a;
  border: none;
  padding: 1rem;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e5e7eb;
}

.table-modern tbody tr:hover {
  background-color: #f1f5f9 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
}

/* ========================================= */
/* FILTER SECTION */
/* ========================================= */
.card-body.border-top {
  background: #fafafa;
  border-top: 1px solid #e4e4e7 !important;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.12);
}

/* ========================================= */
/* PAGINATION STYLES - BLACK ACTIVE */
/* ========================================= */
.pagination {
  margin: 0;
  gap: 0.5rem;
  justify-content: flex-end;
}

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
  box-shadow: none;
}

.pagination .page-item.disabled .page-link {
  background-color: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
}

/* Text Colors */
.text-primary {
  color: #18181b !important;
}

.text-muted {
  color: #71717a !important;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.avatar-initial.bg-label-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* Kwitansi Link */
a.text-decoration-none {
  color: #18181b;
  font-weight: 500;
  transition: all 0.2s ease;
}

a.text-decoration-none:hover {
  color: #27272a;
}

a.text-decoration-none i {
  color: #18181b !important;
}
</style>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const exportBtn = document.getElementById('btnExportExcel');

    exportBtn.addEventListener('click', function(e) {
      e.preventDefault();
      const formData = new FormData(filterForm);
      const params = new URLSearchParams();
      for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
      }
      let url = "{{ route('laporan.kwitansi.export') }}";
      const qs = params.toString();
      if (qs) url += '?' + qs;
      window.location.href = url;
    });
  });
</script>
@endsection

@section('content')

<div class="card mt-4">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
    <h5 class="mb-2 mb-md-0">Daftar Kwitansi</h5>
    <button id="btnExportExcel" type="button" class="btn btn-success">
      <i class="bi bi-file-earmark-excel"></i> Export Excel
    </button>
  </div>

  <div class="card-body border-top">
    <form id="filterForm" class="mb-3" method="GET" action="{{ route('laporan.kwitansi.index') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="status">Status Pembayaran</label>
          <select name="status" id="status" class="form-select">
            <option value="">-- Semua Status --</option>
            <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="belum bayar" {{ request('status') === 'belum bayar' ? 'selected' : '' }}>Belum Bayar</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="kabupaten">Kabupaten</label>
          <select name="kabupaten" id="kabupaten" class="form-select">
            <option value="">-- Semua Kabupaten --</option>
            @foreach($kabupatens as $kab)
              <option value="{{ $kab }}" {{ request('kabupaten') === $kab ? 'selected' : '' }}>{{ $kab }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="kecamatan">Kecamatan</label>
          <select name="kecamatan" id="kecamatan" class="form-select">
            <option value="">-- Semua Kecamatan --</option>
            @foreach($kecamatans as $kec)
              <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="submit" class="btn btn-primary"><i class="ri-filter-3-line me-1"></i>Terapkan Filter</button>
        <a href="{{ route('laporan.kwitansi.index') }}" class="btn btn-outline-secondary"><i class="ri-refresh-line me-1"></i>Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-modern table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width:60px;">No</th>
            <th><i class="ri-user-3-line me-1"></i>Nama Lengkap</th>
            <th><i class="ri-map-pin-line me-1"></i>Alamat</th>
            <th><i class="ri-rocket-line me-1"></i>Nama Paket</th>
            <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
            <th><i class="ri-speed-line me-1"></i>Kecepatan</th>
            <th><i class="ri-shield-check-line me-1"></i>Status</th>
            <th>Kabupaten</th>
            <th>Kecamatan</th>
            <th><i class="ri-file-text-line me-1"></i>Kwitansi</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tagihans as $tagihan)
          <tr>
            <td class="text-center fw-semibold">{{ ($tagihans->firstItem() ?? 0) + $loop->index }}</td>
            <td>
              <div class="d-flex align-items-center">
                
                <span class="fw-semibold">{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</span>
              </div>
            </td>
            <td>
              <div>
                {{ \Illuminate\Support\Str::limit($tagihan->pelanggan->alamat_jalan ?? '-', 35) }}
                <br>
                <small class="text-muted">
                  <i class="ri-map-pin-2-line"></i>
                  {{ $tagihan->pelanggan->kecamatan ?? '-' }}, {{ $tagihan->pelanggan->kabupaten ?? '-' }}
                </small>
              </div>
            </td>
            <td><span class="badge bg-label-info">{{ $tagihan->paket->nama_paket ?? '-' }}</span></td>
            <td><span class="fw-bold text-primary">Rp {{ number_format($tagihan->harga, 0, ',', '.') }}</span></td>
            <td><span class="badge bg-label-dark"><i class="ri-speed-line me-1"></i>{{ $tagihan->paket->kecepatan ?? '-' }}</span></td>
            <td>
              @php
                $statusClass = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'badge bg-success',
                    'belum bayar' => 'badge bg-danger',
                    default => 'badge bg-secondary',
                };
                $statusIcon = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'ri-checkbox-circle-line',
                    'belum bayar' => 'ri-close-circle-line',
                    default => 'ri-information-line',
                };
              @endphp
              <span class="{{ $statusClass }}">
                <i class="{{ $statusIcon }} me-1"></i>{{ ucfirst($tagihan->status_pembayaran ?? '-') }}
              </span>
            </td>
            <td>{{ $tagihan->pelanggan->kabupaten ?? '-' }}</td>
            <td>{{ $tagihan->pelanggan->kecamatan ?? '-' }}</td>
            <td>
              @if(!empty($tagihan->kwitansi))
                <a href="{{ asset('storage/'.$tagihan->kwitansi) }}" target="_blank" class="text-decoration-none">
                  <i class="bi bi-file-earmark-pdf-fill text-danger"></i> Lihat
                </a>
              @else
                -
              @endif
            </td>
            <td>{{ $tagihan->catatan ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center text-muted py-4">Tidak ada data kwitansi.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
      <div class="text-muted small">
        Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }} dari {{ $tagihans->total() }} kwitansi
      </div>
      <div>
        {{ $tagihans->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection
