<div id="manage-group-modal" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>@{{ typeof group.id !== 'undefined' ? 'Edit Group' : 'Create Group' }}</h4>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="grp-name" type="text" name="name" maxlength="80" v-model="group.name">
                        <label for="grp-name" v-bind:class="{'active': group.name.length > 0}">Group Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="input-field col s12">
                        <textarea id="description" name="description" class="materialize-textarea" v-model="group.description"></textarea>
                        <label for="description" v-bind:class="{'active': group.description.length > 0}">Description (Optional)</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="group_id" id="grp-group-id" :value="group.id" v-if="typeof group.id !== 'undefined'" />
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_group"
                    value="1" >@{{ typeof group.id !== 'undefined' ? 'Update Group' : 'Create Group' }}</button>
        </div>
    </form>
</div>