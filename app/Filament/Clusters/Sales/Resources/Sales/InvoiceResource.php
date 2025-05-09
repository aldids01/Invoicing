<?php

namespace App\Filament\Clusters\Sales\Resources\Sales;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\Pages;
use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\RelationManagers;
use App\Models\Sales\Invoice;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use TomatoPHP\FilamentLocations\Models\Country;
use TomatoPHP\FilamentLocations\Models\Currency;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationParentItem = 'Sales';
    protected static ?int $navigationSort  = 200;
    protected static ?string $recordTitleAttribute = 'number';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                    ->schema(static::getInformation())->columns(3),
                    Forms\Components\Section::make('Invoice Items')
                        ->headerActions([
                            Action::make('reset')
                                ->modalHeading('Are you sure?')
                                ->modalDescription('All existing items will be removed from the order.')
                                ->requiresConfirmation()
                                ->color('danger')
                                ->action(fn (Forms\Set $set) => $set('items', [])),
                        ])
                    ->schema([static::getItem()]),
                ])->columnSpan(['lg' => fn (?Invoice $record) => $record === null ? 3 : 2]),
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Created at')
                                ->content(fn (Invoice $record): ?string => $record->created_at?->diffForHumans()),

                            Forms\Components\Placeholder::make('updated_at')
                                ->label('Last modified at')
                                ->content(fn (Invoice $record): ?string => $record->updated_at?->diffForHumans()),
                        ])
                        ->columnSpan(['lg' => 1])
                        ->hidden(fn (?Invoice $record) => $record === null),
                    Forms\Components\Section::make()
                        ->schema(static::getSummary())
                        ->columnSpan(['lg' => 1])
                        ->hidden(fn (?Invoice $record) => $record === null),
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
                        Forms\Components\DatePicker::make('due_date')
                            ->default(now())
                            ->required(),
                    ])->columns(2),
                Forms\Components\Select::make('customer_id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->getFilamentName())
                    ->relationship('customer', 'id', modifyQueryUsing: fn (Builder $query) => $query->where('business_id', Filament::getTenant()?->id))
                    ->default(null)
                    ->required()
                    ->createOptionForm([
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
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mobile')
                                    ->tel(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
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
                        $latestInvoice = Invoice::latest('number')->first();

                        if ($latestInvoice) {
                            // Extract the numeric part and increment
                            $lastNumber = (int) str_replace('INV-', '', $latestInvoice->number);
                            return 'INV-' . sprintf('%06d', $lastNumber + 1); // Adjust %06d for your desired padding
                        }

                        return 'INV-000001'; // Default for the very first invoice
                    })
                    ->disabled()
                    ->label('Invoice no')
                    ->dehydrated()
                    ->maxLength(32)
                    ->unique(Invoice::class, 'number', ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('po')
                    ->label('PO No')
                    ->default('PO-' . random_int(100000, 999999))
                    ->unique(Invoice::class, 'po', ignoreRecord: true)
                    ->required()
                    ->maxLength(32),
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
                    Forms\Components\Fieldset::make('Items')
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            Select::make('product_id')
                                ->relationship('product', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('business_id', Filament::getTenant()?->id))
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    static::updateItemDetails($state, $set, 'product');
                                })
                                ->disabled(fn (callable $get) => filled($get('service_id'))),
                            Select::make('service_id')
                                ->relationship('service', 'name')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    static::updateItemDetails($state, $set, 'service');
                                })
                                ->disabled(fn (callable $get) => filled($get('product_id')))
                                ->createOptionForm([
                                    Forms\Components\Section::make()
                                        ->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->label('Service name')
                                                ->maxLength(255)
                                                ->columnSpanFull()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                    if ($operation !== 'create') {
                                                        return;
                                                    }

                                                    $set('sac', Str::slug($state));
                                                }),
                                            hidden::make('sac')
                                                ->unique(Service::class, 'sac', ignoreRecord: true),


                                            Forms\Components\MarkdownEditor::make('description')
                                                ->columnSpan('full'),
                                        ])
                                        ->columns(2),
                                    Forms\Components\Section::make('Pricing')
                                        ->schema([
                                            Forms\Components\TextInput::make('price')
                                                ->numeric()
                                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                                ->required(),

                                            Forms\Components\TextInput::make('qty')
                                                ->label('Quantity')
                                                ->numeric()
                                                ->rules(['integer', 'min:0'])
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->collapsed(),
                                    Forms\Components\Section::make('Images')
                                        ->schema([
                                            SpatieMediaLibraryFileUpload::make('media')
                                                ->collection('service-images')
                                                ->multiple()
                                                ->maxFiles(5)
                                                ->hiddenLabel(),
                                        ])
                                        ->collapsed(),
                                ]),

                        ])->columnSpan(12),
                    Forms\Components\Fieldset::make('Financial')
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
                        ])
                        ->columnSpan(12)
                        ->hidden(fn (callable $get) => ! (filled($get('product_id')) || filled($get('service_id')))),

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
                        ->collection('invoice-attachment')
                        ->multiple()
                        ->hiddenLabel(),
                ])
                ->collapsed(),
            Forms\Components\TextInput::make('shipping_cost')
                ->inlineLabel()
                ->reactive()
                ->live(onBlur: true)
                ->numeric()
                ->default(0),
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
                    $set('total', $subtotal); // 'total' now strictly represents the subtotal of items (including tax per item)
                    return Number::currency($subtotal, 'NGN');
                })->extraAttributes(['class' => 'font-bold text-right']),
            hidden::make('total')
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
                    $subtotal = $get('total') ?? 0; // Get the calculated subtotal
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
                ->label('Amount Due')
                ->inlineLabel()
                ->content(function (callable $get, $set){
                    $subtotal = $get('total') ?? 0; // Get the subtotal from the hidden 'total'
                    $shippingCost = $get('shipping_cost') ?? 0; // Get the current shipping cost
                    $totalDiscount = $get('total_discount') ?? 0; // Get the total discount amount

                    $grandTotal = ($subtotal + $shippingCost) - $totalDiscount;
                    $set('shipping_cost', $shippingCost);
                    $set('amount_due', $grandTotal);
                    return Number::currency($grandTotal, 'NGN');
                })->extraAttributes(['class' => 'font-bold text-right']),
            hidden::make('amount_due')
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
                Tables\Columns\SpatieMediaLibraryImageColumn::make('invoice-attachment')
                    ->label('Attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->collection('invoice-attachment'),
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('po')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Subtotal')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_discount')
                    ->label('Discount')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_due')
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
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('billing.id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('shipping.id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('business.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->with(['items.product', 'items.service']);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.first_name', 'customer.last_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {

        return [
            'Customer' => optional($record->customer)->fullname,
        ];
    }
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return Pages\ViewInvoice::getUrl(['record' => $record]);
    }
}
