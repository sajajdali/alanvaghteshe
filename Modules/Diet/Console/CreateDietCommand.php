<?php

namespace Modules\Diet\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Enum\FoodFactEnum;
use Modules\Diet\Enum\FoodTypeEnum;

class CreateDietCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diet:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create new diet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path('app/karafs/foods.json');
//        return $this->insertCategories();


        if (file_exists($filePath)) {
            // Read the content of the file
            $jsonContents = File::get($filePath);
            $decodedData = json_decode($jsonContents, true, 512, JSON_UNESCAPED_UNICODE);
            foreach ($decodedData['result'] as $item) {
                $foodFactKarafs = $item['foodFact'];
                $primaryFoodUnit = $item['primaryFoodUnit'];
                $basicFood = new BasicFood();
                $basicFood->name = $item['name'];
                $basicFood->type = FoodTypeEnum::SIMPLE;
                $basicFood->active = true;
                $foodFact = [];

                foreach ($this->removeAmount($foodFactKarafs) as $factKey => $factValue) {
                    if (FoodFactEnum::tryFrom($factKey)) {
                        $foodFact[FoodFactEnum::tryFrom($factKey)->name] = (string) round($factValue , 2);
                    }
                }

                $basicFood->detail = [
                    BasicFood::JSON_DETAIL_KEY_KARAFS => $this->castArrayValuesToString($item)
                ];
                $basicFood->food_fact = $foodFact;

                $basicFood->save();
                // food categories
                if (!empty($item['categories'])) {
                    $categoriesList = [];

                    foreach ($item['categories'] as $category) {
                        $categoriesList[] = $this->getCategoriesId($category);
                    }

                    if (!empty($categoriesList)) {
                        $basicFood->category()->attach($categoriesList);
                    }
                }

                // food units
                if (!empty($item['foodUnitRatioArray'])) {
                    $foodUnitData = [];

                    foreach ($item['foodUnitRatioArray'] as $foodUnitKarafs) {
                        $ratio = $foodUnitKarafs['ratio'];
                        $unitId = $this->getFoodUnitId($foodUnitKarafs['unitId']);

                        $foodUnitData[$unitId] = [
                            'quantity_per_unit' => $ratio ?? null,
                            'is_primary' => $foodUnitKarafs['unitId'] == $primaryFoodUnit ? 1 : 0,
                        ];
                    }

                    if (!empty($foodUnitData)) {
                        $basicFood->units()->sync($foodUnitData, false);
                    }
                }
            }

        } else {
            return $this->info('not found');

        }
    }

    private function removeAmount($inputArray)
    {
        // Remove "Amount" from keys
        $modifiedArray = array_map(function ($key) {
            // Remove "Amount" from the end of the key
            return str_replace('Amount', '', $key);
        }, array_keys($inputArray));

// Combine the modified keys with the original values
        return array_combine($modifiedArray, $inputArray);
    }

    public function castArrayValuesToString($dataArray)
    {
        return $this->castValuesToString($dataArray);
    }

    // Example of using the castValuesToString function

    private function castValuesToString($array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->castValuesToString($value); // Recursive call for nested arrays
            } elseif (is_int($value) || is_float($value)) {
                if (is_float($value)){
                    $value = round($value, 2);
                }
                $value = (string) $value;
            }
        }

        return $array;
    }

    private function getCategoriesId($uId): ?int
    {
        $categories = [
            'f6633000-cef4-4c30-84d3-f430ab4f1911' => 1,
            'a7cf1872-41e3-4d2b-8814-1e6dc7e7142c' => 2,
            '64fbf2de-3376-4aa0-9cd0-149c1f9664a9' => 3,
            '69563bf4-e27d-456f-97f9-37b99eb0ec29' => 4,
            '88ac8115-a091-4b41-9e90-9131e0aefb88' => 5,
            '40cdc1a2-3984-4933-af3c-afc488484cbe' => 6,
            '03e6db21-85c4-45ea-864f-ec015ebda6ca' => 7,
            '2adf7f98-3c7d-4422-b521-909bc0924373' => 8,
            '053260dc-4c99-40b3-a570-a1664c59c87e' => 9,
            '964c1f2b-c02f-4763-844a-aee420c0e4b9' => 10,
            '81fc1dca-b311-493f-9623-ee5321091d2c' => 11,
            'c9c85fd5-a47a-476d-a87b-dd5de2a608ca' => 12,
            'a07a36e3-f982-4ea4-94be-924d890622cb' => 13,
            'ebbb0a06-bd67-4a89-9c33-ba9f3e212bed' => 14,
            'ffa9aaa7-9ebb-4367-a060-f2eee0e2c341' => 15,
            'be4fd418-d93c-425b-9808-9a7c929d8c50' => 16,
            'ae5fab9a-4324-447d-8b2e-c407bc21588f' => 17,
            '1d16b638-a49f-4b38-b069-554843039d81' => 18,
        ];
        if (array_key_exists($uId, $categories)) {
            return $categories[$uId];
        } else {
            return null;
        }

    }

    private function getFoodUnitId($uuid): ?int
    {
        $categories = [
            "5e1c58cf883a966e03fb452c" => 1,
            "5e1c58e5883a966e03fb452d" => 2,
            "5e1c595d883a966e03fb4530" => 3,
            "5e1c6558883a966e03fb457e" => 4,
            "5e244b2a6bb7ed3b5baaccfa" => 5,
            "5e244b3f6bb7ed3b5baaccfb" => 6,
            "5e244b5b6bb7ed3b5baaccfc" => 7,
            "5e244b6c6bb7ed3b5baaccfd" => 8,
            "5e244b7c6bb7ed3b5baaccfe" => 9,
            "5e244bbe6bb7ed3b5baaccff" => 10,
            "5e244bdb6bb7ed3b5baacd00" => 11,
            "5e244bec6bb7ed3b5baacd01" => 12,
            "5e244bfe6bb7ed3b5baacd02" => 13,
            "5e244c096bb7ed3b5baacd03" => 14,
            "5e244c1a6bb7ed3b5baacd04" => 15,
            "5e244c2f6bb7ed3b5baacd05" => 16,
            "5e244c3d6bb7ed3b5baacd06" => 17,
            "5e244c496bb7ed3b5baacd07" => 18,
            "5e244d006bb7ed3b5baacd08" => 19,
            "5e244d096bb7ed3b5baacd09" => 20,
            "5e244d1c6bb7ed3b5baacd0a" => 21,
            "5e244d2b6bb7ed3b5baacd0b" => 22,
            "5e244d576bb7ed3b5baacd0c" => 23,
            "5e244d6a6bb7ed3b5baacd0d" => 24,
            "5e244d836bb7ed3b5baacd0e" => 25,
            "5e244d8b6bb7ed3b5baacd0f" => 26,
            "5e244d966bb7ed3b5baacd10" => 27,
            "5fd8db75fbaf127e6f3954d8" => 28,
            "5ff08371ce0ed47060b20f8c" => 29,
            "6145cb4a463d66579e26d87e" => 30,
            "61fac39caf39461e80d458cc" => 31,
            "61fac39caf39461e80d458cb" => 32,
            "61fac39caf39461e80d458cd" => 33,
            "61fac39caf39461e80d458ce" => 34,
            "61fac39caf39461e80d458cf" => 35,
            "61fac39caf39461e80d458d0" => 36,
            "64ec49fb902955ca909320f0" => 37,
            "65d1bca46ad9f8955af941ff" => 38,
        ];
        if (array_key_exists($uuid, $categories)) {
            return $categories[$uuid];
        } else {
            return null;
        }
    }

    private function insertCategories()
    {
        foreach ($this->categories() as $categoryUid => $category) {
            \DB::table('food_categories')->insert([
                'uuid' => $categoryUid,
                'title' => $category,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }

    private function categories()
    {
        $categories = [
            'f6633000-cef4-4c30-84d3-f430ab4f1911' => 'ادویه جات',
            'a7cf1872-41e3-4d2b-8814-1e6dc7e7142c' => 'پلوها',
            '64fbf2de-3376-4aa0-9cd0-149c1f9664a9' => 'تنقلات',
            '69563bf4-e27d-456f-97f9-37b99eb0ec29' => 'چربی ها',
            '88ac8115-a091-4b41-9e90-9131e0aefb88' => 'حبوبات',
            '40cdc1a2-3984-4933-af3c-afc488484cbe' => 'خورشت ها',
            '03e6db21-85c4-45ea-864f-ec015ebda6ca' => 'سالاد ها',
            '2adf7f98-3c7d-4422-b521-909bc0924373' => 'ساندویچ ها',
            '053260dc-4c99-40b3-a570-a1664c59c87e' => 'سبزیجات',
            '964c1f2b-c02f-4763-844a-aee420c0e4b9' => 'شیرینی ها',
            '81fc1dca-b311-493f-9623-ee5321091d2c' => 'فست فود',
            'c9c85fd5-a47a-476d-a87b-dd5de2a608ca' => 'گوشت ها',
            'a07a36e3-f982-4ea4-94be-924d890622cb' => 'لبنیات',
            'ebbb0a06-bd67-4a89-9c33-ba9f3e212bed' => 'متفرقه',
            'ffa9aaa7-9ebb-4367-a060-f2eee0e2c341' => 'میوه ها',
            'be4fd418-d93c-425b-9808-9a7c929d8c50' => 'مغز ها ',
            'ae5fab9a-4324-447d-8b2e-c407bc21588f' => 'نان و غلات',
            '1d16b638-a49f-4b38-b069-554843039d81' => 'نوشیدنی ها',
        ];
        return $categories;
    }

    private function makeUnitFile()
    {
        $foodUnits = FoodUnit::all();
        // Create an array with id as key and _id as value
        $dataArray = [];
        foreach ($foodUnits as $unit) {
            $dataArray [$unit->detail['_id']] = $unit->id;
        }

// Convert the array to a JSON string
        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT);

// Create the txt file in the storage directory
        $fileContents = sprintf("units.txt\n%s", $jsonData);
        $filePath = storage_path('app/units.txt');

        File::put($filePath, $fileContents);
    }

    private function insertFoodUnits()
    {
        $foodUnitsKarafs = $this->foodUnitsKarafs();
        foreach ($foodUnitsKarafs as $unit) {
            FoodUnit::create([
                'name' => $unit['name'],
                'detail' => $unit,
            ]);
        }

    }

    private function foodUnitsKarafs()
    {
        return json_decode('[
        {
            "_id": "5e1c58cf883a966e03fb452c",
            "name": "بشقاب",
            "deleted": false,
            "createdAt": "2020-01-13T11:47:27.029Z",
            "updatedAt": "2022-05-30T08:47:08.217Z",
            "__v": 0,
            "score": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1653900428217
        },
        {
            "_id": "5e1c58e5883a966e03fb452d",
            "name": "قاشق غذاخوری",
            "deleted": false,
            "createdAt": "2020-01-13T11:47:49.031Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:53.089Z",
            "score": 47096,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e1c595d883a966e03fb4530",
            "name": "گرم",
            "deleted": false,
            "createdAt": "2020-01-13T11:49:49.331Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:52.029Z",
            "score": 75767,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e1c6558883a966e03fb457e",
            "name": "لیوان",
            "deleted": false,
            "createdAt": "2020-01-13T12:40:56.820Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:00.381Z",
            "score": 33929,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244b2a6bb7ed3b5baaccfa",
            "name": "عدد",
            "deleted": false,
            "createdAt": "2020-01-19T12:27:22.236Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:53.391Z",
            "score": 46335,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244b3f6bb7ed3b5baaccfb",
            "name": "قاشق چایخوری",
            "deleted": false,
            "createdAt": "2020-01-19T12:27:43.765Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:00.964Z",
            "score": 2314,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244b5b6bb7ed3b5baaccfc",
            "name": "عدد کوچک",
            "deleted": false,
            "createdAt": "2020-01-19T12:28:11.082Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:54.743Z",
            "score": 6320,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244b6c6bb7ed3b5baaccfd",
            "name": "برش",
            "deleted": false,
            "createdAt": "2020-01-19T12:28:28.396Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:05.992Z",
            "score": 3701,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244b7c6bb7ed3b5baaccfe",
            "name": "برش مثلثی",
            "deleted": false,
            "createdAt": "2020-01-19T12:28:44.316Z",
            "updatedAt": "2020-01-19T12:28:44.316Z",
            "__v": 0,
            "score": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1579436924316
        },
        {
            "_id": "5e244bbe6bb7ed3b5baaccff",
            "name": "قاشق مرباخوری",
            "deleted": false,
            "createdAt": "2020-01-19T12:29:50.061Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:17.403Z",
            "score": 2260,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244bdb6bb7ed3b5baacd00",
            "name": "عدد متوسط",
            "deleted": false,
            "createdAt": "2020-01-19T12:30:19.539Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:52.058Z",
            "score": 23431,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244bec6bb7ed3b5baacd01",
            "name": "لیوان خرد شده",
            "deleted": false,
            "createdAt": "2020-01-19T12:30:36.529Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:53.671Z",
            "score": 1732,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244bfe6bb7ed3b5baacd02",
            "name": "کف دست",
            "deleted": false,
            "createdAt": "2020-01-19T12:30:54.864Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:55.489Z",
            "score": 19000,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244c096bb7ed3b5baacd03",
            "name": "پرس",
            "deleted": false,
            "createdAt": "2020-01-19T12:31:05.330Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:47:00.781Z",
            "score": 97,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244c1a6bb7ed3b5baacd04",
            "name": "قاشق غذاخوری خرد شده",
            "deleted": false,
            "createdAt": "2020-01-19T12:31:22.094Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:47:32.067Z",
            "score": 127,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244c2f6bb7ed3b5baacd05",
            "name": "حلقه متوسط",
            "deleted": false,
            "createdAt": "2020-01-19T12:31:43.047Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T13:25:01.425Z",
            "score": 3,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244c3d6bb7ed3b5baacd06",
            "name": "عدد بزرگ",
            "deleted": false,
            "createdAt": "2020-01-19T12:31:57.453Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:47:33.746Z",
            "score": 102,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244c496bb7ed3b5baacd07",
            "name": "خلال متوسط",
            "deleted": false,
            "createdAt": "2020-01-19T12:32:09.616Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:00.248Z",
            "score": 570,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d006bb7ed3b5baacd08",
            "name": "حبه",
            "deleted": false,
            "createdAt": "2020-01-19T12:35:12.915Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:46:14.919Z",
            "score": 422,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d096bb7ed3b5baacd09",
            "name": "برگ",
            "deleted": false,
            "createdAt": "2020-01-19T12:35:21.866Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:44:54.252Z",
            "score": 816,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d1c6bb7ed3b5baacd0a",
            "name": "بشقاب متوسط",
            "deleted": false,
            "createdAt": "2020-01-19T12:35:40.575Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T11:50:04.996Z",
            "score": 27,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d2b6bb7ed3b5baacd0b",
            "name": "ساقه",
            "deleted": false,
            "createdAt": "2020-01-19T12:35:55.630Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T11:01:13.799Z",
            "score": 30,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d576bb7ed3b5baacd0c",
            "name": "برش متوسط",
            "deleted": false,
            "createdAt": "2020-01-19T12:36:39.827Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:46:12.565Z",
            "score": 441,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d6a6bb7ed3b5baacd0d",
            "name": "فیله",
            "deleted": false,
            "createdAt": "2020-01-19T12:36:58.549Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:55.967Z",
            "score": 366,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d836bb7ed3b5baacd0e",
            "name": "بشقاب کوچک",
            "deleted": false,
            "createdAt": "2020-01-19T12:37:23.021Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:48:51.697Z",
            "score": 152,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d8b6bb7ed3b5baacd0f",
            "name": "قوطی کبریت",
            "deleted": false,
            "createdAt": "2020-01-19T12:37:31.572Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:43:04.722Z",
            "score": 5655,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5e244d966bb7ed3b5baacd10",
            "name": "سیخ",
            "deleted": false,
            "createdAt": "2020-01-19T12:37:42.870Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T10:42:55.307Z",
            "score": 1274,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5fd8db75fbaf127e6f3954d8",
            "name": "فنجان",
            "deleted": false,
            "createdAt": "2020-12-15T15:51:17.279Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T13:10:05.571Z",
            "score": 7,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "5ff08371ce0ed47060b20f8c",
            "name": "یک دست",
            "deleted": false,
            "createdAt": "2021-01-02T14:30:09.965Z",
            "updatedAt": "2022-02-02T17:47:08.277Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2021-04-03T12:28:16.627Z",
            "score": 10,
            "timestampUpdatedAt": 1643824028277
        },
        {
            "_id": "6145cb4a463d66579e26d87e",
            "name": "اسکوپ",
            "deleted": false,
            "createdAt": "2021-09-18T11:19:38.328Z",
            "updatedAt": "2021-09-18T11:19:38.328Z",
            "__v": 0,
            "score": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1631963978328
        },
        {
            "_id": "61fac39caf39461e80d458cc",
            "name": "بسته",
            "deleted": false,
            "score": 0,
            "createdAt": "2022-02-02T17:47:08.248Z",
            "updatedAt": "2022-02-02T17:47:08.248Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1643824028248
        },
        {
            "_id": "61fac39caf39461e80d458cb",
            "name": "استکان",
            "deleted": false,
            "score": 0,
            "createdAt": "2022-02-02T17:47:08.248Z",
            "updatedAt": "2022-02-02T17:47:08.248Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1643824028248
        },
        {
            "_id": "61fac39caf39461e80d458cd",
            "name": "یک عدد ۲۰۰ گرمی",
            "deleted": false,
            "score": 0,
            "createdAt": "2022-02-02T17:47:08.249Z",
            "updatedAt": "2022-02-02T17:47:08.249Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1643824028249
        },
        {
            "_id": "61fac39caf39461e80d458ce",
            "name": "یک سیخ (۱۵۰ گرمی)",
            "deleted": false,
            "score": 66,
            "lastScoreUpdatedAt": "2021-04-03T10:59:17.853Z",
            "createdAt": "2022-02-02T17:47:08.249Z",
            "updatedAt": "2022-02-02T17:47:08.249Z",
            "__v": 0,
            "timestampUpdatedAt": 1643824028249
        },
        {
            "_id": "61fac39caf39461e80d458cf",
            "name": "یک سیخ (۲۰۰ گرمی)",
            "deleted": false,
            "score": 0,
            "createdAt": "2022-02-02T17:47:08.249Z",
            "updatedAt": "2022-02-02T17:47:08.249Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1643824028249
        },
        {
            "_id": "61fac39caf39461e80d458d0",
            "name": "تکه‌ی ۱۰*۱۰",
            "deleted": false,
            "score": 0,
            "createdAt": "2022-02-02T17:47:08.249Z",
            "updatedAt": "2022-02-02T17:47:08.249Z",
            "__v": 0,
            "lastScoreUpdatedAt": "2022-05-09T12:10:13.855Z",
            "timestampUpdatedAt": 1643824028249
        },
        {
            "_id": "64ec49fb902955ca909320f0",
            "name": "ساشه",
            "deleted": false,
            "score": 0,
            "lastScoreUpdatedAt": "2023-07-09T10:21:01.462Z",
            "createdAt": "2023-08-28T07:17:15.012Z",
            "updatedAt": "2023-08-28T07:17:15.012Z",
            "__v": 0,
            "timestampUpdatedAt": 1693207035012
        },
        {
            "_id": "65d1bca46ad9f8955af941ff",
            "name": "کفگیر",
            "deleted": false,
            "score": 0,
            "lastScoreUpdatedAt": "2024-02-17T19:45:10.113Z",
            "createdAt": "2024-02-18T08:15:32.133Z",
            "updatedAt": "2024-02-18T08:15:32.133Z",
            "__v": 0,
            "timestampUpdatedAt": 1708244132133
        }
    ]', JSON_UNESCAPED_UNICODE);
    }

}
