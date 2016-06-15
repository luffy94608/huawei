<?php

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeTableSeeder extends Seeder
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
                "name"  => "订车服务",
            ],
            [
                "name"  => "订餐服务",
            ],
        ];

        foreach ($datum as $data)
        {
            $model = new Type();
            $model->name = $data['name'];
            $model->save();
        }
    }
}
