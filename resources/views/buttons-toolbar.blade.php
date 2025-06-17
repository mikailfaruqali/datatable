<div class="btn-group mb-2">
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
        Export / Columns
    </button>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="{{ $buttonPrintFunction }}">{{ __('snawbar-datatable::datatable.print') }}</a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="{{ $buttonExcelFunction }}">{{ __('snawbar-datatable::datatable.excel') }}</a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="{{ $buttonColumnVisibilityFunction }}">{{ __('snawbar-datatable::datatable.toggle-columns') }}</a></li>
    </ul>
</div>