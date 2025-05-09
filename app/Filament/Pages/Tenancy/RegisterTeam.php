<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Business;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use TomatoPHP\FilamentLocations\Models\Country;

class RegisterTeam extends RegisterTenant
{
    protected ?string $maxWidth = '3xl';
    public static function getLabel(): string
    {
        return 'Add Business';
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
                        Register
                    </x-filament::button>
                    BLADE))),

            ]);
    }
    protected function getFormActions(): array
    {
        return [];
    }
    protected function handleRegistration(array $data): Business
    {
        // Create the new business (team)
        $team = Business::create($data);

        // Get the currently authenticated user
        $user = auth()->user();

        if ($user) {
            // Immediately update the user's business_id to the newly created team
            $user->update([
                'business_id' => $team->id,
            ]);

            // Attach the user as a member of the business
            $team->members()->attach($user);
        }

        // Find the 'super_admin' role and update its business_id if it exists
        $role = Role::where('name', 'super_admin')->first();

        if ($role) {
            $role->update([
                'business_id' => $team->id,
            ]);
        }

        // Return the newly created business (team)
        return $team;
    }

}
