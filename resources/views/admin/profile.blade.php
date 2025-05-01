@extends('layouts.app')

@section('title', 'Profile | TrackBooth')

@if(isset($outlet) && !empty($outlet->nama_outlet))
    @section('page', "Dashboard Outlet $outlet->nama_outlet")
    @push('outlet')
        / {{ $outlet->nama_outlet }} 
    @endpush
@else
@section('page', 'Profile')
@endif
@section('style')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
@endsection
@section('content')
<div class="app-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-5">

                @include('components.alert')

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">

                        <!-- Profile Picture -->
                        <div class="text-center mb-4">
                            <img class="img-fluid rounded-circle shadow"
                                 src="{{ asset('dist/assets/img/user2-160x160.jpg')}}"
                                 alt="User profile picture"
                                 style="width: 100px; height: 100px;">
                        </div>

                        <!-- Basic Info -->
                        <h4 class="text-center fw-semibold mb-1">{{ $user->name }}</h4>
        
                        <p class="text-center text-muted mb-0">{{ $user->email }}</p>
                        <p class="text-center text-muted mb-4"><small>{{ ucfirst($user->role) }} • Bergabung {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</small></p>

                        <!-- Form -->
                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                       value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control form-control-sm"
                                       value="{{ old('username', $user->username) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                       value="{{ old('email', $user->email) }}">
                            </div>

                            <hr class="my-4">

                            <h6 class="text-muted mb-3">Ganti Password <small class="text-danger">(opsional)</small></h6>

                            <div class="mb-3 position-relative">
                                <input type="password" name="password" class="form-control form-control-sm"
                                       id="password" placeholder="Password Baru">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('password', this)" style="cursor: pointer;">
                                    <i class="bi bi-eye text-muted"></i>
                                </span>
                            </div>
                            
                            <div class="mb-3 position-relative">
                                <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                       id="password_confirmation" placeholder="Konfirmasi Password">
                                <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('password_confirmation', this)" style="cursor: pointer;">
                                    <i class="bi bi-eye text-muted"></i>
                                </span>
                            </div>
                            

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@section('scripts')
<script>
    function togglePassword(fieldId, iconSpan) {
        const input = document.getElementById(fieldId);
        const icon = iconSpan.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection

@endsection