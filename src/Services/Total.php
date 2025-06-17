<?php

namespace Snawbar\DataTable\Services;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Total
{
    protected array $attributes = [];

    public static function make($column): self
    {
        return tap(new static, fn ($instance) => $instance->attributes += [
            'column' => $column,
            'alias' => $column,
        ]);
    }

    public function column($column): self
    {
        $this->attributes['column'] = $column;

        return $this;
    }

    public function alias($alias): self
    {
        $this->attributes['alias'] = $alias;

        return $this;
    }

    public function title($title): self
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function formatUsing($callback = NULL): self
    {
        $this->attributes['formatter'] = $callback;

        return $this;
    }

    public function relatedColumn($column = NULL): self
    {
        $this->attributes['column'] = $column;

        return $this;
    }

    public function visible($flag = TRUE): self
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->attributes['key'] ?? $this->attributes['column'];
    }

    public function getAlias(): string
    {
        return $this->attributes['alias'] ?? $this->getColumn();
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? $this->getColumn();
    }

    public function rawExpression(): Expression
    {
        return DB::raw(sprintf('SUM(%s) as %s', $this->getColumn(), $this->getAlias()));
    }

    public function getVisible()
    {
        return $this->attributes['visible'] ?? TRUE;
    }

    public function getFormmater(): ?callable
    {
        return $this->attributes['formatter'] ?? NULL;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
