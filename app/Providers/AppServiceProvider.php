<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // get_email_config();
        view()->share('actual_url', asset('assets/'));
        config(['app.actual_url' => asset('assets/')]);

        view()->share('img_path', asset('assets/uploads/'));
        config(['app.img_path' => asset('assets/uploads/')]);

        view()->share('pusher_key', 'e0327d3f0e77f9fe8bc7');
        config(['app.pusher_key' => 'e0327d3f0e77f9fe8bc7']);

        view()->share('pusher_secret', 'ba763956d02e7557371e');
        config(['app.pusher_secret' => 'ba763956d02e7557371e']);

        view()->share('pusher_app_id', '2079365');
        config(['app.pusher_app_id' => '2079365']);

        view()->share('public_path', asset('/'));
        config(['app.public_path' => asset('/')]);
    }
}
