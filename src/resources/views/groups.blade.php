@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">

        <div class="container" id="listing">
            <div class="row mt-3" v-show="groups.length > 0">
                <group-card class="s12 m4" v-for="(group, index) in groups" :key="group.id" :group="group" :index="index"
                             v-on:edit-group="editGroup" v-on:delete-group="deleteGroup"></group-card>
            </div>
            <div class="col s12" v-if="groups.length === 0">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Customer Groups
                    @endslot
                    You can add one or more customer groups to allow you categorise your customers.
                    @slot('buttons')
                        <a href="#" v-on:click.prevent="createGroup" class="btn btn-primary btn-sm">Add Group</a>
                    @endslot
                @endcomponent
            </div>
            @include('modules-customers::modals.new-group')
        </div>

    </div>

</div>


@endsection
@section('body_js')
    <script type="text/javascript">
        var vm = new Vue({
            el: '#listing',
            data: {
                groups: {!! json_encode(!empty($groups) ? $groups : []) !!},
                group: {name: '', description: ''},
            },
            methods: {
                createGroup: function () {
                    this.group = {name: '', description: ''};
                    $('#manage-group-modal').modal('show');
                },
                editGroup: function (index) {
                    let group = typeof this.groups[index] !== 'undefined' ? this.groups[index] : null;
                    if (group === null) {
                        return;
                    }
                    this.group = group;
                    $('#manage-group-modal').modal('show');
                },
                deleteGroup: function (index) {
                    let group = typeof this.groups[index] !== 'undefined' ? this.groups[index] : null;
                    if (group === null) {
                        return;
                    }
                    group.is_default = group.is_default ? 1 : 0;
                    this.group = group;
                    let context = this;
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete group " + context.group.name,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
                        preConfirm: (login) => {
                        return axios.delete("/mcu/groups-delete/" + context.group.id)
                            .then(function (response) {
                                console.log(response);
                                context.groups.splice(index, 1);
                                return swal("Deleted!", "The group was successfully deleted.", "success");
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
                                return swal("Delete Failed", message, "warning");
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    })
                    /*.then(function() {
                        Swal.fire('Ajax request finished!')
                    })*/
                }
            }
        });

        new Vue({
            el: '#sub-menu-action',
            data: {

            },
            methods: {
                createGroup: function () {
                    vm.createGroup();
                }
            }
        })
    </script>
@endsection
