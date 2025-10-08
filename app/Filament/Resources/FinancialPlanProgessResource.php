<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancialPlanProgessResource\Pages;
use App\Filament\Resources\FinancialPlanProgessResource\RelationManagers;
use App\Models\financialPlanModel as FinancialPlan;
use App\Models\financialPlanProgressModel as FinancialPlanProgess;
use App\Models\moneyPlacingModel as MoneyPlacing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Hidden;

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
                    ->relationship(
                        'financialPlan',
                        'description',
                        function (Builder $query) {
                            $query->where('user_id', auth()->id());
                        }
                    )
                    ->required(),
                    Forms\Components\Select::make('money_placing_id')
                    ->label('Alokasi Uang (nominal akan berkurang dari alokasi yang dipilih)')
                    ->options(function () {
                        $userId = auth()->id();
                        $moneyPlacings = MoneyPlacing::where('user_id', $userId)->get();
                        $options = [];
                        foreach ($moneyPlacings as $placing) {
                            $options[$placing->id] = $placing->name . ' (Rp.' . number_format($placing->amount, 0, ',', '.') . ')';
                        }
                        return $options;
                    })
                    ->required(),

                    Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Disetor')
                    ->prefix('IDR')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ,

                Hidden::make('presentase_progress')->default(0),


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
                Split::make([
                    Tables\Columns\TextColumn::make('financialPlan.description')
                        ->label('Rencana'),
                    Stack::make([

                        Tables\Columns\TextColumn::make('amount')
                            ->label('Jumlah')
                            ->money('IDR', true),

                        Tables\Columns\TextColumn::make('presentase_progress')
                            ->label('Progres')
                            ->prefix('+ ')
                            ->suffix('%')
                            ->color('success'),

                    ]),
                    Tables\Columns\TextColumn::make('date')
                        ->label('Tanggal')
                        ->date(),
                ])
            ])
            // ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('id_financial_plan')
                    ->relationship('financialPlan', 'description')
                    ->label('Rencana Keuangan'),
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
            ->emptyStateHeading('Belum ada "Progress Tabungan"')
            ->emptyStateDescription('Progress tabungan adalah rekap proses menabung dari menu "Tabungan"')
            ->emptyStateIcon('heroicon-o-plus')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Data Progress Tabungan')
                    ->
                    icon('heroicon-o-plus')->
                    color('primary'),
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
