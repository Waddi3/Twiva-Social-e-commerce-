<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\User\Entities\User;

class PostPrice extends Model
{
    protected $guarded = [];


    public function Post()
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }

    public function getInfluencerAmountAttribute()
    {
        if ($this->final_price > 0) {
            return ($this->final_price * 85) / 100;
        }
        return ($this->price * 85) / 100;
    }

    public function whatsAppGroup()
    {
        return $this->belongsTo(WhatsAppGroup::class, 'infulancer_id', 'id');
    }

    public function scopeRelatedToUser($query, $user_id = null)
    {

        if (is_null($user_id)) {
            $user_id = auth('api')->id();
        }
        return $query->where(function ($where) use ($user_id) {
            return $where->where(function ($user_where) use ($user_id) {
                return $user_where->where('infulancer_id', $user_id)->where('user_type', (new User())->getMorphClass());
            })->orWhere(function ($or_where)  use ($user_id) {
                return $or_where->where('user_type', (new WhatsAppGroup())->getMorphClass())
                    ->whereHas('whatsAppGroup', function ($where_has) use ($user_id) {
                        return $where_has->where('user_id', $user_id);
                    });
            });
        });
    }
}
