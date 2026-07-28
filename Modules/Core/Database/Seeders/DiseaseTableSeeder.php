<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DiseaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $diseaseList = [
                "فشار خون",
                "دیابت",
                "افسردگی",
                "کبدچرب",
                "سکته مغزی",
                "سکته قلبی",
                "آرتروز",
                "سیاتیک",
        ];

        foreach ($diseaseList as $index => $disease) {
            \Modules\Core\Entities\Disease::create([
                'name' => $disease,
                'status' => true,
                'priority' => ($index+1)*10,
            ]);
        }
    }
}
