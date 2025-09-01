<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialPlanProgessResource\Pages;
use App\Filament\Resources\FinancialPlanProgessResource\RelationManagers;
use App\Models\financialPlanModel as FinancialPlan;
use App\Models\financialPlanProgressModel as FinancialPlanProgess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\financialPlanProgressModel;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancialPlanProgessResource extends Resource
{
    protected static ?string $model = FinancialPlanProgess::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Money Trakcer';
    protected static ?string $navigationLabel = 'Progress Tabungan';
    protected static ?int $navigationSort = 4;
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->whereHas('financialPlan', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_financial_plan')
                    ->label('Rencana Keuangan')
                    ->relationship('financialPlan', 'name',
                    function (Builder $query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Disetor')
                    ->numeric()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $plan = financialPlan::find($get('id_financial_plan'));
                        if ($plan && $plan->target_amount > 0) {
                            $total = floatval($state);
                            $progress = ($total / $plan->target_amount) * 100;
                            $set('presentase_progress', number_format($progress, 2));
                        }
                    }),

                Forms\Components\TextInput::make('presentase_progress')
                    ->label('Presentase')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(), // tetap disimpan walaupun disabled

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->default(today())
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('financialPlan.name')
                    ->label('Rencana'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('presentase_progress')
                    ->label('Progres')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date(),
            ])
            // ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('id_financial_plan')
                    ->relationship('financialPlan', 'name')
                    ->label('Rencana Keuangan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListFinancialPlanProgesses::route('/'),
            'create' => Pages\CreateFinancialPlanProgess::route('/create'),
            'edit' => Pages\EditFinancialPlanProgess::route('/{record}/edit'),
        ];
    }
}
