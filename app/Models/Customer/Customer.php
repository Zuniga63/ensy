<?php

namespace App\Models\Customer;

use App\Models\Building;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoicePayment;
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

  //------------------------------------------------------------
  // SETERS AND GETTERS
  //------------------------------------------------------------
  protected $appends = ['full_name', 'image_url'];

  /**
   * The relationships that should always be loaded.
   *
   * @var array
   */
  protected $with = ['contacts'];

  /**
   * Retorna el nombre completo del cliente
   */
  public function getFullNameAttribute()
  {
    $fullName = "$this->first_name $this->last_name";
    return trim($fullName);
  }

  public function getImageUrlAttribute()
  {
    $color = "7F9CF5";
    $bg = "EBF4FF";

    if ($this->sex === 'f') {
      $color = "EC4899";
      $bg = "fbcfe8";
    }

    return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . "&color=$color&background=$bg";
  }

  /**
   * Convierte todos los caracteres 
   */
  public function setEmailAttribute($value)
  {
    $this->attributes['email'] = $value ? strtolower($value) : null;
  }

  //--------------------------------------------------------------
  // RELACIONES
  //--------------------------------------------------------------

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

  /**
   * Obtiene los inmuebles que son propiedad del
   * cliente.
   */
  public function buildings()
  {
    return $this->hasMany(Building::class, 'owner_id');
  }
  /**
   * Get the invoice that this user has registered.
   */
  public function invoices()
  {
    return $this->hasMany(Invoice::class, 'customer_id');
  }

  /**
   * Get the payment registered for this customer
   */
  public function invoicePayments()
  {
    return $this->hasMany(InvoicePayment::class, 'customer_id');
  }

  /**
   * Get the invoice more recent of customer
   */
  public function lastInvoice()
  {
    return $this->hasOne(Invoice::class)
      ->orderBy('expedition_date', 'DESC')
      ->where('cancel', 0)
      ->without('items')
      ->select(['id', 'customer_id', 'amount', 'expedition_date as date', 'cancel']);
  }

  /**
   * Get the payment more recent of customer
   */
  public function lastPayment()
  {
    return $this->hasOne(InvoicePayment::class)
      ->where('cancel', 0)
      ->where('initial_payment', 0)
      ->orderBy('payment_date', 'DESC')
      ->without('transaction')
      ->select(['id', 'customer_id', 'payment_date as date', 'amount']);
  }

  public function oldInvoicePending()
  {
    return $this->hasOne(Invoice::class)
      ->where('cancel', 0)
      ->without('items')
      ->whereNotNull('balance')
      ->orderBy('expedition_date')
      ->select(['id', 'customer_id', 'balance', 'expedition_date as date', 'cancel']);
  }
}
