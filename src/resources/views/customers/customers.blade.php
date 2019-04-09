@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="row row-cards row-deck" id="customers-list">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="true"
                           data-side-pagination="server"
                           data-show-refresh="true"
                           data-unique-id="id"
                           data-id-field="id"
                           data-row-attributes="app.formatters.customers_customers"
                           data-response-handler="processRecords"
                           data-url="{{ route('customers-search') }}?groups={{ $groupFilters or '' }}"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-search-on-enter-key="true"
                           v-if="showTable"
                            id="contacts-table"
                       v-on:click="clicked($event)">
                        <thead>
                        <tr>
                            <th class="w-1" data-field="avatar">&nbsp;</th>
                            <th data-field="firstname">First Name</th>
                            <th data-field="lastname">Last Name</th>
                            <th data-field="email">Email</th>
                            <th data-field="phone">Phone</th>
                            <th data-field="created_at">Added On</th>
                            <th data-field="menu">Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <div class="col s12" v-if="showEmptyState">
                        @component('layouts.blocks.tabler.empty-fullpage')
                            @slot('title')
                                No Customers
                            @endslot
                            You can add one or more customers by clicking the Add Customer button
                            @slot('buttons')
                                <a href="{{ route('customers-new') }}" class="btn btn-primary btn-sm">Add Customer</a>
                            @endslot
                        @endcomponent
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@section('body_js')
<script>
    app.currentUser = {!! json_encode($dorcasUser) !!};
    let vmPage = new Vue({
        el: '#customers-list',
        data: {
            caccounts: [],
            request_id: '{{ !empty($requestId) ? $requestId : '' }}',
            caccount: {},
            customers: {{ $customersCount }},
        },
        computed: {
            showEmptyState: function () {
                return this.customers === 0;
            },
            showTable: function () {
                return this.customers > 0;
            }
        },
        mounted: function () {
           this.triggerEdit();
       },
       methods: {
        titleCase: function (string) {
            return v.titleCase(string);
        },
        clicked: function ($event) {
            let target = $event.target;
            if (!target.hasAttribute('data-action')) {
                target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
            }
            console.log(target, target.getAttribute('data-action'));
            let action = target.getAttribute('data-action').toLowerCase();
            let index = parseInt(target.getAttribute('data-index'), 10);
            if (isNaN(index)) {
                console.log('Index is not set.');
                return;
            }
            if (action === 'view') {
                return true;
            } else if (action === 'delete') {
                this.deleteItem(index);
            } else {
                return true;
            }
        },
        deleteItem: function (index) {
            let context = this;
            let caccount = typeof this.caccounts[index] !== 'undefined' ? this.caccounts[index] : {};
            console.log(caccount);
            if (typeof caccount.id === 'undefined') {
                return false;
            }
            swal({
                animation: true,
                title: "Delete this Customer?",
                text: "You are about to remove this customer record",
                customClass: 'swal2-btns-left',
                showCancelButton: true,
                confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                confirmButtonText: 'Yes, continue.',
                cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
            })
            .then((b) => {
                console.log(b);
                if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                    return '';
                }
                context.is_publishing = true;
                axios.delete("/xhr/access-grants/" + caccount.id)
                .then(function (response) {
                    console.log(response);
                    window.location = '{{ url()->current() }}';
                    return swal("Done!", "The customer was successfully deleted.", "success");
                })
                .catch(function (error) {
                    let message = '';
                    if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                let e = error.response.data.errors[0];
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
                            return swal("Deleting Failed", message, "warning");
                        });
            });
        },

        /*clickAction: function (event) {
            console.log(event.target);
            var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
            var attrs = Hub.utilities.getElementAttributes(target);
                // get the attributes
                var classList = target.classList;
                if (classList.contains('view')) {
                    return true;
                } else if (classList.contains('remove')) {
                    this.delete(attrs);
                }
            },
            delete: function (attributes) {
                console.log(attributes);
                var name = attributes['data-name'] || '';
                var id = attributes['data-id'] || null;
                if (id === null) {
                    return false;
                }
                context = this;
                swal({
                    title: "Are you sure?",
                    text: "You are about to delete " + name + " from your contacts.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function() {
                    axios.delete("/xhr/crm/customers/" + id)
                    .then(function (response) {
                        console.log(response);
                        context.visible = false;
                        context.contactsCount -= 1;
                        $('#contacts-table').bootstrapTable('removeByUniqueId', response.data.data.id);
                        return swal("Deleted!", "The customer was successfully deleted.", "success");
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
                            return swal("Delete Failed", message, "warning");
                        });
                });
            },*/
            triggerEdit: function () {
                if (this.request_id.length === 0 || this.caccounts.length === 0) {
                    return '';
                }
                let indexOf = -1;
                let totalCount = this.caccounts.length;
                for (let i = 0; i < totalCount; i++) {
                    if (this.caccount[i].id !== this.request_id) {
                        continue;
                    }
                    indexOf = i;
                    break;
                }
                if (indexOf === -1) {
                    return '';
                }
                this.editItem(indexOf);
            }
        }

    });

    function processRecords(response) {
        console.log(response);
        vmPage.caccounts = response.rows;
        vmPage.triggerEdit();
        return response;
    }
</script>
@endsection