<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'invoice_item';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'quantity',
    'description',
    'unit_value',
    'discount',
    'amount',
    'cancel',
    'cancel_message',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'cancel' => 'boolean',
  ];

  /**
   * Get the invoice to this payment
   */
  public function invoice()
  {
    return $this->belongsTo(Invoice::class, 'invoice_id');
  }
}
