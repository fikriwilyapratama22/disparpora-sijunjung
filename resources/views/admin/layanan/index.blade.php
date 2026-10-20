@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">
        <div class="card-header mb-2 px-3">
            <div class="flex-wrap d-flex justify-content-between align-items-center">
                <div>
                    <div class="header-title">
                        <h2 class="card-title">Layanan</h2>
                        <p>List data layanan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ url('/admin/layanan/create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Tambah Layanan
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table id="layananTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nama Layanan</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Gambar</th>
                                        <th class="text-center">Dibuat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($layanan as $item)
                                        <tr>
                                            <td>{{ $item->layanan }}</td>
                                            <td>{{ Str::limit($item->deskripsi ?? '-', 50) }}</td>
                                            <td class="text-center">
                                                @if ($item->gambar)
                                                    <img src="{{ asset('layanan/' . $item->gambar) }}"
                                                        alt="{{ $item->layanan }}"
                                                        style="width:60px;height:40px;object-fit:cover;">
                                                @else
                                                    <span class="text-muted">Tidak ada</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ url('/admin/layanan/' . $item->id . '/edit') }}"
                                                    class="btn btn-sm btn-warning me-1">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="removeData({{ $item->id }})"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function removeData(id) {
        Swal.fire({
            title: "Apakah kamu yakin ingin menghapus?",
            showDenyButton: true,
            confirmButtonText: "Ya",
            denyButtonText: "Tidak",
            confirmButtonColor: '#1AA053',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/admin/layanan/' + id + '/delete';
            }
        });
    }

    // Tampilkan alert jika ada pesan sukses dari session (dari controller)
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#1AA053',
        });
    @endif
</script>

@endsection
