<div class="modal fade" id="edit-customer-modal" tabindex="-1" role="dialog" aria-labelledby="edit-customer-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="edit-customer-modalLabel">Edit Customer Profile</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">


        <form action="" method="post" v-on:submit.prevent="updateCustomer">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="firstname" required type="text" class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" maxlength="30"
                            id="input-firstname" v-model="customer.firstname">
                            <label class="form-label" for="input-firstname" @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>Firstname</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="lastname" required type="text" class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" maxlength="30"
                            id="input-lastname" v-model="customer.lastname">
                            <label class="form-label" for="input-lastname" @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="email" type="email" class="validate {{ $errors->has('email') ? ' invalid' : '' }}"
                            id="input-email" required v-model="customer.email">
                            <label class="form-label" for="input-email" @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Account Email</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" name="phone" type="text" class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" maxlength="14"
                            id="input-phone" v-model="customer.phone">
                            <label class="form-label" for="input-phone" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone Number</label>
                        </div>
                    </div>
                </fieldset>
            </div>
            </div>
            {{ method_field('PUT') }}
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary" type="submit">
                        Update Profile Fields
                    </button>
                </div>
            </div>
        </form>

    </div>
    <div class="modal-footer">


        @if (!empty($customer->contacts['data']) || !empty($availableFields))
        <div class="col-md-12">
            <form action="" method="post">
                {{ csrf_field() }}
                <div class="row">
                    @if (!empty($customer->contacts['data']))
                    @foreach ($customer->contacts['data'] as $contact)
                    <div class="col-md-6 form-group">
                        <input type="hidden" name="fields[]" value="{{ $contact['id'] }}" />
                        <input class="form-control" name="values[]" type="text" id="field-{{ $contact['id'] }}" value="{{ $contact['value'] }}">
                        <label class="form-label" for="field-{{ $contact['id'] }}">{{ title_case($contact['name']) }}</label>
                    </div>
                    @endforeach
                    @endif
                    @if (!empty($availableFields))
                    @foreach ($availableFields as $contact)
                    <div class="col-md-6 form-group">
                        <input type="hidden" name="fields[]" value="{{ $contact->id }}" />
                        <input class="form-control" name="values[]" type="text" id="field-{{ $contact->id }}" value="">
                        <label class="form-label" for="field-{{ $contact->id }}">{{ title_case($contact->name) }}</label>
                    </div>
                    @endforeach
                    @endif

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit" name="action" value="save_contact_fields">
                            Update Custom Fields
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

    </div>

    </div>
  </div>
</div>