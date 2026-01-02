@extends('layouts/layoutMaster')

@section('title', 'Edit Rekening')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
])
<style>
  .form-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s;
  }
  .form-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
  }
  .card-header-custom {
    background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
    border-radius: 12px 12px 0 0;
    padding: 1.25rem 1.5rem;
    border: none;
  }
  .card-title-custom {
    color: white;
    font-weight: 600;
    font-size: 1.125rem;
    margin: 0;
    display: flex;
    align-items: center;
  }
  .card-title-custom i {
    margin-right: 0.75rem;
    font-size: 1.5rem;
  }
  .form-label {
    font-weight: 600;
    color: #5a5f7d;
    margin-bottom: 0;
    font-size: 0.875rem;
    min-width: 200px;
  }
  .form-label i {
    margin-right: 0.5rem;
    color: #696cff;
  }
  .form-control, .form-select {
    border-radius: 8px;
    border: 1.5px solid #e8e8e8;
    padding: 0.625rem 1rem;
    transition: all 0.3s;
    font-size: 0.9375rem;
  }
  .form-control:focus, .form-select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
  }
  .form-control::placeholder {
    color: #a8afc7;
    font-size: 0.875rem;
  }
  .btn-save {
    padding: 0.625rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
  }
  .btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(105, 108, 255, 0.4);
  }
  .btn-cancel {
    padding: 0.625rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
  }
  .btn-cancel:hover {
    transform: translateY(-2px);
  }
  .page-header {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e8e8e8;
  }
  .page-header h4 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  .page-header p {
    color: #6c757d;
    margin: 0;
    font-size: 0.875rem;
  }
  .input-group-custom {
    display: flex;
    align-items: center;
    border: 1.5px solid #e8e8e8;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
  }
  .input-group-custom:focus-within {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
  }
  .input-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 0 1rem;
    height: 100%;
    min-height: 44px;
    color: #696cff;
    font-size: 1.25rem;
    border-right: 1.5px solid #e8e8e8;
  }
  .input-group-custom .form-control {
    border: none;
    box-shadow: none;
    flex: 1;
  }
  .input-group-custom .form-control:focus {
    border: none;
    box-shadow: none;
  }
  .form-row-inline {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  .divider-vertical {
    width: 2px;
    height: 24px;
    background: #e8e8e8;
  }
  @media (max-width: 768px) {
    .form-row-inline {
      flex-direction: column;
      align-items: stretch;
    }
    .form-label {
      min-width: auto;
      margin-bottom: 0.5rem;
    }
    .divider-vertical {
      display: none;
    }
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Form validation atau script khusus bisa ditambahkan di sini
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-rekening-edit">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-edit-box-line me-2"></i>Edit Rekening
                    </h4>
                    <p class="text-muted mb-0">Perbarui data rekening bank di bawah ini</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('rekenings.index') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-rekening" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-rekening" action="{{ route('rekenings.update', $rekening->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-12">
                    <div class="card form-card mb-4">
                        <div class="card-header-custom">
                            <h5 class="card-title-custom">
                                <i class="ri-bank-line"></i>
                                Informasi Rekening Bank
                            </h5>
                        </div>
                        <div class="card-body p-4">

                            <!-- Nama Bank -->
                            <div class="form-row-inline">
                                <label for="nama_bank" class="form-label">
                                    <i class="ri-bank-line"></i>Nama Bank
                                </label>
                                <div class="divider-vertical"></div>
                                <div class="input-group-custom flex-fill">
                                    <div class="input-icon">
                                        <i class="ri-bank-fill"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control @error('nama_bank') is-invalid @enderror" 
                                        id="nama_bank" 
                                        name="nama_bank" 
                                        placeholder="Contoh: Bank BCA, Bank Mandiri, Bank BNI"
                                        value="{{ old('nama_bank', $rekening->nama_bank) }}"
                                        required>
                                </div>
                                @error('nama_bank')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nomor Rekening -->
                            <div class="form-row-inline">
                                <label for="nomor_rekening" class="form-label">
                                    <i class="ri-bank-card-line"></i>Nomor Rekening
                                </label>
                                <div class="divider-vertical"></div>
                                <div class="input-group-custom flex-fill">
                                    <div class="input-icon">
                                        <i class="ri-barcode-line"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control @error('nomor_rekening') is-invalid @enderror" 
                                        id="nomor_rekening" 
                                        name="nomor_rekening" 
                                        placeholder="Contoh: 1234567890"
                                        value="{{ old('nomor_rekening', $rekening->nomor_rekening) }}"
                                        required>
                                </div>
                                @error('nomor_rekening')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nama Pemilik -->
                            <div class="form-row-inline mb-0">
                                <label for="nama_pemilik" class="form-label">
                                    <i class="ri-user-3-line"></i>Nama Pemilik
                                </label>
                                <div class="divider-vertical"></div>
                                <div class="input-group-custom flex-fill">
                                    <div class="input-icon">
                                        <i class="ri-user-fill"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        class="form-control @error('nama_pemilik') is-invalid @enderror" 
                                        id="nama_pemilik" 
                                        name="nama_pemilik" 
                                        placeholder="Contoh: John Doe"
                                        value="{{ old('nama_pemilik', $rekening->nama_pemilik) }}"
                                        required>
                                </div>
                                @error('nama_pemilik')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('rekenings.index') }}" class="btn btn-label-secondary btn-cancel flex-fill">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-save flex-fill">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
