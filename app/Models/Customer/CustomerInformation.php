<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInformation extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer_information';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'document_type',
    'document_expedition_date',
    'document_expedition_place',
    'document_path',
    'birthplace',
    'birth_date',
    'nacionality',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'document_expedition_place' => 'array',
    'birthplace' => 'array',
  ];

  /**
   * Customer associated with this contact
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
