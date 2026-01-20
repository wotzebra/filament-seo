<?php

namespace Wotz\Seo\Tags;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseTag implements Tag
{
    protected string $attribute = 'name';

    protected string $prefix = '';

    protected string $identifierPrefix = '';

    protected bool $isTranslatable = false;

    protected ?string $content = '';

    final public function __construct(
        protected Model $model,
        protected string $key,
        protected string|Closure|null $defaultAttribute = null,
        protected array $settings = []
    ) {}

    public static function make(
        Model $model,
        string $key,
        string|Closure|null $defaultAttribute = null,
        array $settings = []
    ): static {
        return new static($model, $key, $defaultAttribute, $settings);
    }

    public function beforeSave(?string $content): ?string
    {
        $maxLength = 255;

        // Limit seo content length based on max length rules from config
        foreach ($this->getRules() as $rule) {
            if (strpos($rule, 'max:') !== false) {
                $maxLength = (int) str_replace('max:', '', $rule);
            }
        }

        return Str::limit(strip_tags($content), $maxLength, '');
    }

    public function getIdentifier(): string
    {
        return $this->identifierPrefix . $this->getKey();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function translatable(bool $isTranslatable = true): self
    {
        $this->isTranslatable = $isTranslatable;

        return $this;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function getPrefixedKey(): string
    {
        return $this->prefix . $this->getKey();
    }

    public function content(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(bool $raw = false): string
    {
        return $this->content ?? '';
    }

    public function getDefaultContent(?string $locale = null): string|Closure|null
    {
        if ($this->defaultAttribute instanceof Closure) {
            return app()->call(
                $this->defaultAttribute,
                [
                    'locale' => $locale,
                ]
            );
        }

        if (is_string($this->defaultAttribute)) {
            if ($this->isTranslatable() && method_exists($this->model, 'getTranslation')) {
                return $this->model->getTranslation($this->defaultAttribute, $locale, false);
            }

            return $this->model->getAttribute($this->defaultAttribute);
        }

        return $this->defaultAttribute;
    }

    public function settings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function getSettings(?string $key = null)
    {
        if ($key) {
            return $this->settings[$key] ?? null;
        }

        return $this->settings;
    }

    public function getRules(): array
    {
        $rules = [];

        if (! $this->getSettings('default') && config('filament-seo.rules.default_empty_required')) {
            $rules[] = 'required';
        }

        $configRules = config('filament-seo.rules.fields.' . $this->getIdentifier(), []);

        return array_merge($rules, $configRules);
    }

    public function render(): string
    {
        $content = str_replace('"', '\'', $this->getContent());

        return "<meta {$this->attribute}=\"{$this->getPrefixedKey()}\" content=\"{$content}\">";
    }

    public function __debugInfo()
    {
        return [
            'identifier' => $this->getIdentifier(),
            'key' => $this->getKey(),
            'content' => $this->getContent(),
            'settings' => $this->getSettings(),
            'html' => $this->render(),
        ];
    }
}
