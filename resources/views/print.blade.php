<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ session()->get('direction', 'ltr') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <style>
        @font-face {
            font-family: font;
            src: url("{{ config('snawbar-datatable.font') }}")
        }

        * {
            font-family: font;
            color: black !important
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        thead {
            background-color: #343a40;
            color: white;
        }

        thead th {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        tbody td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        .table_caption caption {
            caption-side: top !important;
            border: 1px solid #181c32;
            border-bottom: none !important;
            font-style: normal !important
        }

        .table_caption caption h1 {
            text-align: center !important;
            font-size: 25px !important;
            font-weight: 800
        }
    </style>
</head>

<body>
    <table class="table table-bordered table-striped table-hover table_caption">
        <caption>
            <h1>{{ $title }}</h1>
        </caption>
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
