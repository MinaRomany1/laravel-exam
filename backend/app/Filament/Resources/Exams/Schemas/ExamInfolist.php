<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('duration')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-'),
                TextEntry::make('category_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('start_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_time')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('multiple_attempts')
                    ->boolean(),
                IconEntry::make('shuffle_questions')
                    ->boolean(),
                IconEntry::make('shuffle_answers')
                    ->boolean(),
                IconEntry::make('show_results')
                    ->boolean(),
                IconEntry::make('show_detailed_result')
                    ->boolean(),
                IconEntry::make('enable_image_answers')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
