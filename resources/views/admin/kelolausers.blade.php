@extends('layouts.app')

@section('title', 'Kelola Users Admin | TrackBooth')

@if(isset($outlets) && !empty($outlets->nama_outlet))
    @section('page', "Kelola Users Outlet $outlets->nama_outlet")
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
                                        <a href="{{ route('admin.dashboardKasir', ['id' => $outlet->id, 'id_user' => $user->id, ]) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-speedometer2"></i>
                                        </a>
                                        <a href="#" class="btn btn-warning btn-sm edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editusermodal" 
                                        data-id="{{ $user->id }}" 
                                        data-nama="{{ $user->name }}" 
                                        data-username="{{ $user->username }}" 
                                        data-email="{{ $user->email }}" 
                                        data-outlet="{{ $user->id_outlet }}"
                                        data-role="{{ $user->role }}"
                                        >
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
        {{-- update user --}}
        <div class="modal fade" id="editusermodal" tabindex="-1" aria-labelledby="editusermodal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUpdateUserLabel">Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="edit_nama" id="edit_nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="edit_username" id="edit_username" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="edit_email" id="edit_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Password  <span class="text-secondary"><i>(kosongkan jika tidak ingin mengubah)</i></span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="edit_password" id="edit_password" placeholder="Minimal 6 karakter">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordEdit()">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_role" class="form-label">Role</label>
                                <select class="form-select" name="edit_role" id="edit_role" required onchange="toggleOutletFieldEdit()">
                                    <option value="admin">Admin</option>
                                    <option value="kasir" selected>Kasir</option>
                                </select>
                            </div>
                            <div class="mb-3" id="outletFieldEdit">
                                <label for="edit_id_outlet" class="form-label">Outlet</label>
                                <select class="form-select" name="edit_id_outlet" id="edit_id_outlet">
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->nama_outlet }}</option>
                                    @endforeach
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                let id = this.getAttribute('data-id');
                let nama = this.getAttribute('data-nama');
                let username = this.getAttribute('data-username');
                let email = this.getAttribute('data-email');
                let outlet = this.getAttribute('data-outlet');
                let role = this.getAttribute('data-role');

                // Isi nilai form dengan data user yang diklik
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_email').value = email;

                // Set role dropdown
                let roleSelect = document.getElementById('edit_role');
                roleSelect.value = role;

                // Atur nilai outlet jika bukan admin
                let outletFieldEdit = document.getElementById('outletFieldEdit');
                let outletSelect = document.getElementById('edit_id_outlet');
                
                if (role === "admin") {
                    outletFieldEdit.style.display = "none"; // Sembunyikan field outlet
                } else {
                    outletFieldEdit.style.display = "block"; // Tampilkan field outlet
                    outletSelect.value = outlet; // Set outlet sesuai user
                }

                // Ubah action form untuk update berdasarkan ID user
                document.querySelector('#editusermodal form').setAttribute('action', `/admin/kelolauser/update/${id}`);
            });
        });

        // Event listener untuk toggle field outlet saat role berubah
        document.getElementById('edit_role').addEventListener('change', function () {
            let outletFieldEdit = document.getElementById('outletFieldEdit');
            if (this.value === "admin") {
                outletFieldEdit.style.display = "none";
            } else {
                outletFieldEdit.style.display = "block";
            }
        });
    });

    // Fungsi untuk toggle visibility password
    function togglePasswordEdit() {
        let passwordInput = document.getElementById("edit_password");
        let toggleIcon = document.getElementById("toggleIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("bi-eye");
            toggleIcon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("bi-eye-slash");
            toggleIcon.classList.add("bi-eye");
        }
    }
</script>


@endsection
