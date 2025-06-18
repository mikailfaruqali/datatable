<?php

namespace Snawbar\DataTable\Services;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Total
{
    protected array $attributes = [];

    public static function make($column): self
    {
        return tap(new static, function ($instance) use ($column) {
            $instance->attributes += is_array($column) ? $column : ['column' => $column, 'alias' => $column];
        });
    }

    public function column($column): self
    {
        $this->attributes['column'] = $column;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->attributes['column'];
    }

    public function alias($alias): self
    {
        $this->attributes['alias'] = $alias;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->attributes['alias'] ?? $this->getColumn();
    }

    public function title($title): self
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->attributes['title'] ?? $this->getColumn();
    }

    public function formatUsing($callback = NULL): self
    {
        $this->attributes['formatter'] = $callback;

        return $this;
    }

    public function getFormatter(): ?callable
    {
        return $this->attributes['formatter'];
    }

    public function relatedColumn($column = NULL): self
    {
        $this->attributes['column'] = $column;

        return $this;
    }

    public function getRelatedColumn($flag = TRUE): self
    {
        $this->attributes['orderable'] = $flag;

        return $this;
    }

    public function visible($flag = TRUE): self
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function getVisible(): bool
    {
        return $this->attributes['visible'] ?? TRUE;
    }

    public function rawExpression(): Expression
    {
        return DB::raw(sprintf('SUM(%s) as %s', $this->getColumn(), $this->getAlias()));
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
