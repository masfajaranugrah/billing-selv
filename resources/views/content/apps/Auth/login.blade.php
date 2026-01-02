@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp


@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/pages-auth.js'
])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{ url('/') }}" class="auth-cover-brand d-flex align-items-center gap-2">
    <span class="app-brand-text demo text-heading fw-semibold">Smart Billing</span>
  </a>
  <!-- /Logo -->

  <div class="authentication-inner row m-0">
    <!-- /Left Section -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
      <img src="{{ asset('assets/img/illustrations/auth-login-illustration-'.$configData['style'].'.png') }}"
           class="auth-cover-illustration w-100" alt="auth-illustration"
           data-app-light-img="illustrations/auth-login-illustration-light.png"
           data-app-dark-img="illustrations/auth-login-illustration-dark.png" />
      <img src="{{ asset('assets/img/illustrations/auth-cover-login-mask-'.$configData['style'].'.png') }}"
           class="authentication-image" alt="mask"
           data-app-light-img="illustrations/auth-cover-login-mask-light.png"
           data-app-dark-img="illustrations/auth-cover-login-mask-dark.png" />
    </div>
    <!-- /Left Section -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-12 px-12 py-6">
      <div class="w-px-400 mx-auto pt-5 pt-lg-0">
        <h4 class="mb-1">Selamat datang di dashboard smart billing</h4>
 

        <!-- ? Update: Arahkan form ke route login.create (POST) -->
      <form id="formAuthentication" class="mb-5" action="{{ route('login.create') }}" method="POST">
  @csrf

  <div class="form-floating form-floating-outline mb-5">
    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
    <label for="email">Email</label>
  </div>

  <div class="mb-5">
    <div class="form-password-toggle">
      <div class="input-group input-group-merge">
        <div class="form-floating form-floating-outline">
          <input type="password" id="password" class="form-control" name="password" placeholder="********" required />
          <label for="password">Password</label>
        </div>
        <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary d-grid w-100">
    Sign in
  </button>
</form>

     
        
      </div>
    </div>
    <!-- /Login -->
  </div>
</div>
@endsection
