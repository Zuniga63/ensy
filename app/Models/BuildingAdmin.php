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

  /**
   * Recupera los inmuebles bajo este grupo de 
   * administración
   */
  public function buildings()
  {
    return $this->hasMany(Building::class);
  }
}
