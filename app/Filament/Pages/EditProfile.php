<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\UploadService;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Perfil')->schema([
                    FileUpload::make('avatar_path')
                        ->label('Foto')
                        ->disk($this->uploadService()->disk())
                        ->visibility('public')
                        ->image()
                        ->avatar()
                        ->directory($this->uploadService()->userDirectory()),
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                    $this->getCurrentPasswordFormComponent(),
                ])->columnSpanFull(),
            ]);
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label('E-mail')
            ->email()
            ->required()
            ->maxLength(255)
            ->disabled();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['email']);

        return $data;
    }

    private function uploadService(): UploadService
    {
        return resolve(UploadService::class);
    }
}
