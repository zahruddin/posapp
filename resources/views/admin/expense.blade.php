@extends('layouts.app')

@section('title', "Data Pengeluaran $outlet->nama_outlet | TrackBooth")

@if(isset($outlet) && !empty($outlet->nama_outlet))
    @section('page', "Data Pengeluaran Outlet $outlet->nama_outlet")

    @push('outlet')
        / {{ $outlet->nama_outlet }} 
    @endpush
@else
@section('page', 'Kelola Produk')
@endif

@section('content')
<div class="app-content">
    <div class="container-fluid">
        @php
            use Carbon\Carbon;
            $startDateFormatted = Carbon::parse($startDate)->translatedFormat('d F Y');
            $endDateFormatted = Carbon::parse($endDate)->translatedFormat('d F Y');
        @endphp
        <form action="{{ route('admin.expense', ['id' => $outlet->id]) }}" method="GET" class="mb-3" id="filterForm">
            <div class="row g-2">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ request('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="{{ request('end_date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
        <h2 class="form-text text-muted">
            Menampilkan data dari: <strong>{{ $startDateFormatted }}</strong> 
            hingga <strong>{{ $endDateFormatted }}</strong>
        </h2>
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
                                <th>Kategori</th> 
                                <th>User</th> 
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
                                    <td>{{ $expense->user->name ?? '-' }}</td> {{-- Tampilkan nama kategori --}}
                                    <td>{{ \Carbon\Carbon::parse($expense->datetime)->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm deleteExpense" data-id="{{ $expense->id }}">
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
                    <form action="{{ route('admin.expenses.store', ['id_outlet' => $outlet->id]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="biaya" class="form-label">Biaya</label>
                                <input type="number" class="form-control" name="biaya" max="10000000"  required>
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

        <!-- Modal Konfirmasi Hapus -->
        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus Pengeluaran ini?
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
        let deleteExepenseId;

        // Saat tombol hapus diklik, simpan ID user
        $('.deleteExpense').click(function () {
            deleteExepenseId = $(this).data('id');
            $('#confirmDeleteModal').modal('show');
        });
        $('#bataldelete').click(function () {
            $('#confirmDeleteModal').modal('hide');
        });
        $('.close').click(function () {
            $('#confirmDeleteModal').modal('hide');
        });

        // Saat tombol konfirmasi di modal diklik
        // let outletId = "{{ Request::segment(4) }}"; 
        let outletId = "{{ $outlet->id }}";
        // console.log("ID Outlet:", idout); // Cek apakah id_outlet sudah benar
        $('#confirmDeleteBtn').click(function () {
            $.ajax({
                url: `/admin/expenses/id/${outletId}/${deleteExepenseId}`,
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