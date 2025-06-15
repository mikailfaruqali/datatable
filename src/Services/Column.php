<?php

namespace Snawbar\DataTable\Services;

class Column
{
    protected array $attributes = [];

    public static function make($data): static
    {
        return tap(new static, fn ($column) => $column->attributes += ['data' => $data, 'name' => $data]);
    }

    public function title($title): static
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function orderable($flag = TRUE): static
    {
        $this->attributes['orderable'] = $flag;

        return $this;
    }

    public function searchable($flag = TRUE): static
    {
        $this->attributes['searchable'] = $flag;

        return $this;
    }

    public function exportable($flag = TRUE): static
    {
        $this->attributes['exportable'] = $flag;

        return $this;
    }

    public function visible($flag = TRUE): static
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function className($class): static
    {
        $this->attributes['className'] = $class;

        return $this;
    }

    public function width($width): static
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    public function render($jsFunction): static
    {
        $this->attributes['render'] = $jsFunction;

        return $this;
    }

    public function defaultContent($value): static
    {
        $this->attributes['defaultContent'] = $value;

        return $this;
    }

    public function name($name): static
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
