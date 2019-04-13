@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        

        <form action="" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="firstname" required type="text" class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" maxlength="30"
                            id="input-firstname">
                            <label class="form-label" for="input-firstname" @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>Firstname</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="lastname" required type="text" class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" maxlength="30"
                            id="input-lastname">
                            <label class="form-label" for="input-lastname" @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="email" type="email" class="validate {{ $errors->has('email') ? ' invalid' : '' }}"
                            id="input-email" required>
                            <label class="form-label" for="input-email" @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Account Email</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="phone" type="text" class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" maxlength="14"
                            id="input-phone">
                            <label class="form-label" for="input-phone" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone Number</label>
                        </div>
                    </div>
                </fieldset>
                </div>
                <div class="col-md-12">
                    {{ csrf_field() }}
                    <div class="row">
                        @if (!empty($contactFields) && $contactFields->count() > 0)
                        @foreach ($contactFields as $field)
                        <div class="col-md-4 form-group">
                            <input type="hidden" name="contact_ids[]" value="{{ $field->name }}" />
                            <input class="form-control" id="field-{{ $field->id }}" type="text" name="contacts[]">
                            <label class="form-label" for="field-{{ $field->id }}">{{ title_case($field->name) }}</label>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-block" type="submit" name="action">
                            Save Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</div>

@endsection