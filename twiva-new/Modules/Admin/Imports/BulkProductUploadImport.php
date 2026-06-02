<?php

namespace Modules\Admin\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Admin\Repositories\BulkProductUploadRepository;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Sizes;
use Modules\Influencer\Entities\SubCategories;

class BulkProductUploadImport implements ToCollection
{
    private Collection $data;
    private int $business_id;
    private string $destinationPath;
    private BulkProductUploadRepository $bulkProductUploadRepository;
    public function __construct(int $business_id, string $destinationPath)
    {
        $this->business_id = $business_id;
        $this->destinationPath = $destinationPath;
        $this->bulkProductUploadRepository = new BulkProductUploadRepository();
    }

    public function collection(Collection $rows)
    {
        $this->data = $rows;
        return $this->importProducts();
    }

    private function importProducts() {
        /*try {*/
            foreach( $this->data as $index => $productArray ) {
                // Skip first row
                if( !$index ) {
                    continue;
                }

                /*dd(Product::where('name', $productArray[0])
                    ->where('user_id', $this->business_id)
                    ->count());
                dd(Product::where('name', $productArray[0])
                    ->where('user_id', $this->business_id)
                    ->count());*/

                // Product must be unique
                if( Product::where('name', $productArray[0])
                    ->where('user_id', $this->business_id)
                    ->count() ) {
                    continue;
                }

                $sizes = implode(',', Sizes::whereIn('name', explode('-', $productArray[8]))->pluck('id')->toArray());
                $colors = implode(',', Colors::whereIn('name', explode('-', $productArray[9]))->pluck('id')->toArray());
                $subCategory = SubCategories::where('name', $productArray[2])->first();
                $product = Product::create([
                    'user_id' => $this->business_id,
                    'title' => $productArray[0],
                    'name' => $productArray[0],
                    'sku' => $productArray[1],
                    'category' => $subCategory ? $subCategory->category_id : null,
                    'sub_category' => $subCategory ? $subCategory->id : null,
                    'brand' => $productArray[3],
                    'price' => $productArray[4],
                    'retail_price' => $productArray[5],
                    'discount' => !is_null($productArray[6]) ? $productArray[5] - $productArray[6] : null,
                    'stock' => $productArray[7],
                    'color' => $colors,
                    'size' => $sizes,
                    'description' => $productArray[10],
                    'specification' => $productArray[11],
                    'status' => 1,
                ]);

                // Import images
                if( $product && !is_null($productArray[12]) ) {
                    $this->addImages($product, $productArray[12]);
                }
            }
            return true;
        /*}
        catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }*/
    }

    public function addImages( Product $product, $subDirectory ) : bool
    {
        if( !File::exists($this->destinationPath.$subDirectory) ) {
            return false;
        }
        $images = File::files($this->destinationPath.$subDirectory);
        $productCoverImageName = $this->bulkProductUploadRepository->getProductCoverImageName($images);

        foreach( $images as $image ) {
            $imageName = $image->getFilename();
            $isCover = false;
            if( $productCoverImageName == $imageName ) {
                $isCover = true;
                if( str_contains($imageName, 'cover') ) {
                    $imageName = str_replace(' ', '_', $product->title).'_'.$product->id.'_cover.'.$image->getExtension();
                }
            }
            $product->productImages()->create([
                'image' => $imageName,
                'is_cover_pic' => $isCover
            ]);

            $this->bulkProductUploadRepository->moveImage($image, $imageName);
        }

        return true;
    }
}
