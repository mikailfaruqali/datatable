<table id="{{ $tableId }}" class="{{ config('snawbar-datatable.table-style') }} {{ $tableClass }}"></table>

{{ datatablePrintHtml($exportableModalHtml) }}
{{ datatablePrintHtml($columnModalHtml) }}

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (! $.fn.DataTable) {
        return alert('Error: DataTable plugin not loaded');
    }

    const order = {{ datatablePrintHtml(datatableWhen($isOrderable, json_encode($defaultOrderBy), '[]')) }};

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
                data.totalable = $(`meta[name='{{ $jsSafeTableId }}-table-totalable']`).attr('content');

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
        columns: {{ datatablePrintHtml($columns) }},
    });
});

function {{ $tableRedrawFunction }} {
    $('#{{ $tableId }}').DataTable().draw();
}

function {{ $jsSafeTableId }}_createAnchorElement(attributes = {}) {
    const anchor = document.createElement('a');

    Object.entries(attributes).forEach(([key, value]) =>
        key === 'onclick' && typeof value === 'function' ? (anchor.onclick = value) : anchor.setAttribute(key, value)
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

function {{ $buttonPrintFunction }} {
    const url = window["{{ $jsSafeTableId }}_getTableCurrentUrl"]({
        columns: getCheckedColumns().join(','),
        print: 1,
    });

    window.open(url, '_blank', 'width=4000,height=4000');
}

function {{ $buttonExcelFunction }} {
    const url = window["{{ $jsSafeTableId }}_getTableCurrentUrl"]({
        columns: getCheckedColumns().join(','),
        excel: 1,
    });

    {{ $jsSafeTableId }}_downloadFile(url, '{{ $exportTitle }}');
}

function {{ $buttonColumnVisibilityFunction }} {
    const btnText = $('#{{ $columnModalId }}_save').text();

    const formData = new FormData(document.getElementById('{{ $columnModalId }}_form'));

    formData.append('tableId',  @js($jsSafeTableId));
    formData.append('className',  @js($className));

    $.ajax({
        type: 'POST',
        url: '{{ route("datatable.columns") }}',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#{{ $columnModalId }}_save').html("{{ __('snawbar-datatable::datatable.chawarwanba') }}").prop("disabled", true);
        },
        success(response) {
            window.location.reload();
        },
        complete: function() {
            $('#{{ $columnModalId }}_save').html(btnText).prop("disabled", false);
        },
    });
}

function getCheckedColumns() {
    return $('#{{ $exportableModalId }} input[type="checkbox"]:checked').map((_, el) => el.value).get();
}

function {{ $loadTotatableFunction }} {
    if ($(`meta[name='{{ $jsSafeTableId }}-table-totalable']`).length === 0) {
        $('meta[name="csrf-token"]').after($('<meta>').attr('name', `{{ $jsSafeTableId }}-table-totalable`).attr('content', 'true'));
    }

    {{ $tableRedrawFunction }};
}

$(document).on('click', '.datatable-button-export', function () {
    $('#{{ $exportableModalId }}_title').text($(this).data('modal-header-text'));
    $('#{{ $exportableModalId }}_submit').attr('onclick', $(this).data('function-export')).text($(this).text());
});

$(document).on('click', '#{{ $exportableModalId }}_submit', function () {
    $('#{{ $exportableModalId }}').modal('hide');
});
</script>
