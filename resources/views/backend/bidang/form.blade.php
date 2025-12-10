@extends('layouts.master')

@section('css')
<!-- CSS Libraries -->
<!-- <link rel="stylesheet" href="{{ asset ('assets/modules/summernote/summernote-bs4.css') }}"> -->
@endsection

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST">
        @method($config['form']->method)
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{$config['title']}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Nama</label>
                            <div class="">
                                <input type="text" name="nama_bidang" id="nama_bidang" class="form-control" value="{{ $data->nama_bidang ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Deskripsi</label>
                            <div class="">
                                <textarea name="deskripsi" id="deskripsi" class="form-control">{{ $data->deskripsi ?? ''}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a onclick="history.back()" class="btn btn-warning"><i class="fas fa-window-close"></i> Batal</a>
                        <div class="float-end">
                            <button class="btn btn-info" type="submit"><i class="fas fa-save"></i> Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('script')
<!-- JS Libraies -->
<!-- <script src="{{ asset ('assets/modules/summernote/summernote-bs4.js') }}"></script> -->
<script>
    $(document).ready(function() {

        $("#formStore").submit(function(e) {
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
                    toastr.error(response.responseJSON.message, 'Failed !');
                }
            });
        });

        $(".image").change(function() {
            let thumb = $(this).parent().find('img');
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    thumb.attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection