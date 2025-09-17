<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialPlanResource\Pages;
use App\Filament\Resources\FinancialPlanResource\RelationManagers;
use App\Models\financialPlanModel as FinancialPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
class FinancialPlanResource extends Resource
{
    protected static ?string $model = FinancialPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Money Trakcer';
    protected static ?string $navigationLabel = 'Rencana Keuangan';
    // Tambahkan properti ini untuk mengubah judul halaman
    protected static ?string $pluralModelLabel = 'Rencana Keuangan';
    protected static ?string $breadcrumb = 'Rencana Keuangan';

    protected static ?int $navigationSort = 3;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\Textarea::make('description')
                    ->label('Rencana')
                    ->rows(2),

                Forms\Components\TextInput::make('target_amount')
                    ->label('Target Jumlah')
                    ->numeric()
                    ->prefix('IDR')
                    ->dehydrated() // agar tetap dikirim ke backend
                    ->required(),

                Forms\Components\TextInput::make('amount_now')
                    ->label('Jumlah Saat Ini')
                    ->prefix('IDR')
                    ->dehydrated() // agar tetap dikirim ke backend
                    ->numeric()
                    ->default(0),

                Forms\Components\DatePicker::make('target_date')
                    ->label('Tanggal Target')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('description')->limit(30),
                        Tables\Columns\TextColumn::make('target_date')->date()->hidden(false),
                    ]),
                    Tables\Columns\TextColumn::make('target_amount')->prefix('Target: ')->money('IDR', true),
                    Stack::make([
                        Tables\Columns\TextColumn::make('amount_now')->money('IDR', true),
                        Tables\Columns\TextColumn::make('progress_persen')
                            ->label('Progress (%)')->prefix('Progress: ')
                            ->getStateUsing(function ($record) {
                                if (!$record->target_amount || $record->target_amount == 0) {
                                    return '0%';
                                }

                                $percent = ($record->amount_now / $record->target_amount) * 100;

                                return number_format($percent, 2) . '%';
                            })->color(fn($record) => ($record->amount_now / $record->target_amount) * 100 < 99 ? 'warning' : 'sucess'),

                    ]),
                ]),
            ])
            ->defaultSort('target_date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Buat Rencana Keuangan')
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
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
            'index' => Pages\ListFinancialPlans::route('/'),
            'create' => Pages\CreateFinancialPlan::route('/create'),
            'edit' => Pages\EditFinancialPlan::route('/{record}/edit'),
        ];
    }
}
