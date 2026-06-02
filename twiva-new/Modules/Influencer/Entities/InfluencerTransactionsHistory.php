<?php

namespace Modules\Influencer\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Home\Entities\Transaction;

class InfluencerTransactionsHistory extends Model
{

    protected $table = 'influencer_transactions_history';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'transaction_amount', 'request_type', 'request_id'];
    #request type
    const BUYER_PURCHASE = Transaction::BUYER_PURCHASE;
    const BUSINESS_PAY_FOR_CAMPAIGN = Transaction::BUSINESS_PAY_FOR_CAMPAIGN;
    const INFLUENCER_PERCENT_FROM_PRODUCT = 5;


    public function getRequestTypeTextAttribute(): string
    {
        switch ($this->request_type)
        {
            case self::BUYER_PURCHASE:
                return 'Buyer Purchased a product from your store';
                break;
            case self::BUSINESS_PAY_FOR_CAMPAIGN:
                return 'Business paid for campaign';
                break;
        }
    }

}
