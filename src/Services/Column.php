<?php

namespace Snawbar\DataTable\Services;

use InvalidArgumentException;

class Column
{
    protected const ALLOWED_TYPES = [
        'number', 'float',
    ];

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

    public function responsivePriority($priority): self
    {
        $this->attributes['responsivePriority'] = $priority;

        return $this;
    }

    public function getResponsivePriority(): ?int
    {
        return $this->attributes['responsivePriority'] ?? NULL;
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

    public function type(string $type): self
    {
        throw_unless(in_array($type, static::ALLOWED_TYPES), new InvalidArgumentException(sprintf('Invalid column type: %s', $type)));

        $this->attributes['type'] = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->attributes['type'] ?? NULL;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
