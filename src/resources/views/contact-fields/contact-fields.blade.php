@extends('layouts.app')
@section('head_css')
    <link href="{{ cdn('vendors/bootstrap-table/bootstrap-table.css') }}" type="text/css" rel="stylesheet">
@endsection
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#" v-on:click.prevent="newField">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Custom Field</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="contact-fields">
            <contact-field v-for="field in fields" class="m4 l4" :field_name="titleCase(field.name)" :key="field.id"
                           v-bind:show-delete="true"
                           :record_id="field.id" v-on:remove="decrementFields"></contact-field>

            <div class="col s12" v-if="showEmptyState">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        build
                    @endslot
                    Customise the details you collect for your customers.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#" v-on:click.prevent="newField">
                            Add Custom Field
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
        function addNewContactField() {
            swal({
                    title: "New Custom Field",
                    text: "Enter the name for the custom field:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "Custom Field Name"
                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.post("/xhr/crm/custom-fields", {
                        name: inputValue
                    }).then(function (response) {
                        console.log(response);
                        vm.fields.push({id: response.data.id, name: response.data.name});
                        return swal("Success", "The custom field was successfully created.", "success");
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
                            return swal("Oops!", message, "warning");
                        });
                });
        }

        var vm = new Vue({
            el: '#contact-fields',
            data: {
                fields: {!! !empty($contactFields) ? json_encode($contactFields) : '[]' !!}
            },
            computed: {
                showEmptyState: function () {
                    return this.fields.length === 0;
                }
            },
            methods: {
                decrementFields: function (id) {
                    console.log('Removing: ' + id);
                    var index = this.fields.findIndex(function (item) {
                        return item.id === id;
                    });
                    if (typeof index !== 'undefined') {
                        var removed = this.fields.splice(index, 1);
                        console.log('Removed => ' + JSON.stringify(removed));
                    }
                },
                titleCase: function (string) {
                    return string.title_case();
                },
                newField: function () {
                    addNewContactField();
                }
            }
        });
        new Vue({
            el: '#breadcrumbs-wrapper',
            methods: {
                newField: function () {
                    addNewContactField();
                }
            }
        });
    </script>
@endsection