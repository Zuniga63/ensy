<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessConfig extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'business_config';
}
