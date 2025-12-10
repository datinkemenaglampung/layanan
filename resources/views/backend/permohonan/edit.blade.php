@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Permohonan: {{ $permohonan->layanan->nama_layanan }}</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('permohonan.update', $permohonan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                @foreach($permohonan->layanan->persyaratans as $persyaratan)
                @php
                $pivot = $permohonan->persyaratans->firstWhere('id', $persyaratan->id)?->pivot;
                @endphp

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <strong>{!! $persyaratan->nama_persyaratan !!}</strong>
                        </div>

                        <div class="card-body">

                            @if($pivot && $pivot->value)
                            <p>
                                File saat ini:
                                <button type="button"
                                    class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#pdfModal"
                                    data-url="{{ asset('storage/permohonan/'.$pivot->value) }}">
                                    Lihat PDF
                                </button>
                            </p>
                            @endif

                            @if(!$pivot || $pivot->status != 'sesuai')
                            <input type="file"
                                name="files[{{ $persyaratan->id }}]"
                                class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload ulang jika ingin mengganti</small>
                            @else
                            <p class="text-success mb-0">Tidak perlu upload ulang</p>
                            @endif

                        </div>

                        <div class="card-footer">
                            <p class="mb-0">
                                Status:
                                @if($pivot->status == 'sesuai')
                                <span class="badge bg-success">DITERIMA</span>
                                @elseif($pivot->status == 'tidak sesuai')
                                <span class="badge bg-danger">DITOLAK</span><br>
                                <small class="text-danger">Catatan: {{ $pivot->catatan }}</small>
                                @else
                                <span class="badge bg-warning text-dark">MENUNGGU</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>

            <div class="mt-3">
                <label class="fw-bold">Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ $permohonan->keterangan }}</textarea>
            </div>

            <div class="text-end mt-4">
                <button class="btn btn-primary px-4">Update</button>
            </div>

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