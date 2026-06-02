<?php

namespace Modules\ToUpModule\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Sizes;

class FixSizeColumnsCommand extends Command
{
    protected $signature = 'fix:sizes';

    protected $description = 'Add fixed list of sizes instead of link them to subcategory';

    public function handle()
    {
        $this->warn('Running FixSizeColumnsCommand.php');
        $this->removeSpaceFromProductSize();
        $this->removeSpaceFromOldSizeTable();
        $this->replaceIDWithName();
        $this->modifySizeTable();
        $this->seedNewSizes();
        $this->replaceNameWithID();
        $this->info('Succeed');


    }

    public function removeSpaceFromProductSize()
    {
        $this->line('');
        $this->info('removeSpaceFromProductSize');
        $bar = $this->output->createProgressBar(Product::count());
        $bar->start();
        $products = Product::withTrashed()->get();
        foreach ($products as $product)
        {
            if ($product->size)
            {
                $newSize = Str::lower($product->size);
                $product->update(['size' => Str::replace(' ', '', $newSize)]);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    private function replaceIDWithName()
    {
        $this->line('');
        $this->info('replace ID With Name');
        $bar = $this->output->createProgressBar(Product::count());
        $bar->start();
        $products = Product::withTrashed()->get();
        foreach ($products as $product)
        {
            $oldSizes = $product->sizeDetails;
            if ($oldSizes && $oldSizes != [])
            {
                $newSizes = '';
                foreach ($oldSizes as $oldSize)
                {
                    $newSizes .= Str::lower($oldSize->name) . ',';
                }
                $product->update(['size' => $newSizes]);
            }
            $bar->advance();
        }
        $bar->finish();
    }

    private function removeSpaceFromOldSizeTable()
    {
        $this->line('');
        $this->info('removeSpaceFromOldSizeTable');
        $bar = $this->output->createProgressBar(Sizes::count());
        $bar->start();
        foreach (Sizes::all() as $size)
        {
            $size->update(['name' => Str::replace(' ', '', $size->name)]);
            $bar->advance();
        }
        $bar->finish();
    }

    private function modifySizeTable()
    {
        $this->line('');
        $this->info('modify Size Table');
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'Modules/ToUpModule/Database/Migrations/2022_09_02_034844_drop_some_columns_in_sizes_table.php'
        ]);
        $this->line('');
        $this->warn('Sizes Table modified ');
    }

    private function seedNewSizes()
    {
        $this->line('');
        $this->info('seedNewSizes');
        $this->call("db:seed", [
            '--force' => true,
            '--class' => 'Modules\Categories\Database\Seeders\SizesDatabaseSeeder'
        ]);
        $this->line('');
        $this->info('Seeing Sizes Table');
    }

    private function replaceNameWithID()
    {
        $this->info('replacing Name With ID');
        $bar = $this->output->createProgressBar(Product::count());
        $bar->start();
        $products = Product::withTrashed()->get();
        foreach ($products as $product)
        {
            $oldSizes = $product->size;
            if ($oldSizes)
            {
                $oldSizes = explode(',', $oldSizes);

                $newSizes = [];
                foreach ($oldSizes as $oldSize)
                {
                    if ($oldSize != "")
                    {
                        $newSizes[] = Sizes::where('name', $oldSize)->first()->id ?? "---$oldSize---" . ',';
                    }
                }
                $product->update(['size' => implode(',', array_unique($newSizes))]);
            }
            $bar->advance();
        }
        $bar->finish();
    }


}
