<?php

namespace Modules\Influencer\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Influencer\Entities\Product;
use Modules\Shop\Entities\Shops;
use Modules\Influencer\Entities\InfluencerShopProducts;

// use Maatwebsite\Excel\Concerns\FromQuery;

class BusinessProductCatalogueExport implements FromCollection,
    // FromQuery
    WithHeadings
{
    /**
     * @return Collection
     */

    protected $businessID;
    protected $domain;

    public function __construct($id)
    {
        $this->businessID = $id;
        $this->domain = Shops::where('user_id', $this->businessID)->first()->domain ?? null;

    }

    public function collection()
    {
        // TODO::H check retail_price
        $products = Product::whereNotNull('price')
            ->with(['user.businessDetails', 'productImages'])
            ->where('user_id', $this->businessID)->get();
        $export = [];
        foreach ($products as $key => $product)
        {
            $export[$key]['id'] = $product->id ?? "";
            $export[$key]['title'] = $product->name ?? "";
            $export[$key]['condition'] = "New";
            $export[$key]['link'] = $this->generateLink($product->id);
            $export[$key]['image_link'] = $product->mainImagePath;
            $export[$key]['availability'] = $product->stock > 0 ? 'in stock' : 'out of stock';
            $export[$key]['status'] = isset($product->status) ? "active" : "hidden";
            $export[$key]['description'] = $product->description ?? "";
            $export[$key]['color'] = count($product?->color_details) ? implode(',', $product?->color_details?->pluck('name')->toArray()) : '';
            $export[$key]['size'] = count($product?->size_details) ? implode(',', $product?->size_details?->pluck('name')->toArray()) : '';
            $export[$key]['price'] = $product->price ?? "0";
            $export[$key]['brand'] = $product->user->name;
            $export[$key]['gtin'] = '';
            $export[$key]['identifier_exists'] = "false";
            $export[$key]['google_product_category'] = $product->category_details->name ?? '';
            $export[$key]['additional_image_link'] = $this->getProductImages($product);
        }
        return collect($export);
    }


    public function generateLink($product_id)
    {
        return route('shop.product.show', ['account' => $this->domain, 'product' => $product_id]);
    }

    private function getProductImages($product)
    {
        return implode(',', $product->productImages->pluck('imageUrl')->toArray());
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

}
