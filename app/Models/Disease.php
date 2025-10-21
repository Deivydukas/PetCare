<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Disease extends Model
{
    use HasFactory;

    protected $fillable = ['health_record_id', 'name', 'treatment'];

    public function healthRecord()
    {
        return $this->belongsTo(HealthRecord::class);
    }
}
