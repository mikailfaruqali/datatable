<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
    dir="{{ session()->get(config('snawbar-datatable.local-direction-session-key', 'direction'), 'rtl') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    @datatablePrintCss()

    <style>
        @font-face {
            font-family: 'SnawbarFont';
            src: url("{{ assetOrUrl(config('snawbar-datatable.font')) }}");
        }

        * {
            font-family: 'SnawbarFont';
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-family: 'SnawbarFont';
            font-size: 14px;
        }

        table.table-bordered>thead>tr>th:not(.border-none) {
            background-color: rgb(155, 194, 230) !important;
            border: 1px solid #181c32 !important;
            font-size: 15px;
            color: black !important;
            padding: 3px !important
        }

        table.table-bordered>tbody>tr>td:not(.border-none) {
            border: 1px solid #181c32 !important;
            font-size: 15px;
            color: black !important;
            padding: 2px !important
        }

        table.table-bordered>tfoot>tr>td:not(.border-none) {
            border: 1px solid #181c32 !important;
            font-size: 15px;
            font-weight: 700;
            color: black !important
        }

        .border-none {
            border: none !important
        }

        .fieldset-top-border {
            border-bottom: none;
            border-right: none;
            border-left: none;
            border-top: 1px solid;
        }

        .fieldset-top-border legend {
            width: auto;
            text-align: center;
            font-size: 1.1em;
            font-weight: bolder;
        }

        .subtable {
            width: 100%;
            vertical-align: top;
            border-collapse: collapse;
        }

        .subtable>thead {
            vertical-align: bottom;
        }

        .subtable>tbody>tr>td {
            border-top: 1px dotted black;
        }
    </style>
</head>

<body>
    <fieldset class="fieldset-top-border">
        <legend>{{ $title }}</legend>
    </fieldset>
    <table class="table table-bordered table-striped table-hover table_caption">
        <thead class="thead-dark">
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $key => $cell)
                        @continue($key === 'subItems')
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>

                @if (isset($row->subItems))
                    <tr>
                        <td colspan="100%">
                            <table class="subtable">
                                <thead>
                                    <tr>
                                        @foreach (datatableSubItemHeaders($row->subItems) as $key)
                                            <th>{{ $key }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($row->subItems as $subItem)
                                        <tr>
                                            @foreach ($subItem as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>

            @datatableRowSpace(5)

            @foreach ($totals as $alias => $total)
                <tr>
                    <td colspan="2">
                        {{ $total->title }}
                    </td>
                    <td colspan="5">
                        {{ $total->value }}
                    </td>
                </tr>
            @endforeach
        </tfoot>

    </table>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>

</html>
