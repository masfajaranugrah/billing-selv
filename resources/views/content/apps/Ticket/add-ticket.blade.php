@extends('layouts/layoutMaster')

@section('title', 'Tambah Ticket - CS')

{{-- Vendor Styles --}}
@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

{{-- Page Scripts --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ==========================================
    // 🔹 INISIALISASI SELECT2
    // ==========================================
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%'
    });

    $('#priority, #category').select2({
        width: '100%'
    });

    // ==========================================
    // 🔹 AUTO-FILL DATA PELANGGAN
    // ==========================================
    $('#pelangganSelect').on('change', function () {
        const selected = $(this).find('option:selected');

        // Reset form jika tidak ada pilihan
        if (!selected || !selected.val()) {
            resetCustomerForm();
            return;
        }

        // Isi data pelanggan ke form
        fillCustomerData(selected);
    });

    // ==========================================
    // 🔹 PREVIEW FILE UPLOAD (OPTIONAL)
    // ==========================================
    const csAttachmentInput = document.getElementById('cs_attachment');
    const csPreview = document.getElementById('cs_preview');

    if (csAttachmentInput && csPreview) {
        csAttachmentInput.addEventListener('change', function() {
            const file = this.files[0];

            if (!file) {
                csPreview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                csPreview.src = e.target.result;
                csPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    // ==========================================
    // 🔹 HELPER FUNCTIONS
    // ==========================================
    function resetCustomerForm() {
        $('#phone').val('');
        $('#nama_pelanggan').val('');
        $('#alamat_pelanggan').val('');
        $('#paket_pelanggan').val('');
        $('#pelanggan_id').val('');
    }

    function fillCustomerData(selected) {
        // Set pelanggan ID
        $('#pelanggan_id').val(selected.val());

        // Set data pelanggan
        $('#phone').val(selected.data('nowhatsapp') || '');
        $('#nama_pelanggan').val(selected.data('nama') || '');
        $('#paket_pelanggan').val(selected.data('paket') || '');

        // Format alamat lengkap
        const alamatParts = [
            selected.data('alamat_jalan'),
            `RT ${selected.data('rt') || '-'}/RW ${selected.data('rw') || '-'}`,
            selected.data('desa'),
            selected.data('kecamatan')
        ].filter(part => part && part !== 'RT -/RW -');

        $('#alamat_pelanggan').val(alamatParts.join(', '));
    }
});
</script>
@endsection

{{-- Page Content --}}
@section('content')
<div class="app-cs-ticket">
    <form action="{{ route('tickets.stores') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="mb-1">Tambah Ticket Baru</h4>
                <p class="text-muted mb-0">Isi data tiket dengan lengkap agar teknisi mudah menindaklanjuti.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tickets.indexs') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Simpan Ticket
                </button>
            </div>
        </div>

        <div class="row g-4">
            {{-- ==========================================
                INFORMASI CUSTOMER
            ========================================== --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-label-primary">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user me-2"></i>Informasi Customer
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Pilih Pelanggan --}}
                        <div class="mb-3">
                            <label class="form-label" for="pelangganSelect">
                                Pilih Pelanggan <span class="text-danger">*</span>
                            </label>
                            <select id="pelangganSelect" class="form-select select2" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelanggan as $p)
                                    <option
                                        value="{{ $p->id }}"
                                        data-paket_id="{{ optional($p->paket)->id }}"
                                        data-nama="{{ $p->nama_lengkap }}"
                                        data-alamat_jalan="{{ $p->alamat_jalan }}"
                                        data-rt="{{ $p->rt }}"
                                        data-rw="{{ $p->rw }}"
                                        data-desa="{{ $p->desa }}"
                                        data-kecamatan="{{ $p->kecamatan }}"
                                        data-kabupaten="{{ $p->kabupaten }}"
                                        data-provinsi="{{ $p->provinsi }}"
                                        data-kode_pos="{{ $p->kode_pos }}"
                                        data-nowhatsapp="{{ $p->no_whatsapp }}"
                                        data-nomorid="{{ $p->nomer_id }}"
                                        data-paket="{{ optional($p->paket)->nama_paket }}"
                                        data-harga="{{ optional($p->paket)->harga }}"
                                        data-masa="{{ optional($p->paket)->masa_pembayaran }}"
                                        data-kecepatan="{{ optional($p->paket)->kecepatan }}"
                                        data-durasi="{{ optional($p->paket)->durasi }}"
                                    >
                                        {{ $p->nomer_id }} - {{ $p->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="pelanggan_id" id="pelanggan_id" required>
                        </div>

                        <div class="row">
                            {{-- Nama Pelanggan --}}
                            <div class="col-md-6 mb-3">
                                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                                <input
                                    type="text"
                                    id="nama_pelanggan"
                                    class="form-control"
                                    placeholder="Otomatis terisi"
                                    readonly
                                >
                            </div>

                            {{-- No. Telepon --}}
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">No. Telepon / WhatsApp</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    placeholder="Otomatis terisi"
                                >
                            </div>
                        </div>

                        {{-- Alamat Pelanggan --}}
                        <div class="mb-3">
                            <label for="alamat_pelanggan" class="form-label">Alamat Pelanggan</label>
                            <textarea
                                id="alamat_pelanggan"
                                class="form-control"
                                rows="2"
                                placeholder="Otomatis terisi"
                                readonly
                            ></textarea>
                        </div>

                        <div class="row">
                            {{-- Paket Internet --}}
                            <div class="col-md-6 mb-3">
                                <label for="paket_pelanggan" class="form-label">Paket Internet</label>
                                <input
                                    type="text"
                                    id="paket_pelanggan"
                                    class="form-control"
                                    placeholder="Otomatis terisi"
                                    readonly
                                >
                            </div>

                            {{-- Link Lokasi --}}
                            <div class="col-md-6 mb-3">
                                <label for="location_link" class="form-label">Link Lokasi (Google Maps)</label>
                                <input
                                    type="url"
                                    class="form-control"
                                    id="location_link"
                                    name="location_link"
                                    placeholder="https://maps.app.goo.gl/..."
                                >
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Kategori Kendala --}}
                        <div class="mb-3">
                            <label for="category" class="form-label">
                                Kategori Kendala <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="internet_down">Internet Down</option>
                                <option value="modem_error">Modem Error</option>
                                <option value="kabel_putus">Kabel Putus</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        {{-- Deskripsi Kendala --}}
                        <div class="mb-3">
                            <label for="issue_description" class="form-label">
                                Deskripsi Kendala <span class="text-danger">*</span>
                            </label>
                            <textarea
                                class="form-control"
                                id="issue_description"
                                name="issue_description"
                                rows="4"
                                placeholder="Jelaskan detail masalah yang dialami pelanggan..."
                                required
                            ></textarea>
                        </div>

                        {{-- Catatan Tambahan --}}
                        <div class="mb-3">
                            <label for="additional_note" class="form-label">Catatan Tambahan</label>
                            <textarea
                                class="form-control"
                                id="additional_note"
                                name="additional_note"
                                rows="2"
                                placeholder="Informasi tambahan (opsional)..."
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                ASSIGNMENT & STATUS
            ========================================== --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-label-info">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-flag me-2"></i>Prioritas & Status
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Prioritas --}}
                        <div class="mb-3">
                            <label for="priority" class="form-label">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="urgent">🔴 Urgent</option>
                                <option value="medium" selected>🟡 Medium</option>
                                <option value="low">🟢 Low</option>
                            </select>
                            <small class="text-muted">Pilih tingkat urgensi penanganan</small>
                        </div>

                        <div class="alert alert-info mb-0" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="ti ti-info-circle me-1"></i>Informasi
                            </h6>
                            <small>
                                Ticket akan otomatis masuk ke antrian teknisi setelah disimpan.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ti ti-device-floppy me-2"></i>Simpan Ticket
                            </button>
                            <a href="{{ route('tickets.indexs') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="ti ti-x me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
