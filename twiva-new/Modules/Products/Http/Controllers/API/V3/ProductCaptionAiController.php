<?php

namespace Modules\Products\Http\Controllers\API\V3;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Products\Services\LLMCaptionService;
use Modules\User\Entities\Language;
use Modules\User\Entities\User;

class ProductCaptionAiController extends Controller
{
    public function __construct()
    {
    }

    public function suggestion()
    {
        $product_id = request('product_id');
        $user = auth('api')->user();
        $language = Language::whereIn('id',$user->brandTheme?->language_ids_list ?? [])->first()?->name ?? 'English';
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
        $product_data = [
            'product_link' => route('shop.product.show', ['account' => $user->shop->domain, 'product' => $product->id]),
            'product_title' => $product->name,
            'product_description' => $product->description,
            'brand_name' => $product->brand,
            'pricing_point' => $product->retail_price,
            'discount' => ($product->discount_rate ?? 0) . "%",
            'color' => $this->removeNA($product->color_details),
            'size' => $this->removeNA($product->size_details),
            'product_category' => $product->category_details->name ?? null, //
        ];
        $message = null;
        $answer = null;
        if ($product->description ?? null) {
            $llm_caption_service = new LLMCaptionService();
            $llm_caption_service->setLanguage($language);
            $llm_caption_service->setMessage($product_data);
            $message = $llm_caption_service->getMessage();
            $llm_caption_service->askToChatGpt();
            $answer = $llm_caption_service->getAnswer();
        }
        return response()->json(['data' => [
            'message' => $message,
            'answer' => $answer,
        ]]);
    }

    private function removeNA($details)
    {
        $filtered = $details->filter(function ($value, $key) {
            return $value->name !== 'N/A';
        });
        return $filtered->pluck('name')->count()
            ? $filtered->pluck('name')->implode(', ')
            : null;
    }
}
