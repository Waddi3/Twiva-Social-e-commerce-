<?php

namespace Modules\Products;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Model;

class EventTicketProduct extends Model {
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'location',
        'ticket_number',
        'business_product_id',
    ];
    public $timestamps = false;
    protected $casts = [
        'date'       => 'date',
        'start_time' => TimeCast::class,
        'end_time'   => TimeCast::class,
    ];

    public static function generateTicketNumber(): string
    {
        $uniqueNumber = \Str::random(5);
        if (!self::where('ticket_number', $uniqueNumber)->exists())
        {
            return $uniqueNumber;
        }
        return self::generateTicketNumber();
    }
}
