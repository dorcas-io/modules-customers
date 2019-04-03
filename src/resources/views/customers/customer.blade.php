@extends('layouts.app')
@section('head_css')
    <style type="text/css">
        #messages-wrapper {
            height: 500px !important;
            position: relative;
            overflow-y: auto;
        }
    </style>
@endsection
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ url('/apps/crm/customers/new') }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">New Customer</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="section row" id="customer-profile">
            <!-- profile-page-header -->
            <div id="profile-page-header" class="card">
                <div class="card-image waves-effect waves-block waves-light">
                    <img class="activator" v-bind:src="backgroundImage" alt="profile background">
                </div>
                <figure class="card-profile-image">
                    <img v-bind:src="photo" alt="profile image"
                         class="circle z-depth-2 responsive-img activator gradient-45deg-light-blue-cyan gradient-shadow">
                </figure>
                <div class="card-content">
                    <div class="row pt-2">
                        <div class="col s12 m2 offset-m2">
                            <h4 class="card-title grey-text text-darken-4 truncate">@{{ fullName }}</h4>
                            <p class="medium-small grey-text">Fullname</p>
                        </div>
                        <div class="col s12 m3 center-align">
                            <h4 class="card-title grey-text text-darken-4 truncate">@{{ customer.email }}</h4>
                            <p class="medium-small grey-text">Email</p>
                        </div>
                        <div class="col s12 m2 center-align">
                            <h4 class="card-title grey-text text-darken-4 truncate">@{{ customer.phone }}</h4>
                            <p class="medium-small grey-text">Phone</p>
                        </div>
                        <div class="col s12 m2 center-align">
                            <h4 class="card-title grey-text text-darken-4 truncate">@{{ addedDate }}</h4>
                            <p class="medium-small grey-text">Added On</p>
                        </div>
                        <div class="col s12 m1 right-align">
                            <a class="btn-floating activator waves-effect waves-light blue darken-3 right">
                                <i class="material-icons">create</i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-reveal">
                    <p>
                        <span class="card-title grey-text text-darken-4">@{{ customer.firstname }} @{{ customer.lastname }}
                          <i class="material-icons right">close</i>
                        </span>
                    </p>
                    <div class="row">
                        <div class="col s12 m6 padding-2">
                            <form action="" method="post" v-on:submit.prevent="updateCustomer">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col s12">
                                        <div class="row">
                                            <div class="col s12 m6 input-field">
                                                <input name="firstname" required type="text" class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" maxlength="30"
                                                       id="input-firstname" v-model="customer.firstname">
                                                <label for="input-firstname" @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>Firstname</label>
                                            </div>
                                            <div class="col s12 m6 input-field">
                                                <input name="lastname" required type="text" class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" maxlength="30"
                                                       id="input-lastname" v-model="customer.lastname">
                                                <label for="input-lastname" @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m6 input-field">
                                                <input name="email" type="email" class="validate {{ $errors->has('email') ? ' invalid' : '' }}"
                                                       id="input-email" required v-model="customer.email">
                                                <label for="input-email" @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Account Email</label>
                                            </div>
                                            <div class="col s12 m6 input-field">
                                                <input name="phone" type="text" class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" maxlength="14"
                                                       id="input-phone" v-model="customer.phone">
                                                <label for="input-phone" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone Number</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ method_field('PUT') }}
                                <div class="row">
                                    <div class="col s12">
                                        <button class="btn waves-effect waves-light" type="submit">
                                            Update Information
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if (!empty($customer->contacts['data']) || !empty($availableFields))
                            <div class="col s12 m6 padding-2">
                                <form action="" method="post">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        @if (!empty($customer->contacts['data']))
                                            @foreach ($customer->contacts['data'] as $contact)
                                                <div class="col s12 m6 input-field">
                                                    <input type="hidden" name="fields[]" value="{{ $contact['id'] }}" />
                                                    <input name="values[]" type="text" id="field-{{ $contact['id'] }}" value="{{ $contact['value'] }}">
                                                    <label for="field-{{ $contact['id'] }}">{{ title_case($contact['name']) }}</label>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if (!empty($availableFields))
                                            @foreach ($availableFields as $contact)
                                                <div class="col s12 m6 input-field">
                                                    <input type="hidden" name="fields[]" value="{{ $contact->id }}" />
                                                    <input name="values[]" type="text" id="field-{{ $contact->id }}" value="">
                                                    <label for="field-{{ $contact->id }}">{{ title_case($contact->name) }}</label>
                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                    <div class="row">
                                        <div class="col s12">
                                            <button class="btn waves-effect waves-light" type="submit" name="action" value="save_contact_fields">
                                                Update Information
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--/ profile-page-header -->
            <!-- profile-page-content -->
            <div id="profile-page-content" class="row">
                <!-- profile-page-sidebar-->
                <div id="profile-page-sidebar" class="col s12 m4">
                    <div class="progress" v-if="processing">
                        <div class="indeterminate"></div>
                    </div>
                    @if (!empty($customer->contacts['data']))
                        <!-- Profile About Details  -->
                        <ul id="profile-page-about-details" class="collection z-depth-1">
                            @foreach ($customer->contacts['data'] as $contact)
                                <li class="collection-item">
                                    <div class="row">
                                        <div class="col s5">
                                            <i class="material-icons left">{{ suggest_contact_field_icon_name($contact['name']) }}</i>
                                            {{ title_case($contact['name']) }}</div>
                                        <div class="col s7 right-align">{{ $contact['value']}}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <!--/ Profile About Details  -->
                    @endif
                    <div class="row ml-2">
                        <div class="col s12">
                            <ul class="tabs tab-profile grey darken-3 z-depth-1">
                                <li class="tab col s6">
                                    <a class="white-text waves-effect waves-light active" href="#groups">
                                        Groups
                                    </a>
                                </li>
                                <li class="tab col s6 disabled">
                                    <a class="white-text waves-effect waves-light" href="#deals">
                                        Deals / Sales
                                    </a>
                                </li>
                            </ul>
                            <div id="groups" class="tab-content col s12">
                                <form method="post" v-on:submit.prevent="addCustomerToGroup" action="" class="mt-5">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="input-field col s12 mb-8">
                                            <select id="grp-customer" v-model="addToGroup.group" class="browser-default" required>
                                                <option value="" disabled>Select a Group</option>
                                                <option v-for="group in groups" v-if="addedGroups.indexOf(group.id) === -1"
                                                        :key="group.id" :value="group.id">@{{ group.name }}</option>
                                            </select>
                                        </div>
                                        <div class="col s12 mt-8">
                                            <button class="btn waves-effect waves-light" type="submit" name="action">Add to Group</button>
                                        </div>
                                    </div>
                                </form>

                                <div class="col s12 mt-8" v-if="typeof customer.groups !== 'undefined' && customer.groups.data.length > 0">
                                    <div class="chip" v-for="(group, index) in customer.groups.data" :key="group.id">
                                        @{{ group.name }}
                                        <i class="close material-icons" data-ignore-click="true" v-bind:data-index="index"
                                           v-on:click.prevent="removeGroup($event)">close</i>
                                    </div>
                                </div>
                            </div>

                            <div id="deals" class="tab-content col s12">
                                <a class="btn waves-effect waves-light mt-8 btn-block modal-trigger"
                                   v-if="deals.length > 0" href="#add-deal">Create a Deal</a>
                                <div class="progress" v-if="loading_deals">
                                    <div class="indeterminate"></div>
                                </div>
                                <div class="row">
                                    <div class="col s12" v-if="deals.length === 0">
                                        @component('layouts.slots.empty-fullpage')
                                            @slot('icon')
                                                attach_money
                                            @endslot
                                            No deals have been created for this customer. You can create one now.
                                            @slot('buttons')
                                                <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger"
                                                   href="#add-deal">
                                                    Create a new Deal
                                                </a>
                                            @endslot
                                        @endcomponent
                                    </div>
                                    <div class="col s12" v-for="(deal, index) in deals" :key="deal.id">
                                        <div class="card">
                                            <div class="card-content teal accent-4 white-text">
                                                <p class="card-stats-title">
                                                    <i class="material-icons">attach_money</i> @{{ deal.name }}</p>
                                                <h4 class="card-stats-number">@{{ deal.value_currency }} @{{ deal.value_amount.formatted }}</h4>
                                                <p class="card-stats-compare">
                                                    <i class="material-icons">flag</i> 80%
                                                    <span class="teal-text text-lighten-5">from yesterday</span>
                                                </p>
                                            </div>
                                            <div class="card-action teal darken-1">

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- profile-page-sidebar-->
                <!-- profile-page-wall -->
                <div id="profile-page-wall" class="col s12 m8">
                    <!-- profile-page-wall-share -->
                    <div id="profile-page-wall-share" class="row">
                        <div class="col s12">
                            <ul class="tabs tab-profile blue z-depth-1">
                                <li class="tab col s3">
                                    <a class="white-text waves-effect waves-light active" href="#add-note">
                                        <i class="material-icons">border_color</i> Add Note
                                    </a>
                                </li>
                            </ul>
                            <!-- Add Note -->
                            <div id="add-note" class="tab-content col s12 grey lighten-4">
                                <div class="row">
                                    <div class="col s2">
                                        <img src="{{ $customer->photo }}" alt="profile image"
                                             class="circle z-depth-2 responsive-img activator gradient-45deg-light-blue-cyan">
                                    </div>
                                    <div class="input-field col s10">
                                        <textarea row="2" class="materialize-textarea" name="new_note"
                                                  id="new_note" v-model="current_note"></textarea>
                                        <label for="new_note" class="">What's on your mind?</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 m6 right-align offset-m6">
                                        <a class="waves-effect waves-light blue btn" v-if="!savingNote" v-on:click.prevent="saveNote">
                                            <i class="material-icons left">rate_review</i> Save
                                        </a>
                                        <div class="progress" v-if="savingNote">
                                            <div class="indeterminate"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ profile-page-wall-share -->
                    <!-- profile-page-wall-posts -->
                    <div id="profile-page-wall-posts" class="row">
                        <div class="col s12">
                            <ul class="tabs tab-profile blue z-depth-1">
                                <li class="tab col s3">
                                    <a class="white-text waves-effect waves-light active" href="#saved-notes">
                                        <i class="material-icons">comment</i> Notes
                                    </a>
                                </li>
                            </ul>
                            <div id="saved-notes" class="tab-content col s12 grey lighten-4">
                                <div class="row" id="messages-wrapper" v-if="notes.length > 0">
                                    <div class="card col s12 hoverable m5 ml-1" v-bind:class="{'mr-1':  note.message.length < 60, 'm11': note.message.length > 60}" v-for="(note, key) in notes" v-bind:key="note.id">
                                        <div class="card-content">
                                            <span class="card-title">
                                                <span class="grey-text text-darken-1 ultra-small">Saved on - @{{ postedAtDate(note.created_at) }}</span>
                                            </span>
                                            <p>@{{ note.message }}</p>
                                        </div>
                                        <div class="card-action">
                                            <a href="#" class="red-text" v-on:click.prevent="deleteNote(note.id, key)">DELETE</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col s12" v-if="notes.length === 0">
                                    @component('layouts.slots.empty-fullpage')
                                        @slot('icon')
                                            comment
                                        @endslot
                                        Store quick notes about this customer for your team.
                                        @slot('buttons')@endslot
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ profile-page-wall-posts -->
                </div>
                <!--/ profile-page-wall -->
            </div>
        </div>
    </div>
    @include('crm.modals.new-deal')
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#profile-page-header',
            data: {
                customer: {!! json_encode($customer) !!},
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/14.png') }}",
                contactFields: {!! !empty($contactFields) ? json_encode($contactFields) : '[]' !!}
            },
            computed: {
                photo: function () {
                    return this.customer.photo.length > 0 ? this.customer.photo : this.defaultPhoto;
                },
                fullName: function () {
                    var names = [this.customer.firstname || '', this.customer.lastname || ''];
                    return names.join(' ').title_case();
                },
                addedDate: function () {
                    return moment(this.customer.created_at).format('DD MMM, YYYY')
                }
            },
            methods: {
                titleCase: function (string) {
                    return string.title_case();
                },
                updateCustomer: function () {
                    var context = this;
                    swal({
                        title: "Update Information?",
                        text: "You are about to update the details for this customer.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#1565C0",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.put("/xhr/crm/customers/" + context.customer.id, {
                            firstname: context.customer.firstname,
                            lastname: context.customer.lastname,
                            email: context.customer.email,
                            phone: context.customer.phone
                        })
                            .then(function (response) {
                                console.log(response);
                                return swal("Saved!", "The changes were successfully saved!.", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                                    var e = error.response.data.errors[0];
                                    message = e.title;
                                } else if (error.request) {
                                    // The request was made but no response was received
                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                    // http.ClientRequest in node.js
                                    message = 'The request was made but no response was received';
                                } else {
                                    // Something happened in setting up the request that triggered an Error
                                    message = error.message;
                                }
                                return swal("Save Failed", message, "warning");
                            });
                    });
                },
                getContactFieldValue: function (id) {
                    if (id.length === 0 || typeof id === 'undefined') {
                        return '';
                    }
                    if (this.customer.contacts.data.length === 0) {
                        return '';
                    }
                    for (var i = 0; i < this.customer.contacts.data.length; i++) {
                        if (this.customer.contacts.data[i].id !== id) {
                            continue;
                        }
                        return this.customer.contacts.data[i].value;
                    }
                    return '';
                }
            }
        });

        new Vue({
            el: '#profile-page-content',
            data: {
                customer: {!! json_encode($customer) !!},
                groups: {!! json_encode($groups) !!},
                current_note: '',
                savingNote: false,
                notes: [],
                deleting: false,
                processing: false,
                addedGroups: [],
                addToGroup: {
                    group: ''
                },
                loading_deals: false,
                deals: []
            },
            methods: {

                removeGroup: function (e) {
                    let attrs = Hub.utilities.getElementAttributes(e.target);
                    console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let group = typeof this.customer.groups.data[index] !== 'undefined' ? this.customer.groups.data[index] : null;
                    if (group === null) {
                        return false;
                    }
                    if (this.processing) {
                        Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.processing = true;
                    let context = this;
                    axios.delete("/xhr/crm/groups/" + group.id + "/customers", {
                        data: {customers: [context.customer.id]}
                    }).then(function (response) {
                        console.log(response);
                        console.log(index);
                        if (index !== null) {
                            context.customer.groups.data.splice(index, 1);
                            context.addedGroups = context.customer.groups.data.map(function (e) { return e.id; });
                        }
                        context.processing = false;
                        Materialize.toast('Group '+group.name+' removed.', 2000);
                    })
                        .catch(function (error) {
                            var message = '';
                            console.log(error);
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.saving = false;
                            return swal("Delete Failed", message, "warning");
                        });
                },
                addCustomerToGroup: function () {
                    let context = this;
                    this.processing =  true;
                    axios.post("/xhr/crm/groups/" + context.addToGroup.group + "/customers", {
                        customers: [context.customer.id]
                    }).then(function (response) {
                        console.log(response);
                        context.processing = false;
                        Materialize.toast('Group added.', 3000);
                        window.location = '{{ url()->current() }}'
                    })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.savingNote = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                },
                postedAtDate: function (dateString) {
                    return moment(dateString).format('DD MMM, YYYY HH:mm')
                },
                deleteNote: function (id, index) {
                    var context = this;
                    if (this.deleting) {
                        Materialize.toast('Wait till the current activity completes.', 3000);
                        return;
                    }
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete this note.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        this.deleting = true;
                        axios.delete("/xhr/crm/customers/" + context.customer.id + "/notes", {
                            data: {id: id}
                        }).then(function (response) {
                                console.log(response);
                                context.deleting = false;
                                return swal("Deleted!", "The note was successfully deleted.", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                                    var e = error.response.data.errors[0];
                                    message = e.title;
                                } else if (error.request) {
                                    // The request was made but no response was received
                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                    // http.ClientRequest in node.js
                                    message = 'The request was made but no response was received';
                                } else {
                                    // Something happened in setting up the request that triggered an Error
                                    message = error.message;
                                }
                                context.deleting = false;
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                },
                saveNote: function () {
                    var context = this;
                    this.savingNote =  true;
                    axios.post("/xhr/crm/customers/" + context.customer.id + "/notes", {
                        note: context.current_note
                    }).then(function (response) {
                            console.log(response);
                            context.savingNote = false;
                            context.notes.splice(0, 0, response.data);
                            context.current_note = "";
                            Materialize.toast('Note saved.', 3000);
                        })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.savingNote = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                }
            },
            mounted: function () {
                var context = this;
                this.addedGroups = this.customer.groups.data.map(function (e) { return e.id; });
                this.savingNote =  true;
                axios.get("/xhr/crm/customers/" + context.customer.id + "/notes")
                    .then(function (response) {
                        console.log(response);
                        context.savingNote = false;
                        context.notes = response.data;
                    })
                    .catch(function (error) {
                        var message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            var e = error.response.data.errors[0];
                            message = e.title;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.savingNote = false;
                    });

                this.loading_deals = true;
                axios.get("/xhr/crm/customers/" + context.customer.id + "/deals")
                    .then(function (response) {
                        console.log(response);
                        context.loading_deals = false;
                        context.deals = response.data.rows;
                    })
                    .catch(function (error) {
                        var message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            var e = error.response.data.errors[0];
                            message = e.title;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.loading_deals = false;
                    });
            }
        });

        new Vue({
            el: '#add-deal',
            data: {
                customer: {!! json_encode($customer) !!},
                defaultCurrency: '',
                ui_configuration: {!! json_encode($UiConfiguration) !!},
                deal: {}
            },
            mounted: function () {
                if (typeof this.deal.value_currency !== 'undefined') {
                    this.deal.value_currency = this.defaultCurrency;
                }
                if (typeof this.ui_configuration.currency !== 'undefined') {
                    this.defaultCurrency = this.ui_configuration.currency;
                } else {
                    this.defaultCurrency = 'NGN';
                }
            }
        });
    </script>
@endsection