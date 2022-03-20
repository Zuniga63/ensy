<?php

namespace App\Models\Invoice;

use App\Models\Customer\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'invoice';

  /**
   * Los campos que pueden ser asignados de forma masiva.
   */
  protected $fillable = [
    'seller_id',
    'customer_id',
    'customer',
    'customer_document',
    'seller',
    'invoice_prefix',
    'invoice_number',
    'invoice_date',
    'amount',
    'cash',
    'credit',
    'balance',
    'cancel',
    'cancel_message',
    'transaction_code',
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
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['items'];

  /**
   * Get the seller of this invoice
   */
  public function seller()
  {
    return $this->belongsTo(User::class, 'seller_id');
  }

  /**
   * Get the customer of this invoice
   */
  public function customer()
  {
    return $this->belongsTo(Customer::class, 'customer_id');
  }

  /**
   * Get the item of this invoice
   */
  public function items()
  {
    return $this->hasMany(InvoiceItem::class);
  }

  /**
   * Get the payments of this invoice
   */
  public function payments()
  {
    return $this->hasMany(InvoicePayment::class);
  }
}
