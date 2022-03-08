<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReference extends Model
{
    use HasFactory;

    /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer_reference';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'document_number',
    'document_type',
    'email',
    'phone',
    'address'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'address' => 'array',
  ];

  /**
   * Convierte todos los caracteres a minusculas
   */
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = $value ? strtolower($value) : null;
  }

  /**
   * Customer associated with this contact
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
