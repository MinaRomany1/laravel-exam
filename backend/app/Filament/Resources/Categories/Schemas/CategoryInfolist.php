<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    TextEntry::make('name'),
                ]),
                Section::make([
                    TextEntry::make('slug'),
                ]),
                section::make([
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->placeholder('-'),
                ]),
                Section::make([
                    TextEntry::make('updated_at')
                        ->dateTime()
                        ->placeholder('-'),
                ])
            ]);
    }
}
