<?php

namespace Snawbar\DataTable\Export;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Exportable implements FromArray, WithHeadings, WithMapping
{
    public function __construct(
        private readonly array $data,
        private readonly array $headers,
    ) {}

    public function array(): array
    {
        return $this->data;
    }

    public function map($row): array
    {
        return array_map(fn ($value) => strip_tags((string) $value), $row);
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
