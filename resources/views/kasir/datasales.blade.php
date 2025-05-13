@extends('layouts.app')
@section('title', 'Data Penjualan | TrackBooth')
@section('page', 'Data Penjualan')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        {{-- ALERT --}}
    
        @include('components.alert')
        {{-- END ALERT --}}

        
        {{-- TABEL card --}}

            <div class="table-container">
                <div class="table-responsive">
                    @php
                        use Carbon\Carbon;
                        $tanggalFormatted = Carbon::parse($tanggal)->translatedFormat('d F Y');
                    @endphp
                    <form action="{{ route('kasir.datasales') }}" method="GET" class="mb-3" id="filterForm">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="date" name="tanggal" id="filterTanggal" class="form-control"
                                    value="{{ request('tanggal', \Carbon\Carbon::today()->format('Y-m-d')) }}" onchange="document.getElementById('filterForm').submit();">
                                <h2 class="form-text text-muted">Tanggal dipilih: <strong>{{ $tanggalFormatted }}</strong></h2>
                            </div>
                        </div>
                    </form>
                    <table class="table table-hover table-borderless align-middle">
                        <thead class="table">
                            <tr>
                                <th>No</th>
                                <th>Outlet</th>
                                <th>Tanggal</th>
                                <th>Total Harga</th>
                                <th>Diskon</th>
                                <th>Total Bayar</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $sales->firstItem(); @endphp
                            @foreach($sales as $sale)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td><strong>{{ $sale->outlet->nama_outlet }}</strong></td>
                                    <td>{{ $sale->created_at->format('d-m-Y H:i') }}</td>
                                    <td><span class="text-success">Rp {{ number_format($sale->total_harga, 0, ',', '.') }}</span></td>
                                    <td><span class="text-danger">Rp {{ number_format($sale->total_diskon, 0, ',', '.') }}</span></td>
                                    <td><strong>Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($sale->metode_bayar) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status_bayar == 'lunas' ? 'success' : ($sale->status_bayar == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($sale->status_bayar) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-detail" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $sale->id }}" aria-expanded="false">
                                            <i data-feather="eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                <!-- Detail Produk -->
                                <tr class="collapse fade" id="detail-{{ $sale->id }}">
                                    <td colspan="9">
                                        <div class="p-2 rounded">
                                            <h6 class="mb-2">🛒 Detail Produk {{ $sale->created_at->format('d-m-Y H:i') }}</h6>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr class="table">
                                                        <th>Nama Produk</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Subtotal</th>
                                                        <th>Diskon</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sale->details as $detail)
                                                        <tr>
                                                            <td>{{ $detail->nama_produk }}</td>
                                                            <td>Rp {{ number_format($detail->harga_produk, 0, ',', '.') }}</td>
                                                            <td>{{ $detail->jumlah }}</td>
                                                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                            <td>Rp {{ number_format($detail->diskon, 0, ',', '.') }}</td>
                                                            <td><strong>Rp {{ number_format($detail->total, 0, ',', '.') }}</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $sales->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        
           
        <!-- /.TABEL card -->
        
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
@section('style')
<style>
    /* Efek Hover untuk Tombol Detail */
    .btn-detail:hover {
        background-color: #0d6efd;
        transform: scale(1.05);
        transition: 0.3s ease-in-out;
    }
    /* Efek Bayangan untuk Tabel */
    .table-container {
        border-radius: 10px;
    }
    /* Garis pemisah lebih soft */
    .table td, .table th {
        vertical-align: middle;
        /* border-bottom: 1px solid #eaeaea; */
    }
</style>
@endsection
