<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase;

use App\Filament\Clusters\Purchase;
use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;
use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\RelationManagers;
use App\Models\Purchase\Debit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tran_date')
                    ->required(),
                Forms\Components\TextInput::make('sub_title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('terms')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'id')
                    ->default(null),
                Forms\Components\Select::make('billing_id')
                    ->relationship('billing', 'id')
                    ->default(null),
                Forms\Components\Select::make('business_id')
                    ->relationship('business', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('sub_total')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('total_discount')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('amount_used')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('amount_unused')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tran_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_used')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_unused')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('payment_status'),
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
