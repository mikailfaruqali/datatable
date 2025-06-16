<?php

namespace Snawbar\DataTable\Services;

class Column
{
    protected array $attributes = [];

    public static function make($data): self
    {
        return tap(new static, fn ($column) => $column->attributes += ['data' => $data, 'name' => $data]);
    }

    public function title($title): self
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function orderable($flag = TRUE): self
    {
        $this->attributes['orderable'] = $flag;

        return $this;
    }

    public function searchable($flag = TRUE): self
    {
        $this->attributes['searchable'] = $flag;

        return $this;
    }

    public function exportable($flag = TRUE): self
    {
        $this->attributes['exportable'] = $flag;

        return $this;
    }

    public function visible($flag = TRUE): self
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function className($class): self
    {
        $this->attributes['className'] = $class;

        return $this;
    }

    public function width($width): self
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    public function render($jsFunction): self
    {
        $this->attributes['render'] = $jsFunction;

        return $this;
    }

    public function defaultContent($value): self
    {
        $this->attributes['defaultContent'] = $value;

        return $this;
    }

    public function name($name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function responsivePriority($priority): self
    {
        $this->attributes['responsivePriority'] = $priority;

        return $this;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
