<?php

use Wotz\Seo\Tags\BaseTag;
use Wotz\Seo\Tags\Tag;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->page = new \Wotz\Seo\Tests\Fixtures\Models\Page;
});

it('can construct a class', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->toBeInstanceOf(Tag::class);
});

it('can save content with a maximum length when length is longer than 255', function () {
    $content = fake()->realTextBetween(280, 300);

    $tag = BaseTag::make($this->page, 'key', $content);

    expect($tag->beforeSave($content))
        ->toBe(Str::limit(strip_tags($content), 255, ''));
});

it('can save content with a maximum length when length is set in the rules', function () {
    config([
        'filament-seo.rules.fields' => [
            'key' => [
                'max:125',
            ],
        ],
    ]);

    $content = fake()->realTextBetween(280, 300);

    $tag = BaseTag::make($this->page, 'key', $content);

    expect($tag->beforeSave($content))
        ->toBe(Str::limit(strip_tags($content), 125, ''));
});

it('has an identifier', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getIdentifier()->toBe('key');
});

it('has a key', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getKey()->toBe('key');
});

it('has a prefixed key', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getPrefixedKey()->toBe('key');
});

it('has content', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getContent()->toBe('');
});

it('has settings', function () {
    expect(BaseTag::make($this->page, 'key', 'content', ['settings']))
        ->getSettings()->toBe(['settings']);
});

it('can return a single setting', function () {
    expect(BaseTag::make($this->page, 'key', 'content', ['settings' => 'value']))
        ->getSettings('settings')->toBe('value');
});

it('returns null when key is not found', function () {
    expect(BaseTag::make($this->page, 'key', 'content', ['settings' => 'value']))
        ->getSettings('not-existing')->toBe(null);
});

it('can render', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->render()->toBe('<meta name="key" content="">');
});

it('has no rules', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getRules()->toBe([]);
});

it('has a required rule when no default setting and filament-seo.rules.default_empty_required is true', function () {
    config([
        'filament-seo.rules.default_empty_required' => true,
    ]);

    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getRules()->toBe(['required']);
});

it('has no required rule when no default setting and filament-seo.rules.default_empty_required is true', function () {
    config([
        'filament-seo.rules.default_empty_required' => false,
    ]);

    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getRules()->toBe([]);
});

it('can set rules via the config', function () {
    config([
        'filament-seo.rules.fields.key' => [
            'max:100',
            'required',
        ],
    ]);

    expect(BaseTag::make($this->page, 'key', 'content'))
        ->getRules()->toBe([
            'max:100',
            'required',
        ]);
});

it('has debug info', function () {
    expect(BaseTag::make($this->page, 'key', 'content'))
        ->__debugInfo()->toBe([
            'identifier' => 'key',
            'key' => 'key',
            'content' => '',
            'settings' => [],
            'html' => '<meta name="key" content="">',
        ]);
});
