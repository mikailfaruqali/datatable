<div class="btn-group mb-2">
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
        Export / Columns
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item datatable-button-export" data-toggle="modal" href="#{{ $exportableModalId }}" data-button-text="{{ __('snawbar-datatable::datatable.print') }}" data-function-export="{{ $buttonPrintFunction }}" data-modal-header-text="{{ __('snawbar-datatable::datatable.tanha am xanana chap bka') }}">
                {{ __('snawbar-datatable::datatable.print') }}
            </a>
        </li>
        <li>
            <a class="dropdown-item datatable-button-export" data-toggle="modal" href="#{{ $exportableModalId }}" data-button-text="{{ __('snawbar-datatable::datatable.excel') }}" data-function-export="{{ $buttonExcelFunction }}" data-modal-header-text="{{ __('snawbar-datatable::datatable.tanha am xanana bka ba excel') }}">
                {{ __('snawbar-datatable::datatable.excel') }}
            </a>
        </li>
        <li>
            <a class="dropdown-item" data-toggle="modal" href="#{{ $columnModalId }}">
                {{ __('snawbar-datatable::datatable.toggle-columns') }}
            </a>
        </li>
    </ul>
</div>