@extends('layouts/layoutMaster')

@section('title', 'Buku Besar Pembukuan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
<style>
/* ... (CSS tetap sama) ... */
</style>
@endsection

@section('content')

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header card-header-modern d-flex justify-content-between align-items-center flex-wrap">
        <div class="mb-2 mb-md-0">
            <h5 class="mb-0 fw-bold">
                <i class="ri-book-line me-2"></i>Laporan Debit & Credit
            </h5>
            <small class="opacity-75">Periode: 
                <span class="fw-bold">
                    {{ \Carbon\Carbon::createFromDate($tahun ?? date('Y'), $bulan ?? date('m'), 1)->locale('id')->isoFormat('MMMM YYYY') }}
                </span>
            </small>
        </div>
        
        <!-- Filter Periode Bulanan -->
        <div class="filter-container">
            <select name="bulan" class="filter-select" id="filterBulan">
                <option value="01" {{ ($bulan ?? date('m')) == '01' ? 'selected' : '' }}>Januari</option>
                <option value="02" {{ ($bulan ?? date('m')) == '02' ? 'selected' : '' }}>Februari</option>
                <option value="03" {{ ($bulan ?? date('m')) == '03' ? 'selected' : '' }}>Maret</option>
                <option value="04" {{ ($bulan ?? date('m')) == '04' ? 'selected' : '' }}>April</option>
                <option value="05" {{ ($bulan ?? date('m')) == '05' ? 'selected' : '' }}>Mei</option>
                <option value="06" {{ ($bulan ?? date('m')) == '06' ? 'selected' : '' }}>Juni</option>
                <option value="07" {{ ($bulan ?? date('m')) == '07' ? 'selected' : '' }}>Juli</option>
                <option value="08" {{ ($bulan ?? date('m')) == '08' ? 'selected' : '' }}>Agustus</option>
                <option value="09" {{ ($bulan ?? date('m')) == '09' ? 'selected' : '' }}>September</option>
                <option value="10" {{ ($bulan ?? date('m')) == '10' ? 'selected' : '' }}>Oktober</option>
                <option value="11" {{ ($bulan ?? date('m')) == '11' ? 'selected' : '' }}>November</option>
                <option value="12" {{ ($bulan ?? date('m')) == '12' ? 'selected' : '' }}>Desember</option>
            </select>
            
            <select name="tahun" class="filter-select" id="filterTahun">
                @for($i = date('Y'); $i >= date('Y') - 10; $i--)
                    <option value="{{ $i }}" {{ ($tahun ?? date('Y')) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
            
            <!-- Loading indicator -->
            <div id="filterLoading" class="d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="table table-modern table-hover align-middle mb-0" id="ledgerTable">
                <thead>
                    <tr>
                        <th style="width: 20%;"><i class="ri-calendar-line me-2"></i>Tanggal</th>
                        <th style="width: 15%;"><i class="ri-text me-2"></i>Keterangan</th>
                        <th style="width: 32%;" class="text-start">
                            <i class="ri-arrow-down-circle-line me-2 text-success"></i>Debit
                        </th>
                        <th style="width: 32%;" class="text-start">
                            <i class="ri-arrow-up-circle-line me-2 text-danger"></i>Credit
                        </th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    @forelse($ledgerData as $item)
                    
                    <!-- Baris Pemasukan -->
                    <tr class="row-pemasukan">
                        <td>
                            <span class="badge bg-label-primary">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success px-3 py-2">
                                <i class="ri-arrow-down-line me-1"></i>Pemasukan
                            </span>
                        </td>
                        <td class="text-start">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">
                                <i class="ri-add-line me-1"></i>Rp {{ number_format($item['total_masuk'],0,',','.') }}
                            </span>
                        </td>
                        <td class="text-start">
                            <span class="text-muted">-</span>
                        </td>
                    </tr>
                    
                    <!-- Baris Pengeluaran -->
                    <tr class="row-pengeluaran">
                        <td>
                            <span class="badge bg-label-primary">
                                {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger px-3 py-2">
                                <i class="ri-arrow-up-line me-1"></i>Pengeluaran
                            </span>
                        </td>
                        <td class="text-start">
                            <span class="text-muted">-</span>
                        </td>
                        <td class="text-start">
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fw-bold">
                                <i class="ri-subtract-line me-1"></i>Rp {{ number_format($item['total_keluar'],0,',','.') }}
                            </span>
                        </td>
                    </tr>
                    
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <div class="mb-3">
                                <i class="ri-inbox-line fs-1 text-muted opacity-50"></i>
                            </div>
                            <p class="mb-0">Tidak ada transaksi untuk periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                    
                    <!-- Baris Total -->
                    @if(count($ledgerData) > 0)
                    <tr class="row-total">
                        <td>
                            <span class="badge bg-primary px-4 py-2 fw-bold">
                                <i class="ri-calculator-line me-1"></i>TOTAL
                            </span>
                        </td>
                        <td>
                            <small class="text-muted fw-bold">
                                <i class="ri-calendar-check-line me-1"></i>
                                Periode: {{ $bulan ?? date('m') }}/{{ $tahun ?? date('Y') }}
                            </small>
                        </td>
                        <td class="text-start" id="totalDebitCell">
                            <div class="d-flex flex-column">
                                <small class="text-muted mb-1">Total Debit</small>
                                <span class="text-success fw-bold fs-6">
                                    Rp {{ number_format($monthTotalMasuk ?? 0,0,',','.') }}
                                </span>
                            </div>
                        </td>
                        <td class="text-start" id="totalCreditCell">
                            <div class="d-flex flex-column">
                                <small class="text-muted mb-1">Total Credit</small>
                                <span class="text-danger fw-bold fs-6">
                                    Rp {{ number_format($monthTotalKeluar ?? 0,0,',','.') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
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
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#ledgerTable').DataTable({
        paging: true,
        pageLength: 50,
        lengthMenu: [25, 50, 100],
        searching: false,
        ordering: true,
        info: true,
        responsive: false,
        order: [[0, 'desc']], // Sort by date descending
        columnDefs: [
            { orderable: false, targets: [1, 2, 3] } // Hanya tanggal yang bisa di-sort
        ],
        language: {
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            },
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ transaksi",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total)",
            zeroRecords: "Tidak ada transaksi yang sesuai",
            emptyTable: "Tidak ada transaksi untuk periode ini"
        },
        drawCallback: function(settings) {
            // Ensure total row is always at the bottom
            const totalRow = $('#ledgerTable tbody tr.row-total');
            if (totalRow.length > 0) {
                totalRow.appendTo('#ledgerTable tbody');
            }
        }
    });

    // ==========================================
    // AUTO-FILTER: Dropdown berubah = otomatis filter
    // ==========================================
    $('#filterBulan, #filterTahun').on('change', function() {
        applyFilter();
    });

    // Fungsi apply filter
    function applyFilter() {
        const bulan = $('#filterBulan').val();
        const tahun = $('#filterTahun').val();
        
        // Show loading state
        $('#filterBulan, #filterTahun').prop('disabled', true);
        $('#filterLoading').removeClass('d-none');
        
        // Redirect dengan parameter
        window.location.href = `{{ route('pembukuan.index') }}?bulan=${bulan}&tahun=${tahun}`;
    }

    // Format number dengan separator
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Optional: Auto refresh setiap 5 menit (bukan 30 detik, lebih efisien)
    let refreshInterval = setInterval(function() {
        refreshData();
    }, 300000); // 5 menit

    function refreshData() {
        const bulan = $('#filterBulan').val();
        const tahun = $('#filterTahun').val();
        
        $.ajax({
            url: '{{ route("pembukuan.index") }}',
            type: 'GET',
            dataType: 'json',
            data: {
                bulan: bulan,
                tahun: tahun
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                // Destroy existing DataTable
                table.destroy();
                
                // Update table body
                let tableBody = '';
                
                if(response.ledgerData && response.ledgerData.length > 0) {
                    response.ledgerData.forEach(function(item) {
                        const date = new Date(item.tanggal);
                        const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
                                          "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                        const formattedDate = ("0" + date.getDate()).slice(-2) + ' ' + 
                                            monthNames[date.getMonth()] + ' ' + 
                                            date.getFullYear();
                        
                        // Baris Pemasukan
                        tableBody += '<tr class="row-pemasukan">';
                        tableBody += '<td><span class="badge bg-label-primary">' + formattedDate + '</span></td>';
                        tableBody += '<td><span class="badge bg-success px-3 py-2">';
                        tableBody += '<i class="ri-arrow-down-line me-1"></i>Pemasukan</span></td>';
                        tableBody += '<td class="text-start">';
                        tableBody += '<span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold">';
                        tableBody += '<i class="ri-add-line me-1"></i>Rp ' + formatNumber(item.total_masuk);
                        tableBody += '</span></td>';
                        tableBody += '<td class="text-start"><span class="text-muted">-</span></td>';
                        tableBody += '</tr>';
                        
                        // Baris Pengeluaran
                        tableBody += '<tr class="row-pengeluaran">';
                        tableBody += '<td><span class="badge bg-label-primary">' + formattedDate + '</span></td>';
                        tableBody += '<td><span class="badge bg-danger px-3 py-2">';
                        tableBody += '<i class="ri-arrow-up-line me-1"></i>Pengeluaran</span></td>';
                        tableBody += '<td class="text-start"><span class="text-muted">-</span></td>';
                        tableBody += '<td class="text-start">';
                        tableBody += '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fw-bold">';
                        tableBody += '<i class="ri-subtract-line me-1"></i>Rp ' + formatNumber(item.total_keluar);
                        tableBody += '</span></td>';
                        tableBody += '</tr>';
                    });
                    
                    // Total row
                    tableBody += '<tr class="row-total">';
                    tableBody += '<td><span class="badge bg-primary px-4 py-2 fw-bold">';
                    tableBody += '<i class="ri-calculator-line me-1"></i>TOTAL</span></td>';
                    tableBody += '<td><small class="text-muted fw-bold">';
                    tableBody += '<i class="ri-calendar-check-line me-1"></i>Periode: ' + bulan + '/' + tahun;
                    tableBody += '</small></td>';
                    tableBody += '<td class="text-start"><div class="d-flex flex-column">';
                    tableBody += '<small class="text-muted mb-1">Total Debit</small>';
                    tableBody += '<span class="text-success fw-bold fs-6">Rp ' + formatNumber(response.todayTotalMasuk) + '</span>';
                    tableBody += '</div></td>';
                    tableBody += '<td class="text-start"><div class="d-flex flex-column">';
                    tableBody += '<small class="text-muted mb-1">Total Credit</small>';
                    tableBody += '<span class="text-danger fw-bold fs-6">Rp ' + formatNumber(response.todayTotalKeluar) + '</span>';
                    tableBody += '</div></td>';
                    tableBody += '</tr>';
                } else {
                    tableBody = '<tr><td colspan="4" class="text-center text-muted py-5">';
                    tableBody += '<div class="mb-3"><i class="ri-inbox-line fs-1 text-muted opacity-50"></i></div>';
                    tableBody += '<p class="mb-0">Tidak ada transaksi untuk periode ini.</p>';
                    tableBody += '</td></tr>';
                }
                
                $('#ledgerTableBody').html(tableBody);
                
                // Reinitialize DataTable
                table = $('#ledgerTable').DataTable({
                    paging: true,
                    pageLength: 50,
                    searching: false,
                    ordering: true,
                    info: true,
                    order: [[0, 'desc']],
                    columnDefs: [{ orderable: false, targets: [1, 2, 3] }],
                    language: {
                        paginate: {
                            previous: '<i class="ri-arrow-left-s-line"></i>',
                            next: '<i class="ri-arrow-right-s-line"></i>'
                        },
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ transaksi"
                    },
                    drawCallback: function(settings) {
                        const totalRow = $('#ledgerTable tbody tr.row-total');
                        if (totalRow.length > 0) {
                            totalRow.appendTo('#ledgerTable tbody');
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing data:', error);
                // Re-enable dropdowns on error
                $('#filterBulan, #filterTahun').prop('disabled', false);
                $('#filterLoading').addClass('d-none');
            }
        });
    }
});
</script>
@endsection
