@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="alert alert-warning text-center" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                Konfigurasi yang salah dapat menyebabkan persyaratan tidak dapat akses masuk ke halaman
            </div>
        </div>
        <div class="col-sm-6 col-lg-6">
            <form id="changeHierarchy" class="formStore" action="{{ route('list-persyaratan.changeHierarchy') }}">
                @method('POST')
                @csrf
                <div class="card">
                    <div class="card-header justify-content-between">
                        <div class="header-title">
                            <div class="row">
                                <h4 class="card-title">List Persyaratan </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dd" id="menuList">
                            {!! $sortable !!}
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <input type="hidden" id="output" name="hierarchy" />
                        <button type="submit" class="btn btn-sm btn-warning" style="display: none"><i class="fa-solid fa-floppy-disk"></i> Ubah
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-6 col-lg-6">
            <form id="formMenumanager" action="{{ $config['form']->action }}">
                @method($config['form']->method)
                @csrf
                <div class="card">
                    <div class="card-header justify-content-between ">
                        <div class="header-title">
                            <div class="row">
                                <h4 class="card-title">Tambah Persyaratan </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="layanan_id" value="{{ $config['layanan_id'] }}">
                        <select id="select2Persyaratan" name="persyaratan_id" class="form-control select2">
                        </select>
                        <hr>
                        <label for="">Wajib isi</label>
                        <select name="wajib" id="wajib" class="form-control">
                            <option value="">.: Pilih :.</option>
                            <option value="1"> Ya </option>
                            <option value="0"> Tidak </option>
                        </select>
                        <hr>
                        <label for="">Level Persyaratan</label>
                        <select name="uploaded_level" id="uploaded_level" class="form-control">
                            <option value="">.: Pilih :.</option>
                            <option value="1"> Admin </option>
                            <option value="0"> Semua </option>
                        </select>
                    </div>
                    <div class="card-footer justify-content-between border-top">
                        <button type="submit" class="btn btn-primary float-end">Tambah <i class="fas fa-floppy-disk"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $('#select2Persyaratan').select2({
            placeholder: "Cari Persyaratan",
            ajax: {
                url: "{{ route('persyaratan.select2') }}",
                dataType: "json",
                cache: true,
                data: function(e) {
                    return {
                        q: e.term || '',
                        page: e.page || 1
                    }
                },
                processResults: function(data) {
                    // Menyesuaikan hasil untuk menambahkan atribut aria-selected
                    var results = data.results.map(function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            element: $('<li>', {
                                'class': 'select2-results__option',
                                'id': 'select2-siqa-result-' + item.id,
                                'role': 'treeitem',
                                'aria-selected': item.ariaSelected,
                                'text': item.text
                            })
                        };
                    });
                    return {
                        results: results
                    };
                }
            },
        });


        $('#menuList').nestable({
            maxDepth: 1
        }).on('change', function() {
            let json_values = window.JSON.stringify($(this).nestable('serialize'));
            $("#output").val(json_values);
            $("#changeHierarchy [type='submit']").fadeIn();
        }).nestable('collapseAll');



        $("#formMenumanager").submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);
            $.ajax({
                cache: false,
                processData: false,
                contentType: false,
                type: "POST",
                url: url,
                data: data,
                beforeSend: function() {
                    btnSubmit.addClass("disabled").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop("disabled", "disabled");
                },
                success: function(response) {
                    let errorCreate = $('#errorCreate');
                    errorCreate.css('display', 'none');
                    errorCreate.find('.alert-text').html('');
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    if (response.status === "success") {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });
                        setTimeout(function() {
                            if (response.redirect === "" || response.redirect === "reload") {
                                location.reload();
                            } else {
                                location.href = response.redirect;
                            }
                        }, 1000);
                    } else {
                        iziToast.error({
                            title: 'error',
                            message: response.message,
                            position: 'topRight'
                        });
                    }
                },
                error: function(response) {
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    iziToast.error(response.responseJSON.message, 'Failed !');
                }
            });
        });

        $("#changeHierarchy").submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);
            $.ajax({
                beforeSend: function() {
                    btnSubmit.addClass("disabled").html("<i class='bx bx-hourglass bx-spin font-size-16 align-middle me-2'></i> Loading ...").prop("disabled", "disabled");
                },
                cache: false,
                processData: false,
                contentType: false,
                type: "POST",
                url: url,
                data: data,
                success: function(response) {
                    let errorCreate = $('#errorCreate');
                    errorCreate.css('display', 'none');
                    errorCreate.find('.alert-text').html('');
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    if (response.status === "success") {
                        iziToast.success({
                            title: 'Success',
                            message: response.message,
                            position: 'topRight'
                        });
                        setTimeout(function() {
                            if (!response.redirect || response.redirect === "reload") {
                                location.reload();
                            } else {
                                location.href = response.redirect;
                            }
                        }, 1000);
                    } else {
                        $.each(response.error, function(key, value) {
                            errorCreate.css('display', 'block');
                            errorCreate.find('.alert-text').append('<span style="display: block">' + value + '</span>');
                        });
                        iziToast.error({
                            title: 'Failed',
                            message: response.message,
                            position: 'topRight'
                        });
                    }
                },
                error: function(response) {
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    iziToast.error({
                        title: 'Failed!',
                        message: response.responseJSON.message,
                        position: 'topRight'
                    });
                }
            });
        });

        $(".btn-delete").click(function(e) {
            let pk = $(this).data('id'),
                pkp = $(this).data('idp'),
                url = `{{ route("layanan.index") }}/deletePersyaratan/` + pk + `/` + pkp;
            console.log(pk);
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
                                location.reload();
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
    });
</script>
@endsection