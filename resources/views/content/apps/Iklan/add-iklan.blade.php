@extends('layouts/layoutMaster')

@section('title', 'Buat Notifikasi Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <h5 class="alert-heading mb-2">
                <i class="ri-error-warning-line me-2"></i>Terjadi Kesalahan!
            </h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%); color: white;">
                <h5 class="mb-0">
                    <i class="ri-notification-3-line me-2"></i>Buat Notifikasi Baru
                </h5>
            </div>

            <form action="{{ route('iklan.store') }}" method="POST" enctype="multipart/form-data" id="iklanForm">
                @csrf
                <div class="card-body">
                    <!-- ? PILIHAN TYPE -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Tipe Notifikasi <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="type-card" data-type="informasi">
                                    <input type="radio" name="type" value="informasi" id="type-informasi" {{ old('type') === 'informasi' ? 'checked' : '' }} required>
                                    <label for="type-informasi" class="type-label">
                                        <div class="type-icon bg-label-info">
                                            <i class="ri-information-line"></i>
                                        </div>
                                        <div class="type-title">Informasi</div>
                                        <small class="text-muted">Info umum ke pelanggan</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="type-card" data-type="maintenance">
                                    <input type="radio" name="type" value="maintenance" id="type-maintenance" {{ old('type') === 'maintenance' ? 'checked' : '' }}>
                                    <label for="type-maintenance" class="type-label">
                                        <div class="type-icon bg-label-warning">
                                            <i class="ri-tools-line"></i>
                                        </div>
                                        <div class="type-title">Maintenance</div>
                                        <small class="text-muted">Pemberitahuan maintenance</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="type-card" data-type="iklan">
                                    <input type="radio" name="type" value="iklan" id="type-iklan" {{ old('type') === 'iklan' ? 'checked' : '' }}>
                                    <label for="type-iklan" class="type-label">
                                        <div class="type-icon bg-label-success">
                                            <i class="ri-megaphone-line"></i>
                                        </div>
                                        <div class="type-title">Iklan/Promosi</div>
                                        <small class="text-muted">Promosi & penawaran</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('type')
                            <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Judul Notifikasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               name="title" 
                               value="{{ old('title') }}"
                               required 
                               maxlength="255"
                               placeholder="Contoh: Promo Spesial Akhir Tahun!">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Pesan Notifikasi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  name="message" 
                                  rows="6" 
                                  required 
                                  minlength="10"
                                  maxlength="1000"
                                  placeholder="Tulis pesan notifikasi Anda di sini...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Minimal 10 karakter</small>
                            <small class="text-muted">
                                <span id="charCount">0</span>/1000 karakter
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Gambar (Opsional)</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               name="image" 
                               accept="image/*" 
                               id="imageInput">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, GIF (Max 2MB)</small>
                        
                        <div id="imagePreview" class="mt-3 position-relative" style="display: none;">
                            <img id="preview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 2px solid #e8e8e8;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" id="removeImage">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex gap-2 justify-content-end">
                    <a href="{{ route('iklan.index') }}" class="btn btn-secondary">
                        <i class="ri-close-line me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ri-save-line me-2"></i>Simpan Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-style')
<style>
.type-card {
    position: relative;
    cursor: pointer;
    transition: all 0.3s;
}

.type-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.type-label {
    display: block;
    padding: 1.5rem 1rem;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    height: 100%;
}

.type-card:hover .type-label {
    border-color: #696cff;
    background: #f8f9ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.type-card input[type="radio"]:checked + .type-label {
    border-color: #696cff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    box-shadow: 0 4px 16px rgba(105, 108, 255, 0.3);
}

.type-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.type-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const messageTextarea = document.querySelector('[name="message"]');
    const charCount = document.getElementById('charCount');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 900) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
        charCount.textContent = messageTextarea.value.length;
    }

    // Image preview
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const preview = document.getElementById('preview');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2048000) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar!');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            preview.src = '';
        });
    }

    // Form validation
    const form = document.getElementById('iklanForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const title = form.querySelector('[name="title"]').value.trim();
            const message = form.querySelector('[name="message"]').value.trim();
            const type = form.querySelector('[name="type"]:checked');

            if (!type) {
                e.preventDefault();
                alert('Pilih tipe notifikasi!');
                return false;
            }

            if (!title) {
                e.preventDefault();
                alert('Judul wajib diisi!');
                return false;
            }

            if (!message || message.length < 10) {
                e.preventDefault();
                alert('Pesan minimal 10 karakter!');
                return false;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }
        });
    }
});
</script>
@endsection
