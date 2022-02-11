<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryDepartment extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'country_department';

  public function towns()
  {
    return $this->hasMany(Town::class);
  }
}
