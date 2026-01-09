@extends('layouts.layoutMaster')

@section('title', 'Buku Besar Pembukuan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
<style>
.card {
    border-radius: 12px;
}
.table-bordered th, .table-bordered td {
    border-color: #e9ecef;
}
.table-light {
    background-color: #f8f9fa;
}
.table-secondary {
    background-color: #e9ecef;
}
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.75rem;
    line-height: 1.2;
    border-radius: 0.2rem;
}
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
<!-- Filter Bulan/Tahun -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0"><i class="ri-calendar-line me-2"></i>Periode: {{ $firstMonth['label'] ?? '-' }}</h5>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('pembukuan.total') }}" class="d-flex gap-2 justify-content-end">
                    <select name="bulan" class="form-select form-select-sm" style="width: auto;">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="form-select form-select-sm" style="width: auto;">
                        @for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-sm btn-dark"><i class="ri-filter-line me-1"></i>Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Saldo Awal Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0"><i class="ri-wallet-3-line me-1"></i>Saldo Awal</h6>
            @if(auth()->check())
                <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                    <i class="ri-edit-2-line me-1"></i>Isi Manual
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Omset Internet Dedicated</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_dedicated ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Omset Home Net Kotor</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_homenet_kotor ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Home Net Bersih</td>
                        <td class="text-end">Rp {{ number_format($saldoAwal->omset_homenet_bersih ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalSaldoAwal">
                                <i class="ri-pencil-line"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Saldo Awal</td>
                        <td class="text-end fw-bold">Rp {{ number_format(($saldoAwal->omset_dedicated ?? 0) + ($saldoAwal->omset_homenet_bersih ?? 0), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Pemasukan Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="ri-arrow-down-line me-1"></i>Pemasukan</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Registrasi</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['registrasi'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="fw-bold text-muted">Dedicated (dari Tagihan)</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Pemasukan Dedicated Kotor</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['dedicatedKotor'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Potongan / Pengembalian</td>
                        <td class="text-end text-danger">- Rp {{ number_format($firstMonth['pemasukan']['potonganDedicated'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold">Pemasukan Dedicated Bersih</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($firstMonth['pemasukan']['dedicatedBersih'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <small class="text-muted">
                                <i class="ri-information-line me-1"></i>
                                Tagihan Lunas: {{ $firstMonth['pemasukan']['jumlahDedicatedLunas'] ?? 0 }} / {{ $firstMonth['pemasukan']['jumlahDedicatedTotal'] ?? 0 }}
                            </small>
                        </td>
                        <td></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="fw-bold text-muted">Home Net</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Pemasukan Home Net Kotor</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['pemasukan']['homeNetKotor'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4">Potongan / Pengembalian</td>
                        <td class="text-end text-danger">- Rp {{ number_format($firstMonth['pemasukan']['potonganHomeNet'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="ps-4 fw-semibold">Pemasukan Home Net Bersih</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($firstMonth['pemasukan']['homeNetBersih'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Pemasukan</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPemasukan'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Pengeluaran Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="ri-arrow-up-line me-1"></i>Pengeluaran</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px">Kode</th>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($firstMonth['pengeluaran']) && is_array($firstMonth['pengeluaran']))
                        @foreach($firstMonth['pengeluaran'] as $item)
                        <tr>
                            <td>{{ $item['kode'] }}</td>
                            <td>{{ $item['kategori'] }}</td>
                            <td class="text-end">Rp {{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif
                    <tr class="table-secondary">
                        <td colspan="2" class="fw-bold">Total Pengeluaran</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPengeluaran'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Piutang Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="ri-money-dollar-circle-line me-1"></i>Piutang</h6>
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Internet Dedicated</th>
                        <th>Home Net</th>
                        <th>Total Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $firstMonth['label'] ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['dedicated'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['piutang']['homeNet'] ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalPiutang'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recap Omset Table -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="ri-bar-chart-2-line me-1"></i>Omset</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Internet Dedicated</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['omset']['dedicated'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Home Net Kotor</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['omset']['kotor'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Home Net Bersih</td>
                        <td class="text-end">Rp {{ number_format($firstMonth['omset']['homeNetBersih'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-secondary">
                        <td class="fw-bold">Total Omset</td>
                        <td class="text-end fw-bold">Rp {{ number_format($firstMonth['totalOmset'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Saldo Awal -->
<div class="modal fade" id="modalSaldoAwal" tabindex="-1" aria-labelledby="modalSaldoAwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSaldoAwalLabel">
                    <i class="ri-edit-2-line me-2"></i>Edit Saldo Awal - {{ $firstMonth['label'] ?? '-' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSaldoAwal" method="POST" action="{{ route('saldo-awal.store') }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="omset_dedicated" class="form-label">Total Omset Internet Dedicated</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_dedicated" 
                                   name="omset_dedicated" 
                                   value="{{ old('omset_dedicated', number_format($saldoAwal->omset_dedicated ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_dedicated')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="omset_homenet_kotor" class="form-label">Total Omset Home Net Kotor</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_homenet_kotor" 
                                   name="omset_homenet_kotor" 
                                   value="{{ old('omset_homenet_kotor', number_format($saldoAwal->omset_homenet_kotor ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_homenet_kotor')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="omset_homenet_bersih" class="form-label">Total Home Net Bersih</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control currency-input" id="omset_homenet_bersih" 
                                   name="omset_homenet_bersih" 
                                   value="{{ old('omset_homenet_bersih', number_format($saldoAwal->omset_homenet_bersih ?? 0, 0, ',', '.')) }}" 
                                   placeholder="0">
                        </div>
                        @error('omset_homenet_bersih')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
])
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('Script loaded');
    
    // Currency input formatting
    $(document).on('input', '.currency-input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        $(this).val(value);
    });

    // Reset form when modal opens
    $('#modalSaldoAwal').on('shown.bs.modal', function () {
        $('.currency-input').each(function() {
            let rawValue = $(this).data('raw-value') || '0';
            $(this).val(parseInt(rawValue).toLocaleString('id-ID'));
        });
    });

    // Store raw values when modal opens
    $('#modalSaldoAwal').on('show.bs.modal', function () {
        $('.currency-input').each(function() {
            let rawValue = $(this).val().replace(/[^\d]/g, '') || '0';
            $(this).data('raw-value', rawValue);
        });
    });

    // Form submission with AJAX
    $('#formSaldoAwal').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Convert currency format to number
        let omset_dedicated = $('#omset_dedicated').val().replace(/[^\d]/g, '') || '0';
        let omset_homenet_kotor = $('#omset_homenet_kotor').val().replace(/[^\d]/g, '') || '0';
        let omset_homenet_bersih = $('#omset_homenet_bersih').val().replace(/[^\d]/g, '') || '0';
        
        let formData = new FormData(this);
        formData.set('omset_dedicated', omset_dedicated);
        formData.set('omset_homenet_kotor', omset_homenet_kotor);
        formData.set('omset_homenet_bersih', omset_homenet_bersih);
        
        console.log('Form data:', Object.fromEntries(formData));
        
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line me-1 spin"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Success:', response);
                
                $('#modalSaldoAwal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message || 'Data berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                
                let errorMessage = 'Terjadi kesalahan';
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errors.push(messages[0]);
                    });
                    errorMessage = errors.join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage
                });
                
                submitBtn.prop('disabled', false).html('<i class="ri-save-line me-1"></i>Simpan');
            }
        });
    });

    // Show session messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: '{!! implode("<br>", $errors->all()) !!}'
        });
    @endif
});
</script>
@endsection
