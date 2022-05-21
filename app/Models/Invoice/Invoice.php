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
    'prefix',
    'number',
    'customer_name',
    'customer_document',
    'customer_address',
    'customer_phone',
    'seller_name',
    'expedition_date',
    'expiration_date',
    'subtotal',
    'discount',
    'amount',
    'cash',
    'credit',
    'cash_change',
    'balance',
    'cancel',
    'cancel_message',
  ];

  protected $appends = ['invoice_number'];

  /**
   * Retorna el nombre completo del cliente
   */
  public function getInvoiceNumberAttribute()
  {
    $prefix = $this->prefix ? "$this->prefix" . "-" : "";
    $number = $this->number;


    if($number < 10){
      $number = "000" . $number;
    }else if($number < 100){
      $number = "00" . $number;
    }else if($number < 1000){
      $number  = "0" . $number;
    }

    return $prefix . $number;
  }

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
