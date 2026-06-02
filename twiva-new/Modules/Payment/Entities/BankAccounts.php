<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\Country;

class BankAccounts extends Model
{
    protected $guarded = [];

    public function beneficiaryCountry()
    {
        return $this->belongsTo(Country::class, 'beneficiary_country_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function bankCountry()
    {
        return $this->belongsTo(Country::class, 'bank_country_id');
    }
}
