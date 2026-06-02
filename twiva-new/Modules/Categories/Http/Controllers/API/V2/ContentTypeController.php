<?php


namespace Modules\Categories\Http\Controllers\API\V2;


use Illuminate\Routing\Controller;
use Modules\Home\Http\Controllers\API\V2\BaseTrait;
use Modules\Influencer\Entities\ContentType;

class ContentTypeController extends Controller
{
    use BaseTrait;

    public function index()
    {
        $contentType = ContentType::select('id', 'name')->get();
        return response(['content_type' => $contentType], SUCCESS);
    }

}
