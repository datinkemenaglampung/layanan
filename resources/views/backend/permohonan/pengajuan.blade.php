@extends('layouts.master')

@section('css')
<!-- CSS Libraries -->
<!-- <link rel="stylesheet" href="{{ asset ('assets/modules/summernote/summernote-bs4.css') }}"> -->
@endsection

@section('content')
<div>
    <form id="formStore" action="{{ $config['form']->action }}" method="POST" enctype="multipart/form-data">
        @method($config['form']->method)
        @csrf

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4>{{ $layanan->nama_layanan }}</h4>
                    </div>

                    <div class="card-body">

                        <!-- Deskripsi -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <strong>Deskripsi</strong>
                                <p class="text-muted mb-0">{{ $layanan->deskripsi }}</p>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2">Persyaratan yang harus dilengkapi:</h6>
                        <input type="hidden" name="layanan_id" value="{{ $layanan->id }}">

                        <!-- Grid persyaratan -->
                        <div class="row">
                            @foreach($layanan->persyaratans as $persyaratan)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100">

                                    <label class="mb-1 fw-semibold">
                                        {!! $persyaratan->nama_persyaratan !!}
                                        @if($persyaratan->pivot->wajib == 1)
                                        <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    <input
                                        type="file"
                                        name="files[{{ $persyaratan->id }}]"
                                        class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        {{ $persyaratan->pivot->wajib == 1 ? 'required' : '' }}>

                                    @if($persyaratan->deskripsi)
                                    <small class="text-muted d-block mt-1">
                                        {{ $persyaratan->deskripsi }}
                                    </small>
                                    @endif

                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Keterangan tambahan -->
                        <div class="form-group mt-3">
                            <label class="fw-semibold">Keterangan Tambahan (opsional)</label>
                            <textarea name="keterangan" class="form-control" placeholder="Tuliskan catatan tambahan..." rows="3"></textarea>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a onclick="history.back()" class="btn btn-warning">
                            <i class="fas fa-window-close"></i> Batal
                        </a>
                        <button class="btn btn-info" type="submit">
                            <i class="fas fa-save"></i> Simpan
                        </button>
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

        function slugify(text) {
            return text
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        // Menangkap event input pada field judul
        $('#nama_layanan').on('input', function() {
            var judul = $(this).val();
            var slug = slugify(judul);
            $('#slug').val(slug);
        });

        $('#select2Bidang').select2({
            placeholder: "Cari Bidang",
            ajax: {
                url: "{{ route('bidang.select2') }}",
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