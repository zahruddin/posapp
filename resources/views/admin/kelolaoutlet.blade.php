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
            <div class="row">
                @php
                    $colors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
                @endphp
            
                @foreach($outlets as $index => $outlet)
                    @php
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card shadow-sm border-0">
                            <!-- Card Header -->
                            <div class="card-header bg-{{ $color }} text-white d-flex align-items-center">
                                <h6 class="mb-0">{{ $loop->iteration }}. {{ $outlet->nama_outlet }}</h6>
                                <button type="button" class="btn btn-sm btn-light ms-auto" data-lte-toggle="card-collapse">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
            
                            <!-- Card Body -->
                            <div class="card-body small">
                                <p class="mb-2"><i class="bi bi-geo-alt-fill text-muted"></i> <strong>Alamat:</strong> {{ $outlet->alamat_outlet }}</p>
                                <p class="mb-2"><i class="bi bi-cash-stack text-muted"></i> <strong>Total Pendapatan:</strong> Rp5.000.000</p>
                                <p class="mb-2"><i class="bi bi-receipt text-muted"></i> <strong>Jumlah Transaksi:</strong> 120</p>
                                <p class="mb-0"><i class="bi bi-calendar-check text-muted"></i> <strong>Bergabung Sejak:</strong> 12 Januari 2024</p>
                            </div>
            
                            <!-- Card Footer -->
                            <div class="card-footer d-flex justify-content-between">
                                <div class="btn-group">
                                    <a href="{{ route('admin.productsOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-{{ $color }} btn-sm">
                                        <i class="bi bi-box-seam"></i> Produk
                                    </a>
                                    <a href="{{ route('admin.kasirOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-{{ $color }} btn-sm">
                                        <i class="bi bi-people"></i> User
                                    </a>
                                    <a href="{{ route('admin.dashboardOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-{{ $color }} btn-sm">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a>
                                </div>
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
