<?php

namespace App\Filament\Clusters\Sales\Resources\Sales;

use App\Filament\Clusters\Sales;
use App\Filament\Clusters\Sales\Resources\Sales\CustomerResource\Pages;
use App\Filament\Clusters\Sales\Resources\Sales\CustomerResource\RelationManagers;
use App\Models\Business;
use App\Models\Sales\Customer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TomatoPHP\FilamentLocations\Models\Country;
use Closure;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $recordTitleAttribute = 'first_name';
    protected static ?int $navigationSort  = 100;
    protected static ?string $navigationParentItem = 'Sales';

    protected static ?string $cluster = Sales::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('mobile')
                            ->tel(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                    ])->columns(2),

                ])->columnSpan(2),
               Forms\Components\Group::make()
                    ->schema([
                    Forms\Components\Section::make('Association')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'company_name', modifyQueryUsing: fn (Builder $query) => $query->where('business_id', Filament::getTenant()?->id))
                            ->createOptionForm([
                                Forms\Components\Section::make('Company Registration')
                                ->schema([
                                    Forms\Components\TextInput::make('company_name')
                                        ->required()
                                        ->columnSpanFull()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('reg_no'),
                                    Forms\Components\TextInput::make('tax_no'),
                                    Forms\Components\TextInput::make('phone'),
                                    Forms\Components\Select::make('business_id')
                                     ->label('Business Name')
                                    ->options([
                                        Filament::getTenant()?->id => Filament::getTenant()?->name,
                                    ])
                                    ->default(fn () => Filament::getTenant()?->id)
                                    ->disabled() // optional: prevents user from changing it
                                    ->dehydrated() // ensures the value is still saved to DB
                                    ->visible(fn () => Filament::getTenant())
                                ])->columns(2),

                            ])
                            ->default(null),
                        Forms\Components\Select::make('billing_id')
                            ->label('Billing Address')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getFilamentName())
                            ->relationship('billing', 'id')
                            ->createOptionForm([
                                Forms\Components\Section::make('Billing Address Information')
                                ->schema([
                                    Forms\Components\Select::make('country')
                                        ->default('Nigeria')
                                        ->columnSpanFull()
                                        ->searchable()
                                        ->options(Country::query()->pluck('name', 'code')->toArray())
                                        ->required(),
                                    Forms\Components\TextInput::make('street_1'),
                                    Forms\Components\TextInput::make('street_2'),
                                    Forms\Components\TextInput::make('zip'),
                                    Forms\Components\TextInput::make('city'),
                                    Forms\Components\TextInput::make('state'),
                                    Forms\Components\Select::make('business_id')
                                        ->label('Business Name')
                                        ->options([
                                            Filament::getTenant()?->id => Filament::getTenant()?->name,
                                        ])
                                        ->default(fn () => Filament::getTenant()?->id)
                                        ->disabled() // optional: prevents user from changing it
                                        ->dehydrated() // ensures the value is still saved to DB
                                        ->visible(fn () => Filament::getTenant())
                                ])->columns(2),

                            ])
                            ->default(null),
                        Forms\Components\Checkbox::make('same_as_billing')
                            ->label('Shipping address is same as billing address')
                            ->default(true)
                            ->reactive(),
                        Forms\Components\Select::make('shipping_id')
                            ->label('Shipping Address')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getFilamentName())
                            ->relationship('shipping', 'id')
                            ->visible(fn (Get $get) => !$get('same_as_billing'))
                            ->createOptionForm([
                                Forms\Components\Section::make('Shipping Address Information')
                                    ->schema([
                                        Forms\Components\Select::make('country')
                                            ->default('Nigeria')
                                            ->columnSpanFull()
                                            ->searchable()
                                            ->options(Country::query()->pluck('name', 'code')->toArray())
                                            ->required(),
                                        Forms\Components\TextInput::make('street_1'),
                                        Forms\Components\TextInput::make('street_2'),
                                        Forms\Components\TextInput::make('zip'),
                                        Forms\Components\TextInput::make('city'),
                                        Forms\Components\TextInput::make('state'),
                                        Forms\Components\Select::make('business_id')
                                            ->label('Business Name')
                                            ->options([
                                                Filament::getTenant()?->id => Filament::getTenant()?->name,
                                            ])
                                            ->default(fn () => Filament::getTenant()?->id)
                                            ->disabled() // optional: prevents user from changing it
                                            ->dehydrated() // ensures the value is still saved to DB
                                            ->visible(fn () => Filament::getTenant())
                                    ])->columns(2),

                            ])
                            ->default(null),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('avatar_url')
                        ->label('Profile Picture')
                        ->circular()
                        ->searchable()
                        ->grow(false)
                        ->getStateUsing(fn($record) => $record->avatar_url
                            ? $record->avatar_url
                            : "https://ui-avatars.com/api/?name=" . urlencode($record->first_name.' '.$record->last_name )),
                    Tables\Columns\TextColumn::make('first_name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('last_name')
                        ->searchable(),
                   Tables\Columns\Layout\Stack::make([
                       Tables\Columns\TextColumn::make('email')
                           ->icon('heroicon-m-envelope')
                           ->searchable(),
                       Tables\Columns\TextColumn::make('mobile')
                           ->icon('heroicon-m-phone')
                           ->searchable(),
                       Tables\Columns\TextColumn::make('phone')
                           ->icon('heroicon-m-phone')
                           ->searchable(),
                       Tables\Columns\TextColumn::make('company.company_name')
                           ->icon('heroicon-m-building-office-2')
                           ->sortable(),
                   ])
                ]),

//                Tables\Columns\TextColumn::make('billing.id')
//                    ->toggleable(isToggledHiddenByDefault: true)
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('shipping.id')
//                    ->toggleable(isToggledHiddenByDefault: true)
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('deleted_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl(name: 'view', parameters: ['record' => $record]);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {

        return [
            'Customer' => "{$record->getFilamentName()}",
        ];
    }
}
