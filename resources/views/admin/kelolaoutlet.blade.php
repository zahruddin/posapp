@extends('layouts.app')

@section('title', 'Kelola Outlet Admin | TrackBooth')
@section('page', 'Kelola Outlet')

@section('content')

  <!--end::App Content Header-->
  <!--begin::App Content-->
  <div class="app-content">
    <!--begin::Container-->
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
        <div class="mb-4">
            <button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#exampleModal">
                Tambah Outlet
            </button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Tambah Outlet</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <form action="{{ route('admin.tambahOutlet') }}" method="POST">
                          @csrf
                          <div class="modal-body">
                              <div class="form-group">
                                  <label for="nama_outlet">Nama Outlet</label>
                                  <input type="text" class="form-control" id="nama_outlet" name="nama_outlet" required>
                              </div>
                              <div class="form-group">
                                  <label for="alamat_outlet">Alamat Outlet</label>
                                  <input type="text" class="form-control" id="alamat_outlet" name="alamat_outlet" required>
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
            {{-- akhir modal --}}
        </div>
      <!--begin::Row-->
      <div class="row g-4 mb-4">
        @foreach($outlets as $outlet)
          <div class="col-md-3">
              <div class="card card-success">
                  <div class="card-header">
                      <h3 class="card-title">{{ $outlet->nama_outlet }}</h3>
                      <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                              <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                              <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                          </button>
                      </div>
                  </div>
                  <div class="card-body">
                      <p><strong>Alamat:</strong> {{ $outlet->alamat_outlet }}</p>
                      <a href="{{ route('admin.dashboardOutlet', ['id' => $outlet->id]) }}" class="btn btn-primary btn-sm">
                          Lihat Dashboard
                      </a>
                  </div>
              </div>
          </div>
        @endforeach
      </div>
    
      <!--end::Row-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::App Content-->
@endsection
