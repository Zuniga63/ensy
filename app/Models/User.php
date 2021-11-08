<?php

namespace App\Models;

use App\Notifications\MyEmailVerification;
use App\Notifications\MyResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
  use HasApiTokens;
  use HasFactory;
  use HasProfilePhoto;
  use Notifiable;
  use TwoFactorAuthenticatable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'profile_photo_url',
  ];

  /**
   * Los siguientes dos metodos son utilizados para personalizar 
   * y traducir los correos enviados por laravel para la confirmacion
   * y para el reseteo de las contraseñas.
   * 
   * Los metodos de referencia se encuentran en:
   *  laravel\framework\src\Illuminate\Auth\Notifications
   * y los metodos para crear las notificaciones son
   * php artisan make:notification MyResetPassword
   * php artisan make:notification MyEmailVerification
   */

  /**
   * Metodo para solicitar el reseteo de la contraseña 
   * por medio de un correo confirmado.
   */
  public function sendPasswordResetNotification($token)
  {
    $this->notify(new MyResetPassword($token));
  }

  /**
   * Metodo para enviar el correo de confirmacion
   * al usuario
   */
  public function sendEmailVerificationNotification()
  {
    $this->notify(new MyEmailVerification());
  }

  public function sessions()
  {
    return $this->hasMany(Session::class, 'user_id');
  }
}
