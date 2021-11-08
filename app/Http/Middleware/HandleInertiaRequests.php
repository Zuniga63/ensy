<?php

namespace App\Http\Middleware;

use App\Models\BusinessConfig;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
  /**
   * The root template that's loaded on the first page visit.
   *
   * @see https://inertiajs.com/server-side-setup#root-template
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determines the current asset version.
   *
   * @see https://inertiajs.com/asset-versioning
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  public function version(Request $request)
  {
    return parent::version($request);
  }

  /**
   * Defines the props that are shared by default.
   *
   * @see https://inertiajs.com/shared-data
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function share(Request $request)
  {
    return array_merge(parent::share($request), [
      'businessConfig' => $this->getBusinessConfig(),
    ]);
  }

  /**
   * Crea laestructura de los datos con la configuración del sitio
   * 
   * @return array
   */
  protected function getBusinessConfig()
  {
    //Se recupera el objeto
    $config = BusinessConfig::first();
    if(!$config){
      return;
    }
    
    return [
      'id' => $config->id,
      'name' => $config->name,
      'logo' => $config->logo ? asset('storage/' . $config->logo) : null,
      'favicon' => $config->favicon ? asset('storage/' . $config->favicon) : null,
      'phone' => [
        'number' => $config->phone,
        'show' => $config->show_phone,
      ],
      'address' => [
        'value' => $config->address,
        'show' => $config->show_address ? true : false,
      ],
      'email' => [
        'value' => $config->email,
        'show' => $config->show_email ? true : false,
      ],
      'whatsapp' => [
        'number' => $config->whatsapp,
        'show' => $config->show_whatsapp ? true : false,
      ],
      'facebook' => [
        'nick' => $config->facebook_nick,
        'link' => $config->facebook_link,
        'show' => $config->show_facebook ? true : false,
      ],
      'instagram' => [
        'nick' => $config->instagram_nick,
        'link' => $config->instagram_link,
        'show' => $config->show_instagram ? true : false,
      ],
    ];
  }
}
