<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer_address';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'town_district_id',
    'address',
    'other',
    'phone',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'state' => 'boolean',
  ];

  /**
   * Customer associated with this address
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
