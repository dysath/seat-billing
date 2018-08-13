<?php

namespace Denngarr\Seat\Billing\Models;

use Illuminate\Database\Eloquent\Model;

class CorporationBill extends Model
{
    public $timestamps = true;

    protected $table = 'seat_billing_corp_bill';

    protected $fillable = ['id', 'corporation_id', 'month', 'year', 'pve_bill', 'mining_bill', 'pve_taxrate', 'mining_taxrate', 'mining_modifier'];

}

