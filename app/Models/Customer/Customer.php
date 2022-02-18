<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'customer';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'first_name',
    'last_name',
    'document_number',
    'document_type',
    'email',
    'image_path',
    'sex'
  ];

  protected $appends = ['full_name', 'image_url'];

  /**
   * Retorna el nombre completo del cliente
   */
  public function getFullNameAttribute()
  {
    $fullName = "$this->first_name $this->last_name";
    return $this->attributes['fullName'] = trim($fullName);
  }

  public function getImageUrlAttribute()
  {
    $color ="7F9CF5";
    $bg = "EBF4FF";

    if($this->sex === 'f'){
      $color = "EC4899";
      $bg = "fbcfe8";
    }

    return 'https://ui-avatars.com/api/?name='.urlencode($this->full_name)."&color=$color&background=$bg";
  }

  /**
   * Get the contacts registered for the customer
   */
  public function contacts()
  {
    return $this->hasMany(CustomerContact::class);
  }

  /**
   * Get the personal data register for the customer
   */
  public function information()
  {
    return $this->hasOne(CustomerInformation::class);
  }

  /**
   * Get the addresses register for the customer
   */
  public function addresses()
  {
    return $this->hasMany(CustomerAddress::class);
  }

  /**
   * Get the personal reference registered for the customer
   */
  public function references()
  {
    return $this->hasMany(CustomerReference::class);
  }

  /**
   * Get the financial data registered for the customer.
   */
  public function financialData()
  {
    return $this->hasOne(CustomerFinancialData::class);
  }
}
