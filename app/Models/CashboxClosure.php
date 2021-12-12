<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashboxClosure extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'cashbox_closure';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'closing_date',
    'base',
    'new_base',
    'incomes',
    'expenses',
    'coins',
    'bills',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'coins' => 'array',
    'bills' => 'array',
  ];

  /**
   * Relationship to cashbox, one to one
   */
  public function cashbox()
  {
    $this->hasOne(Cashbox::class);
  }
}
