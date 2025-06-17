<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session()->get('direction', 'ltr') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <style>
        @font-face {
            font-family: font;
            src: url("{{ assetOrUrl(config('snawbar-datatable.font')) }}")
        }

        * {
            font-family: font;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-family: font;
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
                    @foreach ($row as $cell)
                        <td>{{ strip_tags($cell) }}</td>
                    @endforeach
                </tr>
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
