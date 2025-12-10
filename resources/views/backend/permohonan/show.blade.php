@extends('layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Periksa Permohonan</h4>
    </div>

    <div class="card-body">

        <h5>{{ $permohonan->layanan->nama_layanan }}</h5>
        <p><strong>Keterangan:</strong> {{ $permohonan->keterangan ?? '-' }}</p>

        <hr>

        <div class="row">
            @foreach($permohonan->persyaratans as $p)
            <div class="col-md-6 mb-3">

                <div class="card h-100 shadow-sm">
                    <div class="card-body">

                        <h6 class="mb-3 fw-bold">{!! $p->nama_persyaratan !!}</h6>

                        <p class="mb-2">
                            File:
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#pdfModal"
                                data-url="{{ asset('storage/permohonan/'.$p->pivot->value) }}">
                                Lihat PDF
                            </button>
                        </p>

                        <p class="mb-2">
                            Status:
                            @if($p->pivot->status == 'sesuai')
                            <span class="badge bg-success">Sesuai</span>
                            @elseif($p->pivot->status == 'tidak sesuai')
                            <span class="badge bg-danger">Tidak Sesuai</span>
                            @else
                            <span class="badge bg-warning text-dark">MENUNGGU</span>
                            @endif
                        </p>

                        @if($p->pivot->catatan)
                        <p class="mb-2">
                            <strong>Catatan:</strong>
                            <em>{{ $p->pivot->catatan }}</em>
                        </p>
                        @endif

                        <form action="{{ route('permohonan.verify', $p->pivot->id) }}" method="POST" class="mt-3">
                            @csrf

                            <label class="fw-bold mb-1">Update Status</label>
                            <select name="status" class="form-select form-select-sm mb-2">
                                <option value="sesuai" {{ $p->pivot->status == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                <option value="tidak sesuai" {{ $p->pivot->status == 'tidak sesuai' ? 'selected' : ''}}>Tidak Sesuai</option>
                            </select>

                            <input
                                type="text"
                                name="catatan"
                                class="form-control form-control-sm"
                                placeholder="Catatan (opsional)" value="{{ $p->pivot->catatan ?? '' }}">

                            <button class="btn btn-sm btn-primary mt-2 w-100">
                                Simpan
                            </button>
                        </form>

                    </div>
                </div>

            </div>
            @endforeach
        </div>

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