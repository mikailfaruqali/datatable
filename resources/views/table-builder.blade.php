<table id="{{ $tableId }}" class="{{ config('snawbar-datatable.table-style') }} {{ $tableClass }}"></table>

<script>
const order = {{ datatable_print_html(datatable_when($isOrderable, json_encode($defaultOrderBy), '[]')) }};

document.addEventListener('DOMContentLoaded', function () {
    $('#{{ $tableId }}').DataTable({
        deferRender: true,
        serverSide: true,
        stateSave: true,
        stateDuration: -1,
        responsive: true,
        processing: true,
        bLengthChange: false,
        searching: false,
        pageLength: '{{ $length }}',
        ordering: '{{ $isOrderable }}',
        order: order,
        language: {
            oPaginate: {
                sPrevious: "{{ __('snawbar-datatable::datatable.previous') }}",
                sNext: "{{ __('snawbar-datatable::datatable.next') }}"
            },
            emptyTable: "{{ __('snawbar-datatable::datatable.hich datayak la xshtada bardast nia') }}",
            zeroRecords: "{{ __('snawbar-datatable::datatable.hich tomarek nadozrayawa') }}",
            info: "{{ __('snawbar-datatable::datatable.nishandani') }} _START_ {{ __('snawbar-datatable::datatable.bo') }} _END_ {{ __('snawbar-datatable::datatable.la') }} _TOTAL_",
            infoEmpty: "{{ __('snawbar-datatable::datatable.nishandani') }} 0 {{ __('snawbar-datatable::datatable.bo') }} 0 {{ __('snawbar-datatable::datatable.la') }} 0",
            infoFiltered: "({{ __('snawbar-datatable::datatable.fltar krawa') }} {{ __('snawbar-datatable::datatable.la') }} _MAX_)",
            sProcessing: "{{ __('snawbar-datatable::datatable.chawarwanba') }}"
        },
        stateSaveCallback: function(settings, data) {
            delete data.search;
            delete data.columns;
            delete data.order;
            delete data.length;
            delete data.start;
        },
        initComplete: function() {
            if ('{{ $shouldJumpToLastPage }}') {
                setTimeout(() => {
                    this.api().page('last').draw('page');
                }, 1);
            }
        },
        ajax: {
            url: '{{ $ajaxUrl }}',
            data: function(data) {
                data.tableId = '{{ $jsSafeTableId }}';
                
				$('{{ $filterContainer }}').find('input, select, textarea').each(function () {
                    data[$(this).attr('name')] = $(this).val();
                });
			},
            dataSrc: function (json) {
                Object.entries(json.totals).forEach(([key, value]) => {
                    $(`#${key}`).text(value.value);
                });

                return json.data;
            },
        },
        columns: {{ datatable_print_html($columns) }},
    });
});

function {{ $tableRedrawFunction }}() {
    $('#{{ $tableId }}').DataTable().draw();
}

function {{ $jsSafeTableId }}_createAnchorElement(attributes = {}) {
    const anchor = document.createElement('a');

    Object.entries(attributes).forEach(([key, value]) =>
        key === 'onclick' && typeof value === 'function' ?
        (anchor.onclick = value) : anchor.setAttribute(key, value)
    );

    return anchor;
}

function {{ $jsSafeTableId }}_downloadFile(url, filename) {
    const anchor = {{ $jsSafeTableId }}_createAnchorElement({
        href: url,
        download: filename,
    });

    $('body').append(anchor);
    anchor.click();
    $(anchor).remove();
}

function {{ $jsSafeTableId }}_getTableCurrentUrl(extra = {}) {
    const table = $('#{{ $tableId }}').DataTable();

    return `${table.ajax.url()}?${$.param(Object.assign({}, table.ajax.params(), extra))}`;
}

@if($printButtonSelector)
    $(document).on('click', '{{ $printButtonSelector }}', function(e) {
        window.open({{ $jsSafeTableId }}_getTableCurrentUrl({print: 1}), '_blank', 'width=4000,height=4000');
    });
@endif

@if($excelButtonSelector)
    $(document).on('click', '{{ $excelButtonSelector }}', function(e) {
        {{ $jsSafeTableId }}_downloadFile({{ $jsSafeTableId }}_getTableCurrentUrl({excel: 1}), '{{ $exportTitle }}');
    });
@endif
</script>
