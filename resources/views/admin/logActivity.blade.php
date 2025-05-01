@extends('layouts.app')

@section('title', 'Log Activity | TrackBooth')


@section('page', 'Log Activity')

@section('content')
<div class="app-content">
    <div class="container-fluid">
        @php
            use Carbon\Carbon;
            $startDateFormatted = Carbon::parse($startDate)->translatedFormat('d F Y');
            $endDateFormatted = Carbon::parse($endDate)->translatedFormat('d F Y');
        @endphp
        <form action="{{ route('admin.log') }}" method="GET" class="mb-3" id="filterForm">
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Action</th>
                                <th>Table Name</th>
                                <th>Record ID</th>
                                <th>Description</th>
                                <th>Kasir</th> <!-- Kolom untuk menampilkan nama kasir -->
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->table_name }}</td>
                                    <td>{{ $log->record_id }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>{{ $log->user ? $log->user->name : 'Unknown' }}</td> <!-- Menampilkan nama kasir -->
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No activity logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        
                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $logs->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
        
        <!-- /.card -->
        
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



@endsection
