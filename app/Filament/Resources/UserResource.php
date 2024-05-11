<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        FileUpload::make('picture')
                            ->image()
                            ->maxSize(4000)
                            ->imageEditor()
                            ->imagePreviewHeight(300)
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->directory('pictures')
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->required(fn (string $operation) => $operation == 'create')
                            ->columnSpanFull()
                            ->rule(Password::default())
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->same('passwordConfirmation'),
                        TextInput::make('passwordConfirmation')
                            ->label('Confirm password')
                            ->password()
                            ->revealable()
                            ->columnSpanFull()
                            ->required(fn (Get $get) => $get('password') != null)
                            ->dehydrated(false),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Created')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
