@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ route('apps.crm.customers.new') }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">New Customer</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="customers-list" v-on:click="clickAction($event)">
            <table class="bootstrap-table responsive-table" v-if="showTable"
                   data-url="{{ url('/xhr/crm/customers') }}?groups={{ $groupFilters or '' }}"
                   data-page-list="[10,25,50,100,200,300,500]"
                   data-row-attributes="Hub.Table.formatCustomers"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-sort-class="sortable"
                   data-pagination="true"
                   data-search="true"
                   data-unique-id="id"
                   data-search-on-enter-key="true"
                    id="contacts-table">
                <thead>
                <tr>
                    <th data-field="basic_info" data-width="25%">Basic Info</th>
                    <th data-field="phone" data-width="10%">Phone</th>
                    <th v-for="header in customHeaders" v-bind:data-field="header.label">@{{ header.title }}</th>
                    <th data-field="created_at" data-width="10%">Added On</th>
                    <th data-field="buttons" data-width="15%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="col s12" v-if="showEmptyState">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        assistant
                    @endslot
                    Add customers to keeps notes on them, as well as the information you need.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.crm.customers.new') }}">
                            Add Customer
                        </a>
                    @endslot
                @endcomponent
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
        new Vue({
            el: '#customers-list',
            data: {
                customers: {{ $customersCount }},
                customHeaders: {!! json_encode(!empty($customFields) ? $customFields : []) !!}
            },
            computed: {
                showEmptyState: function () {
                    return this.customers === 0;
                },
                showTable: function () {
                    return this.customers > 0;
                }
            },
            methods: {
                clickAction: function (event) {
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
                }
            }
        });
    </script>
@endsection