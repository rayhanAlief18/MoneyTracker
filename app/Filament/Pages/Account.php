<?php

namespace App\Filament\Pages;

use Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filmaent\Forms;
use Filament\Forms\Form; 
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
class Account extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Akun Saya';
    protected static ?string $title = 'Edit Akun';
    protected static string $view = 'filament.pages.account.account-page';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill(Auth::user()->toArray());
    }

    protected function getFormSchema():array
    {
        return [
            TextInput::make('name')
                ->label('Nama')
                ->required()
                ->columnSpan(1),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->columnSpan(1),

                TextInput::make('job')
                ->label('Pekerjaan')
                ->required()
                ->columnSpan(2),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->nullable()
                ->columnSpan(1)
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),

            TextInput::make('password_confirmation')
            ->label('Konfirmasi Password')
            ->password()
            ->columnSpan(1)
            ->same('password')
            ->dehydrated()
        ];
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->columns(2); // <--- penting
    }
    public function update()
    {
        $user = Auth::user();
        $state = $this->form->getState();
        if (array_key_exists('password', $state) && $state['password'] === null) {
            unset($state['password']);
        }
        $user->update($this->form->getState());

        Notification::make()
        ->title('Akun berhasil diperbarui')
        ->success()
        ->send();
    }
}
