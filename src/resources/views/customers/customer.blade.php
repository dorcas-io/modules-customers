@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-4" id="customer_profile_card">
        <div class="card card-profile">
            <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }"></div>
            <div class="card-body text-center">
                <img class="card-profile-img" v-bind:src="photo">
                <h3 class="mb-3">@{{ fullName }}</h3>
                <p class="mb-4">
                    <div class="list-group text-left">
                        <p class="list-group-item"><i class="fa fa-desktop"></i> @{{ customer.email }}</p>
                        <p class="list-group-item"><i class="fa fa-phone" aria-hidden="true"></i> @{{ customer.phone }}</p>
                        <p class="list-group-item"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> @{{ addedDate }}</p>
                    </div>
                </p>
                <button v-on:click.prevent="editCustomer" class="btn btn-outline-primary btn-sm text-center">
                    <span class="fa fa-address-card"></span> Edit Profile
                </button>
            </div>
        @include('modules-customers::modals.edit')
        </div>

        <div class="card">
          <div class="card-status bg-green"></div>
          <div class="card-header">
            <h3 class="card-title">Custom Fields</h3>
          </div>
          <div class="card-body">
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
          </div>
        </div>

    </div>


    <div class="col-md-5 col-xl-5">

        <div class="card">
          <div class="card-status bg-blue"></div>
          <div class="card-header">
            <h3 class="card-title">Groups</h3>
          </div>
          <div class="card-body">
            Manage <strong>groups</strong> that @{{ customer.firstname }} belongs to below:
          </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notes</h3>
                <div class="card-options">       
                    <a href="#" class="btn btn-primary">Add Customer Notes</a>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-group card-list-group">
                    <li class="list-group-item py-5">
                        <div class="media">
                            <div class="media-object avatar avatar-md mr-4" style="background-image: url({{ cdn('images/avatar/avatar-9.png') }})"></div>
                            <div class="media-body">
                                <div class="media-heading">
                                    <small class="float-right text-muted">12 min</small>
                                    <h5>Peter Richards</h5>
                                </div>
                                <div>
                                    Donec id elit non mi porta gravida at eget metus. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cum sociis natoque penatibus et magnis dis
                                    parturient montes, nascetur ridiculus mus. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Add Customer Notes</button>
            </div>
        </div>
    </div>
</div>
@include('modules-customers::modals.new-deal')
@endsection

@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#customer_profile_card',
            data: {
                customer: {!! json_encode($customer) !!},
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/14.png') }}",
                contactFields: {!! !empty($contactFields) ? json_encode($contactFields) : '[]' !!},
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
                editCustomer: function (index) {
                    $('#edit-customer-modal').modal('show');
                },
                updateCustomer: function () {
                    var context = this;
                    Swal.fire({
                        title: "Update Customer Profile?",
                        text: "You are about to update the details for this customer.",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#1565C0",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        preConfirm: (update) => {
                        return axios.put("/mcu/customers-customers/" + context.customer.id, {
                            firstname: context.customer.firstname,
                            lastname: context.customer.lastname,
                            email: context.customer.email,
                            phone: context.customer.phone
                        })
                           .then(function (response) {
                                console.log(response);
                                //$('#edit-customer-modal').modal('hide');
                                return swal("Saved!", "The changes were successfully saved!", "success");
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
                        },
                        allowOutsideClick: () => !Swal.isLoading()                        
                    })
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
                customer: {!! json_encode($customer) !!}
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