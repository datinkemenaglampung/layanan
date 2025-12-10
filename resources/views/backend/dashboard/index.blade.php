@extends('layouts.master')

@section('content')
<div class="row">
    @foreach ($config['layanan'] as $i => $layanan)
    @php
    // Generate warna stabil berbasis ID atau index
    $idRef = $layanan->id ?? $i;
    $hue = crc32($idRef) % 360;
    $color = "hsl($hue, 70%, 60%)";
    @endphp

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <a href="{{ route('permohonan.pengajuan', $layanan->slug) }}" class="text-decoration-none">
                <div class="card-body d-flex align-items-center">

                    {{-- Ikon --}}
                    <div class="p-3 rounded" style="background-color: {{ $color }};">
                        <i class="{{ $layanan->icon ?? 'fas fa-cog' }} text-white fs-3"></i>
                    </div>

                    {{-- Informasi Layanan --}}
                    <div class="ms-3">
                        <p class="text-muted mb-0">
                            {{ $layanan->bidang->nama_bidang ?? '-' }}
                        </p>
                        <h5 class="mb-0 fw-bold">
                            {{ $layanan->nama_layanan }}
                        </h5>
                    </div>

                </div>
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
        <div class="card">
            <div class="card-header">
                <h4>Statistik Berita</h4>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col">
                        <select name="year" id="year" class="form-control">
                            <option value="">Tahun</option>
                            <?php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 4;
                            for ($year = $startYear; $year <= $currentYear; $year++): ?>
                                <option value="<?= $year; ?>" <?= $year == $currentYear ? 'selected' : '' ?>><?= $year; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col">
                        <select name="select2User" id="select2User" class="form-control select2">
                            <option value="">All</option>
                            <!-- @if (Auth::user()->id)
                            <option value="{{Auth::user()->id}}" selected>{{Auth::user()->name}}</option>
                            @endif -->
                        </select>
                    </div>
                </div>
                <hr>
                <canvas id="myChart" height="50"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset ('assets/modules/chart.min.js') }}"></script>
<script>
    $(document).ready(function() {

        $('#select2User').select2({
            placeholder: "Cari User",
            ajax: {
                url: "{{ route('users.select2') }}",
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

        reloadChart();

        $('#year, #select2User').change(function() {
            reloadChart();
        })

        function reloadChart() {
            // Tahun yang ingin ditampilkan
            const Year = $('#year').val(); // Ganti sesuai kebutuhan
            const User = $('#select2User').select2('val');
            const apiUrl = `/backend/dashboard/graph`;

            // Lakukan AJAX request
            $.ajax({
                url: apiUrl,
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "year": Year,
                    "user": User,
                },
                dataType: "json",
                success: function(response) {
                    const labels = response.labels; // Nama bulan
                    const data = response.data; // Jumlah berita

                    // Inisialisasi Chart.js
                    var news_chart = document.getElementById("myChart").getContext("2d");
                    var myChart = new Chart(news_chart, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `Jumlah Berita (${year})`,
                                data: data,
                                borderWidth: 5,
                                borderColor: '#6777ef',
                                backgroundColor: 'transparent',
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#6777ef',
                                pointRadius: 4
                            }]
                        },
                        options: {
                            legend: {
                                display: false
                            },
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        display: false,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        stepSize: 150
                                    }
                                }],
                                xAxes: [{
                                    gridLines: {
                                        color: '#fbfbfb',
                                        lineWidth: 2
                                    }
                                }]
                            },
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }
    });
</script>
@endsection