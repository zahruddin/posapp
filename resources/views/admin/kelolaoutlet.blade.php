@extends('layouts.app')

@section('title', 'Kelola Outlet Admin | TrackBooth')
@section('page', 'Kelola Outlet')

@section('content')
    <!--end::App Content Header-->
    <!--begin::App Content-->
    <div class="app-content">
        <div class="container-fluid">
            {{-- ALERT --}}
            @include('components.alert')
            {{-- END ALERT --}}
    
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#tambahoutlet">
                    <i class="bi bi-plus-lg"></i> Tambah Outlet
                </button>
            </div>
    
            <!-- Modal Tambah Outlet -->
            <div class="modal fade" id="tambahoutlet" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tambah Outlet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.tambahOutlet') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_outlet" class="form-label">Nama Outlet</label>
                                    <input type="text" class="form-control" id="nama_outlet" name="nama_outlet" placeholder="Masukkan nama outlet" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat_outlet" class="form-label">Alamat Outlet</label>
                                    <input type="text" class="form-control" id="alamat_outlet" name="alamat_outlet" placeholder="Masukkan alamat outlet" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Akhir Modal -->
    
            <!-- Row untuk menampilkan daftar outlet -->
            <div class="row g-3">
                @php
                    $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                @endphp
                @foreach($outlets as $index => $outlet)
                    @php
                        $color = $colors[$index % count($colors)]; // Ambil warna berdasarkan indeks
                    @endphp
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <!-- Card Header dengan warna dinamis -->
                            <div class="card-header bg-{{ $color }} text-white d-flex align-items-center">
                                <h5 class="mb-0">{{ $loop->iteration }}. {{ $outlet->nama_outlet }}</h5>
                                <div class="card-tools ms-auto">
                                    <button type="button" class="btn btn-sm btn-light" data-lte-toggle="card-collapse">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                        
                            <!-- Card Body (Collapsible) -->
                            <div class="card-body">
                                <p><i class="bi bi-geo-alt"></i> <strong>Alamat:</strong> {{ $outlet->alamat_outlet }}</p>
                                <p><i class="bi bi-cash-stack"></i> <strong>Total Pendapatan:</strong> Rp5.000.000</p> 
                                <p><i class="bi bi-receipt"></i> <strong>Jumlah Transaksi:</strong> 120</p> 
                                <p><i class="bi bi-calendar-check"></i> <strong>Bergabung Sejak:</strong> 12 Januari 2024</p> 
                            </div>
                        
                            <!-- Card Footer -->
                            <div class="card-footer d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.dashboardOutlet', ['id' => $outlet->id]) }}" class="btn btn-sm btn-primary d-flex align-items-center">
                                    <i class="bi bi-bar-chart me-2"></i> Dashboard
                                </a>
                                <button class="btn btn-danger btn-sm deleteOutlet" data-id="{{ $outlet->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>  
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <!-- Akhir Row -->

            {{-- MODAL Konfirmasi Hapus --}}
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus pengguna ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="bataldelete" data-bs-dismiss="modal">Batal</button>
                            <button id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end modal notif konfirmasi delete --}}
        </div>
    </div>
    
     
    @endsection
    <!--end::App Content-->
    @section('scripts')
    <script>
        $(document).ready(function () {
            let deleteOutlet;
    
            // Saat tombol hapus diklik, simpan ID user
            $('.deleteOutlet').click(function () {
                deleteOutlet = $(this).data('id');
                $('#confirmDeleteModal').modal('show');
            });
            $('#bataldelete').click(function () {
                $('#confirmDeleteModal').modal('hide');
            });
            $('.close').click(function () {
                $('#confirmDeleteModal').modal('hide');
            });
    
            // Saat tombol konfirmasi di modal diklik
            $('#confirmDeleteBtn').click(function () {
                $.ajax({
                    url: "/admin/kelolaoutlet/hapusoutlet/" + deleteOutlet,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}" // Kirim token CSRF
                    },
                    // $('#confirmDeleteModal').modal('hide'), // Tutup modal
                    success: function (response) {
                        // alert(response.success); // Tampilkan pesan sukses
                        location.reload(); // Refresh halaman
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON.error); // Tampilkan error
                    }
                });
            });
        });
    </script>
    @endsection
