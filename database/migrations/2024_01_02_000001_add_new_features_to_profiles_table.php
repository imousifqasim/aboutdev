<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Spotlight/CTA Button
            $table->string('spotlight_label')->nullable()->after('custom_domain');
            $table->string('spotlight_url')->nullable()->after('spotlight_label');
            $table->string('spotlight_icon')->nullable()->after('spotlight_url');

            // Testimonials (JSON array of {name, role, text, avatar})
            $table->json('testimonials')->nullable()->after('spotlight_icon');

            // Resume/CV (JSON: {education: [], experience: [], skills: []})
            $table->json('resume')->nullable()->after('testimonials');

            // Contact Form toggle
            $table->boolean('contact_form_enabled')->default(false)->after('resume');

            // Background Image (hero-style)
            $table->string('background_image')->nullable()->after('contact_form_enabled');

            // Email Signature enabled
            $table->boolean('email_signature_enabled')->default(true)->after('background_image');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'spotlight_label', 'spotlight_url', 'spotlight_icon',
                'testimonials', 'resume', 'contact_form_enabled',
                'background_image', 'email_signature_enabled',
            ]);
        });
    }
};
