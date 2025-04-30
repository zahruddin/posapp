@extends('layouts.app')
@section('title', 'Pengeluaran | TrackBooth')
@section('page', 'Pengeluaran')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
        @include('components.alert')
        {{-- END ALERT --}}

        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPengeluaran">
                        Tambah Pengeluaran
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Biaya</th>
                                <th>Keterangan</th>
                                <th>Kategori</th> {{-- Tambahan --}}
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $index => $expense)
                                <tr>
                                    <td>{{ $expenses->firstItem() + $index }}</td>
                                    <td>Rp {{ number_format($expense->biaya, 0, ',', '.') }}</td>
                                    <td>{{ $expense->keterangan ?? '-' }}</td>
                                    <td>{{ $expense->category->nama_kategori ?? '-' }}</td> {{-- Tampilkan nama kategori --}}
                                    <td>{{ \Carbon\Carbon::parse($expense->datetime)->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-warning btn-sm edit-btn"
                                           data-bs-toggle="modal"
                                           data-bs-target="#editExpenseModal"
                                           data-id="{{ $expense->id }}"
                                           data-biaya="{{ $expense->biaya }}"
                                           data-keterangan="{{ $expense->keterangan }}"
                                           data-datetime="{{ $expense->datetime }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-expense" data-id="{{ $expense->id }}">
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
                    {{ $expenses->links('vendor.pagination.bootstrap-5') }}
                </div>
        
            </div>
        </div>
        <!-- /.card -->



        {{-- MODAL tambah pengeluaran --}}
        <div class="modal fade" id="modalTambahPengeluaran" tabindex="-1" aria-labelledby="modalTambahPengeluaranLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahPengeluaranLabel">Tambah Pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('kasir.expenses.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="biaya" class="form-label">Biaya</label>
                                <input type="number" class="form-control" name="biaya" required>
                            </div>
        
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                            </div>
        
                            {{-- <div class="mb-3">
                                <label for="datetime" class="form-label">Tanggal & Waktu</label>
                                <input type="datetime-local" class="form-control" name="datetime" required>
                            </div> --}}
        
                            <div class="mb-3">
                                <label for="kategori_pengeluaran_id" class="form-label">Kategori Pengeluaran</label>
                                <select name="expense_category_id" class="form-select" id="expense_category_id" onchange="toggleInputKategoriBaru()">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategori_pengeluaran as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                    <option value="tambah-baru">+ Tambah Kategori Baru</option>
                                </select>
                            </div>
        
                            <div class="mb-3 d-none" id="inputKategoriBaru">
                                <label for="kategori_baru" class="form-label">Kategori Baru</label>
                                <input type="text" class="form-control" name="kategori_baru" placeholder="Masukkan nama kategori baru">
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
                        <h5 class="modal-title" id="modalUpdateUserLabel">Update Pengeluaran</h5>
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
                        Apakah Anda yakin ingin menghapus data ini?
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
    function toggleInputKategoriBaru() {
        const select = document.getElementById('expense_category_id');
        const inputBaru = document.getElementById('inputKategoriBaru');

        if (select.value === 'tambah-baru') {
            inputBaru.classList.remove('d-none');
        } else {
            inputBaru.classList.add('d-none');
        }
    }
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
