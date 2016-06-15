<?php

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datum = [
            [
                "name"  => "1号楼4层",
            ],
            [
                "name"  => "2号楼三层",
            ],
        ];

        foreach ($datum as $data)
        {
            $model = new Area();
            $model->name = $data['name'];
            $model->save();
        }
    }
}
