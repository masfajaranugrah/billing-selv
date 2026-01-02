@extends('layouts/layoutMaster')

@section('title', 'Tagihan Outstanding')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
  
  {{-- ? Statistics Cards --}}
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-primary h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="ri-file-list-3-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div>
              <p class="mb-1 text-muted small">Total Tagihan</p>
              <h3 class="mb-0 fw-bold">{{ number_format($statistics['total']) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-danger h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-danger">
                <i class="ri-error-warning-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div>
              <p class="mb-1 text-muted small">Belum Bayar</p>
              <h3 class="mb-0 fw-bold text-danger">{{ number_format($statistics['belum_bayar']) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-warning h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="ri-time-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div>
              <p class="mb-1 text-muted small">Overdue</p>
              <h3 class="mb-0 fw-bold text-warning">{{ number_format($statistics['overdue']) }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card card-border-shadow-info h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3">
              <span class="avatar-initial rounded bg-label-info">
                <i class="ri-money-dollar-circle-line" style="font-size:24px;"></i>
              </span>
            </div>
            <div>
              <p class="mb-1 text-muted small">Nilai Outstanding</p>
              <h3 class="mb-0 fw-bold text-info">
                Rp {{ number_format($statistics['nilai_outstanding'], 0, ',', '.') }}
              </h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ? Filter Section --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0 fw-bold">
        <i class="ri-filter-3-line me-2"></i>Filter Tagihan Outstanding
      </h5>
    </div>
    <div class="card-body">
      <form method="GET" action="{{ route('tagihan.outstanding') }}">
        <div class="row g-3">
          {{-- Search --}}
          <div class="col-md-4">
            <label class="form-label small">Pencarian</label>
            <input 
              type="text" 
              name="search" 
              class="form-control" 
              placeholder="Cari nama, ID, WA..."
              value="{{ request('search') }}">
          </div>

          {{-- Status Filter --}}
          <div class="col-md-2">
            <label class="form-label small">Status</label>
            <select name="status_filter" class="form-select">
              <option value="semua">Semua Status</option>
              <option value="belum bayar" {{ request('status_filter') == 'belum bayar' ? 'selected' : '' }}>
                Belum Bayar
              </option>
              <option value="proses_verifikasi" {{ request('status_filter') == 'proses_verifikasi' ? 'selected' : '' }}>
                Proses Verifikasi
              </option>
              <option value="lunas" {{ request('status_filter') == 'lunas' ? 'selected' : '' }}>
                Lunas
              </option>
            </select>
          </div>

          {{-- Bulan Filter --}}
          <div class="col-md-2">
            <label class="form-label small">Bulan</label>
            <select name="bulan" class="form-select">
              <option value="">Semua Bulan</option>
              @foreach($bulanList as $num => $nama)
                <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>
                  {{ $nama }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Tahun Filter --}}
          <div class="col-md-2">
            <label class="form-label small">Tahun</label>
            <select name="tahun" class="form-select">
              <option value="">Semua Tahun</option>
              @foreach($tahunList as $tahun)
                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                  {{ $tahun }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Buttons --}}
          <div class="col-md-2">
            <label class="form-label small">&nbsp;</label>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary w-100">
                <i class="ri-search-line me-1"></i>Filter
              </button>
              @if(request()->hasAny(['search', 'status_filter', 'bulan', 'tahun']))
              <a href="{{ route('tagihan.outstanding') }}" class="btn btn-secondary">
                <i class="ri-refresh-line"></i>
              </a>
              @endif
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ? Data Table --}}
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold">
            <i class="ri-file-list-line me-2"></i>Daftar Tagihan Outstanding
          </h5>
          <small class="text-muted">Semua tagihan dari berbagai periode</small>
        </div>
        @if($tagihans->total() > 0)
        <span class="badge bg-label-primary" style="padding: 10px 20px;">
          <i class="ri-database-2-line me-1"></i>{{ $tagihans->total() }} Total
        </span>
        @endif
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>No</th>
            <th>No. ID</th>
            <th>Nama Pelanggan</th>
            <th>Paket</th>
            <th>Harga</th>
            <th>Tanggal Mulai</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tagihans as $index => $item)
            @php
              $no = ($tagihans->currentPage() - 1) * $tagihans->perPage() + $index + 1;
              $isOverdue = $item->status_pembayaran != 'lunas' && 
                           \Carbon\Carbon::parse($item->tanggal_berakhir)->isPast();
            @endphp
            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
              <td>{{ $no }}</td>
              <td>
                <span class="badge bg-label-dark">{{ $item->nomer_id }}</span>
              </td>
              <td>
                <strong>{{ $item->nama_lengkap }}</strong><br>
                <small class="text-muted">{{ $item->no_whatsapp }}</small>
              </td>
              <td>
                <span class="badge bg-label-info">
                  {{ $item->paket['nama_paket'] }}
                </span>
              </td>
              <td>
                <strong>Rp {{ number_format($item->paket['harga'], 0, ',', '.') }}</strong>
              </td>
              <td>
                <small>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</small>
              </td>
              <td>
                <small class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                  {{ \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') }}
                  @if($isOverdue)
                    <br><span class="badge bg-danger">OVERDUE</span>
                  @endif
                </small>
              </td>
              <td>
                @if($item->status_pembayaran == 'lunas')
                  <span class="badge bg-success">Lunas</span>
                @elseif($item->status_pembayaran == 'proses_verifikasi')
                  <span class="badge bg-warning">Proses</span>
                @else
                  <span class="badge bg-danger">Belum Bayar</span>
                @endif
              </td>
              <td>
                <div class="d-flex gap-2">
                  <button 
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#detailModal{{ $item->id }}">
                    <i class="ri-eye-line"></i>
                  </button>
                  
                  @if($item->status_pembayaran != 'lunas')
                  <button 
                    type="button"
                    class="btn btn-sm btn-outline-success btn-konfirmasi"
                    data-id="{{ $item->id }}"
                    data-nama="{{ $item->nama_lengkap }}">
                    <i class="ri-check-line"></i>
                  </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-5">
                <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 text-muted">Tidak ada data tagihan</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- ? Pagination --}}
    @if($tagihans->hasPages())
    <div class="card-footer">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          Menampilkan {{ $tagihans->firstItem() ?? 0 }} - {{ $tagihans->lastItem() ?? 0 }} 
          dari {{ $tagihans->total() }} data
        </div>
        {{ $tagihans->links('pagination::bootstrap-5') }}
      </div>
    </div>
    @endif
  </div>

</div>
@endsection
