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
                        <a href="{{ route('bidang.create') }}" class="btn btn-primary float">
                            <i class="fas fa-plus"></i> Tambah
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
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
                url: `{{ route('bidang.index') }}`
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            pageLength: 10,
            columns: [{
                    data: 'nama_bidang',
                    name: 'nama_bidang',
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi',
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
                        url = `{{ route("bidang.index") }}/` + pk;
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