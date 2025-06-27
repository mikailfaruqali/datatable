<?php

namespace Snawbar\DataTable\Services;

class ColumnStyle
{
    protected string $content;

    protected array $styles = [];

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public static function make(string $content): self
    {
        return new static($content);
    }

    public function backgroundColor(string $color, ?callable $condition = NULL): self
    {
        if (is_null($condition) || $condition($this)) {
            $this->styles['background-color'] = $color;
        }

        return $this;
    }

    public function textColor(string $color, ?callable $condition = NULL): self
    {
        if (is_null($condition) || $condition($this)) {
            $this->styles['color'] = $color;
        }

        return $this;
    }

    public function setFont(string $fontFamily, ?callable $condition = NULL): self
    {
        if (is_null($condition) || $condition($this)) {
            $this->styles['font-family'] = $fontFamily;
        }

        return $this;
    }

    public function setFontSize(int $px, ?callable $condition = NULL): self
    {
        if (is_null($condition) || $condition($this)) {
            $this->styles['font-size'] = sprintf('%spx', $px);
        }

        return $this;
    }

    public function fontWeight(string $weight, ?callable $condition = NULL): self
    {
        if (is_null($condition) || $condition($this)) {
            $this->styles['font-weight'] = $weight;
        }

        return $this;
    }

    public function render(): string
    {
        return sprintf('<span class="fill-column-area" style="%s">%s</span>', $this->buildStyleString(), $this->content);
    }

    private function buildStyleString(): string
    {
        return implode(' ', array_map(fn (string $property, string $value) => sprintf('%s: %s;', $property, $value), array_keys($this->styles), $this->styles));
    }
}
