<div class="modal fade" id="manage-group-modal" tabindex="-1" role="dialog" aria-labelledby="manage-group-modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="manage-group-modalLabel">@{{ typeof group.id !== 'undefined' ? 'Edit Group' : 'Create Group' }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('customers-groups-post') }}" id="form-customers-group-post" method="post">
            {{ csrf_field() }}
            <fieldset class="form-fieldset">
              <div class="form-group">
                <label class="form-label" for="grp-name" v-bind:class="{'active': group.name.length > 0}">Group Name</label>
                <input class="form-control" id="grp-name" type="text" name="name" maxlength="80" v-model="group.name">
              </div>
              <div class="form-group">
                <label class="form-label" for="description" v-bind:class="{'active': group.description.length > 0}">Description (Optional)</label>
                <textarea class="form-control" id="description" name="description" class="materialize-textarea" v-model="group.description"></textarea>
              </div>
            </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <input type="hidden" name="group_id" id="grp-group-id" :value="group.id" v-if="typeof group.id !== 'undefined'" />
        <button type="submit" name="save_group" form="form-customers-group-post" class="btn btn-primary">@{{ typeof group.id !== 'undefined' ? 'Update Group' : 'Create Group' }}</button>
      </div>
    </div>
  </div>
</div>