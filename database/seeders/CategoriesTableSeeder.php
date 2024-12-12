<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('categories')->delete();

        \DB::table('categories')->insert(array(
            0 =>
            array(
                'name' => [
                    [
                        'text' => 'omar',
                        'localCode' => 'en',
                    ],
                    [
                        'text' => 'omar',
                        'localCode' => 'ar',
                    ]
                ],
                'description' => [
                    [
                        'text' => 'omar',
                        'localCode' => 'en',
                    ],
                    [
                        'text' => 'omar',
                        'localCode' => 'ar',
                    ]
                ],                'type' => 'food',
                'color' => '#12574',
                'image' => 'data/categories/105/0-101.png',
                'published' => true,
                'restaurant' => 388,
                'slug' => [
                    [
                        'text' => '105/khdar2',
                        'localCode' => 'en',
                    ],
                    [
                        'text' => '105/khdar2',
                        'localCode' => 'ar',
                    ]
                ],
            ),
        ));
    }
}
