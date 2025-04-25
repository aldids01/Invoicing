<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase;

use App\Filament\Clusters\Purchase;
use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;
use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\RelationManagers;
use App\Models\Purchase\Debit;
use App\Models\Sales\Products;
use App\Models\Sales\Service;
use App\PaymentStatus;
use App\Status;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use TomatoPHP\FilamentLocations\Models\Country;
use TomatoPHP\FilamentLocations\Models\Currency;

class DebitResource extends Resource
{
    protected static ?string $model = Debit::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationParentItem = 'Purchase';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getInformation())->columns(3),
                        Forms\Components\Section::make('Debit Note Items')
                            ->headerActions([
                                Action::make('reset')
                                    ->modalHeading('Are you sure?')
                                    ->modalDescription('All existing items will be removed from the order.')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->action(fn (Forms\Set $set) => $set('items', [])),
                            ])
                            ->schema([static::getItem()]),
                    ])->columnSpan(['lg' => fn (?Debit $record) => $record === null ? 3 : 2]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (Debit $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (Debit $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->columnSpan(['lg' => 1])
                            ->hidden(fn (?Debit $record) => $record === null),
                        Forms\Components\Section::make()
                            ->schema(static::getSummary())
                            ->columnSpan(['lg' => 1])
                            ->hidden(fn (?Debit $record) => $record === null),
                    ])


            ])->columns(3);
    }
    public static function getInformation():array
    {
        return [
            Forms\Components\Group::make([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\DatePicker::make('tran_date')
                            ->default(now())
                            ->required(),
                    ])->columns(2),
                Forms\Components\Select::make('vendor_id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getFilamentName())
                    ->relationship('vendor', 'id', modifyQueryUsing: fn (Builder $query) => $query->where('business_id', Filament::getTenant()?->id))
                    ->default(null)
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Section::make('Vendor Information')
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
                                    ->maxLength(255)
                                    ->default(null),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->default(null),
                            ])->columns(2)->collapsed(),
                        Forms\Components\Section::make('Association')
                            ->schema([
                                Forms\Components\Select::make('company_id')
                                    ->columnSpanFull()
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

                                    ]),
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

                                    ]),
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

                                    ]),
                                Forms\Components\Checkbox::make('same_as_billing')
                                    ->label('Shipping address is same as billing address')
                                    ->default(true)
                                    ->columnSpanFull()
                                    ->inline()
                                    ->inlineLabel()
                                    ->reactive(),
                                Forms\Components\Select::make('business_id')
                                    ->label('Business Name')
                                    ->options([
                                        Filament::getTenant()?->id => Filament::getTenant()?->name,
                                    ])
                                    ->columnSpanFull()
                                    ->default(fn () => Filament::getTenant()?->id)
                                    ->disabled() // optional: prevents user from changing it
                                    ->dehydrated() // ensures the value is still saved to DB
                                    ->visible(fn () => Filament::getTenant())
                            ])->columns(2)->collapsed(),
                    ]),
                Forms\Components\TextInput::make('sub_title')
                    ->maxLength(255),
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
            ])->columnSpan(2),
            Forms\Components\Group::make([
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->default(function () {
                        $latestInvoice = Debit::latest('number')->first();

                        if ($latestInvoice) {
                            // Extract the numeric part and increment
                            $lastNumber = (int) str_replace('DBN-', '', $latestInvoice->number);
                            return 'DBN-' . sprintf('%06d', $lastNumber + 1); // Adjust %06d for your desired padding
                        }

                        return 'DBN-000001'; // Default for the very first invoice
                    })
                    ->disabled()
                    ->label('Debit no')
                    ->dehydrated()
                    ->maxLength(32)
                    ->unique(Debit::class, 'number', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('currency')
                    ->required()
                    ->default('NGN')
                    ->options(Currency::query()->pluck('iso', 'iso')->toArray()),
                Forms\Components\TextInput::make('shipping_method')
                    ->maxLength(255),
            ])->columns(1),
        ];
    }
    public static function getItem(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('items')
            ->relationship('items')
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->reactive()
                            ->columnSpan(1)
                            ->afterStateUpdated(function ($state, callable $set) {
                                static::updateItemDetails($state, $set, 'product');
                            })
                            ->disabled(fn (callable $get) => filled($get('service_id'))),
                        Select::make('service_id')
                            ->relationship('service', 'name')
                            ->reactive()
                            ->columnSpan(1)
                            ->afterStateUpdated(function ($state, callable $set) {
                                static::updateItemDetails($state, $set, 'service');
                            })
                            ->disabled(fn (callable $get) => filled($get('product_id'))),
                    ])->columns(2),
                Forms\Components\Grid::make()
                    ->columns(6)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('quantity')
                            ->required()
                            ->reactive()
                            ->live(onBlur: true)
                            ->default(1)
                            ->label('Quantity')
                            ->numeric()
                            ->afterStateUpdated(fn (callable $set, $get) => static::updateTotalPrice($get, $set)),
                        TextInput::make('rate')
                            ->required()
                            ->label('Rate')
                            ->numeric()
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (callable $set, $get) => $set('sub_total', ($get('quantity') ?? 1) * ($get('rate') ?? 0))),
                        TextInput::make('tax')
                            ->label('Tax')
                            ->numeric()
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (callable $set, $get) => static::updateTotalPrice($get, $set)),
                        TextInput::make('discount')
                            ->prefix('%')
                            ->label('Discount')
                            ->reactive()
                            ->live(onBlur: true)
                            ->numeric()
                            ->afterStateUpdated(fn (callable $set, $get) => static::updateTotalPrice($get, $set)),
                        TextInput::make('sub_total')
                            ->required()
                            ->label('Sub Total')
                            ->numeric()
                            ->readOnly(),
                        TextInput::make('total_price')
                            ->required()
                            ->label('Total Price')
                            ->numeric()
                            ->readOnly(),
                    ])->columnSpan(6),

            ])->defaultItems(1)->columns(5)
            ->hiddenLabel()
            ->required();
    }
    public static function getSummary():array
    {
        return [
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\RichEditor::make('terms')
                        ->label('Terms and conditions'),
                    Forms\Components\RichEditor::make('notes'),
                    Forms\Components\ToggleButtons::make('payment_status')
                        ->inline()
                        ->options(PaymentStatus::class)
                        ->default('pending')
                        ->required(),
                    Forms\Components\ToggleButtons::make('status')
                        ->inline()
                        ->options(Status::class)
                        ->default('new')
                        ->required(),
                ])->columns(2),


            Forms\Components\Section::make('Attachment')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('media')
                        ->collection('Debit-attachment')
                        ->multiple()
                        ->hiddenLabel(),
                ])
                ->collapsed(),
            Forms\Components\Placeholder::make('sub_total_placeholder')
                ->label('Sub total')
                ->inlineLabel()
                ->content(function (callable $get, $set){
                    $subtotal = 0;
                    if ($repeaters = $get('items')) {
                        foreach ($repeaters as $key => $repeater) {
                            $subtotal += ($get("items.{$key}.quantity") * $get("items.{$key}.rate") + ($get("items.{$key}.tax") ?? 0));
                        }
                    }
                    $set('sub_total', $subtotal); // 'total' now strictly represents the subtotal of items (including tax per item)
                    return Number::currency($subtotal, 'NGN');
                })->extraAttributes(['class' => 'font-bold text-right']),
            hidden::make('sub_total')
                ->default(0),
            Forms\Components\Placeholder::make('discount_total_placeholder')
                ->label(function (callable $get) {
                    $totalDiscount = 0;
                    if ($repeaters = $get('items')) {
                        foreach ($repeaters as $key => $repeater) {
                            $totalDiscount += $get("items.{$key}.discount");
                        }
                    }
                    return "Discount {$totalDiscount}%";
                })
                ->inlineLabel()
                ->content(function (callable $get, $set) {
                    $totalDiscountAmount = 0;
                    $subtotal = $get('sub_total') ?? 0; // Get the calculated subtotal
                    if ($repeaters = $get('items')) {
                        foreach ($repeaters as $key => $repeater) {
                            $itemSubtotal = ($get("items.{$key}.quantity") * $get("items.{$key}.rate"));
                            $discountPercentage = $get("items.{$key}.discount") ?? 0;
                            $discountAmount = ($discountPercentage / 100) * $itemSubtotal;
                            $totalDiscountAmount += $discountAmount;
                        }
                    }
                    $set('total_discount', $totalDiscountAmount);
                    return Number::currency($totalDiscountAmount, 'NGN');
                })->extraAttributes(['class' => 'font-bold text-right']),
            hidden::make('total_discount')
                ->default(0),
            Forms\Components\Placeholder::make('grand_total_placeholder')
                ->label('Total')
                ->inlineLabel()
                ->content(function (callable $get, $set){
                    $subtotal = $get('sub_total') ?? 0; // Get the subtotal from the hidden 'total'
                    $totalDiscount = $get('total_discount') ?? 0; // Get the total discount amount

                    $grandTotal = $subtotal - $totalDiscount;
                    $set('total', $grandTotal);
                    return Number::currency($grandTotal, 'NGN');
                })->extraAttributes(['class' => 'font-bold text-right']),
            hidden::make('total')
                ->default(0),
        ];
    }

    protected static function updateItemDetails($state, callable $set, string $type): void
    {
        $set('rate', null);
        $set('sub_total', null);
        $set('total_price', null);

        if ($state) {
            $model = match ($type) {
                'product' => Products::find($state),
                'service' => Service::find($state),
                default => null,
            };

            if ($model) {
                $set('rate', $model->price);
                $set('sub_total', $model->price);
                $set('total_price', $model->price);
            }
        }
    }

    protected static function updateTotalPrice(callable $get, callable $set): void
    {
        $quantity = $get('quantity') ?? 1;
        $rate = $get('rate') ?? 0;
        $tax = $get('tax') ?? 0;
        $discount = $get('discount') ?? 0;

        $subtotal = $rate * $quantity;
        $taxAmount = $tax; // Assuming tax is a direct amount
        $discountAmount = ($discount / 100) * ($subtotal + $taxAmount);
        $total = ($subtotal + $taxAmount) - $discountAmount;
        $g_total = $subtotal + $taxAmount;

        $set('total_price', round($total, 2));
        $set('sub_total', round($g_total, 2));
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('Debit-attachment')
                    ->label('Attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->collection('Debit-attachment'),
                Tables\Columns\TextColumn::make('number')
                    ->label('Delivery Challan No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_title')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor.full_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing.id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping.id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_discount')
                    ->numeric()
                    ->label('Discount')
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn ($state): string => Status::from($state)->getLabel())
                    ->icon(fn ($state): string => Status::from($state)->getIcon())
                    ->color(fn ($state): string => Status::from($state)->getColor())
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->formatStateUsing(fn ($state): string => PaymentStatus::from($state)->getLabel())
                    ->icon(fn ($state): string =>PaymentStatus::from($state)->getIcon())
                    ->color(fn ($state): string =>PaymentStatus::from($state)->getColor())
                    ->badge(),
                Tables\Columns\TextColumn::make('tran_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDebits::route('/'),
            'create' => Pages\CreateDebit::route('/create'),
            'view' => Pages\ViewDebit::route('/{record}'),
            'edit' => Pages\EditDebit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
