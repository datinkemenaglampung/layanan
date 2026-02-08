@extends('layouts.master')

@section('content')
<div>
    <div class="row justify-content-center">
        <div class="col-lg-10">
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
                                    <label class="col-form-label text-md-right">Title</label>
                                    <div class="">
                                        <input type="text" name="title" id="title" class="form-control" value="{{ $data->title ?? '' }}">
                                    </div>
                                    <label class="col-form-label text-md-right">Description</label>
                                    <div class="">
                                        <input type="text" name="description" id="description" class="form-control" value="{{ $data->description ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label text-md-right">Layanan</label>
                                    <div class="">
                                        <select id="select2Layanan" name="layanan_id" class="form-control select2">
                                            @if(isset($data))
                                            <option value="{{ $data->layanan->id }}" selected>{{ $data->layanan->nama_layanan }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div id="builder"></div>

                                <input type="hidden" name="questions" id="questions">
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
        <div class="col-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Inputan</h4>
                </div>
                <div class="card-body">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-add" data-type="radio">
                            <i class="fas fa-dot-circle"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-add" data-type="checkbox">
                            <i class="fas fa-check-square"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-add" data-type="text">
                            <i class="fas fa-font"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-add" data-type="textarea">
                            <i class="fas fa-align-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(function() {

        let questions = [];

        /* =========================
            TAMBAH PERTANYAAN
        ========================== */
        $('.btn-add').on('click', function() {
            let type = $(this).data('type');

            questions.push({
                type: type,
                question: '',
                required: false,
                options: (type === 'radio' || type === 'checkbox') ? [''] : null
            });

            render();
        });

        /* =========================
            RENDER
        ========================== */
        function render() {
            let html = '';

            $.each(questions, function(i, q) {
                html += `
            <div class="card mb-3">
                <div class="card-body">

                    <input type="text" class="form-control mb-2 question"
                        data-index="${i}"
                        placeholder="Pertanyaan"
                        value="${q.question}">

                    ${renderOptions(q, i)}

                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input required"
                            data-index="${i}"
                            ${q.required ? 'checked' : ''}>
                        <label class="form-check-label">Wajib</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button"
                        class="btn btn-danger float-end"
                        data-index="${i}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
            });

            $('#builder').html(html);
        }

        function renderOptions(q, i) {
            if (!q.options) return '';

            let html = `<label class="fw-bold">Pilihan</label>`;

            $.each(q.options, function(j, opt) {
                // 🔹 pastikan ambil text
                let text = (typeof opt === 'string') ? opt : opt.text || '';

                html += `
        <div class="input-group mb-2">
            <input type="text" class="form-control option"
                data-q="${i}" data-o="${j}"
                value="${text}">
            <button type="button"
                class="btn btn-outline-danger btn-remove-option"
                data-q="${i}" data-o="${j}">
                ✕
            </button>
        </div>`;
            });

            html += `
    <button type="button"
        class="btn btn-success btn-sm btn-add-option"
        data-index="${i}">
        + Tambah Opsi
    </button>`;

            return html;
        }


        /* =========================
            EVENT INPUT
        ========================== */
        $(document).on('input', '.question', function() {
            let i = $(this).data('index');
            questions[i].question = $(this).val();
        });

        $(document).on('change', '.required', function() {
            let i = $(this).data('index');
            questions[i].required = this.checked;
        });

        $(document).on('input', '.option', function() {
            let q = $(this).data('q');
            let o = $(this).data('o');
            questions[q].options[o] = $(this).val();
        });

        /* =========================
            TAMBAH / HAPUS OPSI
        ========================== */
        $(document).on('click', '.btn-add-option', function() {
            let i = $(this).data('index');
            questions[i].options.push('');
            render();
        });

        $(document).on('click', '.btn-remove-option', function() {
            let q = $(this).data('q');
            let o = $(this).data('o');
            questions[q].options.splice(o, 1);
            render();
        });

        /* =========================
            HAPUS PERTANYAAN
        ========================== */
        $(document).on('click', '.btn-remove-question', function() {
            let i = $(this).data('index');
            questions.splice(i, 1);
            render();
        });

        $('#select2Layanan').select2({
            placeholder: "Cari Layanan",
            ajax: {
                url: "{{ route('layanan.select2') }}",
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

        /* =========================
            SUBMIT AJAX
        ========================== */
        $('#formStore').on('submit', function(e) {
            e.preventDefault();

            $('#questions').val(JSON.stringify(questions));

            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);

            $.ajax({
                ache: false,
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


        // =========================
        // LOAD DATA EDIT
        // =========================
        @if(!empty($questions) && count($questions))
        questions = @json($questions);
        render();
        @endif
    });
</script>
@endsection