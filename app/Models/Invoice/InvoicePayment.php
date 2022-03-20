<?php

namespace App\Models\Invoice;

use App\Models\Customer\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
	use HasFactory;

	/**
	 * The table associated with model
	 * @var string 
	 */
	protected $table = 'invoice_payment';

	/**
	 * Los campos que pueden ser asignados de forma masiva.
	 */
	protected $fillable = [
		'customer_id',
		'payment_date',
		'amount',
		'cancel',
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
	 * Get the invoice to this payment
	 */
	public function invoice()
	{
		return $this->belongsTo(Invoice::class, 'invoice_id');
	}

	/**
	 * Get the customer who made the payment
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id');
	}
}
