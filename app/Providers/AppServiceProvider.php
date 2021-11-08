<?php

namespace App\Providers;

use App\Models\BusinessConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->loadBusinessConfig();
  }

  protected function loadBusinessConfig()
  {
    try {
      $data = BusinessConfig::first(['name', 'favicon']);
      if ($data) {
        if ($data->name) {
          config(['app.name' => $data->name]);
        }

        if ($data->favicon) {
          $path = asset('storage/' . $data->favicon);
          config(['app.favicon' => $path]);
        }
      }
    } catch (\Throwable $th) {
      config(['app.name' => env('APP_NAME', 'APP')]);
      config(['app.favicon' => env('APP_FAVICON', '')]);
    }
  }
}
