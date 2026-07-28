<?php

namespace Modules\Diet\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Modules\Diet\Entities\RecipeKarafs;

class ConvertRecipeKarafsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diet:convert_recipe';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $listFiles = $this->listRecipeFile();
        $url = 'https://v2.karafsapp.com/api/recipe/';
        $authorizationHeader = 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiQUNDRVNTX1RPS0VOIiwidXNlciI6eyJvYmplY3RJZCI6ImM1NDg4MzM0LWM4YWUtNDIxYy1hZjgyLWM4NDJhMmY3ZjcxOCIsImthcmFmc1NpZ251cENvbXBsZXRlZCI6dHJ1ZSwiaXNNaWdyYXRlZCI6dHJ1ZSwicGhvbmVOdW1iZXIiOiIwOTE5NzcyOTEwMSIsIm5hbWUiOiLYs9in2YXYp9mG27EiLCJyZWdpc3RlcmVkRGF0ZSI6MTcwNzgxNjU2NDc5OX0sImlhdCI6MTcxNzA0MzM2MywiZXhwIjoxNzE3MzAyNTYzfQ.p0BqfE6raiWWzO5Y49eGCPPCovz5gSevZUOvwRaJhqU';


        $recipeList = [];
        foreach ($listFiles as $key => $id){
            if ($key <= 140 || $key > 180) {
                continue;
            }
            $response = Http::withHeaders([
                'Authorization' => $authorizationHeader
            ])->get($url . $id);
            $result = json_decode($response->body());
            $recipeList[] = $result->result;
            $karafsRecipe = new RecipeKarafs();
            $karafsRecipe->recipe = $result->result;
            $karafsRecipe->uuid = $id;
            $karafsRecipe->save();


        }
        $storagePath = storage_path('app');
        $jsonFilePath = $storagePath . '/recipe_new.txt';

        File::put($jsonFilePath, json_encode( $this->castValuesToString($recipeList) , JSON_UNESCAPED_UNICODE) );

    }

    private function listRecipeFile()
    {

        $jsonString = [
            0 => "5fb3b00e6583de6aedcf58bd",
            1 => "5fc25357601ebb01502c10bc",
            2 => "5f7ed94bf915d464063b16d3",
            3 => "5f85af843e75122da054ee6e",
            4 => "5fafc4ce8c55687aeec9a8f9",
            5 => "5fb006f87a7794322cdb6b1a",
            6 => "5fb0f2b37a7794322cdc340d",
            7 => "5fb0f43b111c3331bcb0b15a",
            8 => "5fb10e2245861f1adb71fa2b",
            9 => "5fb14527662f281abb181ca7",
            10 => "5fb22873662f281abb191965",
            11 => "5fb2e31b452f9d6b4162a3f9",
            12 => "5fccf9306ad0570e94213173",
            13 => "5fd50ef63fb30a412a4fd95b",
            14 => "5f7ed4a39cc4396420449c6a",
            15 => "5fb017abf4baf43233e7d82f",
            16 => "5fb22cc0662f281abb192ecd",
            17 => "5f7dbcf24630ef6df7dc4759",
            18 => "5f8222f5cc750263d8d75373",
            19 => "5fb10ee445861f1adb71fa79",
            20 => "5fb16645e757861afedc4593",
            21 => "5fb18b06e757861afedc9173",
            22 => "5fbc145d95145531163460d2",
            23 => "5fccf9fa3e472b0eb2076693",
            24 => "5fcf6c18d4a74a4b51a787ba",
            25 => "5fd0dd9c2113fa376793e795",
            26 => "5fd513fde4bce64147ad5049",
            27 => "5fdb42f4dc54593716cf33df",
            28 => "5f7c6c7332e83d464a96cab5",
            29 => "5f7db8ec0197cd6dd5eebb1f",
            30 => "5f8595763e75122da054a3d7",
            31 => "5f85ee971cee6f2deaa325ea",
            32 => "5f85f80a9d06592e0f3d6da7",
            33 => "5fb14f1545861f1adb7253ed",
            34 => "5fb24da3a7f2c36ad2ec01ed",
            35 => "5fbaa501c89f195892466a89",
            36 => "5fc37ea130624b57f4982ea7",
            37 => "5fd0d5cf203ab436ef17121d",
            38 => "5fd0e3b8203ab436ef1721cf",
            39 => "5fd8b13618888e78886773d0",
            40 => "5fda13780c23844e7b030a34",
            41 => "5fe71679c94ea038f915f4e0",
            42 => "5fe719d7c94ea038f915fa01",
            43 => "5fe7963da70b7638b6017bcb",
            44 => "5f7dc1fbce36176ddb0a2000",
            45 => "5f81c515f915d464063d81d4",
            46 => "5f81d3e1cc750263d8d6e3f7",
            47 => "5f820a62f915d464063dd922",
            48 => "5f8311e2cc750263d8d7f75a",
            49 => "5f832ad5dbd8226400565e24",
            50 => "5f8336c89cc4396420484af1",
            51 => "5f833957cc750263d8d81d06",
            52 => "5f86df272a830f2db85cc7a8",
            53 => "5f88529cb311031b5581a788",
            54 => "5f885b8a49b90f1b3ba8eebe",
            55 => "5f88bcdfcc583f1b8a0d8fd2",
            56 => "5fb1114965f8781b28a891a4",
            57 => "5fb12b925142261b2f695fff",
            58 => "5fb152753b09461ad5e0bbbe",
            59 => "5fb24e299971d56b3b50548a",
            60 => "5fb3ade426427a6b0fd50e5b",
            61 => "5fba482ddd138a7447871674",
            62 => "5fba4f53eca618741ba2bbdd",
            63 => "5fbaa410dc5bec5869aff29d",
            64 => "5fbd1d5a9514553116352028",
            65 => "5fbd1f8f0ad894313585e9d8",
            66 => "5fbd4b7fa8260b30bb10330d",
            67 => "5fbfa44aa5d5c51ff63e0fe9",
            68 => "5fbfa709df36f21f99133e5d",
            69 => "5fc173ff388abf1ff5f1aaa1",
            70 => "5fc257078e435f13b6c94042",
            71 => "5fc259fb4b05b91406e356c1",
            72 => "5fc25bdcf781e21b077a9a84",
            73 => "5fc271f65041972f98ab5381",
            74 => "5fc37c498ab997579a13dab9",
            75 => "5fc3aeec032a3b5797b98021",
            76 => "5fcca207e7724b20723c6e18",
            77 => "5fccd81b3e472b0eb2072dc9",
            78 => "5fcd306f19f8b20e9bededd2",
            79 => "5fcd3338cc80b40e73e9e997",
            80 => "5fcd36ab19f8b20e9bedfb42",
            81 => "5fcd3df86ad0570e94217d8b",
            82 => "5fd0d8d0203ab436ef171633",
            83 => "5fd0dc623eed26370d6a332a",
            84 => "5fd9cf7341f0757e29a83ccb",
            85 => "5fda18588a56324f4ed6739b",
            86 => "5fda208b8a56324f4ed684f0",
            87 => "5fe0908b15939556d7e15d43",
            88 => "5fe5e39b7b66f1387a39c454",
            89 => "5fe6fa1af5f1e83900a65781",
            90 => "5fe702b15b5d0a38806b065f",
            91 => "6002f0a92339e730b466da47",
            92 => "5f8160e83b39466419432a0d",
            93 => "5f818e853bfd6963c54a6d9c",
            94 => "5f81bfa3cc750263d8d6bee9",
            95 => "5f8458db04b3ec7f74df8b12",
            96 => "5f859c3093799f2e09317951",
            97 => "5f85a4173adace2da6265f5c",
            98 => "5f85b4016ff7392dbe59a557",
            99 => "5f85f1831cee6f2deaa32d62",
            100 => "5f85fbba2a830f2db85bcb71",
            101 => "5f86e6f06ff7392dbe5afbc9",
            102 => "5f8828079ea5281b4f91fa77",
            103 => "5f8b051dc0bfcd1ba90634f1",
            104 => "5f8c6dbf662020055dcce342",
            105 => "5f8c740265eb4305faa514c8",
            106 => "5f8db13d51256660a03f5d78",
            107 => "5f901fdafe64da5a5bd39f6d",
            108 => "5fb162e245861f1adb7281d4",
            109 => "5fb1824365f8781b28a9416a",
            110 => "5fb185c596f3061ac1b73a22",
            111 => "5fcd28dccc80b40e73e9da05",
            112 => "5fcf7196ea6caa4ba288ef16",
            113 => "5fcf7237b3fea94b8325ff80",
            114 => "5fd74d7961e47d764f5f4ab9",
            115 => "5fd7537b8c507276c4e9a148",
            116 => "5fe0a38190fc6d5762ae480d",
            117 => "5fe1e53b6f13431b30e551bb",
            118 => "5f88c20b63a2871b8485c9e2",
            119 => "5f90453f87f94f5ab51a1f59",
            120 => "5fafc3a91629117b4858dad5",
            121 => "5fb1064545aec3320f62de1e",
            122 => "5fcf72a4e648464b7c92ab7f",
            123 => "5fd8b6db18888e7888677981",
            124 => "5fe1e59e1833041aa8be4907",
            125 => "5fe7955f5b5d0a38806befce",
            126 => "6002e6dc29900130d473f430",
            127 => "5f7c6d3332e83d464a96cac8",
            128 => "5f7c9b7cdd25ab6dbda747ec",
            129 => "5f7ca2eb9534dc6debda61ab",
            130 => "5f7caa5cce36176ddb09265d",
            131 => "5f7caea2ce36176ddb092c45",
            132 => "5f7da299dd25ab6dbda81800",
            133 => "5f7da63b9534dc6debdb2057",
            134 => "5f815cd2f915d464063cc8df",
            135 => "5f817f93f915d464063d1734",
            136 => "5f833d3e1f74ca63bf43be95",
            137 => "5f845588f758147f995efdc6",
            138 => "5f86d0d16ff7392dbe5abcc1",
            139 => "5f8729c21cd1b91b354fb82c",
            140 => "5f88c4f4c0bfcd1ba904533a",
            141 => "5f8c41f02c65f40583d8293e",
            142 => "5f8c456dc5b9e905940dfe61",
            143 => "5fb1256965f8781b28a8adeb",
            144 => "5fb179773b09461ad5e10778",
            145 => "5fb17d0c65f8781b28a93614",
            146 => "5fb193195142261b2f6a1dbc",
            147 => "5f82028c4a81f363dea10c47",
            148 => "5f8580cf3e75122da054517c",
            149 => "5f8da889b2c2f660bb8ac1d4",
            150 => "5fb12821e9da571b05a9fec1",
            151 => "5fbcf20e0ad894313585a4e1",
            152 => "5fbfae6da5d5c51ff63e18ce",
            153 => "5fbfb0835a07ec1fd3d76b63",
            154 => "5fce743c138dec53ddc49e9c",
            155 => "5fd9bccac0c08c7eb696e190",
            156 => "5fe791de5b5d0a38806be7bd",
            157 => "5fe2f8af65183c1aaffd8e31",
            158 => "5f7c823fce36176ddb08d58e",
            159 => "5fe2fabf2725b11b295a446b"
        ];
        return $jsonString;

    }

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

    private function extractIds($dataArray)
    {
        $ids = [];

        foreach ($dataArray as $item) {
            if (isset($item['_id'])) {
                $ids[] = $item['_id'];
            }
        }

        return $ids;
    }

}
