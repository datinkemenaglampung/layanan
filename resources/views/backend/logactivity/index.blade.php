@extends('layouts.master')

@section('content')
<div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Data {{$config['title']}} </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dt" class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Model</th>
                                    <th>Model ID</th>
                                    <th>Description</th>
                                    <th>Ip Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#dt').DataTable({

            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('logactivity.index') }}`
            },
            columns: [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'action',
                    name: 'action'
                },
                {
                    data: 'model',
                    name: 'model'
                },
                {
                    data: 'model_id',
                    name: 'model_id'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'ip_address',
                    name: 'ip_address'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ],
        });
    });
</script>
@endsection