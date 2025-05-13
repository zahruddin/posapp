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
            <div class="table-responsive">
                <table id="outletTable" class="table">
                    <thead class="">
                        <tr>
                            <th>#</th>
                            <th>Nama Outlet</th>
                            <th>Alamat</th>
                            <th>Ditambahkan pada</th>
                            <th>Menu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outlets as $index => $outlet)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $outlet->nama_outlet }}</td>
                            <td>{{ $outlet->alamat_outlet }}</td>
                            <td>{{ \Carbon\Carbon::parse($outlet->created_at)->format('d-m-Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.dashboardOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-speedometer2"></i><span> Dashboard</span>
                                    </a>
                                    <a href="{{ route('admin.productsOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-box-seam"></i><span> Produk</span>
                                    </a>
                                    <a href="{{ route('admin.kasirOutlet', ['id' => $outlet->id]) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-people"></i><span> Users</span>
                                    </a>
                                    <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-people"></i><span> Penjualan</span>
                                    </a>
                                    <a href="{{ route('admin.expense', ['id' => $outlet->id]) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-people"></i><span> Pengeluaran</span>
                                    </a>
                                    <a href="{{ route('admin.seduh', ['id' => $outlet->id]) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-people"></i><span> Penyeduhan</span>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editoutletmodal" 
                                        data-id="{{ $outlet->id }}" 
                                        data-nama="{{ $outlet->nama_outlet }}" 
                                        data-alamat="{{ $outlet->alamat_outlet }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm deleteOutlet" data-id="{{ $outlet->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Akhir Row -->
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
            <!-- Modal EDIT Outlet -->
            <div class="modal fade" id="editoutletmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Outlet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editOutletForm" action="{{ route('admin.tambahOutlet') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="edit_id">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_outlet" class="form-label">Nama Outlet</label>
                                    <input type="text" class="form-control" id="edit_nama_outlet" name="nama_outlet" placeholder="Masukkan nama outlet" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat_outlet" class="form-label">Alamat Outlet</label>
                                    <input type="text" class="form-control" id="edit_alamat_outlet" name="alamat_outlet" placeholder="Masukkan alamat outlet" required>
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
            {{-- MODAL Konfirmasi Hapus --}}
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus seluruh data dari outlet ini termasuk riwayat transaksi?
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    let id = this.getAttribute('data-id');
                    let nama = this.getAttribute('data-nama');
                    let alamat = this.getAttribute('data-alamat');
    
                    // Isi nilai form dengan data produk yang diklik
                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_nama_outlet').value = nama;
                    document.getElementById('edit_alamat_outlet').value = alamat;
    
                    // Ubah action form untuk update produk berdasarkan ID
                    document.getElementById('editOutletForm').setAttribute('action', `/admin/kelolaoutlet/editoutlet/${id}`);
                });
            });
        });
    </script>
    @endsection
