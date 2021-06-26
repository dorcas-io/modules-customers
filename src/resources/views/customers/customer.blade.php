@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row" id="customer_profile_card">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-4">
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
                <ul class="list-group">
                    @foreach ($customer->contacts['data'] as $contact)
                    <li class="list-group-item">
                        <i class="fa {{ suggest_contact_field_icon_name_tabler($contact['name']) }}" aria-hidden="true"></i>
                        <h5 class="list-group-item-heading">{{ title_case($contact['name']) }}</h5>
                        <p class="list-group-item-text">{{ $contact['value']}}</p>
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
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#profile_groups">Groups</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane container active" id="profile_groups">
                        <br/>
                        <form method="post" v-on:submit.prevent="addCustomerToGroup" action="">
                            {{ csrf_field() }}
                            <fieldset class="form-fieldset">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <select id="grp-customer" v-model="addToGroup.group" class="form-control" required>
                                            <option value="" disabled>Select a Group</option>
                                            <option v-for="group in groups" v-if="addedGroups.indexOf(group.id) === -1"
                                            :key="group.id" :value="group.id">@{{ group.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" type="submit" name="action">Add to Group</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>

                        <div class="col-md-6" v-if="typeof customer.groups !== 'undefined' && customer.groups.data.length > 0">
                            <div class="tag" v-for="(group, index) in customer.groups.data" :key="group.id">
                              @{{ group.name }}
                              <a href="#" class="tag-addon tag-danger"><i class="fe fe-trash" data-ignore-click="true" v-bind:data-index="index"
                                v-on:click.prevent="removeGroup($event)"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notes</h3>
            </div>
            <div class="card-body">
                <form method="post" v-on:submit.prevent="addCustomerToGroup" action="">
                    <div class="form-group">
                        <label class="form-label">New Note</label>
                        <textarea class="form-control" rows="2" name="new_note" id="new_note" v-model="current_note"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" v-if="!savingNote" v-on:click.prevent="saveNote">Save Note</button>
                    </div>
                </form>

                <!-- v-bind:class="{'mr-1':  note.message.length < 60, 'm11': note.message.length > 60}" -->
                <ul class="list-group card-list-group" v-if="notes.length > 0">
                    <li class="list-group-item py-5" v-for="(note, key) in notes" v-bind:key="note.id">
                        <div class="media">
                            <div class="media-object avatar avatar-md mr-4" style="background-image: url({{ cdn('images/avatar/avatar-9.png') }})"></div>
                            <div class="media-body">
                                <div class="media-heading">
                                    <small class="float-right text-muted">Saved on - @{{ postedAtDate(note.created_at) }}</small>
                                    <!-- <h5>Peter Richards</h5> -->
                                </div>
                                <div>
                                    <br/>
                                    @{{ note.message }}
                                </div>
                                <button class="btn btn-danger btn-sm" v-on:click.prevent="deleteNote(note.id, key)">Delete Note</button>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="alert alert-primary mt-5 mb-6" v-if="notes.length === 0">
                    <div><strong>No Notes!</strong> Add Notes about @{{ customer.firstname }}.</div>
                </div>

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
                postedAtDate: function (dateString) {
                    return moment(dateString).format('DD MMM, YYYY HH:mm')
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
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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
                },
                removeGroup: function (e) {
                    let attrs = app.utilities.getElementAttributes(e.target);
                    console.log(attrs);
                    let index = attrs['data-index'] || null;
                    let group = typeof this.customer.groups.data[index] !== 'undefined' ? this.customer.groups.data[index] : null;
                    if (group === null) {
                        return false;
                    }
                    if (this.processing) {
                        //Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.processing = true;
                    let context = this;
                    axios.delete("/mcu/customers-groups/" + group.id, {
                        data: {customers: [context.customer.id]}
                    }).then(function (response) {
                        //console.log(response);
                        //console.log(index);
                        if (index !== null) {
                            context.customer.groups.data.splice(index, 1);
                            context.addedGroups = context.customer.groups.data.map(function (e) { return e.id; });
                        }
                        context.processing = false;
                        //Materialize.toast('Group '+group.name+' removed.', 2000);
                        return swal("Deleted!", "Group "+group.name+" was successfully deleted", "success");
                    })
                        .catch(function (error) {
                            var message = '';
                            console.log(error);
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            //context.saving = false;
                            return swal("Delete Failed", message, "warning");
                        });
                },
                addCustomerToGroup: function () {
                    let context = this;
                    this.processing =  true;
                    axios.post("/mcu/customers-groups/" + context.addToGroup.group, {
                        customers: [context.customer.id]
                    }).then(function (response) {
                        //console.log(response);
                        context.processing = false;
                        //Materialize.toast('Group added.', 3000);
                        window.location = '{{ url()->current() }}'
                        return swal("Added!", context.customer.firstname + " successfully added to group", "success");
                    })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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
                            //Materialize.toast('Error: '+message, 4000);
                            swal("Add Failed:", message, "warning");
                        });
                },
                deleteNote: function (id, index) {
                    var context = this;
                    if (this.deleting) {
                        Materialize.toast('Wait till the current activity completes.', 3000);
                        return;
                    }
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete this note.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_notes) => {
                        this.deleting = true;
                        return axios.delete("/mcu/customers-notes/" + context.customer.id, {
                            data: {id: id}
                        })
                            .then(function (response) {
                                console.log(response);
                                context.deleting = false;
                                window.location = '{{ url()->current() }}'
                                return swal("Deleted!", "The note was successfully deleted.", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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
                        },
                        allowOutsideClick: () => !Swal.isLoading() 
                    });
                },
                saveNote: function () {
                    var context = this;
                    this.savingNote =  true;
                    axios.post("/mcu/customers-notes/" + context.customer.id, {
                        note: context.current_note
                    }).then(function (response) {
                            console.log(response);
                            context.savingNote = false;
                            context.notes.splice(0, 0, response.data);
                            context.current_note = "";
                            //Materialize.toast('Note saved.', 3000);
                            return swal("Saved!", "Note successfully saved", "success");
                        })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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
                            //Materialize.toast('Error: '+message, 4000);
                            return swal("Error Saving:", message, "warning");
                        });
                }

            },
            mounted: function () {
                var context = this;
                this.addedGroups = this.customer.groups.data.map(function (e) { return e.id; });
                this.savingNote =  true;
                axios.get("/mcu/customers-notes/" + context.customer.id)
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
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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

                /*this.loading_deals = true;
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
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
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
                    });*/
            }
        });

        new Vue({
            el: '#profile-page-content',
            data: {
            },
            methods: {




            }
        });

        /*new Vue({
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
        });*/
    </script>
@endsection