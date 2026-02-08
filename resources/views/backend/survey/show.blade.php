@extends('layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Header Survey --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title">{{ $survey->title }}</h2>
                @if($survey->description)
                <p class="card-text text-muted">{{ $survey->description }}</p>
                @endif
            </div>
        </div>

        {{-- Form Survey --}}
        <form id="formSurvey"
            action="{{ route('survey.submit', $survey->id) }}"
            method="POST">
            @csrf
            <input type="hidden" name='kode_satker' value="{{ $config['kode_satker'] }}">
            @foreach($survey->questions as $q)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">

                    {{-- Pertanyaan --}}
                    <label class="form-label fw-bold">
                        {{ $q->question }}
                        @if($q->is_required)
                        <span class="text-danger">*</span>
                        @endif
                    </label>

                    {{-- TEXT --}}
                    @if($q->type === 'text')
                    <input type="text"
                        name="answers[{{ $q->id }}]"
                        class="form-control"
                        placeholder="Jawaban Anda"
                        {{ $q->is_required ? 'required' : '' }}>

                    {{-- TEXTAREA --}}
                    @elseif($q->type === 'textarea')
                    <textarea name="answers[{{ $q->id }}]"
                        class="form-control"
                        rows="3"
                        placeholder="Jawaban Anda"
                        {{ $q->is_required ? 'required' : '' }}></textarea>

                    {{-- RADIO --}}
                    @elseif($q->type === 'radio')
                    <div class="mt-2">
                        @foreach($q->options as $opt)
                        <div class="form-check mb-1">
                            <input class="form-check-input"
                                type="radio"
                                name="answers[{{ $q->id }}]"
                                value="{{ $opt->id }}"
                                id="q{{ $q->id }}_opt{{ $opt->id }}"
                                {{ $q->is_required ? 'required' : '' }}>
                            <label class="form-check-label"
                                for="q{{ $q->id }}_opt{{ $opt->id }}">
                                {{ $opt->option_text }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                    {{-- CHECKBOX --}}
                    @elseif($q->type === 'checkbox')
                    <div class="mt-2">
                        @foreach($q->options as $opt)
                        <div class="form-check mb-1">
                            <input class="form-check-input"
                                type="checkbox"
                                name="answers[{{ $q->id }}][]"
                                value="{{ $opt->id }}"
                                id="q{{ $q->id }}_opt{{ $opt->id }}">
                            <label class="form-check-label"
                                for="q{{ $q->id }}_opt{{ $opt->id }}">
                                {{ $opt->option_text }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endif

                </div>
            </div>
            @endforeach

            {{-- Submit --}}
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    Submit Survey
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
@section('script')
<!-- JS Libraies -->
<!-- <script src="{{ asset ('assets/modules/summernote/summernote-bs4.js') }}"></script> -->
<script>
    $(document).ready(function() {

        $("#formSurvey").submit(function(e) {
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
    });
</script>
@endsection