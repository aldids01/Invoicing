<x-filament-panels::page>
    <style>
        /* --- Settings applied dynamically --- */

        /* Base colors */
        :root { /* Define CSS variables for easier reuse */
            --primary-color: {{ $themeSettings['primary_brand_color'] ?? '#1e40af' }};
            --secondary-bg-color: {{ $themeSettings['secondary_header_bg_color'] ?? '#cccccc' }};
            --background-color: {{ $themeSettings['background_color'] ?? '#fff' }};
            --header-text-color: {{ $themeSettings['header_text_color'] ?? '#ffffff' }};
            --default-text-color: {{ $themeSettings['default_text_color'] ?? '#000000' }};

            --invoice-container-width: {{ $themeSettings['invoice_container_width'] ?? '70' }}%;
            --invoice-container-padding: {{ $themeSettings['invoice_container_padding'] ?? '25' }}px;
            --section-margin-bottom: {{ $themeSettings['header_footer_margin_bottom'] ?? '30' }}px;
            --section-border-width: {{ $themeSettings['section_border_width'] ?? '2' }}px;
            --header-padding-bottom: {{ $themeSettings['header_padding_bottom'] ?? '20' }}px;

            --invoice-header-font-family: {{ $themeSettings['invoice_header_font_family'] ?? '"Felix Titling", serif' }};
            --body-font-family: {{ $themeSettings['body_font_family'] ?? 'sans-serif' }};
            --default-font-weight: {{ $themeSettings['default_font_weight'] ?? 'bold' }};
            --invoice-header-font-size: {{ $themeSettings['invoice_header_font_size'] ?? '2' }}em;

            --table-border-width: {{ $themeSettings['table_border_width'] ?? '1' }}px;
            --table-item-padding: {{ $themeSettings['table_item_padding'] ?? '2' }}px;
            --table-item-margin-bottom: {{ $themeSettings['table_item_margin_bottom'] ?? '20' }}px;

            --invoice-totals-width: {{ $themeSettings['invoice_totals_width'] ?? '50' }}%;

            --default-text-align: {{ $themeSettings['default_text_align'] ?? 'right' }};
            --totals-header-text-align: {{ $themeSettings['totals_header_text_align'] ?? 'left' }};
            --totals-value-text-align: {{ $themeSettings['totals_header_text_align'] ?? 'right' }};

            --footer-margin-top: {{ $themeSettings['footer_margin_top'] ?? '150' }}px;
        }

        /* Use CSS variables or inject directly below */

        .invoice-container {
            width: var(--invoice-container-width);
            margin: 0 auto; /* Center with margin */
            background-color: var(--background-color);
            padding: var(--invoice-container-padding);
            border: var(--table-border-width) solid #ddd; /* Use a subtle border */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Slightly stronger shadow */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Align items to the top */
            margin-bottom: var(--section-margin-bottom);
            border-bottom: var(--section-border-width) solid var(--primary-color);
            padding-bottom: var(--header-padding-bottom);
        }
        .company-details, .invoice-details {
            width: 48%; /* Use relative width for flexibility */
            box-sizing: border-box;
        }

        .company-details h2 {
            margin-top: 0;
            color: var(--primary-color);
            font-weight: var(--default-font-weight);
            font-family: var(--invoice-header-font-family); /* Apply header font */
        }
        .company-details p {
            margin: 5px 0;
        }
        .invoice-details {
            text-align: var(--default-text-align); /* Use default text alignment */
        }

        .invoice-details h1 {
            margin-top: 0;
            color: var(--primary-color);
            text-align: var(--default-text-align);
            font-weight: var(--default-font-weight);
            font-size: var(--invoice-header-font-size);
            font-family: var(--invoice-header-font-family);
        }

        .invoice-details p {
            margin: 5px 0;
        }

        .billing-details {
            margin-bottom: var(--section-margin-bottom);
            border-bottom: var(--section-border-width) solid var(--primary-color);
            padding-bottom: var(--header-padding-bottom); /* Using header padding for consistency */
        }
        .billing-details h3 {
            font-weight: var(--default-font-weight);
            color: var(--primary-color); /* Apply primary color to heading */
            margin-top: 0;
            margin-bottom: 10px;
        }
        .billing-details p {
            margin: 2px 0;
        }

        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: var(--table-item-margin-bottom);
            border: var(--table-border-width) solid var(--primary-color); /* Apply border to the whole table */
        }

        invoice-items th, .invoice-items td {
            border: var(--table-border-width) solid var(--primary-color);
            padding: var(--table-item-padding);
            text-align: left; /* Keep item text left-aligned */
        }

        .invoice-items th {
            background-color: var(--primary-color);
            color: var(--header-text-color);
            font-weight: var(--default-font-weight);
        }

        /* Style for the "No items" row */
        .empty{
            text-align: center;
            font-style: italic;
        }

        .invoice-totals {
            width: var(--invoice-totals-width);
            float: right;
            margin-top: 20px; /* Add some space above totals */
            box-sizing: border-box;
        }

        .invoice-totals table {
            width: 100%;
            border-collapse: collapse;
            border: var(--table-border-width) solid var(--primary-color); /* Apply border to the whole table */
        }

        .invoice-totals th, .invoice-totals td {
            border: var(--table-border-width) solid var(--primary-color);
            padding: var(--table-item-padding);
        }

        .invoice-totals th {
            background-color: var(--secondary-bg-color);
            text-align: var(--totals-header-text-align);
            color: var(--primary-color);
            font-weight: var(--default-font-weight);
        }

        .invoice-totals td {
            text-align: var(--totals-value-text-align); /* Use specific alignment for values */
        }

        .invoice-totals table tr:last-child th,
        .invoice-totals table tr:last-child td {
            font-weight: bold; /* Make the final total row bold */
        }

        {{--.invoice-totals {--}}
        {{--    width: {{ $themeSettings['invoice_totals_width'] ?? '50' }}%;--}}
        {{--    float: right; /* Static */--}}
        {{--    margin-top: {{ $themeSettings['totals_margin_top'] ?? '1' }}px; /* Make sure this field exists or use a default */--}}
        {{--}--}}

        {{--.invoice-totals table {--}}
        {{--    width: 100%;--}}
        {{--    border-collapse: collapse;--}}
        {{--}--}}

        {{--.invoice-totals th, .invoice-totals td {--}}
        {{--    border: {{ $themeSettings['table_border_width'] ?? '1' }}px solid var(--primary-color);--}}
        {{--    padding: {{ $themeSettings['table_item_padding'] ?? '2' }}px;--}}
        {{--    text-align: {{ $themeSettings['default_text_align'] ?? 'right' }};--}}
        {{--}--}}

        {{--.invoice-totals th {--}}
        {{--    background-color: var(--secondary-bg-color); /* Using variable */--}}
        {{--    text-align: {{ $themeSettings['totals_header_text_align'] ?? 'left' }};--}}
        {{--    color: var(--primary-color); /* Using variable */--}}
        {{--}--}}

        .payment-info {
            margin-top: {{ $themeSettings['payment_info_margin_top'] ?? '1' }}px; /* Make sure this field exists or use a default */
            margin-bottom: {{ $themeSettings['header_footer_margin_bottom'] ?? '30' }}px;
        }

        .invoice-footer {
            margin-top: {{ $themeSettings['footer_margin_top'] ?? '150' }}px;
            text-align: {{ $themeSettings['default_text_align'] ?? 'center' }}; /* Assuming center, original was center */
            font-weight: {{ $themeSettings['default_font_weight'] ?? 'bold' }};
            color: var(--primary-color); /* Using variable */
        }

        /* Print styles are static */
        @media print {
            body { background-color: #fff; }
            .invoice-container { box-shadow: none; border: none; }
        }

        /* --- End dynamic settings --- */
    </style>
{{--    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">--}}
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-details">
                <h2>{{ $companyName ?? 'Your Company Name' }}</h2>
                <p>{{ $companyAddress ?? 'Your Company Address' }}</p>
                <p>Email: {{ $companyEmail ?? 'your.email@example.com' }}</p>
                <p>Phone: {{ $companyPhone ?? 'Your Phone Number' }}</p>
            </div>
            <div class="invoice-details">
                <h1>Invoice</h1>
                <p>Invoice #: {{ $invoiceNumber ?? 'N/A' }}</p>
                <p>Date: {{ $invoiceDate ?? 'N/A' }}</p>
                <p>Due Date: {{ $dueDate ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="billing-details">
            <h3>Bill To:</h3>
            <p>{{ $customerName ?? 'Customer Name' }}</p>
            <p>{{ $customerAddress ?? 'Customer Address' }}</p>
            <p>Email: {{ $customerEmail ?? 'customer.email@example.com' }}</p>
        </div>

        <div class="invoice-items">
{{--            {{ dd($items); }}--}}
            <table>
                <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                {{-- Loop through invoice items here --}}
                @forelse ($items ?? [] as $item)
                    <tr>
                        <td>{{ $item['product']['name'] ?? $item['service']['name'] ?? 'N/A' }}</td>
                        <td>{{ $item['quantity'] ?? 'N/A' }}</td>
                        <td>{{ number_format($item['sub_total'] ?? 0, 2, '.', ',') }}</td>
                        <td>{{ $item['discount'] ?? '0' }}%</td>
                        <td>{{ number_format($item['total_price'] ?? 0, 2, '.', ',')}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">No items on this invoice.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="invoice-totals">
            <table>
                <tr>
                    <th>Subtotal:</th>
                    <td>{{ $currency.' '.number_format($subtotal ?? 0, 2, '.', ',') }}</td>
                </tr>
{{--                <tr>--}}
{{--                    <th>Tax:</th>--}}
{{--                    <td>{{ number_format($tax ?? 0, 2, '.', ',')}}</td>--}}
{{--                </tr>--}}
                <tr>
                    <th>Discount:</th>
                    <td>{{ $currency.' '.number_format($totalDiscount ?? 0, 2, '.', ',')}}</td>
                </tr>
                <tr>
                    <th>Deposit:</th>
                    <td>{{ $currency.' '.number_format($deposit ?? 0, 2, '.', ',')}}</td>
                </tr>
                <tr>
                    <th>Amount Due:</th>
                    <td>{{ $currency.' '.number_format($total ?? 0, 2, '.', ',')}}</td>
                </tr>
            </table>
        </div>

        <div class="payment-info">
            <h3>Payment Information:</h3>
            <p>{{ $paymentInfo ?? 'Please make payment to the provided bank account details.' }}</p>
        </div>

        <div class="invoice-footer">
            <p>{{ $footerNote ?? 'Thank you for your business!' }}</p>
        </div>
    </div>
</x-filament-panels::page>
