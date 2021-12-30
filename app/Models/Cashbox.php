<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
  use HasFactory;
  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'cashbox';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'slug',
    'code',
    'order',
  ];

  /**
   * Get the cash register closures associated with cashbox
   */
  public function closures()
  { 
    return $this->hasMany(CashboxClosure::class);
  }

  /**
   * Get the transactions associated with cashbox
   */
  public function transactions()
  {
    return $this->hasMany(CashboxTransaction::class);
  }
}
