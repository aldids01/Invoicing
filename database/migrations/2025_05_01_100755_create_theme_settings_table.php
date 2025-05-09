<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            // Colors
            $table->string('primary_brand_color', 20)->default('#1e40af');
            $table->string('secondary_header_bg_color', 20)->default('#cccccc');
            $table->string('background_color', 20)->default('#fff');
            $table->string('header_text_color', 20)->default('#ffffff');
            $table->string('default_text_color', 20)->default('#000000'); // Assuming black

            // Typography
            $table->string('invoice_header_font_family', 255)->default('"Felix Titling", serif');
            $table->string('body_font_family', 255)->nullable(); // Allow null if no body font is set
            $table->string('default_font_weight', 20)->default('bold');
            $table->string('invoice_header_font_size', 20)->default('2em');

            // Spacing & Sizing (Storing as string to include units)
            $table->string('invoice_container_width', 20)->default('70%');
            $table->string('invoice_container_padding', 20)->default('25px');
            $table->string('header_footer_margin_bottom', 20)->default('30px');
            $table->string('header_padding_bottom', 20)->default('20px');
            $table->string('table_item_padding', 20)->default('2px');
            $table->string('table_item_margin_bottom', 20)->default('20px');
            $table->string('footer_margin_top', 20)->default('150px');

            // Layout & Borders (Storing border width as string to include px)
            $table->string('table_border_width', 20)->default('1px');
            $table->string('section_border_width', 20)->default('2px');
            $table->string('invoice_totals_width', 20)->default('50%');

            // Alignment
            $table->string('default_text_align', 20)->default('right'); // For totals td, etc.
            $table->string('totals_header_text_align', 20)->default('left'); // For totals th
            $table->string('footer_text_align', 20)->default('center'); // For footer

            $table->unsignedBigInteger('business_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_settings');
    }
};
