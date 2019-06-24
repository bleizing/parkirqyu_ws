<?php

use Illuminate\Database\Seeder;

use App\ParkirRate;

class ParkirRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parkir_rate_mobil = ParkirRate::create([
            'satu_jam_pertama' => 5000,
            'tiap_jam' => 2000,
            'per_hari' => 50000,
            'parkir_type' => 1
        ]);

    	$parkir_rate_motor = ParkirRate::create([
    		'satu_jam_pertama' => 2000,
    		'tiap_jam' => 1000,
    		'per_hari' => 20000,
    		'parkir_type' => 2
    	]);
    }
}
