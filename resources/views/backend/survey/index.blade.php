@extends('layouts.master')


@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Data {{$config['title']}} </h4>
                    </div>
                    <div class="">
                        <a href="{{ route('survey.create') }}" class="btn btn-primary float-end">
                            <i class="fas fa-plus"></i> Tambah
                        </a>
                    </div>
                    <a href="http://" target="_blank" rel="noopener noreferrer"></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Description</th>
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
                url: `{{ route('survey.index') }}`
            },
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            pageLength: 10,
            columns: [{
                    data: 'title',
                    name: 'title',
                },
                {
                    data: 'description',
                    name: 'description',
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
                        url = `{{ route("survey.index") }}/` + pk;
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