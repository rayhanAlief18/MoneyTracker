<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\moneyPlacingModel;
use App\Models\transactionModel as Transaction;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Str;
use Filament\Forms\Components; // Import ini untuk mempermudah
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $pluralModelLabel = 'Cashflow (income / outcome)';
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Cashflow';
    protected static ?string $navigationGroup = 'Money Trakcer';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())->orderBy('date','desc');    
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('money_placing_id')
                ->label('Dari penempatan')
                // ->relationship('moneyPlacing','name'),
                ->options(function (){
                    $option =[];
                    $moneyPlacing = moneyPlacingModel::where('user_id',auth()->id())->get();
                    foreach($moneyPlacing as $mp){
                        $option[$mp->id] = $mp->name. ' (Rp '.number_format($mp->amount,0,',','.').')';
                    }
                    return $option;
                }),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->live() // Supaya bisa reactive
                    ->required(),
                
                Forms\Components\Select::make('categories_id')
                ->label('Kategori')
                    ->options(function ($get) {
                        $type = $get('type');
                        if ($type === 'pemasukan') {
                            return \App\Models\categoriesModel::where('type', 'pemasukan')->pluck('name', 'id');
                        } elseif ($type === 'pengeluaran') {
                            return \App\Models\categoriesModel::where('type', 'pengeluaran')->pluck('name', 'id');
                        }
                        return \App\Models\categoriesModel::pluck('name', 'id');
                    })
                    ->required()
                    ->visible(fn (Forms\Get $get) => !is_null($get('type'))),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(Carbon::now()),

                Forms\Components\Textarea::make('note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(255),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('type')->label('Tipe')->searchable(),
                        Tables\Columns\TextColumn::make('date')->label('Tanggal')->date(),
                    ]),
                    Stack::make([
                        Tables\Columns\TextColumn::make('categories.name')->label('Kategori')->searchable(),
                        Tables\Columns\TextColumn::make('amount')->money('IDR', true)
                        ->color(fn($record)=> $record->type === 'pemasukan' ? 'success' : 'danger')
                        ->prefix(fn($record)=> $record->type === 'pemasukan' ? '+ ' : '- ')
                        ->searchable(),
                    ]),
                ])
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                        'hutang' => 'Hutang',
                    ]),
                Tables\Filters\SelectFilter::make('money_placing_id')
                    ->label('Alokasi Uang')
                    ->options(function(){
                        $option = moneyPlacingModel::where('user_id',auth()->id())->pluck('name','id');
                        return $option;
                    }),
                Tables\Filters\SelectFilter::make('categories_id')
                    ->label('Kategori')
                    ->relationship('categories', 'name'),
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        return Transaction::query()
                            ->selectRaw('DISTINCT YEAR(date) as year')
                            ->orderBy('year')
                            ->pluck('year', 'year');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value']) && $data['value'] !== '') {
                            $query->whereYear('date', intval($data['value']));
                        }
                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options(function () {
                        $months = Transaction::query()
                        ->selectRaw('DISTINCT MONTH(date) as month_number')
                        ->orderBy('month_number')
                        ->pluck('month_number')
                        ->mapWithKeys(function ($monthNumber){
                            return [$monthNumber => Carbon::createFromFormat('m',$monthNumber)->locale('id')->translatedFormat('F')];
                        })->toArray();
                        return $months;
                    })
                    ->query(function(Builder $query, array $data) {
                        if (isset($data['value'])&& $data['value'] !== '') {
                            $query->whereMonth('date', intval($data['value']));
                        }
                        return $query;
                    }),
                    
            ])->actions([
                
                Action::make('note')
                ->label('Note')
                ->modalHeading('Deskripsi Cashflow')
                ->icon('heroicon-o-information-circle')
                ->modalContent(
                    function(Model $record){
                        return view('filament.pages.transaction-resource.modal-description-transaction',[
                            'record'=>$record,
                        ]);
                    }
                )->modalSubmitActionLabel('Tutup') // ganti label submit,
                ->modalCancelAction(false), // hilangkan tombol cancel,
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->visible(function($record){
                        return !(
                            $record->type == 'hutang' 
                            || in_array($record->categories_id, [8,9,10,11]) 
                        );
                    }),
                    DeleteAction::make()->visible(function($record){
                        return !(
                            $record->type == 'hutang' 
                            || in_array($record->categories_id, [8,9,10,11]) 
                        );
                    }),
                ]),

                // Tables\Actions\EditAction::make()->visible(function($record){
                //     return !(
                //         $record->type == 'hutang' 
                //         || in_array($record->categories_id, [8,9,10,11]) 
                //     );
                // }),
                // Tables\Actions\DeleteAction::make()->visible(function($record){
                //     return !(
                //         $record->type == 'hutang' 
                //         || in_array($record->categories_id, [8,9,10,11]) 
                //     );
                // }),

                
            ])->bulkActions([
                
            ])

            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tambah Data Cashflow')
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
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
