@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-4 col-xl-4">
        <div class="card">
            <div class="card-status bg-purple"></div>
            <div class="card-header">
                <h3 class="card-title">Customer Manager</h3>
                <div class="card-options">
                    <a href="{{ route('customers-customers') }}" class="btn btn-success btn-sm">View</a>
                </div>
            </div>
            <div class="card-body">
                Manage your <strong>customers</strong>, their contact information, custom configured data, and other relationship information.
            </div>
        </div>
    </div>

    <div class="col-md-4 col-xl-4">
        <div class="card">
            <div class="card-status bg-teal"></div>
            <div class="card-header">
                <h3 class="card-title">Custom Fields</h3>
                <div class="card-options">
                    <a href="{{ route('customers-custom-fields') }}" class="btn btn-primary btn-sm">Manage</a>
                </div>
            </div>
            <div class="card-body">
                Manage <strong>custom fields</strong> for customer contact information, so you store what you need from your customers.
            </div>
        </div>
    </div>

</div>


@endsection
@section('body_js')
    
@endsection
