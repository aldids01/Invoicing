<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource;
use App\Models\ThemeSetting;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;
    protected static string $view = 'filament.clusters.sales.resources.sales.invoice-resource.pages.invoice-template';
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        // Assuming your Invoice model has relationships or attributes for the data
        $this->data = [
            'companyName' => Filament::getTenant()->name,
            'companyAddress' => Filament::getTenant()->country,
            'companyEmail' => Filament::getTenant()->email,
            'companyPhone' => Filament::getTenant()->phone,
            'invoiceNumber' => $this->record->number,
            'invoiceDate' => $this->record->tran_date->format('dS-m-Y'),
            'dueDate' => $this->record->due_date->format('dS-m-Y'),
            'customerName' => $this->record->customer->full_name,
            'customerAddress' => $this->record->customer->address,
            'customerEmail' => $this->record->customer->email,
            'items' => $this->record->items->toArray(), // Assuming a relationship to items
            'deposit' => $this->record->deposit,
            'subtotal' => $this->record->total,
            'totalDiscount' => $this->record->total_discount,
            'tax' => $this->record->tax,
            'total' => $this->record->amount_due,
            'currency' => $this->record->currency,
            'paymentInfo' => 'Bank Transfer: Account XYZ',
            'footerNote' => 'Thank you for your business!',
            'themeSettings' => ThemeSetting::query()->first(),
        ];
    }

    public function getViewData(): array
    {
        return $this->data;
    }
    protected function getHeaderActions(): array
    {
        $fontFamilies = [
            // --- Generic Families ---
            'serif'      => 'Serif (Generic)',
            'sans-serif' => 'Sans-serif (Generic)',
            'monospace'  => 'Monospace (Generic)',
            'cursive'    => 'Cursive (Generic)',
            'fantasy'    => 'Fantasy (Generic)',

            // --- Common Web-Safe & Popular Fonts ---
            // Serif Fonts
            '"Times New Roman", Times, serif'       => 'Times New Roman',
            'Georgia, serif'                         => 'Georgia',
            '"Palatino Linotype", "Book Antiqua", Palatino, serif' => 'Palatino Linotype, Book Antiqua',
            '"serif-garamond", "eb-garamond", serif' => 'Garamond (with modern fallbacks)',
            '"Apple Garamond", "Apple SD Gothic Neo", serif' => 'Apple Garamond',
            'Cambria, Georgia, serif'                => 'Cambria',
            'Didot, "Bodoni MT", "Noto Serif Display", "URW Palladio L", serif' => 'Didot',
            'Hoefler Text, "Liberation Serif", Times, "Times New Roman", serif' => 'Hoefler Text',
            'Lucida Bright, Georgia, serif'          => 'Lucida Bright',
            'Palatino, "URW Palladio L", serif'      => 'Palatino',
            '"Book Antiqua", Palatino, serif'        => 'Book Antiqua',

            // Sans-serif Fonts
            'Arial, sans-serif'                      => 'Arial',
            'Helvetica, sans-serif'                  => 'Helvetica',
            '"Arial Black", Gadget, sans-serif'      => 'Arial Black',
            'Verdana, Geneva, sans-serif'            => 'Verdana',
            'Tahoma, Geneva, sans-serif'              => 'Tahoma',
            '"Trebuchet MS", Helvetica, sans-serif'  => 'Trebuchet MS',
            '"Arial Narrow", sans-serif'             => 'Arial Narrow',
            '"Lucida Sans Unicode", "Lucida Grande", sans-serif' => 'Lucida Sans Unicode',
            '"Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"' => 'Segoe UI (Modern Stack)',
            'Roboto, sans-serif'                     => 'Roboto', // Common Google Font
            'Open Sans, sans-serif'                  => 'Open Sans', // Common Google Font
            'Lato, sans-serif'                       => 'Lato', // Common Google Font
            'Montserrat, sans-serif'                 => 'Montserrat', // Common Google Font
            'Poppins, sans-serif'                    => 'Poppins', // Common Google Font
            'Ubuntu, sans-serif'                     => 'Ubuntu', // Common Google Font
            'Oswald, sans-serif'                     => 'Oswald', // Common Google Font
            'Source Sans Pro, sans-serif'            => 'Source Sans Pro', // Common Google Font
            '"Noto Sans", sans-serif'                => 'Noto Sans', // Google Font (supports many languages)
            '"Roboto Condensed", sans-serif'         => 'Roboto Condensed',
            '"PT Sans", sans-serif'                  => 'PT Sans',
            '"Open Sans Condensed", sans-serif'      => 'Open Sans Condensed',
            '"Fjalla One", sans-serif'               => 'Fjalla One',
            '"Passion One", sans-serif'              => 'Passion One',
            '"Francois One", sans-serif'             => 'Francois One',
            '"Acme", sans-serif'                     => 'Acme',
            '"Luckiest Guy", cursive'               => 'Luckiest Guy (often used as display)', // Although cursive, often used like fantasy
            '"Lobster", cursive'                    => 'Lobster', // Cursive/Display Google Font
            '"Bebas Neue", sans-serif'               => 'Bebas Neue', // Common display font

            // Monospace Fonts
            '"Courier New", Courier, monospace'      => 'Courier New',
            '"Lucida Console", Monaco, monospace'    => 'Lucida Console, Monaco',
            'Consolas, "Andale Mono", "Ubuntu Mono", monospace' => 'Consolas',
            '"Bitstream Vera Sans Mono", Consolas, "Lucida Console", Terminal, "Andale Mono", "Courier New", Courier, monospace' => 'Bitstream Vera Sans Mono (Detailed Stack)',
            '"Cousine", monospace'                   => 'Cousine (Google Font)',
            '"PT Mono", monospace'                   => 'PT Mono',
            '"Inconsolata", monospace'               => 'Inconsolata (Google Font)',

            // Cursive Fonts
            '"Brush Script MT", cursive'             => 'Brush Script MT',
            '"Lucida Handwriting", cursive'          => 'Lucida Handwriting',
            '"Segoe Script", cursive'               => 'Segoe Script',
            '"Dancing Script", cursive'              => 'Dancing Script (Google Font)',
            '"Pacifico", cursive'                    => 'Pacifico (Google Font)',
            '"Great Vibes", cursive'                 => 'Great Vibes (Google Font)',
            '"Permanent Marker", cursive'            => 'Permanent Marker (Google Font)',

            // Fantasy Fonts (often display fonts)
            'Impact, Charcoal, sans-serif'           => 'Impact',
            'Haettenschweiler, "Arial Narrow Bold", sans-serif' => 'Haettenschweiler',
            '"Stencil Std", fantasy'                 => 'Stencil Std',
            '"Algerian", fantasy'                    => 'Algerian',
            '"Copperplate Gothic Light", "Copperplate Gothic", fantasy' => 'Copperplate Gothic',
            'Broadway, "Keania One", fantasy'        => 'Broadway',
            '"Felix Titling", serif'                 => 'Felix Titling (Original Default)' // Explicitly include the original default
        ];
        // Sort the font families alphabetically by their display label
        asort($fontFamilies);
        return [
            Actions\EditAction::make(),
            Actions\Action::make('Theme')
                ->label('Theme Settings')
                ->modalHeading('Theme Settings')
                ->slideOver() // <-- Makes it a slideover
                ->form([
                    // Paste the form schema defined above here
                    // Section::make('Colors')...
                    Section::make('Colors')
                        ->schema([
                            ColorPicker::make('primary_brand_color')->label('Primary Brand Color')->default('#1e40af'),
                            ColorPicker::make('secondary_header_bg_color')->label('Secondary Header Background')->default('#cccccc'),
                            ColorPicker::make('background_color')->label('Background Color')->default('#fff'),
                            ColorPicker::make('header_text_color')->label('Header Text Color (on primary background)')->default('#ffffff'),
                            ColorPicker::make('default_text_color')->label('Default Text Color')->default('#000000'),
                        ])->columns(2),

                    Section::make('Typography')
                        ->schema([
                            Select::make('invoice_header_font_family')
                                ->label('Invoice Header Font Family')
                                ->helperText('Select the font family for invoice headers.')
                                ->options($fontFamilies) // Use the predefined list
                                ->default('"Felix Titling", serif') // Keep the default value
                                ->searchable(), // Optionally make the select searchable if the list is long

                            Select::make('body_font_family')
                                ->label('Body Font Family')
                                ->helperText('Select the font family for the main body text.')
                                ->options($fontFamilies) // Use the predefined list
                                ->placeholder('Select a font family') // Optional placeholder
                                ->searchable(), // Optionally make the select searchable
                            Select::make('default_font_weight')->label('Default Font Weight (Headers)')->options(['normal' => 'Normal','bold' => 'Bold','bolder' => 'Bolder','lighter' => 'Lighter'])->default('bold'),
                            TextInput::make('invoice_header_font_size')->label('Invoice Title Font Size')->numeric()->suffix('em')->default(2)->minValue(0.5),
                        ])->columns(2),

                    Section::make('Spacing & Sizing')
                        ->schema([
                            TextInput::make('invoice_container_width')->label('Container Width')->numeric()->suffix('%')->default(70)->minValue(30)->maxValue(100),
                            TextInput::make('invoice_container_padding')->label('Container Padding')->numeric()->suffix('px')->default(25)->minValue(0),
                            TextInput::make('header_footer_margin_bottom')->label('Header/Billing/Payment Bottom Margin')->numeric()->suffix('px')->default(30)->minValue(0),
                            TextInput::make('header_padding_bottom')->label('Header Padding Bottom')->numeric()->suffix('px')->default(20)->minValue(0),
                            TextInput::make('table_item_padding')->label('Table Cell Padding')->numeric()->suffix('px')->default(2)->minValue(0),
                            TextInput::make('table_item_margin_bottom')->label('Item Table Margin Bottom')->numeric()->suffix('px')->default(20)->minValue(0),
                            TextInput::make('footer_margin_top')->label('Footer Top Margin')->numeric()->suffix('px')->default(150)->minValue(0),
                        ])->columns(2),

                    Section::make('Layout & Borders')
                        ->schema([
                            Select::make('default_text_align')->label('Default Text Alignment (Totals Table Cells)')->options(['left' => 'Left','center' => 'Center','right' => 'Right',])->default('right'),
                            Select::make('totals_header_text_align')->label('Totals Table Header Text Alignment')->options(['left' => 'Left','center' => 'Center','right' => 'Right',])->default('left'),
                            TextInput::make('table_border_width')->label('Table Border Width')->numeric()->suffix('px')->default(1)->minValue(0),
                            TextInput::make('section_border_width')->label('Section Border Width (Header, Billing)')->numeric()->suffix('px')->default(2)->minValue(0),
                            TextInput::make('invoice_totals_width')->label('Totals Section Width')->numeric()->suffix('%')->default(50)->minValue(20)->maxValue(100),
                        ])->columns(2),
                ])
                ->action(function (array $data) {

                    // \App\Models\Settings::updateOrCreate([], $data);
                    ThemeSetting::updateOrCreate([], $data);

                    Notification::make()
                        ->title('Invoice theme settings saved.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
