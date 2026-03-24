<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LeadsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $batchSize = 1000;
        $totalRecords = 10000;
        for ($i = 0; $i < $totalRecords / $batchSize; $i++) 
        {
            $leads = [];
            for ($j = 0; $j < $batchSize; $j++) 
            {
                $leads[] = [
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->numerify('9#########'),
                    'notes' => $faker->sentence,
                    'source' => $faker->randomElement(['Website', 'Referral', 'Ad']),
                    'campaign' => $faker->word,
                    'classification' => $faker->randomElement(['Hot', 'Warm', 'Cold']),
                    'field1' => $faker->word,
                    'field2' => $faker->word,
                    'field3' => $faker->word,
                    'field4' => $faker->word,
                    'status' => $faker->randomElement(['NEW LEAD']),
                    'unallocated_lead' => $faker->boolean,
                    'is_allocated' => $faker->boolean,
                    'lead_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'last_comment' => $faker->sentence,
                    'remind_date' => $faker->dateTimeBetween('now', '+1 month'),
                    'remind_time' => $faker->time('H:i:s'),
                    'user_id' => $faker->numberBetween(1, 10),
                    'updated_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'allocated_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'is_interested_allocated' => $faker->boolean,
                    'projects' => $faker->word,
                    'app_city' => $faker->city,
                    'app_contact' => $faker->phoneNumber,
                    'app_doa' => $faker->date(),
                    'app_dob' => $faker->date('Y-m-d', '-18 years'),
                    'app_name' => $faker->name,
                    'budget' => $faker->randomFloat(2, 1000, 50000),
                    'catg_id' => $faker->numberBetween(1, 20),
                    'conversion_type' => $faker->randomElement(['booked', 'completed', 'cancelled', null]),
                    'final_price' => $faker->randomFloat(2, 1000, 50000),
                    'project_id' => $faker->numberBetween(1, 50),
                    'size' => $faker->randomElement(['Small', 'Medium', 'Large']),
                    'sub_catg_id' => $faker->numberBetween(1, 100),
                    'type' => $faker->word,
                    'whatsapp_no' => $faker->numerify('9#########'),
                    'checklist_status' => $faker->randomElement(['open', 'close']),

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('leads')->insert($leads);
            $this->command->info("Inserted batch " . ($i + 1));
        }
    }
}
