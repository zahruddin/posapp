@extends('layouts.app')

@section('title', 'Kelola Produk Admin | TrackBooth')

@if(isset($outlet) && !empty($outlet->nama_outlet))
    @section('page', "Kelola Produk Outlet $outlet->nama_outlet")

    @push('outlet')
        / {{ $outlet->nama_outlet }} 
    @endpush
@else
@section('page', 'Kelola Produk')
@endif


@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
    
        @include('components.alert')
        {{-- END ALERT --}}

        
        {{-- TABEL card --}}
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProduct">
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
                                        @php
                                            $gambarPath = public_path($product->gambar);
                                            $gambarURL = file_exists($gambarPath) && !empty($product->gambar) ? asset($product->gambar) : null;
                                        @endphp
                                    
                                        @if($gambarURL)
                                            <img src="{{ $gambarURL }}" class="img-thumbnail" width="50" height="50" alt="Produk">
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-image" style="font-size: 1.5rem;"></i>
                                            </span>
                                        @endif
                                    </td> 
                                    <td>Rp {{ number_format($product->harga_produk, 0, ',', '.') }}</td>
                                    <td>{{ $product->stok_produk }}</td>
                                    <td>{{ Str::limit($product->deskripsi, 50, '...') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'aktif' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.editProduct', ['id' => $product->id]) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm deleteUser deleteProduct" data-id="{{ $product->id }}">
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
                    {{ $products->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        <!-- /.TABEL card -->
        {{-- MODAL Tambah Produk --}}
        <div class="modal fade" id="modalTambahProduct" tabindex="-1" aria-labelledby="modalTambahProductLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahProductLabel">Tambah Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.tambahProduct', ['id_outlet' => $outlet->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="nama_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga_produk" class="form-label">Harga</label>
                                <input type="number" class="form-control" name="harga_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok_produk" class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambar" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="gambar" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
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
        let deleteProductId;

        // Saat tombol hapus diklik, simpan ID user
        $('.deleteUser').click(function () {
            deleteProductId = $(this).data('id');
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
                url: `/admin/kelolaoutlet/id/${outletId}/products/${deleteProductId}`,
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
