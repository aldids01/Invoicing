<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Business;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use TomatoPHP\FilamentLocations\Models\Country;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Business profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Business Information')
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->label('')
                                    ->visible('public')
                                    ->avatar()
                                    ->alignCenter()
                                    ->columnSpanFull()
                                    ->imageEditor()
                                    ->circleCropper()
                                    ->directory('businesses'),
                                TextInput::make('name')
                                    ->label('Business Name')
                                    ->columnSpanFull()
                                    ->required()->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                hidden::make('slug')
                                    ->unique(Business::class, 'slug', ignoreRecord: true),
                                TextInput::make('phone')
                                    ->tel()
                                    ->label('Business Phone')
                                    ->unique(ignoreRecord: true),
                                TextInput::make('email')
                                    ->email()
                                    ->label('Business Email')
                                    ->unique(ignoreRecord: true),

                                RichEditor::make('description')
                                    ->columnSpanFull()
                                    ->label('Business Description'),
                            ])->columnSpan(2)->columns(2),
                        Forms\Components\Wizard\Step::make('Business Address')
                            ->schema([
                                TextInput::make('website'),
                                TextInput::make('address'),
                                TextInput::make('city'),
                                Forms\Components\Select::make('country')
                                    ->default('Nigeria')
                                    ->searchable()
                                    ->options(Country::query()->pluck('name', 'code')->toArray()),
                                TextInput::make('postal_Code'),
                                TextInput::make('reg_no'),
                                TextInput::make('tax_id'),
                            ])->columnSpan(2)->columns(2)
                    ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        size="sm"
                        wire:submit="register"
                    >
                        Save
                    </x-filament::button>
                    BLADE))),

            ]);
    }
    protected function getFormActions(): array
    {
        return [];
    }
}
