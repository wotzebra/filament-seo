<?php

namespace Wotz\Seo\Filament\Traits;

use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;

trait SavesSeoFields
{
    public function afterSave(): void
    {
        $this->saveSeoFields();
    }

    public function afterCreate(): void
    {
        $this->saveSeoFields();
    }

    protected function saveSeoFields(): void
    {
        $state = $this->form->getState();

        if (! $this->hasSeoFields($state)) {
            return;
        }

        $seoFieldState = [];

        foreach ($state as $key => $value) {
            if ($key === 'seoFields') {
                $seoFieldState += $value;
            }

            if (is_array($value) && array_key_exists('seoFields', $value)) {
                foreach ($value['seoFields'] as $seoName => $seoValue) {
                    data_set($seoFieldState, "{$seoName}.{$key}", $seoValue);
                }
            }
        }

        $this->record->saveSeoFieldState($seoFieldState);

        $this->record->refresh();

        $this->data['seoFields'] = $this->record->fillSeoFieldState();

        LocaleCollection::each(function (Locale $locale) {
            $this->data[$locale->locale()]['seoFields'] = $this->record->fillSeoFieldState($locale->locale());
        });
    }

    protected function hasSeoFields(array $state): bool
    {
        if (isset($state['seoFields'])) {
            return true;
        }

        return LocaleCollection::some(fn (Locale $locale): bool => isset($state[$locale->locale()]['seoFields']));
    }
}
