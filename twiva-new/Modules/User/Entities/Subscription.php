<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscription';
    protected $id = 'id';
    protected $primaryKey = 'id';
    protected $fillable = ['subscription_title', 'type', 'price', 'note', 'is_active', 'no_of_days', 'max_products'];
}
