<div id="add-deal" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Create a Deal</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m4">
                        <input id="name" type="text" name="name" required maxlength="80" v-model="deal.name">
                        <label for="name">Deal Name</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <select id="value_currency" name="value_currency" v-model="defaultCurrency" required>
                            @foreach ($isoCurrencies as $currency)
                                <option value="{{ $currency['alphabeticCode'] }}">{{ $currency['currency'] }} - {{ $currency['alphabeticCode'] }}</option>
                            @endforeach
                        </select>
                        <label for="value_currency">Currency</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <input id="value_amount" type="number" name="value_amount" step="0.01" min="0"
                               required>
                        <label for="value_amount">Amount (Value on Deal)</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="note" name="note" class="materialize-textarea" v-model="deal.note"></textarea>
                        <label for="note">Notes (optional description of the deal)</label>
                    </div>
                    <input type="hidden" name="deal_id" id="deal_id" v-if="typeof deal.id !== 'undefined'"
                           v-model="deal.id">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="save_deal">Create Deal</button>
        </div>
    </form>
</div>