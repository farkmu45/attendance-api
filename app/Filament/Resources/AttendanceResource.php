<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('time')
                    ->sortable()
                    ->dateTime(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state == 'IN' ? 'Entrance' : 'Exit')
                    ->color(fn (string $state): string => match ($state) {
                        'IN' => 'success',
                        'OUT' => 'warning',
                    }),
                IconColumn::make('is_deviate')
                    ->boolean()
                    ->label('On time?'),

            ])
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
