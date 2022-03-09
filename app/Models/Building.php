<?php

namespace App\Models;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'building';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'owner_id',
    'country_department_id',
    'town_id',
    'town_district_id',
    'building_admin_id',
    'code',
    'image_path',
    'description',
    'features',
    'address',
    'building_type',
    'socioeconomic',
    'rooms',
    'bathrooms',
    'parking_lots',
    'area',
    'private_area',
    'floor',
    'antiquity',
    'lease_fee',
    'admin_fee',
    'commission',
    'insurance_carrier',
    'insurance_commission',
    'insurance_type',
    'available'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'features' => 'array',
    'address' => 'array',
    'available' => 'boolean',
  ];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = ['buildingAdmin', 'owner'];

  protected $appends = ['image_url'];

  /**
   * Recuera la información de la administración
   * de este inmueble
   */
  public function buildingAdmin()
  {
    return $this->belongsTo(BuildingAdmin::class);
  }

  /**
   * Recupera las imagenes asociadas al edificio
   */
  public function images()
  {
    return $this->hasMany(BuildingImage::class);
  }

  /**
   * Recupera al propietario del inmueble.
   */
  public function owner()
  {
    return $this->belongsTo(Customer::class, 'owner_id');
  }

  public function getImageUrlAttribute()
  {
    $url = null;
    if($this->image_path){
      $path = urlencode($this->image_path);
      $url = asset('storage/' . $path);
    }

    return $this->attributes['imageUrl'] = $url;
  }
}
