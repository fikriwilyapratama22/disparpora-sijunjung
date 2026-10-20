@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0" style="margin-top: 100px !important;">
        <div class="card-header mb-2 px-3">
            <div class="flex-wrap d-flex justify-content-between align-items-center">
                <div>
                    <div class="header-title">
                        <h2 class="card-title">BukuTamu</h2>
                        <p>List data tamu</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">

                        </div>

                        <div class="table-responsive">
                            <table id="layananTable" class="table table-striped">
                              <thead>
                                        <tr>
                                            <th class="text-center">Nama</th>
                                            <th class="text-center">Lokawisata</th>
                                            <th class="text-center">Alamat</th>
                                            <th class="text-center">No. Telfon</th>
                                            <th class="text-center">Pesan</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>

                       <tbody>
    @foreach ($bukutamu as $item)
        <tr>
            <td class="text-center">{{ $item->nama }}</td>
           <td>{{ $item->infoTempat->name ?? '-' }}</td>
            <td class="text-center">{{ Str::limit($item->alamat ?? '-', 50) }}</td>
            <td class="text-center">{{ $item->no_telfon }}</td>
            <td class="text-center">{{ $item->pesan }}</td>
            <td class="text-center">
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
            // Buat dan submit form DELETE secara dinamis
            const form = document.createElement('form');
            form.action = `/admin/bukutamu/${id}/destroy`;
            form.method = 'POST';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
    </script>
@endsection
