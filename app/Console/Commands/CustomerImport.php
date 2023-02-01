<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customer from url csv file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Customer::truncate();
        $response = Http::withBasicAuth(config('loop.loop_user'),config('loop.loop_password'))->get('https://backend-developer.view.agentur-loop.com/customers.csv');
        if($response->ok()){
            $data = $this->csvStringToArray($response->body());
            unset($data[0]);
            $bar = $this->output->createProgressBar();
            $bar->start();
            $chunk_arrays = array_chunk($data,2000);
            foreach ($chunk_arrays as $customers) {
                $customer_data = [];
                foreach ($customers as $customer) {
                    try {
                        $customer_data[] = [
                            'id'=>$customer[0],
                            'job_title'=>$customer[1],
                            'email'=>$customer[2],
                            'name'=>$customer[3],
                            'registered_since'=>Carbon::createFromFormat('l,F d,Y',$customer[4]),
                            'phone'=>$customer[5],
                            'created_at'=>now(),
                            'updated_at'=>now()
                        ];
                    } catch (\Exception $e) {
                        Log::error('customer data error : '.$e->getMessage(),$customer);
                    }
                }
                try {
                    Customer::insert($customer_data);
                } catch (\Exception $e) {
                    Log::error('customer data database error : '.$e->getMessage());
                }
                $bar->advance(2000);
            }
            $bar->finish();
        }else{
            $this->info('something went wrong');
            Log::error('customer import error',['status'=>$response->status(),'message'=>$response->body()]);
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
