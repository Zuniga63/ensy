<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingAdmin extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'building_admin';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'country_department_id',
    'town_id',
    'town_district_id',
    'address',
    'admin_first_name',
    'admin_last_name',
    'admin_document_number',
    'phones',
    'email',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'phones' => 'array',
  ];

  protected $appends = ['full_name'];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = ['department', 'town', 'district'];
  /**
   * Retorna el nombre completo del cliente
   */
  public function getFullNameAttribute()
  {
    $fullName = "$this->admin_first_name $this->admin_last_name";
    return $this->attributes['fullName'] = trim($fullName);
  }

  /**
   * Convierte todos los caracteres del email a minusculas
   */
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = $value ? strtolower($value) : null;
  }

  /**
   * Recupera los inmuebles bajo este grupo de 
   * administración
   */
  public function buildings()
  {
    return $this->hasMany(Building::class);
  }

  /**
   * Recupera el departamento en el que se encuentra
   * la administración de edificio.
   */
  public function department()
  {
    return $this->belongsTo(CountryDepartment::class, 'country_department_id');
  }

  /**
   * Recupera el municipio en el que se 
   * registró la administración del edificio.
   */
  public function town()
  {
    return $this->belongsTo(Town::class);
  }

  /**
   * Se registra el barrio en el que se registró 
   * la administración del edificio.
   */
  public function district()
  {
    return $this->belongsTo(TownDistrict::class, 'town_district_id');
  }
}
