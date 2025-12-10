@extends('layouts.master')


@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title mb-0">
                            <h4 class="card-title mb-0">Data {{ $config['title'] }}</h4>
                        </div>
                        <button type="button" class="btn btn-primary float" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Tambah Permohonan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Nama Layanan</th>
                                    <th>Nama Pemohon</th>
                                    <th>Status</th>
                                    <th data-priority="1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $('#dataTable').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('permohonan.index') }}`
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            pageLength: 10,
            columns: [{
                    data: 'layanan.nama_layanan',
                    name: 'layanan.nama_layanan',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, full, meta) {
                        if (data == 'menunggu') {
                            return '<span class="badge bg-info">' + data + '</span>'
                        } else if (data == 'diproses') {
                            return '<span class="badge bg-warning">' + data + '</span>'
                        } else if (data === 'ditolak') {
                            return '<span class="badge bg-danger">' + data + '</span>'
                        } else {
                            return '<span class="badge bg-success">' + data + '</span>'
                        }
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
                $(row).find('.btn-delete').click(function() {
                    let pk = $(this).data('id'),
                        url = `{{ route("permohonan.index") }}/` + pk;
                    swal({
                        title: "Anda Yakin ?",
                        text: "Data tidak dapat dikembalikan setelah di hapus!",
                        icon: "warning",
                        buttons: {
                            cancel: {
                                text: "Tidak, Batalkan ",
                                value: false,
                                visible: true,
                                className: "btn-cancel", // Tambahkan kelas CSS kustom jika diperlukan
                                closeModal: true,
                            },
                            confirm: {
                                text: "Ya, Hapus",
                                value: true,
                                visible: true,
                                className: "btn-confirm", // Tambahkan kelas CSS kustom jika diperlukan
                                closeModal: true,
                            },
                        },
                        dangerMode: true,
                    }).then((result) => {
                        if (result == true) {
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE',
                                },
                                error: function(response) {
                                    iziToast.error({
                                        title: 'error',
                                        message: 'Failed!',
                                        position: 'topRight'
                                    });
                                },
                                success: function(response) {
                                    if (response.status === "success") {
                                        iziToast.success({
                                            title: 'Success',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                        api.draw();
                                    } else {
                                        iziToast.error({
                                            title: 'error',
                                            message: response.message,
                                            position: 'topRight'
                                        });
                                    }
                                }
                            });
                        }
                    });
                });
            }
        });

    });
</script>
@endsection
@section('modal')
{{--Modal--}}
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Layanan</h5>
                <button type="button" class="ms-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
        </div>
    </div>
</div>
@endsection