<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    public function sale(){
        return $this->hasMany('App\Sales');
    }

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    protected $fillable = [
        'customer_id',
        'total',
        // other fields that can be mass assigned
    ];

    protected $casts = [
        'is_paid' => 'boolean', // Ensuring it is treated as a boolean
    ];






}
