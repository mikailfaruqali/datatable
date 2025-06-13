<script>
document.addEventListener('DOMContentLoaded', function () {
    const {{ $jsSafeTableId }} =  $('#{{ $tableId }}').DataTable({
        deferRender: true,
        serverSide: true,
        stateSave: true,
        stateDuration: -1,
        responsive: true,
        processing: true,
        bLengthChange: false,
        searching: false,
        pageLength: "{{ $length }}",
        ordering: "{{ $isOrderable }}",
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
            url: "{{ $ajaxUrl }}",
            data: function(data) {
				$({{ $filterContainer }}).find('input, select, textarea').each(function () {
                    data[$(this).attr('name')] = $(this).val();
                });
			},
        },
        columns: @json($columns)
    });

    function {{ $tableRedrawFunction }}() {
        {{ $jsSafeTableId }}.ajax.reload(null, false);
    }
});
</script>