<?php

namespace Modules\Platform\Http\Controllers\API\V2;

use Illuminate\Support\Facades\DB;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Products\Services\V2\V2LLMCaptionService;
use Modules\User\Entities\User;

class ApiV2LMMPlatformController
{
    public function __construct(public V2LLMCaptionService $llm_caption_service) {}

    public function generator($product_id)
    {
        $user = auth('api')->user()->load('brandTheme');
        if ($user->type == User::InfluencerType) {
            $product = InfluencerShopProducts::select('id', 'product_description as description', 'product_id', 'product_title as name')
                ->with('actualDetail:id,name,category,sub_category,brand,description,color,size,price,retail_price,discount')
                ->where('influencer_id', $user->id)
                ->where('id', $product_id)
                ->firstOrFail();
        } else {
            $product = Product::where('user_id', $user->id)
                ->select('id', 'name', 'category', 'sub_category', 'brand', 'description', 'color', 'size', 'price', 'retail_price', 'discount')
                ->where('id', $product_id)
                ->firstOrFail();
        }

        $brand_theme = $user->brandTheme()
            ->select('*')
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', languages.id, 'name', languages.name)) FROM languages WHERE FIND_IN_SET(languages.id, brand_themes.language_ids)) AS languages")
            )
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', interests.id, 'name', interests.name)) FROM interests WHERE FIND_IN_SET(interests.id, brand_themes.interest_ids)) AS interests")
            )
            ->first();


        $product_data = [
            'product_link' => route('shop.product.show', ['account' => $user->shop->domain, 'product' => $product->id]),
            'product_title' => $product->name,
            'product_description' => $product->description,
            'brand_name' => $product->brand,
            'pricing_point' => $product->retail_price,
            'discount' => ($product->discount_rate ?? 0) . "%",
            'color' => Product::removeNA($product->color_details),
            'size' => Product::removeNA($product->size_details),
            'product_category' => $product->category_details->name ?? null,
        ];
        $this->llm_caption_service->setBrandTheme($brand_theme);
        $this->llm_caption_service->generateQuestion($product_data);
        $question = $this->llm_caption_service->getQuestion();
        // dd($question);
        $this->llm_caption_service->runRequest();
        $answer = $this->llm_caption_service->getAnswer();
        $answers = $this->llm_caption_service->getAnswers();

        return response()->json([
            'data' => [
                'question' => $question,
                'answer' => $answer,
                'answers' => $answers,
            ]
        ]);
    }
}
