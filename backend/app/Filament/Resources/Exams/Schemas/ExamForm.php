<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('duration_minutes')
                    ->label('Duration (minutes)')
                    ->numeric(),
                TextInput::make('description'),
                Select::make('category_id')
                    ->relationship(name: 'category', titleAttribute: 'name'),
                DateTimePicker::make('start_time'),
                DateTimePicker::make('end_time'),
                Repeater::make('examQuestions')
                    ->relationship()
                    ->schema([
                        Select::make('question_id')
                            ->label('Question')
                            ->options(
                                \App\Models\Question::pluck('question_text', 'id')
                            )
                            ->searchable()
                            ->required()
                    ])
                    ->orderColumn('order_index')
                    ->reorderableWithDragAndDrop()->columnSpanFull()
                    ->addActionLabel('Attach Question'),
                Toggle::make('multiple_attempts')
                    ->required(),
                Toggle::make('shuffle_questions')
                    ->required(),
                Toggle::make('shuffle_answers')
                    ->required(),
                Toggle::make('show_results')
                    ->required(),
                Toggle::make('show_detailed_result')
                    ->required(),
                Toggle::make('enable_image_answers')
                    ->required(),
            ]);
    }
}
