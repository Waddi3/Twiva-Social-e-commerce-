<?php

namespace Modules\Influencer\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Shop\Entities\Shops;
use Modules\Influencer\Entities\InfluencerShopProducts;

class ProductCatalogueExport implements FromCollection, WithHeadings
{

    protected $id;
    protected $domain;
    public function __construct($id)
    {
        $this->id = $id;
        $this->domain = Shops::where('user_id', $this->id)->first()->domain ?? null;
    }

    public function collection()
    {
        $products = InfluencerShopProducts::whereNotNull('product_price')
            ->whereHas('actualDetail', function ($query) {
                $query->where('status', 1)->whereHas('user');
            })
            ->with(['actualDetail.user.businessDetails', 'influencerProductImages', 'productMainImage', 'BusinessProductImages', 'businessProductMainImage', 'actualDetail.productImages'])
            ->where('influencer_id', $this->id)->get();
        $export = [];
        foreach ($products as $key => $product)
        {
            $productImages = $this->getProductImages($product);
            $export[$key]['id'] = $product->id ?? "";
            $export[$key]['title'] = $product->product_title ?? "";
            $export[$key]['condition'] = 'New';
            $export[$key]['link'] = $this->generateLink($product->id);
            $export[$key]['image_link'] = $product->mainImagePath ?? explode(',', $productImages)[0];
            $export[$key]['availability'] = $product->actualDetail->stock > 0 ? 'in stock' : 'out of stock';
            $export[$key]['status'] = isset($product->actualDetail->status) ? "active" : "hidden";
            $export[$key]['description'] = $product->actualDetail->description ?? "";
            $export[$key]['color'] = count($product->actualDetail?->color_details) ? implode(',', $product->actualDetail?->color_details?->pluck('name')->toArray()) : '';
            $export[$key]['size'] = count($product->actualDetail?->size_details) ? implode(',', $product->actualDetail?->size_details?->pluck('name')->toArray()) : '';
            $export[$key]['price'] = $product->product_price ?? "0";
            $export[$key]['brand'] = $product->actualDetail->user->name;
            $export[$key]['gtin'] = '';
            $export[$key]['identifier_exists'] = "false";
            $export[$key]['google_product_category'] = $product->actualDetail->category_details->name ?? '';
            $export[$key]['additional_image_link'] = $this->getProductImages($product);
        }
        return collect($export);
    }

    public function generateLink($product_id)
    {
        return route('shop.product.show', ['account' => $this->domain, 'product' => $product_id]);
    }

    public function headings(): array
    {
        return [
            'id',
            'title',
            'condition',
            'link',
            'image_link',
            'availability',
            'status',
            'description',
            'color',
            'size',
            'price',
            'brand',
            'gtin',
            'identifier_exists',
            'google_product_category',
            'additional_image_link',
        ];
    }
    private function getProductImages($product)
    {
        $influencerProductsImages = $product->influencerProductImages->pluck('imageUrl')->toArray();
        $businessProductsImages = $product->businessProductImages->pluck('imageUrl')->toArray();
        $influencerProductsImages = count($influencerProductsImages) ? implode(',', $influencerProductsImages) . ',' : '';
        return $influencerProductsImages . implode(',', $businessProductsImages);
    }
}
