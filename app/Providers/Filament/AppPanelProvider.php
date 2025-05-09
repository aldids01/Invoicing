<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Models\Business;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Forms\Components\FileUpload;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use TomatoPHP\FilamentSubscriptions\Filament\Pages\Billing;
use TomatoPHP\FilamentSubscriptions\FilamentSubscriptionsProvider;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
//            ->domain('https://leevainvoice.com')
            ->login()
            ->favicon(asset('images/leeva_logo.png'))
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->maxContentWidth(MaxWidth::Full)
            ->registration()
            ->tenant(Business::class, slugAttribute: 'slug', ownershipRelationship: 'business')
            ->tenantMiddleware([
                \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
            ], isPersistent: true)
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfile::class)
            ->requiresTenantSubscription()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15')
            ->pages([
                Billing::class
            ])
            ->tenantBillingProvider(new FilamentSubscriptionsProvider())
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        userMenuLabel: 'My Profile', // Customizes the 'account' link label in the panel User Menu (default = null)
                        shouldRegisterNavigation: true, // Adds a main navigation item for the My Profile page (default = false)
                        navigationGroup: 'Settings', // Sets the navigation group for the My Profile page (default = null)
                        hasAvatars: true, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->avatarUploadComponent(fn($fileUpload) => $fileUpload->disableLabel())
                    // OR, replace with your own component
                    ->avatarUploadComponent(
                        fn() => FileUpload::make('avatar_url')
                            ->label('')
                            ->image()
                            ->disk('public')
                    )
                    ->enableTwoFactorAuthentication(),
//                FilamentSocialitePlugin::make()
//                    ->providers([
//                        Provider::make('google')
//                            ->label('Google')
//                            ->icon('fab-google')
//                            ->color(Color::hex('#2f2a6b'))
//                            ->outlined(true)
//                            ->stateless(false),
//                        Provider::make('microsoft')
//                            ->label('Microsoft')
//                            ->icon('fab-github')
//                            ->color(Color::hex('#2f2a6b'))
//                            ->outlined(true)
//                            ->stateless(false),
//                    ])->registration(true)
//                    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin) {
//                        $user = User::firstOrNew([
//                            'email' => $oauthUser->getEmail(),
//                        ]);
//                        $user->name = $oauthUser->getName();
//                        $user->email = $oauthUser->getEmail();
//                        $user->email_verified_at = now();
//                        $user->save();
//
//                        return $user;
//                    }),
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
    }
}
