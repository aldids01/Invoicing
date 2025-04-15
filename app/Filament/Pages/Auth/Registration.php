<?php
namespace App\Filament\Pages\Auth;

use App\Models\Business;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;

class Registration extends Register
{
    protected ?string $maxWidth = '3xl';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Contact')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                        ]),
//                    Wizard\Step::make('Business')
//                        ->schema([
//                            Forms\Components\Repeater::make('business')
//                                ->relationship('business' )
//                                ->schema([
//                                    TextInput::make('name')
//                                        ->label('Business Name')
//                                        ->required()
//                                        ->live(onBlur: true)
//                                        ->afterStateUpdated(function (string $operation, $state, Set $set) {
//                                            $set('slug', Str::slug($state));
//                                        }),
//                                    TextInput::make('slug')
//                                        ->disabled()
//                                        ->dehydrated()
//                                        ->required()
//                                        ->maxLength(255)
//                                        ->unique(Business::class, 'slug', ignoreRecord: true),
//                                    TextInput::make('phone')->label('Business Phone')->unique(ignoreRecord: true),
//                                    TextInput::make('email')->label('Business Email')->unique(ignoreRecord: true),
//                                ])
//                        ]),
                    Wizard\Step::make('Password')
                        ->schema([
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
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
    public function register(): RegistrationResponse
    {
        $data = $this->form->getState();

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign "Super Admin" role
        $user->assignRole('super_Admin');

        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Success Notification
        Notification::make()
            ->title('Registration Successful')
            ->success()
            ->send();

        // Redirect to dashboard
        return app(RegistrationResponse::class);
    }
    protected function getGithubFormComponent(): Component
    {
        return TextInput::make('github')
            ->prefix('https://github.com/')
            ->label(__('GitHub'))
            ->maxLength(255);
    }

    protected function getTwitterFormComponent(): Component
    {
        return TextInput::make('twitter')
            ->prefix('https://x.com/')
            ->label(__('Twitter (X)'))
            ->maxLength(255);
    }
}
