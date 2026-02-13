<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('question_text')
                ->required()
            ,
            TextInput::make('mark')
                ->required()
                ->numeric()
                ->default(1),
            Select::make('question_type')
                ->options([
                    'multiple_choice' => 'Multiple choice',
                    'true_false' => 'True / False',
                    'text' => 'Text',
                ])
                ->reactive()
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state === 'true_false') {
                        $set('answers', [
                            ['answer_text' => 'True', 'is_correct' => false],
                            ['answer_text' => 'False', 'is_correct' => false],
                        ]);
                    }
                }),
            FileUpload::make('image_url')
                ->image(),
            Repeater::make('answers')
                ->relationship()
                ->columnSpanFull()
                ->columns(2)
                ->visible(fn($get) => $get('question_type') !== 'text')
                // Disable add/delete for true/false
                ->addable(fn($get) => $get('question_type') !== 'true_false')
                ->deletable(fn($get) => $get('question_type') !== 'true_false')
                ->reorderable(fn($get) => $get('question_type') !== 'true_false')
                ->rule(function () {
                    return function (string $attribute, $value, \Closure $fail) {
                        $answers = $value ?? [];
                        $correctCount = collect($answers)
                            ->filter(fn($item) => !empty($item['is_correct']))
                            ->count();
                        if ($correctCount !== 1) {
                            $fail('Exactly one answer must be marked as correct.');
                        }
                    };
                })
                ->schema([
                    TextInput::make('answer_text')
                        ->required(),
                    Radio::make('is_correct')
                        ->boolean()
                        ->inline(false)
                ])->live(),
        ]);
    }
}
