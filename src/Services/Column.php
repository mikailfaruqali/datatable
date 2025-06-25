<?php

namespace Snawbar\DataTable\Services;

class Column
{
    protected array $attributes = [];

    public static function make($column): self
    {
        return tap(new static, function ($instance) use ($column) {
            $instance->attributes += is_array($column) ? $column : ['data' => $column, 'title' => $column];
        });
    }

    public function getData(): string
    {
        return $this->attributes['data'];
    }

    public function title($title): self
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? $this->getData();
    }

    public function orderable($flag = TRUE): self
    {
        $this->attributes['orderable'] = $flag;

        return $this;
    }

    public function getOrderable(): string
    {
        return $this->attributes['orderable'] ?? TRUE;
    }

    public function exportable($flag = TRUE): self
    {
        $this->attributes['exportable'] = $flag;

        return $this;
    }

    public function getExportable(): string
    {
        return $this->attributes['exportable'] ?? TRUE;
    }

    public function visible($flag = TRUE): self
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function getVisible(): string
    {
        return $this->attributes['visible'] ?? TRUE;
    }

    public function className($class): self
    {
        $this->attributes['className'] = $class;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->attributes['className'] ?? NULL;
    }

    public function width($width): self
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->attributes['width'] ?? NULL;
    }

    public function defaultContent($value): self
    {
        $this->attributes['defaultContent'] = $value;

        return $this;
    }

    public function getDefaultContent(): ?string
    {
        return $this->attributes['defaultContent'] ?? NULL;
    }

    public function name($name): self
    {
        $this->attributes['name'] = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->attributes['name'] ?? NULL;
    }

    public function responsivePriority($priority): self
    {
        $this->attributes['responsivePriority'] = $priority;

        return $this;
    }

    public function getResponsivePriority(): ?int
    {
        return $this->attributes['responsivePriority'] ?? NULL;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
