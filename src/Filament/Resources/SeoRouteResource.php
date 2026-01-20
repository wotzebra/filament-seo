<?php

namespace Wotz\Seo\Filament\Resources;

use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\MediaLibrary\Filament\AttachmentInput;
use Wotz\MediaLibrary\Tables\Columns\AttachmentColumn;
use Wotz\Seo\Models\SeoRoute;
use Wotz\TranslatableTabs\Forms\TranslatableTabs;
use Wotz\TranslatableTabs\Tables\LocalesColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class SeoRouteResource extends Resource
{
    protected static ?string $model = SeoRoute::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'SEO';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            TranslatableTabs::make()
                ->defaultFields([
                    TextInput::make('route')->disabled(),
                    TextInput::make('og_type'),
                    AttachmentInput::make('og_image'),
                ])
                ->translatableFields(fn (string $locale) => [
                    TextInput::make('og_title'),
                    Textarea::make('og_description'),
                    TextInput::make('meta_title'),
                    Textarea::make('meta_description'),
                    Checkbox::make('online'),

                ])->columnSpan(['lg' => 2]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route'),
                TextColumn::make('url_list')
                    ->getStateUsing(
                        static function (SeoRoute $record) {
                            $route = Route::getRoutes()->getByName($record->route);

                            if ($route) {
                                return $route->uri;
                            }

                            return LocaleCollection::map(function (Locale $locale) use ($record) {
                                return Route::getRoutes()->getByName($locale->locale() . '.' . $record->route)?->uri;
                            })->filter()->join('<br>');
                        }
                    )
                    ->html(),
                TextColumn::make('og_type'),
                AttachmentColumn::make('og_image'),
                LocalesColumn::make('online'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoRoutes::route('/'),
            'edit' => Pages\EditSeoRoute::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
