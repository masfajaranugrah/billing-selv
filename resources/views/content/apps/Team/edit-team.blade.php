@extends('layouts/layoutMaster')

@section('title', 'Edit User')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
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
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
  }
  .form-label i {
    margin-right: 0.5rem;
    color: #696cff;
    font-size: 1.1rem;
  }
  .form-control, .form-select {
    border-radius: 8px;
    border: 1.5px solid #e8e8e8;
    padding: 0.75rem 1rem;
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
  .form-text-muted {
    color: #a8afc7;
    font-size: 0.8125rem;
    margin-top: 0.25rem;
    display: block;
  }
  /* Select2 Custom Styling */
  .select2-container--default .select2-selection--single {
    border: 1.5px solid #e8e8e8 !important;
    border-radius: 8px !important;
    height: auto !important;
    padding: 0.625rem 1rem !important;
    transition: all 0.3s;
  }
  .select2-container--default .select2-selection--single:focus,
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #696cff !important;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15) !important;
    outline: none !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding: 0 !important;
    color: #5a5f7d;
    font-size: 0.9375rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #a8afc7 !important;
    font-size: 0.875rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    right: 1rem !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #696cff transparent transparent transparent !important;
    border-width: 6px 5px 0 5px !important;
  }
  .select2-dropdown {
    border: 1.5px solid #e8e8e8 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
  }
  .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: #696cff !important;
  }
  .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1.5px solid #e8e8e8 !important;
    border-radius: 8px !important;
    padding: 0.5rem 1rem !important;
  }
  .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #696cff !important;
    outline: none !important;
  }
  .password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #a8afc7;
    font-size: 1.25rem;
  }
  .password-toggle:hover {
    color: #696cff;
  }
  .password-wrapper {
    position: relative;
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Inisialisasi Select2
    $('#role').select2({
        placeholder: '-- Pilih Peran Pengguna --',
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada hasil ditemukan";
            }
        }
    });

    $('#employee_id').select2({
        placeholder: '-- Pilih Karyawan --',
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada karyawan ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    // --- AUTOSELECT KARYAWAN BERDASARKAN NAMA ---
    var employeeName = '{{ old('employee_id', $user->employee->full_name ?? '') }}';

    if(employeeName) {
        // Cari option yang memiliki value sama dengan nama
        var option = $("#employee_id option").filter(function() {
            return $(this).text().trim() === employeeName;
        }).first();

        if(option.length) {
            $('#employee_id').val(option.val()).trigger('change');
        }
    }

    // --- AUTOSELECT ROLE ---
    var roleValue = '{{ old('role', $user->role) }}';
    if(roleValue) {
        $('#role').val(roleValue).trigger('change');
    }

    // Toggle Password Visibility
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            }
        });
    });

});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-user-edit">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-edit-box-line me-2"></i>Edit User
                    </h4>
                    <p class="text-muted mb-0">Perbarui data user dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('users.index') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-user" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-user" action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informasi User -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-user-settings-line"></i>
                        Informasi User
                    </h5>
                </div>
                <div class="card-body p-4">

                    <!-- Karyawan -->
                    <div class="mb-4">
                        <label for="employee_id" class="form-label">
                            <i class="ri-user-search-line"></i>Pilih Karyawan
                        </label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Pilih karyawan yang akan dibuat akun user
                        </small>
                        @error('employee_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="ri-mail-line"></i>Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            placeholder="contoh@email.com"
                            value="{{ old('email', $user->email) }}"
                            required>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Email akan digunakan untuk login
                        </small>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label for="role" class="form-label">
                            <i class="ri-shield-user-line"></i>Peran (Role)
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="administrator" {{ old('role', $user->role) == 'administrator' ? 'selected' : '' }}>Administrator</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="marketing" {{ old('role', $user->role) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="customer_service" {{ old('role', $user->role) == 'customer_service' ? 'selected' : '' }}>Customer Service</option>
                            <option value="team" {{ old('role', $user->role) == 'team' ? 'selected' : '' }}>Team</option>
                            <option value="karyawan" {{ old('role', $user->role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="logistic" {{ old('role', $user->role) == 'logistic' ? 'selected' : '' }}>Logistic</option>


                        </select>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Tentukan hak akses pengguna di sistem
                        </small>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="ri-lock-password-line"></i>Password Baru (Opsional)
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="Masukkan password baru">
                            <span class="password-toggle">
                                <i class="ri-eye-off-line"></i>
                            </span>
                        </div>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter
                        </small>
                        @error('password')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-0">
                        <label for="password_confirmation" class="form-label">
                            <i class="ri-lock-line"></i>Konfirmasi Password
                        </label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                class="form-control @error('password_confirmation') is-invalid @enderror" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                placeholder="Masukkan ulang password">
                            <span class="password-toggle">
                                <i class="ri-eye-off-line"></i>
                            </span>
                        </div>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Harus sama dengan password baru di atas
                        </small>
                        @error('password_confirmation')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('users.index') }}" class="btn btn-label-secondary btn-cancel flex-fill">
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
