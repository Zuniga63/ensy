<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFinancialData extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer_financial_data';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'occupation',
    'position',
    'dependent',
    'description',
    'salary',
    'company',
    'admission_date',
    'departure_date',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'dependent' => 'boolean',
    'company' => 'array',
    'salary' => 'decimal:2'
  ];

  /**
   * Customer associated with this contact
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
