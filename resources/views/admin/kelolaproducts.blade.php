@extends('layouts.app')

@section('title', 'Kelola Produk Admin | TrackBooth')

@if(isset($outlets) && !empty($outlets->nama_outlet))
    @section('page', 'Kelola Produk Outlet')
    @push('outlet')
        / {{ $outlets->nama_outlet }} 
    @endpush
@else
@section('page', 'Kelola Produk')
@endif


@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        {{-- END ALERT --}}

        {{-- MODAL tambah user --}}
        <div class="modal fade" id="modalTambahProduct" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Produk</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.tambahProduct') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="text" class="form-control" name="nama_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_produk">Harga</label>
                                <input type="number" class="form-control" name="harga_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="stok_produk">Stok</label>
                                <input type="number" class="form-control" name="stok_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="gambar">Gambar Produk</label>
                                <input type="file" class="form-control-file" name="gambar" id="gambarInput" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                                <div class="mt-2">
                                    <img id="gambarPreview" src="#" alt="Preview Gambar" class="d-none rounded img-thumbnail" width="100">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        {{-- </div> --}}
        {{-- end modal tambah user --}}
        {{-- card --}}
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahProduct">
                        Tambah Product
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Gambar</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = $products->firstItem(); // Ambil nomor item pertama di halaman ini
                            @endphp
                            @foreach($products as $index => $product)
                                <tr>
                                    <td>{{ $no++ }}</td> {{-- Nomor akan bertambah setiap iterasi --}}
                                    <td>{{ $product->nama_produk }}</td>
                                    <td>
                                        @if($product->gambar)
                                            <img src="{{ asset('storage/' . $product->gambar) }}" class="img-thumbnail" width="50" height="50" alt="Produk">
                                        @else
                                            <span class="text-muted">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                                    <td>{{ $product->stok_produk }}</td>
                                    <td>{{ Str::limit($product->deskripsi, 50, '...') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->status == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.editProduct', ['id' => $product->id]) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteProduct" data-id="{{ $product->id }}">
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
                    {{ $products->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
        {{-- <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahProduct">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
            </div>
            <div class="card-body">
                <table id="tableProducts" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Gambar</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->nama_produk }}</td>
                                <td>
                                    @if($product->gambar)
                                        <img src="{{ asset('storage/' . $product->gambar) }}" class="img-thumbnail" width="50" height="50" alt="Produk">
                                    @else
                                        <span class="text-muted">Tidak Ada</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                                <td>{{ $product->stok_produk }}</td>
                                <td>{{ Str::limit($product->deskripsi, 50, '...') }}</td>
                                <td>
                                    <span class="badge badge-{{ $product->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.editProduct', ['id' => $product->id]) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm deleteProduct" data-id="{{ $product->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>                                            
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div> --}}
        <!-- /.card -->
        {{-- modal notif konfirmasi delete --}}
        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus pengguna ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="bataldelete" data-dismiss="modal">Batal</button>
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
    document.getElementById('gambarInput').addEventListener('change', function(event) {
        let input = event.target;
        let preview = document.getElementById('gambarPreview');
        
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
</script>
@endsection
