<?php


namespace Tests\Feature;

use Illuminate\Http\Testing\File;

class AdminUpdateClubTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/clubs';

    /**
     * Set base full accurate data
     * This includes required and optional data
     *
     * @return array
     */
    protected function fullData(): array
    {
        return [
                'name' => [
                    0 => [
                        'text' => $this->faker->name,
                        'localeCode' => 'en'
                    ],
                    1 => [
                        'text' => $this->faker->name,
                        'localeCode' => 'ar'
                    ]
                ],
                'logo' => File::create('logo.png', 100),
                'aboutClub' => [
                    0 => [
                        'text' => $this->faker->text,
                        'localeCode' => 'en'
                    ],
                    1 => [
                        'text' => $this->faker->text,
                        'localeCode' => 'ar'
                    ]
                ],
                'address' => [
                        'lat' => $this->faker->latitude($min = -90, $max = 90),
                        'long' => $this->faker->longitude($min = -180, $max = 180),
                        'address' => $this->faker->address,
                ],
                'images' => [
                    0 => File::create('img1.png', 100),
                    1 => File::create('img2.png', 100)
                ],
                'published' => true,
                'branches' => [
                    0 => [
                        'name' => [
                            0 => [
                                'text' => $this->faker->name,
                                'localeCode' => 'en',
                            ],
                            1 => [
                                'text' => $this->faker->name,
                                'localeCode' => 'ar',
                            ]
                        ],
                        'address' => [
                            'lat' => $this->faker->latitude($min = -90, $max = 90),
                            'long' => $this->faker->longitude($min = -180, $max = 180),
                            'address' => $this->faker->address,
                        ]
                    ],
                    1 => [
                        'name' => [
                            0 => [
                                'text' => $this->faker->name,
                                'localeCode' => 'en',
                            ],
                            1 => [
                                'text' => $this->faker->name,
                                'localeCode' => 'ar',
                            ]
                        ],
                        'address' => [
                            'lat' => $this->faker->latitude($min = -90, $max = 90),
                            'long' => $this->faker->longitude($min = -180, $max = 180),
                            'address' => $this->faker->address,
                        ]
                    ]
                ],
                'workTimes' => [
                    0 => [
                        'day' => 'saturday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    1 => [
                        'day' => 'sunday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    2 => [
                        'day' => 'monday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    3 => [
                        'day' => 'tuesday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    4 => [
                        'day' => 'wednesday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    5 => [
                        'day' => 'thursday',
                        'available' => 'yes',
                        'open' => '8:00 am',
                        'close' => '10:00 pm'
                    ],
                    6 => [
                        'day' => 'friday',
                        'available' => 'no',
                    ],
                ],
                'packages' => [
                    0 => [
                        'name' => 'year',
                        'price' => '100'
                    ],
                    2 => [
                        'name' => '6 أشهر',
                        'price' => '50'
                    ],
                    3 => [
                        'name' => '3 أشهر',
                        'price' => '25'
                    ],
                    4 => [
                        'name' => 'شهر',
                        'price' => '15'
                    ],
                    ],
            ];
    }

    /**
     * Set what response record is expected to be returned
     *
     * @return array
     */
    protected function recordShape(): array
    {
        return [
            'name' => 'array',
            'aboutClub' => 'array',
            'address' => 'array',
            'packages' => 'array',
        ];
    }



    /**
     * Method testSuccessUpdate
     * test Success Update
     * @return void
     */
    public function _testSuccessUpdate()
    {
        $this->successUpdate(45, $this->fullData(), $this->recordShape());
    }

}
