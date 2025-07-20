<?php

namespace Snawbar\DataTable\Export;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class Exportable implements FromArray, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithProperties, WithStrictNullComparison
{
    private array $rows;

    private array $headers;

    private array $columns;

    private string $title;

    private array $totals;

    public function __construct(array $rows, array $headers, array $columns, string $title, array $totals = [])
    {
        $this->rows = $rows;
        $this->headers = $headers;
        $this->columns = $columns;
        $this->title = $title;
        $this->totals = $totals;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function map($row): array
    {
        return array_map(fn ($value) => strip_tags((string) $value), $row);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function columnFormats(): array
    {
        $letters = array_map(fn ($i) => Coordinate::stringFromColumnIndex($i + 1), array_keys($this->columns));

        $formats = array_map(function ($column) {
            switch ($column->type) {
                case 'number':
                    return NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2;
                case 'float':
                    return '#,##0.00###############';
                default:
                    return NumberFormat::FORMAT_GENERAL;
            }
        }, $this->columns);

        return array_combine($letters, $formats);
    }

    public function properties(): array
    {
        return [
            'title' => $this->title,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $afterSheet) {
                $this->styleTitle($afterSheet);
                $this->styleHeaders($afterSheet);

                if (filled($this->totals)) {
                    $this->addTotals($afterSheet);
                }

                $this->configureSheet($afterSheet);
            },
        ];
    }

    private function styleTitle(AfterSheet $afterSheet): void
    {
        $titleRange = sprintf('A1:%s1', $afterSheet->sheet->getHighestColumn());
        $afterSheet->sheet->mergeCells($titleRange);
        $afterSheet->sheet->setCellValue('A1', $this->title);
        $afterSheet->sheet->getStyle($titleRange)->applyFromArray([
            'font' => ['bold' => TRUE, 'size' => 18],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE6F1']],
        ]);
    }

    private function styleHeaders(AfterSheet $afterSheet): void
    {
        $headerRange = sprintf('A2:%s2', $afterSheet->sheet->getHighestColumn());
        $afterSheet->sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => TRUE, 'color' => ['argb' => 'FFFFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
        ]);
    }

    private function addTotals(AfterSheet $afterSheet): void
    {
        $worksheet = $afterSheet->sheet->getDelegate();
        $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        $currentRow = $worksheet->getHighestRow() + 2;

        foreach ($this->totals as $label => $value) {
            $labelCell = sprintf('%s%d', Coordinate::stringFromColumnIndex($highestColumnIndex - 1), $currentRow);
            $valueCell = sprintf('%s%d', Coordinate::stringFromColumnIndex($highestColumnIndex), $currentRow);

            $worksheet->setCellValue($labelCell, $label);
            $worksheet->setCellValue($valueCell, $value);

            $styleArray = [
                'font' => ['bold' => TRUE, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'e0b887']],
            ];

            $worksheet->getStyle($labelCell)->applyFromArray($styleArray);
            $worksheet->getStyle($valueCell)->applyFromArray(array_merge($styleArray, [
                'numberFormat' => ['formatCode' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2],
            ]));

            $currentRow++;
        }
    }

    private function configureSheet(AfterSheet $afterSheet): void
    {
        $afterSheet->sheet->freezePane('A3');

        if (session()->get(config('snawbar-datatable.local-direction-session-key', 'direction')) === 'rtl') {
            $afterSheet->sheet->getDelegate()->setRightToLeft(TRUE);
        }

        $afterSheet->sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    }
}
