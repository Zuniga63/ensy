<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashboxTransaction extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'cashbox_transaction';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'cashbox_id',
    'transaction_date',
    'description',
    'amount',
    'code',
    'transfer',
    'blocked',
  ];

  /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
      'transfer' => 'boolean',
      'blocked' => 'boolean',
      'amount' => 'decimal:2',
  ];

  /**
   * Get the cahsbox associated with transaction
   */
  public function cashbox()
  {
    return $this->hasOne(Cashbox::class);
  }
}
