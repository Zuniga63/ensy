<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
  use HasFactory;

  /**
   * The table associated with model
   * @var string 
   */
  protected $table = 'daily_activity';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['start_date', 'end_date', 'title', 'description'];
}
