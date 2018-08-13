<?php

namespace Denngarr\Seat\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterBill extends Model
{
    public $timestamps = true;

    protected $table = 'seat_billing_character_bill';

    protected $fillable = ['id', 'character_id', 'month', 'year', 'mining_bill', 'mining_taxrate'];

}

