<?php

namespace App\Filament\Clusters\Item\Resources;

use App\Filament\Clusters\Item;
use App\Filament\Clusters\Item\Resources\Sales\ProductsResource\Pages;
use App\Filament\Clusters\Item\Resources\Sales\ProductsResource\RelationManagers;
use App\Models\Sales\Products;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationParentItem = 'Item';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                hidden::make('slug')
                                    ->unique(Products::class, 'slug', ignoreRecord: true)
                                    ->default(0),

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


                                Forms\Components\TextInput::make('cost')
                                    ->label('Cost per item')
                                    ->helperText('Customers won\'t see this price.')
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                                    ->required(),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU (Stock Keeping Unit)')
                                    ->unique(Products::class, 'sku', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->required(),

                                Forms\Components\TextInput::make('barcode')
                                    ->label('Barcode (ISBN, UPC, GTIN, etc.)')
                                    ->unique(Products::class, 'barcode', ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),

                                Forms\Components\TextInput::make('security_stock')
                                    ->helperText('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.')
                                    ->numeric()
                                    ->rules(['integer', 'min:0'])
                                    ->required(),
                            ])
                            ->collapsed()
                            ->columns(2),

                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visible')
                                    ->helperText('This product will be hidden from all sales channels.')
                                    ->default(true),

                                Forms\Components\DatePicker::make('published_at')
                                    ->label('Availability')
                                    ->default(now())
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Associations')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->required(),
                            ]),
                        Forms\Components\Section::make('Images')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('media')
                                    ->collection('product-images')
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->hiddenLabel(),
                            ])
                            ->collapsible(),
                        Forms\Components\Section::make('Shipping')
                            ->schema([
                                Forms\Components\Checkbox::make('backorder')
                                    ->label('This product can be returned'),

                                Forms\Components\Checkbox::make('requires_shipping')
                                    ->label('This product will be shipped'),
                            ])
                    ])
                    ->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label('Image')
                    ->stacked()
                    ->circular()
                    ->collection('product-images'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visibility')
                    ->boolean(),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qty')
                    ->numeric()
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('security_stock')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('old_price')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->summarize(Tables\Columns\Summarizers\Sum::make())
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('backorder')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('requires_shipping')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weight_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weight_unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('height_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('height_unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('width_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('width_unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('depth_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('depth_unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('volume_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('volume_unit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('business.name')
                    ->numeric()
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
            'index' => Item\Resources\ProductsResource\Pages\ListProducts::route('/'),
            'create' => Item\Resources\ProductsResource\Pages\CreateProducts::route('/create'),
            'view' => Item\Resources\ProductsResource\Pages\ViewProducts::route('/{record}'),
            'edit' => Item\Resources\ProductsResource\Pages\EditProducts::route('/{record}/edit'),
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
