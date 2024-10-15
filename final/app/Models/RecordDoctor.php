<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordDoctor extends Model
{
    use HasFactory;
    protected $table = 'record_dectors';
    protected $fillable = [
       ' doctors_id',
       'records_id',];
  
}
