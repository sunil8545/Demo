<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import product from url csv file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Product::truncate();
        $response = Http::withBasicAuth(config('loop.loop_user'),config('loop.loop_password'))->get('https://backend-developer.view.agentur-loop.com/products.csv');
        if($response->ok()){
            $data = $this->csvStringToArray($response->body());
            unset($data[0]);
            $bar = $this->output->createProgressBar();
            $bar->start();
            $chunk_arrays = array_chunk($data,2000);
            foreach ($chunk_arrays as $products) {
                $product_data = [];
                foreach ($products as $product) {
                    $product_data[] = [
                        'id'=>$product[0],
                        'name'=>$product[1],
                        'price'=>$product[2],
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ];
                }
                try {
                    Product::insert($product_data);
                } catch (\Exception $e) {
                    Log::error('product data database error : '.$e->getMessage());
                }
                $bar->advance(2000);
            }
            $bar->finish();
        }else{
            $this->info('something went wrong');
            Log::error('product import error',['status'=>$response->status(),'message'=>$response->body()]);
        }

    }

    protected function csvStringToArray($string):array
    {
        $array = [];
        $lines = explode(PHP_EOL,$string);
        foreach($lines as $line){
            $array[] = str_getcsv($line);
        }
        return $array;
    }
}
