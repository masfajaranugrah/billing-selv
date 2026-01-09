@extends('layouts/layoutMaster')

@section('title', 'Daftar Tagihan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* MODERN CLEAN STYLES */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
  --success-color: #28c76f;
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
  transform: translateY(-2px);
}

/* Dashboard Cards with Border Accent */
.card-border-shadow-primary::before,
.card-border-shadow-success::before,
.card-border-shadow-warning::before,
.card-border-shadow-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.card-border-shadow-primary::before {
  background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
}

.card-border-shadow-success::before {
  background: linear-gradient(180deg, #d1d5db 0%, #9ca3af 100%);
}

.card-border-shadow-warning::before {
  background: linear-gradient(180deg, #e5e7eb 0%, #d1d5db 100%);
}

.card-border-shadow-info::before {
  background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
}

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e5e7eb;
  transition: var(--transition);
}

.stats-card p,
.stats-card h2,
.stats-card .text-muted {
  color: #0f172a !important;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  background: #f3f4f6;
  color: #111827;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.card:hover .avatar-initial {
  transform: scale(1.05);
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* Override Bootstrap default button colors */
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

.btn.btn-primary:focus,
.btn-primary:focus,
.btn.btn-primary:focus-visible,
.btn-primary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

.btn.btn-primary:active,
.btn-primary:active {
  background: #09090b !important;
  color: #fafafa !important;
}

/* Warning Button - Black */
.btn.btn-warning,
.btn-warning {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-warning:hover,
.btn-warning:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-warning:focus,
.btn-warning:focus,
.btn.btn-warning:focus-visible,
.btn-warning:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
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

.btn.btn-success:focus,
.btn-success:focus,
.btn.btn-success:focus-visible,
.btn-success:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Secondary Button - Black */
.btn.btn-secondary,
.btn-secondary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-secondary:hover,
.btn-secondary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-secondary:focus,
.btn-secondary:focus,
.btn.btn-secondary:focus-visible,
.btn-secondary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Danger Button - Black */
.btn.btn-danger,
.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-danger:hover,
.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-danger:focus,
.btn-danger:focus,
.btn.btn-danger:focus-visible,
.btn-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Search Button - Black (shadcn-like) */
.btn.btn-search-dark,
.btn-search-dark {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-search-dark:hover,
.btn-search-dark:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-search-dark:focus,
.btn-search-dark:focus,
.btn.btn-search-dark:focus-visible,
.btn-search-dark:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

.btn.btn-search-dark:active,
.btn-search-dark:active {
  background: #09090b !important;
}

/* Outline Buttons - Light background, black text */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn.btn-outline-danger,
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-danger {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn.btn-outline-danger:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-danger:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:focus,
.btn.btn-outline-secondary:focus,
.btn.btn-outline-danger:focus,
.btn-outline-primary:focus-visible,
.btn-outline-secondary:focus-visible,
.btn-outline-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

/* Small Button */
.btn.btn-sm,
.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* Icon Button */
.btn-icon {
  width: 2rem;
  height: 2rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES & TEXT */
/* ========================================= */

/* Badges */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

/* Neutralize accent labels and badges - shadcn style */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* Badge Paket - Black background, white text */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.stats-icon.bg-label-primary,
.stats-icon.bg-label-success,
.stats-icon.bg-label-warning,
.stats-icon.bg-label-info {
  background: #f4f4f5 !important;
  color: #18181b !important;
}

/* Status Lunas - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Belum Bayar - Red with white text, rounded */
.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Solid badges default */
.bg-info,
.bg-warning,
.bg-primary,
.bg-dark {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* All text colors - Black (shadcn style) */
.text-success,
.text-info,
.text-warning,
.text-primary,
.text-danger,
.text-muted {
  color: #71717a !important;
}

 

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.12);
}

/* ========================================= */
/* SHADCN UI STYLE MODAL */
/* ========================================= */
.modal-content {
  border-radius: 12px;
  border: 1px solid #e4e4e7;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header .modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #18181b;
  margin: 0;
}

.modal-header.bg-primary {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-primary .modal-title {
  color: #fafafa;
}

.modal-header.bg-warning {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-warning .modal-title {
  color: #fafafa;
}

.modal-header .btn-close {
  padding: 0.5rem;
  margin: -0.5rem -0.5rem -0.5rem auto;
  opacity: 0.5;
  transition: opacity 0.15s ease;
}

.modal-header .btn-close:hover {
  opacity: 1;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 65vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1rem 1.5rem;
  margin-top: 0.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Modal Dialog Centered with proper spacing */
.modal-dialog-centered {
  min-height: calc(100% - 3.5rem);
  margin: 1.75rem auto;
}

/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Table */
.table {
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: #f8fafc;
  border: none;
  padding: 1rem;
  font-weight: 600;
  color: #0f172a;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:not(.empty-state-row):hover {
  background: #f1f5f9;
  transform: scale(1.001);
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
}

.table thead th:first-child,
.table tbody td:first-child {
  text-align: center;
}

/* Empty State */
.empty-state-row td {
  background: #fafbfc !important;
  border: none !important;
}

.empty-state-content {
  padding: 3rem 1rem;
}

/* Loading Overlay */
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

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e8e8e8;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #111827 0%, #0b1220 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(105, 108, 255, 0.4);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.customer-status {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:first-child,
.modal-body > .detail-section:first-of-type {
  margin-top: 0.5rem;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #111827;
}

.detail-section h6 {
  color: #111827;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #111827;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  min-width: 150px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a8afc7;
  font-size: 1rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

/* Card Header */
.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.card-header-custom {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* Input Groups */
.input-group-text {
  border-radius: 8px 0 0 8px;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  color: #5a5f7d;
  font-weight: 500;
}

/* ========================================= */
/* PAGINATION STYLES */
/* ========================================= */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
  border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.pagination-info {
  color: #71717a;
  font-size: 0.875rem;
  font-weight: 500;
}

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

/* DataTables pagination styles */
.dataTables_wrapper .dataTables_info {
  float: left !important;
  padding-top: 1.25rem;
  padding-bottom: 1rem;
  color: #71717a;
  font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate {
  float: right !important;
  text-align: right !important;
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.dataTables_wrapper .dataTables_paginate .pagination {
  justify-content: flex-end !important;
  margin: 0 !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 4px !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
  background: #fff !important;
  background-color: #fff !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  cursor: not-allowed !important;
}

.dataTables_wrapper::after {
  content: '';
  display: table;
  clear: both;
}

/* Hide DataTables default controls if using custom pagination */
.dataTables_length,
.dataTables_filter {
  display: none !important;
}

/* Hide default Laravel pagination results text */
.pagination-wrapper .pagination + div,
.pagination-wrapper nav + div,
.pagination-wrapper div:has(> nav) > p,
.pagination-wrapper > div > nav ~ *:not(.pagination),
.pagination-wrapper > div:last-child p {
  display: none !important;
}

/* Alternative: Hide any 'Showing X to Y of Z results' text */
.pagination-wrapper div:last-child > p,
.pagination-wrapper > div > .text-sm,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p {
  display: none !important;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-body {
    padding: 1.25rem;
  }
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    const formatDate = d => d.toISOString().split('T')[0];

    // ========================================
    // FLATPICKR INITIALIZATION
    // ========================================
    $(document).on('shown.bs.modal', '[id^="modalEditTagihan-"]', function () {
        flatpickr($(this).find('.flatpickr-edit-start'), {
            dateFormat: "Y-m-d",
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
        flatpickr($(this).find('.flatpickr-edit-end'), {
            dateFormat: "Y-m-d",
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
    });

    flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        defaultDate: new Date(),
      allowInput: true,
      minDate: null,
      disableMobile: true
    });

    flatpickr("#tanggal_berakhir", {
        dateFormat: "Y-m-d",
      allowInput: false,
      minDate: null,
      disableMobile: true
    });

    // ========================================
    // SELECT2 PELANGGAN
    // ========================================
    $('#pelangganSelect').select2({
        placeholder: '-- Pilih Pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan')
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    if (tglMulai) {
        tglMulai.value = formatDate(new Date());
    }

    function fillFields(selected) {
        if (!selected || !selected.val()) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        const fields = [
            'nama','alamat_jalan','rt','rw','desa','kecamatan','kabupaten','provinsi',
            'kode_pos','nowhatsapp','nomorid','paket','harga','masa','kecepatan','paket_id'
        ];

        fields.forEach(f => {
            const el = $('#' + (f === 'masa' ? 'masa_pembayaran' : f === 'nama' ? 'nama_lengkap' : f === 'nowhatsapp' ? 'no_whatsapp' : f === 'nomorid' ? 'nomer_id' : f));
            el.val(selected.data(f));
        });

        $('#pelanggan_id').val(selected.val());

        const startDate = new Date($('#tanggal_mulai').val());
        const masa = selected.data('masa') || selected.data('durasi');
        if (masa) {
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + parseInt(masa));
            $('#tanggal_berakhir').val(formatDate(endDate));
        }
    }

    $('#pelangganSelect').on('change', function () {
        fillFields($(this).find('option:selected'));
    });

    if (tglMulai) {
        tglMulai.addEventListener('change', function () {
            fillFields($('#pelangganSelect').find('option:selected'));
        });
    }

    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        const list = $('#pelangganSelect option').filter((_, el) => el.value);
        if (list.length === 1) {
            $('#pelangganSelect').val(list.val()).trigger('change');
        }
    });

    // ========================================
    // AUTO SUBMIT ON FILTER CHANGE
    // ========================================
    $('#statusFilter').on('change', function() {
        $('#filterForm').submit();
    });

    // ========================================
    // LOADING OVERLAY ON FORM SUBMIT
    // ========================================
    $('#filterForm').on('submit', function() {
        showLoading();
    });

    // ========================================
    // SWEETALERT DELETE
    // ========================================
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = this;

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            html: '<p class="mb-0">Yakin ingin menghapus tagihan ini?<br><strong class="text-danger">Data tidak dapat dikembalikan!</strong></p>',
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
            showDenyButton: false,
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Hapus',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                setTimeout(() => form.submit(), 500);
            }
        });
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `<p class="mb-0">Apakah <strong>${nama}</strong> sudah membayar?</p>`,
            icon: 'question',
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-check-line me-1"></i>Ya, Lunas',
            cancelButtonText: 'Batal',
            buttonsStyling: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.post(`/dashboard/admin/tagihan/${id}/bayar`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                .done(resp => {
                    hideLoading();
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil dikonfirmasi',
                            timer: 1500,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => location.reload());
                    }
                })
                .fail(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan server',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    });

    // ========================================
    // MASS TAGIHAN
    // ========================================
    $('#modalMassTagihan').on('shown.bs.modal', function () {
        flatpickr(".flatpickr-select-start-all", {
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
        flatpickr(".flatpickr-select-start-end", {
            dateFormat: "Y-m-d",
            defaultDate: new Date().fp_incr(7),
        allowInput: true,
        minDate: null,
        disableMobile: true
        });

        // Reset search dan checkbox saat modal dibuka
        $('#searchPelanggan').val('');
        $('#selectAllPelanggan').prop('checked', false);
        $('.pelanggan-checkbox').prop('checked', false);
        $('.pelanggan-item').show();
        updateSelectedCount();
    });

    // ========================================
    // SEARCH PELANGGAN
    // ========================================
    $(document).on('keyup input paste', '#searchPelanggan', function() {
        const searchTerm = $(this).val().toLowerCase().trim();

        // Hapus pesan "tidak ada hasil" jika ada
        $('#noResultMessage').remove();

        if (searchTerm === '') {
            $('.pelanggan-item').show();
            updateSelectAllState();
            return;
        }

        let visibleCount = 0;
        $('.pelanggan-item').each(function() {
            const $item = $(this);
            const nama = String($item.attr('data-nama') || '').toLowerCase();
            const nomerId = String($item.attr('data-nomer-id') || '').toLowerCase();
            const wa = String($item.attr('data-wa') || '').toLowerCase();

            // Normalize search term (hapus spasi, dash, dll untuk nomor)
            const normalizedSearch = searchTerm.replace(/[\s\-+]/g, '');
            const normalizedWa = wa.replace(/[\s\-+]/g, '');

            if (nama.includes(searchTerm) ||
                nomerId.includes(searchTerm) ||
                wa.includes(searchTerm) ||
                normalizedWa.includes(normalizedSearch)) {
                $item.show();
                visibleCount++;
            } else {
              // Sembunyikan saja tanpa menghapus pilihan supaya tidak hilang saat berganti search
              $item.hide();
            }
        });

        // Update select all state setelah filter
        updateSelectAllState();
        updateSelectedCount();

        // Jika tidak ada hasil, tampilkan pesan
        if (visibleCount === 0) {
            $('#pelangganList').append('<div id="noResultMessage" class="text-center py-3 text-muted"><i class="ri-search-line me-1"></i>Tidak ada hasil ditemukan</div>');
        }
    });

    // ========================================
    // SELECT ALL
    // ========================================
    $('#selectAllPelanggan').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.pelanggan-item:visible .pelanggan-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // ========================================
    // INDIVIDUAL CHECKBOX
    // ========================================
    $(document).on('change', '.pelanggan-checkbox', function() {
        updateSelectedCount();
        updateSelectAllState();
    });

    // ========================================
    // UPDATE SELECTED COUNT
    // ========================================
    function updateSelectedCount() {
        const count = $('.pelanggan-checkbox:checked').length;
        $('#selectedCount').text(count + ' dipilih');
        $('#submitCount').text(count);

        // Disable submit jika tidak ada yang dipilih
        if (count === 0) {
            $('#btnSubmitMass').prop('disabled', true).addClass('opacity-50');
        } else {
            $('#btnSubmitMass').prop('disabled', false).removeClass('opacity-50');
        }
    }

    // ========================================
    // UPDATE SELECT ALL STATE
    // ========================================
    function updateSelectAllState() {
        const visibleCheckboxes = $('.pelanggan-item:visible .pelanggan-checkbox');
        const checkedCheckboxes = $('.pelanggan-item:visible .pelanggan-checkbox:checked');

        if (visibleCheckboxes.length === 0) {
            $('#selectAllPelanggan').prop('checked', false);
        } else {
            $('#selectAllPelanggan').prop('checked', visibleCheckboxes.length === checkedCheckboxes.length);
        }
    }

    // ========================================
    // FORM SUBMIT VALIDATION
    // ========================================
    $('#formMassTagihan').on('submit', function(e) {
        const selectedCount = $('.pelanggan-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal 1 pelanggan untuk dibuatkan tagihan.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        showLoading();
    });

    // ========================================
    // ? BUTTON DETAIL - SHOW MODAL
    // ========================================
    $(document).on('click', '.btn-detail', function() {
        const $row = $(this).closest('tr');

        // Ambil data dari table cells
        // Ambil data utama sesuai urutan kolom tabel
        const nomorId = $row.find('.badge.bg-label-dark').text().trim();
        const namaLengkap = $row.find('td:nth-child(4) strong').text().trim();
        const noWhatsapp = $row.find('code').text().trim().replace(/\D/g, '');
        const noWhatsappDisplay = $row.find('code').text().trim();
        const status = $row.find('td:nth-child(6) .badge').text().trim();
        const paket = $row.find('td:nth-child(7) .badge').text().trim();
        const harga = $row.find('td:nth-child(8) strong').text().trim();

        // Data dari attribute
        const alamat = $row.data('alamat') || '-';
        const kecamatan = $row.data('kecamatan') || '-';
        const kabupaten = $row.data('kabupaten') || '-';
        const provinsi = $row.data('provinsi') || '-';
        const kecepatan = $row.data('kecepatan') || '-';
        const tanggalMulai = $row.data('tanggal-mulai') || '-';
        const jatuhTempo = $row.data('jatuh-tempo') || '-';
        const catatan = $row.data('catatan') || '-';
        const buktiUrl = $row.data('bukti') || '';

        // Badge status color
        const statusClass = status.toLowerCase().includes('lunas') ? 'bg-success' : 'bg-danger';
        const statusIcon = status.toLowerCase().includes('lunas') ? 'checkbox-circle' : 'close-circle';

        // Build modal content
        const modalContent = `
            <div class="customer-header-info">
                <div class="customer-avatar">
                    ${namaLengkap.charAt(0).toUpperCase()}
                </div>
                <h5 class="customer-name">${namaLengkap}</h5>
                <span class="badge ${statusClass} customer-status">
                    <i class="ri-${statusIcon}-line me-1"></i>
                    ${status}
                </span>
            </div>

            <!-- Informasi Dasar -->
            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Dasar</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-barcode-line"></i>
                        Nomor ID
                    </div>
                    <div class="detail-value"><strong>${nomorId}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-user-line"></i>
                        Nama Lengkap
                    </div>
                    <div class="detail-value">${namaLengkap}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-whatsapp-line"></i>
                        WhatsApp
                    </div>
                    <div class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <i class="ri-whatsapp-line me-1"></i>${noWhatsappDisplay}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-2-line"></i>
                        Alamat
                    </div>
                    <div class="detail-value">${alamat}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-building-line"></i>
                        Kecamatan
                    </div>
                    <div class="detail-value">${kecamatan}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-pin-range-line"></i>
                        Kabupaten
                    </div>
                    <div class="detail-value">${kabupaten}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-global-line"></i>
                        Provinsi
                    </div>
                    <div class="detail-value">${provinsi}</div>
                </div>
            </div>

            <!-- Paket Internet -->
            <div class="detail-section">
                <h6><i class="ri-wifi-line"></i>Paket Internet</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-box-3-line"></i>
                        Nama Paket
                    </div>
                    <div class="detail-value">${paket}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-speed-line"></i>
                        Kecepatan
                    </div>
                    <div class="detail-value"><strong>${kecepatan}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-money-dollar-circle-line"></i>
                        Harga
                    </div>
                    <div class="detail-value"><strong class="text-primary">${harga}</strong></div>
                </div>
            </div>

            <!-- Tagihan -->
            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Detail Tagihan</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-line"></i>
                        Tanggal Mulai
                    </div>
                    <div class="detail-value">${tanggalMulai}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-close-line"></i>
                        Jatuh Tempo
                    </div>
                    <div class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-file-text-line"></i>
                        Catatan
                    </div>
                    <div class="detail-value">${catatan}</div>
                </div>

            </div>
        `;

        // Populate modal dan tampilkan
        $('#detailModal .modal-body').html(modalContent);
        $('#detailModal').modal('show');
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid px-4 py-4">
  <!-- ========================================= -->
  <!-- DASHBOARD CARDS -->
  <!-- ========================================= -->
  <div class="row g-4 mb-4">
    <!-- Total Customer -->
    <div class="col-xl-3 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-primary me-3">
            <i class="ri-group-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Total Customer</p>
            <h2 class="mb-0 fw-bold">{{ $totalCustomer }}</h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Lunas -->
    <div class="col-xl-3 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-success me-3">
            <i class="ri-checkbox-circle-line"></i>
          </div>
          <div>

            <p class="mb-0 text-muted small">Tagihan Lunas</p>
            <h2 class="mb-0 fw-bold text-success">{{ $customerLunas ?? 0 }}</h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Belum Lunas -->
    <div class="col-xl-3 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-warning me-3">
            <i class="ri-error-warning-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Belum Lunas</p>
            <h2 class="mb-0 fw-bold text-warning">{{ $belumLunas }}</h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Paket -->
    <div class="col-xl-3 col-md-6">
      <div class="stats-card">
        <div class="d-flex align-items-center">
          <div class="stats-icon bg-label-info me-3">
            <i class="ri-box-3-line"></i>
          </div>
          <div>
            <p class="mb-0 text-muted small">Total Paket</p>
            <h2 class="mb-0 fw-bold text-info">{{ $totalPaket }}</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- FILTER & SEARCH -->
  <!-- ========================================= -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('tagihan.get') }}" id="filterForm">
        <div class="row g-3 align-items-end">
          <div class="col-md-9">
            <label class="form-label small fw-semibold mb-2">
              <i class="ri-search-line me-1"></i>Pencarian
            </label>
            <input
              type="text"
              name="search"
              class="form-control"
              placeholder="Cari nama, No. ID, WhatsApp..."
              value="{{ request('search') }}">
          </div>

          <div class="col-md-3">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-search-dark flex-grow-1">
                <i class="ri-search-line me-1"></i>Cari
              </button>
              @if(request()->hasAny(['search']))
                <a href="{{ route('tagihan.get') }}" class="btn btn-secondary">
                  <i class="ri-refresh-line me-1"></i>Reset
                </a>
              @endif
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- DAFTAR TAGIHAN -->
  <!-- ========================================= -->
  <div class="card border-0 shadow-sm">
    <div class="card-header-custom">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h4 class="mb-1 fw-bold">
            <i class="ri-file-list-3-line me-2"></i>Daftar Tagihan
          </h4>
          <p class="mb-0 opacity-75 small">Kelola seluruh tagihan pelanggan secara efisien.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
          @if($tagihans->total() > 0)
            <span class="badge" style="padding: 10px 20px; font-size: 0.9rem; background: rgba(24, 24, 27, 0.1); color: #18181b; border: 1px solid rgba(24, 24, 27, 0.2);">
              <i class="ri-database-2-line me-1"></i>
              {{ $tagihans->total() }} Tagihan
            </span>
          @endif

          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
            <i class="ri-add-line me-1"></i>Tambah Tagihan
          </button>
          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalMassTagihan">
            <i class="ri-group-line me-1"></i>Tagihan Massal
          </button>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive p-3">
        <table class="table table-hover align-middle" style="width: 100%;">
          <thead>
            <tr>
              <th>No</th>
              <th><i class="ri-eye-line me-1"></i>Detail</th>
              <th><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th><i class="ri-user-3-line me-1"></i>Nama</th>
              <th><i class="ri-whatsapp-line me-1"></i>No. WA</th>
              <th><i class="ri-shield-check-line me-1"></i>Status</th>
              <th><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
              <th><i class="ri-settings-3-line me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $item)
            @php
              $status = strtolower($item['status_pembayaran'] ?? '');
              $badgeClass = match($status) {
                'lunas' => 'badge bg-success',
                'belum bayar' => 'badge bg-danger',
                default => 'badge bg-secondary',
              };

              $alamatParts = [];
              if($item['alamat_jalan']) $alamatParts[] = $item['alamat_jalan'];
              if($item['rt'] || $item['rw']) $alamatParts[] = 'RT '.$item['rt'].' / RW '.$item['rw'];
              if($item['desa']) $alamatParts[] = 'Desa '.$item['desa'];
              if($item['kecamatan']) $alamatParts[] = 'Kecamatan '.$item['kecamatan'];
              if($item['kabupaten']) $alamatParts[] = 'Kabupaten '.$item['kabupaten'];
              if($item['provinsi']) $alamatParts[] = $item['provinsi'];
              $alamatLengkap = implode(', ', $alamatParts);

              $buktiUrl = !empty($item['bukti_pembayaran']) ? asset('storage/kwitansi/' . $item['bukti_pembayaran']) : '';
            @endphp
            <tr
              data-alamat="{{ $alamatLengkap }}"
              data-kecamatan="{{ $item['kecamatan'] ?? '-' }}"
              data-kabupaten="{{ $item['kabupaten'] ?? '-' }}"
              data-provinsi="{{ $item['provinsi'] ?? '-' }}"
              data-kecepatan="{{ $item['paket']['kecepatan'] ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}"
              data-jatuh-tempo="{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}"
              data-catatan="{{ $item['catatan'] ?? '-' }}"
              data-bukti="{{ $buktiUrl }}"
            >
              <td class="text-muted fw-semibold" style="width: 60px;">{{ ($tagihans->firstItem() ?? 1) + $loop->index }}</td>
              <td>
                <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge bg-label-dark">{{ $item['nomer_id'] }}</span></td>
              <td><strong>{{ $item['nama_lengkap'] }}</strong></td>
              <td style="min-width: 180px;">
                <a href="https://wa.me/{{ $item['no_whatsapp'] }}" target="_blank" class="text-decoration-none">
                  <code style="background: #18181b; padding: 6px 12px; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; color: #fafafa; white-space: nowrap; display: inline-flex; align-items: center;">
                    <i class="ri-whatsapp-line me-1"></i>{{ $item['no_whatsapp'] }}
                  </code>
                </a>
              </td>
              <td>
                <span class="{{ $badgeClass }}">
                  <i class="ri-{{ $status == 'lunas' ? 'checkbox-circle' : 'close-circle' }}-line me-1"></i>
                  {{ ucfirst($status ?: '-') }}
                </span>
              </td>
              <td><strong>Rp {{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</strong></td>
              <td>
                <div class="d-flex gap-2">
                  <button type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditTagihan-{{ $item['id'] }}"
                    title="Edit">
                    <i class="ri-edit-2-line"></i>
                  </button>

                  <form action="{{ route('tagihan.destroy', $item['id']) }}" method="POST" class="delete-form d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr class="empty-state-row">
              <td colspan="8" class="text-center">
                <div class="empty-state-content">
                  <div class="mb-3">
                    <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                  </div>

                  @if(request()->hasAny(['search']))
                    <h5 class="text-muted mb-2">
                      <i class="ri-search-eye-line me-2"></i>Data Tidak Ditemukan
                    </h5>
                    <p class="text-muted mb-3">
                      Tidak ada data yang sesuai dengan pencarian Anda.
                    </p>

                    <div class="mb-3">
                      @if(request('search'))
                        <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                          <i class="ri-search-line me-1"></i>
                          Pencarian: "{{ request('search') }}"
                        </span>
                      @endif
                    </div>

                    <a href="{{ route('tagihan.get') }}" class="btn btn-primary mt-2">
                      <i class="ri-refresh-line me-1"></i>Reset & Tampilkan Semua Data
                    </a>
                  @else
                    <h5 class="text-muted mb-2">
                      <i class="ri-file-list-line me-2"></i>Belum Ada Data Tagihan
                    </h5>
                    <p class="text-muted">
                      Saat ini belum ada data tagihan yang terdaftar dalam sistem.
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

    @if($tagihans->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $tagihans->total() }}</strong> tagihan
        </div>
        <div>
          {{ $tagihans->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri-information-line me-2"></i>Detail Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      {{-- <div class="modal-footer py-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
      </div> --}}
    </div>
  </div>
</div>


<!-- ========================================= -->
<!-- MODAL: TAMBAH TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-add-circle-line me-2"></i>Tambah Tagihan Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- Pilih Pelanggan -->
            <div class="col-12">
              <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
              <select id="pelangganSelect" name="pelanggan_id" class="form-select select2" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($pelanggan as $p)
                  <option value="{{ $p->id }}"
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
                    data-durasi="{{ optional($p->paket)->durasi }}">
                    {{ $p->nomer_id }} - {{ $p->nama_lengkap }}
                  </option>
                @endforeach
              </select>
            </div>

            <input type="hidden" name="paket_id" id="paket_id">

            <!-- Info Pelanggan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-user-3-line me-2"></i>Informasi Pelanggan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kode Pos</label>
              <input type="text" id="kode_pos" class="form-control bg-light" readonly>
            </div>

            <!-- Alamat -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-map-pin-line me-2"></i>Alamat Lengkap
              </h6>
            </div>

            <div class="col-12">
              <label class="form-label small text-muted">Alamat Jalan</label>
              <input type="text" id="alamat_jalan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RT</label>
              <input type="text" id="rt" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RW</label>
              <input type="text" id="rw" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Desa/Kelurahan</label>
              <input type="text" id="desa" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kecamatan</label>
              <input type="text" id="kecamatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kabupaten/Kota</label>
              <input type="text" id="kabupaten" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Provinsi</label>
              <input type="text" id="provinsi" class="form-control bg-light" readonly>
            </div>

            <!-- Paket -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-box-3-line me-2"></i>Informasi Paket
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Paket</label>
              <input type="text" id="paket" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Harga Paket</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" id="harga" name="harga" class="form-control bg-light" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kecepatan</label>
              <div class="input-group">
                <input type="text" id="kecepatan" class="form-control bg-light" readonly>
                <span class="input-group-text">Mbps</span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Masa Pembayaran</label>
              <div class="input-group">
                <input type="text" id="masa_pembayaran" class="form-control bg-light" readonly>
                <span class="input-group-text">Hari</span>
              </div>
            </div>

            <!-- Tagihan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-calendar-check-line me-2"></i>Detail Tagihan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control bg-light" readonly required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Upload Bukti Pembayaran (Opsional)</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: EDIT TAGIHAN (FOREACH) -->
<!-- ========================================= -->
@foreach($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-edit-2-line me-2"></i>Edit Tagihan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nama Pelanggan</label>
              <input type="text" class="form-control bg-light" value="{{ $tagihan['nama_lengkap'] ?? '-' }}" readonly>
            </div>

            <input type="hidden" name="pelanggan_id" value="{{ $tagihan['pelanggan_id'] ?? '' }}">
            <input type="hidden" name="paket_id" value="{{ $tagihan['paket']['id'] ?? '' }}">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="text" name="tanggal_mulai" class="form-control flatpickr-edit-start" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
              <input type="text" name="tanggal_berakhir" class="form-control flatpickr-edit-end" value="{{ $tagihan['tanggal_berakhir'] }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" name="catatan" rows="2">{{ $tagihan['catatan'] ?? '' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Bukti Pembayaran</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-save-line me-1"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<!-- ========================================= -->
<!-- MODAL: MASS TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalMassTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.massStore') }}" method="POST" id="formMassTagihan">
        @csrf

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-group-line me-2"></i>Buat Tagihan Massal
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- Search Box -->
          <div class="mb-3">
            <label class="form-label fw-semibold">
              <i class="ri-search-line me-1"></i>Cari Pelanggan
            </label>
            <input type="text" id="searchPelanggan" class="form-control" placeholder="Cari berdasarkan nama, No. ID, WhatsApp...">
          </div>

          <!-- Select All -->
          <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="selectAllPelanggan">
              <label class="form-check-label fw-semibold" for="selectAllPelanggan">
                Pilih Semua
              </label>
            </div>
            <span class="badge bg-primary" id="selectedCount">0 dipilih</span>
          </div>

          <!-- List Pelanggan dengan Checkbox -->
          <div class="border rounded p-3 mb-3" style="max-height: 300px; overflow-y: auto; background: #f8f9fa;" id="pelangganList">
            @foreach($pelanggan as $p)
            <div class="pelanggan-item py-2 border-bottom"
                 data-nama="{{ strtolower($p->nama_lengkap ?? '') }}"
                 data-nomer-id="{{ strtolower($p->nomer_id ?? '') }}"
                 data-wa="{{ strtolower(str_replace([' ', '-', '+'], '', $p->no_whatsapp ?? '')) }}">
              <div class="form-check">
                <input class="form-check-input pelanggan-checkbox" type="checkbox" name="pelanggan_ids[]" value="{{ $p->id }}" id="pelanggan_{{ $p->id }}">
                <label class="form-check-label w-100" for="pelanggan_{{ $p->id }}">
                  <span class="badge bg-dark me-2">{{ $p->nomer_id }}</span>
                  <strong>{{ $p->nama_lengkap }}</strong>
                  @if($p->paket)
                    <span class="badge bg-label-info ms-2">{{ $p->paket->nama_paket }}</span>
                  @else
                    <span class="badge bg-label-danger ms-2">Tidak ada paket</span>
                  @endif
                </label>
              </div>
            </div>
            @endforeach
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
            <input type="text" name="tanggal_mulai" class="form-control flatpickr-select-start-all" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
            <input type="text" name="tanggal_berakhir" class="form-control flatpickr-select-start-end" required>
          </div>

          <div class="alert alert-info small mb-0">
            <i class="ri-information-line me-1"></i>
            Hanya pelanggan yang dipilih akan dibuatkan tagihan baru
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning" id="btnSubmitMass">
            <i class="ri-check-circle-line me-1"></i>Buat Tagihan (<span id="submitCount">0</span>)
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

