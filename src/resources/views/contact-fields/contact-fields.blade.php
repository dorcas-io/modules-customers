@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">

        <div class="container" id="contact-fields">
            <div class="row mt-3" v-show="fields.length > 0">
                <contact-field v-for="field in fields" class="m4 l4" :field_name="titleCase(field.name)" :key="field.id"
                           v-bind:show-delete="true" :record_id="field.id" v-on:remove="decrementFields"></contact-field>
            </div>
            <div class="col s12" v-if="showEmptyState">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Custom Fields
                    @endslot
                    You can add one or more custom fields help customize the details you collect from customers.
                    @slot('buttons')
                        <a href="#" v-on:click.prevent="newField" class="btn btn-primary btn-sm">Add Custom Field</a>
                    @endslot
                @endcomponent
            </div>
        </div>

    </div>

</div>


@endsection
@section('body_js')

    
    <script type="text/javascript">
 
        function addNewContactField() {
            Swal.fire({
                    title: "New Custom Field",
                    text: "Enter the name for the custom field:",
                    input: "text",
                    showCancelButton: true,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "Custom Field Name",
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write something!'
                        }
                    },

                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Add Field",
                    showLoaderOnConfirm: true,
                    preConfirm: (result) => {
                        console.log(result);
                    return axios.post("/mcu/customers-custom-fields", {
                            name: result
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
                    },
                    allowOutsideClick: () => !Swal.isLoading()
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
            el: '#sub-menu-action',
            methods: {
                newField: function () {
                    addNewContactField();
                }
            }
        });

    </script>
@endsection
