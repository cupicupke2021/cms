<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use         DB;
class InventorySeedr extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
		$faker = Faker::create('id_ID');
		   	for($i = 1; $i <= 3000000; $i++){
			//id`, `pid`, `itm_code`, `itm_group`, `wh_loc`
    	    //insert data ke table pegawai menggunakan Faker
    		DB::table('trs_inventory_dtl')->insert([
    			'pid' => $faker->numberBetween(0,99999999),
    			'wh_loc' => $faker->numberBetween(0,99999999),
				'wh_status'=>$faker->randomElement(['OLD']),
				'storage_id'=>$faker->numberBetween(0,99999999),
				'asset_name'=>$faker->numberBetween(0,99999999)
    		]);
    	}
    }
}
