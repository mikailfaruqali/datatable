<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session()->get('direction', 'ltr') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <style>
        @font-face {
            font-family: font;
            src: url("{{ asset_or_url(config('snawbar-datatable.font')) }}")
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

        table.table-bordered {
            border: 1px solid #181c32 !important
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid #181c32 !important;
            font-size: 15px;
            font-weight: bold;
            text-overflow: ellipsis;
            color: black !important
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid #181c32 !important;
            font-size: 15px;
            text-overflow: ellipsis;
            color: black !important
        }

        table.table-bordered>tfoot>tr>td {
            border: 1px solid #181c32 !important;
            font-size: 15px;
            text-overflow: ellipsis;
            color: black !important
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
