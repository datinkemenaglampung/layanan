@extends('layouts.master')

@section('css')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset ('assets/modules/summernote/summernote-bs4.css') }}">
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
                            <label class="col-form-label text-md-right">Kode Satker</label>
                            <div class="">
                                <input type="text" name="kode_satker" id="kode_satker" class="form-control" value="{{ $data->kode_satker ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Nama Satker</label>
                            <div class="">
                                <input type="text" name="nama_satker" id="nama_satker" class="form-control" value="{{ $data->nama_satker ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Slug</label>
                            <div class="">
                                <input type="text" name="slug" id="slug" class="form-control" readonly value="{{ $data->slug ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Keteragan</label>
                            <div class="">
                                <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ $data->keterangan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Kode Atasan</label>
                            <div class="">
                                <input type="text" name="kode_atasan" id="kode_atasan" class="form-control" value="{{ $data->kode_atasan ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label text-md-right">Kode Grup SatKer</label>
                            <div class="">
                                <select name="kode_grup" id="kode_grup" class="form-control">
                                    <option value="">.: Pilih :.</option>
                                    <option value="20000" {{ isset($data) && $data->kode_grup == '20000' ? 'selected' : '' }}>Provinsi</option>
                                    <option value="21000" {{ isset($data) && $data->kode_grup == '21000' ? 'selected' : '' }}>Daerah</option>
                                    <option value="21100" {{ isset($data) && $data->kode_grup == '21100' ? 'selected' : '' }}>KUA</option>
                                    <option value="21200" {{ isset($data) && $data->kode_grup == '21200' ? 'selected' : '' }}>MAN</option>
                                    <option value="21300" {{ isset($data) && $data->kode_grup == '21300' ? 'selected' : '' }}>MTsN</option>
                                    <option value="21400" {{ isset($data) && $data->kode_grup == '21400' ? 'selected' : '' }}>MIN</option>
                                </select>
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
<script src="{{ asset ('assets/modules/summernote/summernote-bs4.js') }}"></script>
<script>
    $(document).ready(function() {

        function slugify(text) {
            return text
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        // Menangkap event input pada field judul
        $('#nama_satker').on('input', function() {
            var judul = $(this).val();
            var slug = slugify(judul);
            $('#slug').val(slug);
        });

        $('#select2Satker').select2({
            placeholder: "Cari Satuan Kerja",
            ajax: {
                url: "{{ route('satuan-kerja.select2') }}",
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