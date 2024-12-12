<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Http\Testing\File;

class AdminUpdateItemsTest extends AdminApiTestCase
{
    /**
     * Module route
     *
     * @var string
     */
    protected $route = '/items';

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
                [
                    'text' => 'Update Itemsaaa',
                    'localCode' => 'en',
                ],
                [
                    'text' => 'Update Items',
                    'localCode' => 'ar',
                ]
            ],
            'image' => File::create('img1.png', 100),
            'protein' => 390,
            'carbohydrates' => 650,
            'fat' => 24,
            'published' => '1',
            'categories' => '3',
            'sizes' => [
                '0' => '2',
            ],
            'restaurant' => '314',
        ];
    }

    /**
     * Set what response record is expected to be returned
     *
     * @return array
     */
    protected function recordShape(): array
    {
        return [];
    }



    /**
     * Method testSuccessUpdate
     * test Success Update
     * @return void
     */
    public function testSuccessUpdate()
    {
        $this->successUpdate(97, $this->fullData());
    }
}
