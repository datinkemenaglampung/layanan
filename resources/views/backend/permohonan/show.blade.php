@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="header-title mb-0">
                <h4 class="card-title mb-0">Periksa Permohonan: {{ $permohonan->layanan->nama_layanan }}</h4>
            </div>
        </div>
    </div>

    <div class="card-body">

        <div class="row">

            @foreach($permohonan->layanan->persyaratans as $persyaratan)
            @php
            // Ambil pivot sesuai persyaratan ID
            $pivot = $permohonan->persyaratans->firstWhere('id', $persyaratan->id)?->pivot;
            @endphp

            <div class="col-md-6 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">

                        <h6 class="fw-bold">{!! $persyaratan->nama_persyaratan !!}</h6>

                        @if($persyaratan->pivot->uploaded_level == 1)
                        <div class="alert alert-info py-1 px-2 mb-2">
                            <small><i class="fa fa-info-circle"></i> Berkas ini diunggah oleh Admin Kabupaten</small>
                        </div>
                        @endif

                        {{-- FILE --}}
                        @if($pivot && $pivot->value)
                        <p>
                            File:
                            <button type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#pdfModal"
                                data-url="{{ asset('storage/permohonan/'.$permohonan->user->username.'/'.$pivot->value) }}">
                                Lihat PDF
                            </button>
                        </p>
                        @else
                        <p class="text-danger">Belum upload</p>
                        @endif

                        {{-- STATUS --}}
                        <p class="mb-2">
                            Status:
                            @if($pivot && $pivot->status == 'sesuai')
                            <span class="badge bg-success">Sesuai</span>

                            @elseif($pivot && $pivot->status == 'tidak sesuai')
                            <span class="badge bg-danger">Tidak Sesuai</span>

                            @elseif($pivot && $pivot->status == 'perbaikan')
                            <span class="badge bg-warning text-dark">perbaikan</span>

                            @else
                            <span class="badge bg-info">Menunggu</span>
                            @endif
                        </p>

                        {{-- FORM UPDATE STATUS --}}
                        @if($pivot)
                        <form action="{{ route('permohonan.verify', $pivot->id) }}" method="POST">
                            @csrf

                            <label class="fw-bold">Update Status</label>
                            <select name="status" class="form-select form-select-sm mb-2">
                                <option value="sesuai" {{ $pivot->status == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                <option value="tidak sesuai" {{ $pivot->status == 'tidak sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                            </select>

                            <input type="text"
                                name="catatan"
                                class="form-control form-control-sm"
                                placeholder="Catatan (opsional)"
                                value="{{ $pivot->catatan ?? '' }}">

                            <button class="btn btn-primary btn-sm mt-2 w-100">Simpan</button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
    <div class="card-footer">
        <form id="formComplete" action="{{ route('permohonan.reviewComplete', $permohonan->id) }}" method="POST">
            @csrf
            @if(auth()->user()->role_id !==2)
            <!-- Keterangan tambahan -->
            <div class="form-group mt-3">
                <label class="fw-semibold">Status</label>
                <select name="status_permohonan" id="status_permohonan" class="form-control">
                    <option value="">.: Pilih :.</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditolak">Tolak</option>
                </select>
                <label class="fw-semibold">Keterangan Tambahan (opsional)</label>
                <textarea name="keterangan" class="form-control" placeholder="Tuliskan catatan tambahan..." rows="3"></textarea>
            </div>
            @endif
            <button
                type="submit"
                class="btn btn-primary float-end">
                Selesaikan Pemeriksaan
            </button>

            <a href="{{ route('permohonan.index') }}"
                class="btn btn-danger">
                Batal
            </a>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    document.getElementById('pdfModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        document.getElementById('pdfFrame').src = url;
    });

    document.getElementById('pdfModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('pdfFrame').src = "";
    });

    $(document).ready(function() {
        $("#formComplete").submit(function(e) {
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

@section('modal')
{{--Modal--}}
<div class="modal fade" id="pdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Surat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="pdfFrame" src="" width="100%" height="100%"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection