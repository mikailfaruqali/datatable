<div class="modal" data-backdrop="static" id="{{ $exportableModalId }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="{{ $exportableModalId }}_title"></h4>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach($columns as $column)
                        <li class="list-group-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="{{ $column->data }}_exportable" name="columns[]" value="{{ $column->data }}" {{ datatableChecked($column->checked) }}>
                                <label class="custom-control-label" for="{{ $column->data }}_exportable">
                                    {{ $column->title }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('snawbar-datatable::datatable.daxstn') }}</button>
                <button type="button" class="btn btn-primary mr-1" id="{{ $exportableModalId }}_submit"></button>
            </div>
        </div>
    </div>
</div>
