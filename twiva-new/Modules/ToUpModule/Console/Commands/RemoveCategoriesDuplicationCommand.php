<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\InfluencerDetails;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Sizes;
use Modules\Influencer\Entities\SubCategories;

class RemoveCategoriesDuplicationCommand extends Command
{
    protected $signature = 'categories:remove-duplication';

    protected $description = 'Categories Remove Duplication';

    public function handle()
    {
        $duplicationStartFromId = 427;

        $this->info('Updating Categories Name');
        $this->updateCategoriesName();
        $this->line('');

        $this->info('Fixing Products Category');
        $this->fixProductsCategory($duplicationStartFromId);
        $this->line('');

        $this->info('Fixing Influencer Interests');
        $this->fixInfluencerInterests($duplicationStartFromId);
        $this->line('');

        $this->info('Fixing SubCategories');
        $this->fixSubCategories($duplicationStartFromId);
        $this->line('');

        $this->info('Removing DuplicatedCategories');
        $this->removeDuplicatedCategories();
        $this->line('');

        $this->info('Inserting New Categories');
        $this->insertNewCategories();
        $this->line('');

        Sizes::query()->firstOrCreate(['name' => 'N/A']);
        Colors::query()->firstOrCreate(['name' => 'N/A'], ['hexaval' => 'N/A']);
        return 1;
    }

    private function updateCategoriesName(): void
    {
        $progressBar = $this->output->createProgressBar(Category::count());
        $progressBar->start();
        foreach (Category::all() as $category)
        {
            $progressBar->advance();
            $category->update(['name' => ucwords($category->name)]);
        }
        $progressBar->finish();

    }

