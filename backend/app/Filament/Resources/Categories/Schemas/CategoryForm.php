<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Section;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($get('auto_slug')) {
                                $set('slug', Str::slug($state));
                            }
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->disabled(fn(callable $get) => $get('auto_slug'))
                        ->unique(ignoreRecord: true),
                ]),
                Section::make([

                    Checkbox::make('auto_slug')
                        ->label('Auto generate slug')
                        ->default(true)
                        ->live()
                        ->dehydrated(false), // not stored in DB
                ]),

            ]);
    }
}
