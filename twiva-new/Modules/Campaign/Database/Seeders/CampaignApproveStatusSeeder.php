<?php

namespace Modules\Campaign\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Campaign\Entities\Post;
use Modules\Campaign\Enums\ApproveStatus;

class CampaignApproveStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Post::whereIn('approve_status',[ApproveStatus::Approved->value])->update([
            'approve_status' => ApproveStatus::Approved->value
        ]);

    }
}
