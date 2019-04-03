@extends('layouts.app')
@section('body_main_content_header_button')

@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section">
            <div class="col s12">
                <div class="card-panel">
                    <h4 class="header2">Customer Information</h4>
                    <div class="row">
                        <form class="col s12" action="" method="post">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col s12">
                                    <div class="row">
                                        <div class="input-field col s12 m3">
                                            <input id="firstname" type="text" name="firstname" maxlength="30" required>
                                            <label for="firstname">Firstname</label>
                                        </div>
                                        <div class="input-field col s12 m3">
                                            <input id="lastname" type="text" name="lastname" maxlength="30" required>
                                            <label for="lastname">Lastname</label>
                                        </div>
                                        <div class="input-field col s12 m3">
                                            <input id="email" type="email" name="email" maxlength="80">
                                            <label for="email" class="active">Email</label>
                                        </div>
                                        <div class="input-field col s12 m3">
                                            <input id="phone" type="text" name="phone" maxlength="30">
                                            <label for="phone" class="active">Phone</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if (!empty($contactFields) && $contactFields->count() > 0)
                                            @foreach ($contactFields as $field)
                                                <div class="input-field col s12 m3">
                                                    <input id="field-{{ $field->id }}" type="text" name="contacts[]">
                                                    <label for="field-{{ $field->id }}">{{ title_case($field->name) }}</label>
                                                    <input type="hidden" name="contact_ids[]" value="{{ $field->name }}" />
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button class="btn cyan waves-effect waves-light left" type="submit" name="action">Add Customer
                                        <i class="material-icons left">add</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
        });

    </script>
@endsection