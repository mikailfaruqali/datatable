<?php

namespace Snawbar\DataTable\Services;

use Closure;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Total
{
    protected array $attributes = [];

    public static function make($column): self
    {
        return tap(new static, function ($instance) use ($column) {
            $instance->attributes += is_array($column) ? $column : ['column' => $column, 'alias' => $column, 'query' => NULL];
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
        return $this->attributes['formatter'] ?? NULL;
    }

    public function relatedColumn($column = NULL): self
    {
        $this->attributes['related_column'] = $column;

        return $this;
    }

    public function getRelatedColumn(): ?string
    {
        return $this->attributes['related_column'] ?? NULL;
    }

    public function visible($flag = TRUE): self
    {
        $this->attributes['visible'] = $flag;

        return $this;
    }

    public function getVisible()
    {
        return $this->attributes['visible'] ?? TRUE;
    }

    public function getRawExpression(): ?Expression
    {
        return $this->rawExpression();
    }

    public function query($query): self
    {
        $this->attributes['query'] = $query;

        return $this;
    }

    public function getQuery(): ?Closure
    {
        return $this->attributes['query'];
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    private function rawExpression(): ?Expression
    {
        if (filled($this->attributes['query'])) {
            return NULL;
        }

        return DB::raw(sprintf('SUM(%s) as %s', $this->getColumn(), $this->getAlias()));
    }
}
