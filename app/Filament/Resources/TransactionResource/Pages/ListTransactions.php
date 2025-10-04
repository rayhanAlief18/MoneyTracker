<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Widgets\MonthlyPlan; // <<< Import widget ini
use App\Models\categoriesModel;
use App\Models\transactionModel;
use App\Filament\Resources\TransactionResource;
use App\Filament\Widgets\MonthlyPlanStats; // <<< Import widget ini
use App\Models\monthlyPlanModel;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Cashflow')
                ->icon('heroicon-o-plus')
                ->color('primary'),

            Actions\Action::make('monthlyPlanStats')
                ->label('Buat Rencana Bulanan')
                ->icon('heroicon-o-chart-bar')
                ->color('primary')
                ->modalSubmitActionLabel('Save Plan')

                ->form([
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('categories', 'name',fn($query)=>$query->where('type','pengeluaran'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->afterStateUpdated(function($set, ?string $state, $get){
                            $categoryName = categoriesModel::find($state)->name ?? '';
                            $mothNow = Carbon::now()->locale('id')->translatedFormat('F');
                            $yearNow = Carbon::now()->year;

                            if($categoryName){
                                $generatedName = $categoryName . ' ' . $mothNow . ' ' . $yearNow;
                                $set('name', $generatedName);
                            }else{
                                $set('name','');
                            }
                        })->live(),
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Rencana')
                        ->maxLength(255)
                        ->readOnly(true) // Biar tidak bisa diubah manual
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Rencana')
                        ->required(),
                    Forms\Components\TextInput::make('max_amount')
                        ->label('Jumlah Maksimum')
                        ->numeric()
                        ->inputMode('decimal')
                        ->default(0)
                        ->prefix('IDR')
                        ->required(),
                    Forms\Components\TextInput::make('amount_now')
                        ->label('Jumlah Sekarang')
                        ->numeric()
                        ->inputMode('decimal')
                        ->default(0)
                        ->prefix('IDR')
                        ->required(),
                    
                ])

                ->action(function (array $data) {
                    try{
                        $dataAda = monthlyPlanModel::get();

                        if($dataAda->where('name',$data['name'])->count() > 0) {
                            Notification::make()
                                ->title('Rencana Bulan Sudah Ada')
                                ->danger()
                                ->send();
                            return;
                        }

                        monthlyPlanModel::create([
                            'user_id' => auth()->id(),
                            'name' => $data['name'], // Tambahkan bulan dan tahun saat ini
                            'description' => $data['description'],
                            'max_amount' => $data['max_amount'],
                            'amount_now' => $data['amount_now'],
                            'year' => Carbon::now()->year,
                            'month' =>  Carbon::now()->locale('id')->translatedFormat('F'),
                            'category_id' => $data['category_id'],
                        ]);
                        Notification::make()
                            ->title('Rencana Bulan Berhasil Dibuat')
                            ->success()
                            ->send();

                        
                    }catch (\Exception $e) {
                        // Handle exception
                        Notification::make()
                            ->title('Failed to create monthly plan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }

                }),

        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlyPlan::class, // Tambahkan widget MonthlyPlan
        ];
    }
}
