@extends('layouts.app')

@section('title', 'Dashboard Outlet | TrackBooth')

@if(isset($outlet) && !empty($outlet->nama_outlet))
    @section('page', "Dashboard Outlet $outlet->nama_outlet")
    @push('outlet')
        / {{ $outlet->nama_outlet }} 
    @endpush
@else
@section('page', 'Kelola Produk')
@endif
@section('content')
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            @php
                use Carbon\Carbon;
                $startDateFormatted = Carbon::parse($startDate)->translatedFormat('d F Y');
                $endDateFormatted = Carbon::parse($endDate)->translatedFormat('d F Y');
            @endphp
            <form action="{{ route('admin.dashboardOutlet', ['id' => $outlet->id]) }}" method="GET" class="mb-3" id="filterForm">
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
            

            <div class="row">
                <!-- Card Total Transaksi -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3>{{ $totalTransaksi }}</h3>
                            <p>Total Transaksi</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                            </path>
                        </svg>
                        <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            
                <!-- Card Total Item Terjual -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3>{{ $totalItemTerjual }}</h3>
                            <p>Total Item Terjual</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M3 3v18h18V3H3zm4 16H5v-2h2v2zm0-4H5v-2h2v2zm0-4H5V9h2v2zm0-4H5V5h2v2zm4 12H9v-2h2v2zm0-4H9v-2h2v2zm0-4H9V9h2v2zm0-4H9V5h2v2zm4 12h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 12h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2z">
                            </path>
                        </svg>
                        <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            
                <!-- Card Total Pendapatan -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                            <p>Total Pendapatan</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 1.5a10.5 10.5 0 100 21 10.5 10.5 0 000-21zm0 19.5a9 9 0 110-18 9 9 0 010 18zm0-13.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 7.5a3 3 0 110-6 3 3 0 010 6z">
                            </path>
                        </svg>
                        <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box text-bg-secondary">
                        <div class="inner">
                            <h3>Rp {{ number_format($totalPendapatanCash, 0, ',', '.') }}</h3>
                            <p>Cash</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 1.5a10.5 10.5 0 100 21 10.5 10.5 0 000-21zm0 19.5a9 9 0 110-18 9 9 0 010 18zm0-13.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 7.5a3 3 0 110-6 3 3 0 010 6z">
                            </path>
                        </svg>
                        <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="small-box text-bg-info">
                        <div class="inner">
                            <h3>Rp {{ number_format($totalPendapatanQris, 0, ',', '.') }}</h3>
                            <p>Qris</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 1.5a10.5 10.5 0 100 21 10.5 10.5 0 000-21zm0 19.5a9 9 0 110-18 9 9 0 010 18zm0-13.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 7.5a3 3 0 110-6 3 3 0 010 6z">
                            </path>
                        </svg>
                        <a href="{{ route('admin.datasales', ['id' => $outlet->id]) }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>
          
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
@endsection
@section('scripts')
<script>
    function updateFilter() {
        var selectedFilter = document.getElementById("filterType").value;

        document.querySelectorAll('.filter-group').forEach(el => el.style.display = 'none');

        if (selectedFilter === 'daily') {
            document.getElementById("dailyFilter").style.display = "block";
        } else if (selectedFilter === 'weekly') {
            document.getElementById("weeklyFilter").style.display = "block";
        } else if (selectedFilter === 'monthly') {
            document.getElementById("monthlyFilter").style.display = "block";
        }
    }
    updateFilter();
</script>
@endsection