<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UserAccount extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Akun';
    protected static ?string $navigationLabel = 'Akun';
    protected static string $view = 'filament.pages.monthly-plan';

    use InteractsWithForms;

    protected static ?string $title = 'Profil Akun';

    // State untuk menyimpan data
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama Lengkap')
                ->required(),
            TextInput::make('email')
                ->label('Alamat Email')
                ->email()
                ->required(),
        ];
    }

    protected function getFormModel(): \Illuminate\Database\Eloquent\Model
    {
        return Auth::user();
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        Auth::user()->update($data);
        
        $this->notify('success', 'Profil berhasil diperbarui.');
    }
}
