@extends('layouts/layoutMaster')

@section('title', 'Tambah Paket Internet')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/quill/katex.scss',
    'resources/assets/vendor/libs/quill/editor.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/tagify/tagify.scss',
    'resources/assets/vendor/libs/highlight/highlight.scss'
])
<style>
  .form-modern {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .card-header-custom {
    color: black;
    border-radius: 12px 12px 0 0 !important;
    padding: 1.5rem;
    border-bottom: 1px solid #f0f0f0;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  }
  
  .form-label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #5a5f7d;
    margin-bottom: 0.5rem;
  }
  
  /* Input with Icon - Seamless Design */
  .input-with-icon {
    position: relative;
    display: flex;
    align-items: stretch;
    width: 100%;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    transition: all 0.2s;
    overflow: hidden;
    background: white;
  }
  
  .input-with-icon:hover {
    border-color: #696cff;
  }
  
  .input-with-icon:focus-within {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
  }
  
  .input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 45px;
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
  }
  
  .input-with-icon input,
  .input-with-icon select {
    flex: 1;
    border: none;
    outline: none;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    background: transparent;
    border-radius: 0;
    color: #2c3e50;
  }
  
  .input-with-icon input::placeholder,
  .input-with-icon select::placeholder {
    color: #a0a5ba;
  }
  
  .input-with-icon input:focus,
  .input-with-icon select:focus {
    box-shadow: none;
    outline: none;
  }
  
  .input-with-icon select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235a5f7d' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
  }
  
  .form-section {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    transition: all 0.2s;
  }
  
  .form-section:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-color: #696cff;
  }
  
  .form-section-title {
    color: #696cff;
    font-weight: 700;
    margin-bottom: 1.25rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: flex;
    align-items: center;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #696cff;
  }
  
  .form-section-title i {
    margin-right: 0.5rem;
    font-size: 1.1rem;
  }
  
  .btn-submit {
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    border: none;
    color: white;
  }
  
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
    color: white;
  }
  
  .btn-cancel {
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    border: 1px solid #d1d5db;
  }
  
  .btn-cancel:hover {
    transform: translateY(-2px);
    background: #f3f4f6;
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
  
  .page-header {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
  }
  
  .page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
  }
  
  .page-title i {
    margin-right: 0.75rem;
    color: #696cff;
  }
  
  .page-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .input-icon {
      min-width: 42px;
      font-size: 1rem;
    }
    
    .input-with-icon input,
    .input-with-icon select {
      padding: 0.625rem 0.875rem;
      font-size: 0.8125rem;
    }
  }
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/quill/katex.js',
    'resources/assets/vendor/libs/quill/quill.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js',
    'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/tagify/tagify.js',
    'resources/assets/vendor/libs/highlight/highlight.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/forms-editors.js'])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaInput = document.getElementById('harga');

    // Format currency input
    hargaInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if(value) {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            this.value = '';
        }
    });

    // Form submission with loading
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading overlay
        $('.loading-overlay').css('display', 'flex');
        
        // Convert formatted price back to raw number
        const rawValue = hargaInput.value.replace(/\D/g, '');
        hargaInput.value = rawValue;
        
        // Disable submit button
        const submitBtn = document.querySelector('.btn-submit');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
        
        // Submit form after brief delay
        setTimeout(() => {
            this.submit();
        }, 500);
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

<!-- Page Header -->
<div class="page-header">
    <h4 class="page-title">
        <i class="ri-package-line"></i>Tambah Paket Internet
    </h4>
    <p class="page-subtitle">Lengkapi detail berikut untuk membuat paket internet baru</p>
</div>

<form action="{{ route('paket.store') }}" method="POST">
    @csrf
    
    <div class="card form-modern border-0 shadow-sm">
        <div class="card-header-custom">
            <h5 class="mb-0 fw-bold">
                <i class="ri-file-list-3-line me-2"></i>Formulir Paket Internet
            </h5>
        </div>

        <div class="card-body p-4">
            
            <!-- Informasi Dasar Paket -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-information-line"></i>Informasi Dasar Paket
                </h6>
                
                <div class="mb-4">
                    <label class="form-label" for="nama_paket">Nama Paket</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <i class="ri-price-tag-3-line"></i>
                        </div>
                        <input 
                            type="text" 
                            id="nama_paket" 
                            name="nama_paket"
                            placeholder="Contoh: Paket Hemat 1 Bulan" 
                            required
                            value="{{ old('nama_paket') }}">
                    </div>
                    <small class="text-muted">Masukkan nama paket yang mudah diingat dan deskriptif</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="harga">Harga Paket (IDR)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                            <input 
                                type="text" 
                                id="harga" 
                                name="harga" 
                                placeholder="Contoh: 50.000" 
                                required
                                value="{{ old('harga') }}">
                        </div>
                        <small class="text-muted">Harga akan diformat otomatis</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="masa_pembayaran">Masa Aktif (Hari)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <input 
                                type="number" 
                                id="masa_pembayaran" 
                                name="masa_pembayaran"
                                placeholder="Contoh: 30" 
                                required
                                value="{{ old('masa_pembayaran') }}">
                        </div>
                        <small class="text-muted">Durasi paket dalam satuan hari</small>
                    </div>
                </div>
            </div>

            <!-- Spesifikasi Teknis -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-settings-4-line"></i>Spesifikasi Teknis
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="kecepatan">Kecepatan Internet (Mbps)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-speed-line"></i>
                            </div>
                            <input 
                                type="number" 
                                id="kecepatan" 
                                name="kecepatan"
                                placeholder="Contoh: 20" 
                                required
                                value="{{ old('kecepatan') }}">
                        </div>
                        <small class="text-muted">Kecepatan maksimal yang ditawarkan</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="cycle">Siklus Pembayaran</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-refresh-line"></i>
                            </div>
                            <select id="cycle" name="cycle" required>
                                <option value="">-- Pilih Siklus --</option>
                                <option value="daily" {{ old('cycle') === 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="weekly" {{ old('cycle') === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ old('cycle') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ old('cycle') === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <small class="text-muted">Periode berulang untuk pembayaran</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('paket.index') }}" class="btn btn-secondary btn-cancel">
                    <i class="ri-close-line me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="ri-save-line me-1"></i>Simpan Paket
                </button>
            </div>

        </div>
    </div>
</form>
@endsection
