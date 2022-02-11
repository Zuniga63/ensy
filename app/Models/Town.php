<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'town';

  /**
   * Retorna la relación con el departamente del
   * publo en la base de datos.
   */
  public function countryDepartment()
  {
    return $this->hasOne(CountryDepartment::class);
  }

  /**
   * Retorna todas las relaciones con los 
   * barrios o distritos del pueblo en cuestion.
   */
  public function districts()
  {
    return $this->hasMany(TownDistrict::class);
  }
}
