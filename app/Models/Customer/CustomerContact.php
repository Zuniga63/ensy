<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
  use HasFactory;
  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer_contact';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'number',
    'description',
    'other',
    'whatsapp',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'other' => 'array',
    'whatsapp' => 'boolean'
  ];

  
  /**
   * Customer associated with this contact
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
