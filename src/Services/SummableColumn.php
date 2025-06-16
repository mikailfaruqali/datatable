<?php

namespace Snawbar\DataTable\Services;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class SummableColumn
{
    protected array $attributes = [];

    public static function make($column): static
    {
        return tap(new static, fn ($instance) => $instance->attributes += [
            'key' => $column,
            'alias' => $column,
        ]);
    }

    public function key($key): static
    {
        $this->attributes['key'] = $key;

        return $this;
    }

    public function alias($alias): static
    {
        $this->attributes['alias'] = $alias;

        return $this;
    }

    public function title($title): static
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function formatUsing($callback = NULL): static
    {
        $this->attributes['formatter'] = $callback;

        return $this;
    }

    public function relatedColumn($column = NULL): static
    {
        $this->attributes['column'] = $column;

        return $this;
    }

    public function visible($flag = TRUE): static
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function selector(): self
    {
        $this->attributes['selector'] ??= $this->getKey();

        return $this;
    }

    public function getKey(): string
    {
        return $this->attributes['key'] ?? $this->attributes['column'];
    }

    public function getAlias(): string
    {
        return $this->attributes['alias'] ?? $this->getKey();
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? $this->getKey();
    }

    public function rawExpression(): Expression
    {
        return DB::raw(sprintf('SUM(%s) as %s', $this->getKey(), $this->getAlias()));
    }

    public function getColumn(): ?string
    {
        return $this->attributes['column'] ?? NULL;
    }

    public function getVisible()
    {
        return $this->attributes['visible'] ?? TRUE;
    }

    public function getFormmater(): ?callable
    {
        return $this->attributes['formatter'] ?? NULL;
    }

    public function getSelector(): string
    {
        return $this->attributes['selector'] ?? $this->getKey();
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