    private function fixProductsCategory($duplicationStartFromId): void
    {
        Product::where('sub_category', '=', 'Tshirt')->update(['sub_category' => null]);
        Product::where('category', '=', '0')->update(['category' => null]);
        Product::where('sub_category', '0')->update(['sub_category' => null]);
        $products = Product::withTrashed()->where('category', '>=', $duplicationStartFromId)->get();
        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();
        foreach ($products as $product)
        {
            $newCategory = Category::where('name', $product->category->name)->first();
            if (!$newCategory)
            {
                $newCategory = $product->category->id;
            }
            if ($newCategory->id > $duplicationStartFromId || $newCategory->name != $product->category->name)
            {
                $this->warn("newCategory is {$newCategory->id} and old category is {$product->category->id}");
                return;
            }
            $product->update(['category' => $newCategory->id]);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

    private function fixInfluencerInterests($duplicationStartFromId): void
    {
        $influencerDetails = InfluencerDetails::withTrashed()->get();
        $progressBar = $this->output->createProgressBar($influencerDetails->count());
        $progressBar->start();

        foreach ($influencerDetails as $influencerDetail)
        {
            $interests = explode(',', $influencerDetail->interests);
            $newInterests = [];
            foreach ($interests as $interest)
            {
                $interest = Str::replace(' ', '', $interest);
                if ($interest >= $duplicationStartFromId)
                {
                    $newID = Category::where('name', Category::find($interest)->name)->first()->id;
                    $newInterests[] = $newID;

                }
                else
                {
                    $newInterests[] = $interest;
                }

            }
            $influencerDetail->update(['interests' => implode(',', $newInterests)]);
            $progressBar->advance();
        }
        $progressBar->finish();

    }

    private function fixSubCategories($duplicationStartFromId): void
    {
        $subCategories = SubCategories::withTrashed()->get();
        $progressBar = $this->output->createProgressBar($subCategories->count());
        $progressBar->start();
        foreach ($subCategories as $subCategory)
        {
            $oldCategory = Category::where("id", $subCategory->category_id)->first();
            if ($oldCategory)
            {
                $newCategory = Category::where("name", $oldCategory->name)->first();
                $subCategory->update(["category_id" => $newCategory->id]);
            }
            else
            {
                $this->warn("$oldCategory has an issue");
            }

            $progressBar->advance();
        }
        $progressBar->finish();

    }

    private function removeDuplicatedCategories(): void
    {
        $duplicatedCategoriesName = Category::query()->select('name')->selectRaw('count("id") as idCounter')->groupBy('name')->havingRaw('idCounter > 1')->get()->pluck('name')->toArray();
        $progressBar = $this->output->createProgressBar(count($duplicatedCategoriesName));
        $progressBar->start();
        foreach ($duplicatedCategoriesName as $duplicatedCategory)
        {
            $duplicatedArray = Category::query()->where('name', $duplicatedCategory)->select('id', 'name')->orderByDesc('id')->get();
            if (count($duplicatedArray) > 1)
            {
                $duplicatedArray[0]->delete();
                $this->info("$duplicatedArray[0] has been deleted!");
            }
            $progressBar->advance();
        }
        $progressBar->finish();
    }

    private function insertNewCategories(): void
    {
        $newCategories = [
            "Luggage & Bags" => [
                ["Suitcases", 1, 1],
                ["Gym Bags", 1, 1],
                ["Accessory Bags", 1, 1],
                ["Laptop Bags", 1, 1],
                ["Backpacks", 1, 1],
                ["Messenger/Shoulder Bags", 1, 1],
                ["Purses", 1, 1],
                ["Waist Belt Bag", 1, 1],
                ["Other", 0, 0],
            ],
            "Books" => [
                ["Educational", 0, 0],
                ["Exercise Books", 0, 0],
                ["Novels", 0, 0],
                ["Diary", 0, 0],
                ["Magazines", 0, 0],
                ["Others", 0, 0],
                ["Text Books", 0, 0],
            ],
            "Office Supplies" => [
                ["Pens, Pencils & Erasers", 0, 0],
                ["Printing Papers", 0, 0],
                ["Notebooks, Binders & Post-it", 0, 0],
                ["notes", 0, 0],
                ["Other", 0, 0],
            ],
            "Software" => [
                ["Business & Finance", 0, 0],
                ["Entertainment", 0, 0],
                ["Digital Software", 0, 0],
                ["Graphics & Design", 0, 0],
                ["Health & Fitness", 0, 0],
                ["Music", 0, 0],
                ["Productivity", 0, 0],
                ["Utilities", 0, 0],
                ["Educational", 0, 0],
                ["Games", 0, 0],
                ["Other", 0, 0],
            ],
            "Athletics & Sports" => [
                ["Balls", 0, 0],
                ["Golf Equipment", 0, 0],
                ["Rackets", 0, 0],
                ["Skating", 0, 0],
                ["Camping equipment", 0, 0],
                ["Boxing", 0, 0],
                ["Gym equipment", 0, 0],
                ["Sport Kits", 0, 0],
                ["Bicycles & Scooters", 0, 0],
                ["Other", 0, 0],
            ],
            "Vehicle & Parts" => [
                ["Vehicles", 0, 0],
                ["Safety and Security", 0, 0],
                ["Motorcycle and Scooters", 0, 0],
                ["Trucks and Trailers", 0, 0],
                ["Vehicle Parts and Accessories", 0, 0],
                ["Watercraft and Boats", 0, 0],
                ["Others", 0, 0],
            ],
            "Services" => [
                ["Automotive Services", 0, 0],
                ["Building & Construction Services", 0, 0],
                ["Transport Services", 0, 0],
                ["Child Care & Education Services", 0, 0],
                ["Cleaning Services", 0, 0],
                ["Computer & IT Services", 0, 0],
                ["Entertainment Services", 0, 0],
                ["Catering & Event Services", 0, 0],
                ["Health & Beauty Services", 0, 0],
                ["Insurance services", 0, 0],
                ["Legal Services", 0, 0],
                ["Photography & Video Services", 0, 0],
                ["Tax & Financial Services", 0, 0],
                ["Travel Agents & Tours", 0, 0],
                ["Other Services", 0, 0],
            ],
            "Others" => [
                ["Toy & Games", 0, 0],
                ["Pet Supplies", 0, 0],
                ["Musical Instruments", 0, 0],
                ["Industrial & Scientific", 0, 0],
                ["Miscellaneous", 0, 0],
            ]
        ];
        foreach (array_keys($newCategories) as $category)
        {
            $category_id = Category::where('name', ucwords($category))->first()->id;
            foreach ($newCategories[$category] as $subCategory)
            {
                SubCategories::firstOrCreate(['name' => ucwords($subCategory[0])], ['category_id' => $category_id, 'is_color' => $subCategory[1], 'is_size' => $subCategory[2]]);
            }
        }


    }


}
