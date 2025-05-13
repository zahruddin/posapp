@extends('layouts.app')

@section('title', "Data Penjualan $outlet->nama_outlet | TrackBooth")

@if(isset($outlet) && !empty($outlet->nama_outlet))
    @section('page', "Data Penjualan Outlet $outlet->nama_outlet")

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

            <div class="table-container">
                <div class="table-responsive">
                    @php
                        use Carbon\Carbon;
                        $startDateFormatted = Carbon::parse($startDate)->translatedFormat('d F Y');
                        $endDateFormatted = Carbon::parse($endDate)->translatedFormat('d F Y');
                    @endphp
                    <form action="{{ route('admin.seduh', ['id' => $outlet->id]) }}" method="GET" class="mb-3" id="filterForm">
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
                    <table class="table table-hover table-borderless align-middle">
                        <thead class="table">
                            <tr>
                                <th>No</th>
                                <th>Outlet</th>
                                <th>jumlah Seduh</th>
                                <th>Keterangan</th>
                                <th>Kasir</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $laporanseduh->firstItem(); @endphp
                            @foreach($laporanseduh as $seduh)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td><strong>{{ $seduh->outlet->nama_outlet }}</strong></td>
                                    <td><strong>{{ $seduh->seduh }}</strong></td>
                                    <td>{{ $seduh->keterangan ?? '-' }}</td>
                                    <td><strong>{{ $seduh->user->name }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($seduh->created_at)->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-seduh" 
                                                data-id="{{ $seduh->id }}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmDeleteModal">
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
                    {{ $laporanseduh->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        
           
        <!-- /.TABEL card -->
        
        {{-- MODAL Konfirmasi Hapus --}}
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus data ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </form>
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
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-seduh');
        const deleteForm = document.getElementById('deleteForm');
        let outletId = "{{ $outlet->id }}";
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const seduhId = this.getAttribute('data-id');
                deleteForm.setAttribute('action', `/admin/kelolaoutlet/id/${outletId}/laporanpenyeduhan/${seduhId}`);
            });
        });
    });
</script>

@endsection
@section('style')

@endsection
