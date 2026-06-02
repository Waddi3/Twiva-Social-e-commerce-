<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\File;

class BulkProductUploadRepository
{

    public function getProductListFileName($destination) : string {
        $files = File::files($destination);
        if( !count($files) ) {
            return '';
        }
        foreach($files as $file) {
            if( $file->getExtension() == 'csv' ) {
                return $file->getFilename();
            }
        }
        return '';
    }

    public function getProductCoverImageName(array $images) : string {
        if( !count($images) ) {
            return '';
        }
        foreach($images as $image) {
            if( str_contains($image->getFileName(), 'cover') ) {
                return $image->getFileName();
            }
        }
        return $images[0]->getFileName();
    }

    /*public function moveImages(string $path) : bool {
        $images = File::files($path.'images/');
        if( count($images) ) {
            foreach($images as $image) {
                File::move($image->getPathName(), public_path('storage/images/products').'/'.$image->getFileName());
            }
            return true;
        }
        return false;
    }*/

    public function moveImage($image, $imageName) : bool {
        if( File::move($image->getPathName(), public_path('storage/images/products').'/'.$imageName) ) {
            return true;
        }
        return false;
    }
}
