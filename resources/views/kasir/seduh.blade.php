@extends('layouts.app')
@section('title', 'Seduh | TrackBooth')
@section('page', 'Seduh')
@section('content')
<div class="app-content">
    <div class="container-fluid">
        @php
            use Carbon\Carbon;
            $startDateFormatted = Carbon::parse($startDate)->translatedFormat('d F Y');
            $endDateFormatted = Carbon::parse($endDate)->translatedFormat('d F Y');
        @endphp
        <form action="{{ route('kasir.seduh') }}" method="GET" class="mb-3" id="filterForm">
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
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambalLaporanseduh">
                        Tambah Laporan Seduh
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jumlah Seduh</th>
                                <th>Keterangan</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporanseduh as $index => $seduh)
                                <tr>
                                    <td>{{ $laporanseduh->firstItem() + $index }}</td>
                                    <td>{{ number_format($seduh->seduh, 1, ',', '.') }}</td>
                                    <td>{{ $seduh->keterangan ?? '-' }}</td>
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
                    {{ $laporanseduh->links('vendor.pagination.bootstrap-5') }}
                </div>
        
            </div>
        </div>
        <!-- /.card -->



        {{-- MODAL tambah pengeluaran --}}
        <div class="modal fade" id="modalTambalLaporanseduh" tabindex="-1" aria-labelledby="modalTambahLaporanSeduh" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLaporanSeduh">Tambah Laporan Seduh</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('kasir.seduh.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="seduh" class="form-label">Jumlah Seduh</label>
                                <input type="number" class="form-control" id="seduh" name="seduh" min="0" step="0.5" required>
                                <div class="invalid-feedback" id="seduh-feedback" style="display:none;">
                                    Nilai harus kelipatan 0.5 (misal: 1, 1.5, 2, 2.5, dst)
                                </div>
                            </div>
        
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3" required></textarea>
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

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const seduhId = this.getAttribute('data-id');
                deleteForm.setAttribute('action', `/kasir/laporanseduh/${seduhId}`);
            });
        });
    });
</script>

<script>
    const seduhInput = document.getElementById('seduh');
    const feedback = document.getElementById('seduh-feedback');

    seduhInput.addEventListener('input', function () {
        const value = parseFloat(this.value);
        const isValid = value * 10 % 5 === 0;

        if (!isValid) {
            this.classList.add('is-invalid');
            feedback.style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            feedback.style.display = 'none';
        }
    });
</script>
@endsection
