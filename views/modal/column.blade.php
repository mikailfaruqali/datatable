<div class="modal" data-backdrop="static" id="{{ $columnModalId }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('snawbar-datatable::datatable.toggle-columns') }}</h4>
            </div>
            <div class="modal-body">
                <form id="{{ $columnModalId }}_form">
                    <ul class="list-group">
                        @foreach($columns as $column)
                            <li class="list-group-item">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="{{ $column->data }}_visiblity" name="columns[]" value="{{ $column->data }}" {{ datatableChecked($column->checked) }}>
                                    <label class="custom-control-label" for="{{ $column->data }}_visiblity">
                                        {{ $column->title }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('snawbar-datatable::datatable.daxstn') }}</button>
                <button type="button" class="btn btn-primary mr-1" id="{{ $columnModalId }}_save" onclick="{{ $buttonColumnVisibilityFunction }}">{{ __('snawbar-datatable::datatable.save') }}</button>
            </div>
        </div>
    </div>
</div>
