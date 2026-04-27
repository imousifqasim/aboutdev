<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@droplaunch.dev',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->role = 'admin';
        $admin->save();

        Profile::create([
            'user_id' => $admin->id,
            'username' => 'admin',
        ]);

        // Create Demo User
        $demo = User::create([
            'name' => 'Tousif Ahmad',
            'email' => 'demo@droplaunch.dev',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $profile = Profile::create([
            'user_id' => $demo->id,
            'username' => 'tousif',
            'bio' => 'Full-stack developer & entrepreneur. Building amazing things on the web.',
            'theme' => 'default',
            'location' => 'Pakistan',
            'social_links' => [
                'github' => 'https://github.com/imousifqasim',
                'twitter' => 'https://twitter.com/tousif',
                'linkedin' => 'https://linkedin.com/in/tousif',
            ],
        ]);

        Subscription::create([
            'user_id' => $demo->id,
            'plan' => 'free',
            'start_date' => now(),
        ]);

        $demo->links()->createMany([
            ['title' => 'My Website', 'url' => 'https://example.com', 'position' => 1],
            ['title' => 'GitHub', 'url' => 'https://github.com/imousifqasim', 'icon' => 'github', 'position' => 2],
            ['title' => 'LinkedIn', 'url' => 'https://linkedin.com/in/tousif', 'icon' => 'linkedin', 'position' => 3],
        ]);

        // Default Site Settings
        $settings = [
            'site_name' => 'DropLaunch',
            'site_description' => 'Create your personal portfolio page and share your links with the world.',
            'site_keywords' => 'portfolio, links, bio, personal page, linktree alternative',
            'premium_price' => '9.99',
            'premium_currency' => 'USD',
            'contact_email' => 'admin@droplaunch.dev',
            'footer_text' => 'Made with DropLaunch',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value);
        }
    }
}
