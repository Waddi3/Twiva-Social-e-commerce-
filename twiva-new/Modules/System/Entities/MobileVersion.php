<?php

namespace Modules\System\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileVersion extends Model
{

    const ANDROID_PLATFORM = 'ANDROID';
    const IOS_PLATFORM = 'IOS';

    protected $fillable = [
        'app_name',
        'platform',
        'version',
        'is_forced',
        'link',
        'store_link',
        'features',
    ];

}
