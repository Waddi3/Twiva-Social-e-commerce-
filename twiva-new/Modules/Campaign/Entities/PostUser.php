<?php

namespace Modules\Campaign\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Home\Entities\Transaction;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\User\Entities\User;


class PostUser extends Model
{
    use SoftDeletes;
    const AppliedStatus = 1;
    const RejectedStatus = 2;
    const DeclinedStatus = 2;

    const AcceptedStatus = 3;
    const HiredStatus = 4;
    const CompletedStatus = 5;

    const PENDING_STATUS = 6;
    protected $guarded = [];


    public function post()
    {
        return $this->belongsTo(Post::class)->with(["Images", "user", "postDetail"]);
    }

    public function postDetail()
    {
        return $this->hasOne(PostDetail::class, 'post_user_id')
            ->with("media");
    }

    public function Images()
    {
        return $this->belongsTo(PostImage::class);
    }

    public function influencer()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function influencerPriceBase()
    {
        return $this->hasMany(PostPrice::class, "post_id", "post_id");
    }

    public function getBusinessInfluencerPriceAttribute()
    {
        return $this->influencerPriceObject()->price ?? null;
    }

    public function influencerPriceObject()
    {
        return collect($this->influencerPriceBase)->filter(function ($influencerPrice) {
            return $influencerPrice->infulancer_id == $this->user_id;
        })->first();
    }

    public function getInfluencerComplementryItemAttribute()
    {
        return $this?->influencerPriceObject()
            ?->complementry_item;
    }


    public function getStatusStringForInfluencerAttribute()
    {
        switch ($this->status) {
            case 1:
                echo "You have accepted this invitation";
                break;
            case 2:
                echo "You have rejected this invitation duo to " . $this->reason;
                break;
            case 4:
                echo "You have been hired ";
                break;
            case 5:
                echo "You have completed this invitation";
                break;
            case 6:
                echo "This invitation is pending";
                break;
        }
    }

    public function getStatusStringForBusinessAttribute()
    {
        switch ($this->status) {
            case 1:
                return "Applied";
            case 2:
                return "Declined";
            case 3:
                return "Accepted";
            case 4:
                return "Hired";
            case 5:
                return "Completed";
            case 6:
                return "Pending";
        }
    }

    public static function getStatusNumber($status)
    {
        return match ($status) {
            "Accepted" => self::AcceptedStatus,
            "Pending" => self::PENDING_STATUS,
            "Hired" => self::HiredStatus,
            "Declined" => self::RejectedStatus,
            "Applied" => self::AppliedStatus,
            default => "",
        };
    }

    public function whatsAppGroup()
    {
        return $this->belongsTo(WhatsAppGroup::class, 'user_id', 'id');
    }

    public function scopeRelatedToUser($query, $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = auth('api')->id();
        }
        return $query->where(function ($where) use ($user_id) {
            return $where->where(function ($user_where) use ($user_id) {
                return $user_where->where('user_id', $user_id)->where('user_type', (new User)->getMorphClass());
            })->orWhere(function ($or_where) use ($user_id) {
                return $or_where->where('user_type', (new WhatsAppGroup)->getMorphClass())
                    ->whereHas('whatsAppGroup', function ($where_has) use ($user_id) {
                        return $where_has->where('user_id', $user_id);
                    });
            });
        });
    }
}
