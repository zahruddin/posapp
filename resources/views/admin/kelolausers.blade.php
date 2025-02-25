@extends('layouts.app')

@section('title', 'Kelola Users Admin | TrackBooth')

@if(isset($outlets) && !empty($outlets->nama_outlet))
    @section('page', 'Kelola Users Outlet')
    @push('outlet')
        / {{ $outlets->nama_outlet }} 
    @endpush
@else
@section('page', 'Kelola Users')
@endif


@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
        @include('components.alert')
        {{-- END ALERT --}}
            
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                        Tambah User
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Nama Outlet</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
                                    <td>{{ $user->outlet->nama_outlet ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.dashboardOutlet', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                        <a href="{{ route('admin.editUser', ['id' => $user->id]) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteUser" data-id="{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>                                            
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                </div>
        
            </div>
        </div>
        <!-- /.card -->



        {{-- MODAL TAMBAH USER --}}
        <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahUserLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.tambahUser') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 6 karakter" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                @php
                                    // Ambil ID outlet dari URL (segment ke-4 dari /admin/kelolaoutlet/id/{id}/kasir)
                                    $idOutletFromUrl = Request::segment(4);
                                @endphp
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" name="role" id="role" required onchange="toggleOutletField()">
                                    @if(!$idOutletFromUrl)
                                        <option value="admin">Admin</option>
                                        <option value="kasir" selected>Kasir</option>
                                    @else
                                        <option value="kasir" selected>Kasir</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3" id="outletField">
                                <label for="id_outlet" class="form-label">Outlet</label>
                                <select class="form-select" name="id_outlet">
                                    @if($idOutletFromUrl)
                                        <option value="{{ $idOutletFromUrl }}" selected>
                                            {{ $outlets->where('id', $idOutletFromUrl)->first()->nama_outlet ?? 'Outlet Tidak Ditemukan' }}
                                        </option>
                                    @else
                                        @foreach($outlets as $outlet)
                                            <option value="{{ $outlet->id }}" {{ ($idOutletFromUrl == $outlet->id) ? 'selected' : '' }}>
                                                {{ $outlet->nama_outlet }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
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
        <!-- Modal Konfirmasi Hapus -->
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": true,            // Aktifkan pagination
            "lengthChange": false,     // Hilangkan opsi "show entries"
            "searching": true,         // Aktifkan pencarian
            "ordering": true,          // Aktifkan sorting
            "info": true,              // Tampilkan info jumlah data
            "autoWidth": false,        // Nonaktifkan auto width
            "responsive": true,        // Aktifkan mode responsif
            "language": {
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                },
                "search": "Cari:",
                "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ pengguna",
                "infoEmpty": "Tidak ada data",
                "lengthMenu": "Tampilkan _MENU_ pengguna per halaman"
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        let deleteUserId;

        // Saat tombol hapus diklik, simpan ID user
        $('.deleteUser').click(function () {
            deleteUserId = $(this).data('id');
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
                url: "/admin/kelolaUsers/hapususer/" + deleteUserId,
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
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var icon = document.getElementById("toggleIcon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
<script>
    function toggleOutletField() {
        var role = document.getElementById('role').value;
        var outletField = document.getElementById('outletField');

        if (role === 'kasir') {
            outletField.style.display = 'block';
        } else {
            outletField.style.display = 'none';
        }
    }

    // Pastikan input outlet tampil saat modal pertama kali dibuka (karena default-nya kasir)
    document.addEventListener("DOMContentLoaded", function () {
        toggleOutletField();
    });

    function togglePassword() {
        var passwordField = document.getElementById("password");
        var toggleIcon = document.getElementById("toggleIcon");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
</script>
@endsection
