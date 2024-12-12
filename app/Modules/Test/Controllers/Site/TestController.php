<?php

namespace App\Modules\Test\Controllers\Site;

use Faker\Factory;
use IntlDateFormatter;
use Illuminate\Http\Request;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use App\Modules\Customers\Models\Customer;
use App\Modules\Categories\Models\Category;
use HZ\Illuminate\Mongez\Traits\RepositoryTrait;
use App\Modules\Newsletters\Services\Gateways\Sms;
use App\Modules\Services\Payments\Methods\HyperPay;

class TestController
{
    use RepositoryTrait;

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        // Mail::send([], [], function ($mailer) {
        //     $mailer->to('hassanzohdy@gmail.com')->subject('Welcome')->setBody('Great');
        // });


        // $fmt = new IntlDateFormatter(
        //     "ar_SA",
        //     IntlDateFormatter::FULL ,
        //     IntlDateFormatter::SHORT,
        // );

        // dd($fmt->format(0));

        // die('');

        // $test = App::make(Sms::class);

        // dd($test->to(Customer::all()->pluck('phoneNumber'), [
        //     'message' => 'title'
        // ]));
        // // $u = $this->usersGroupsRepository->create([
        // //     'name' => 'Super Admin',
        // //     'permissions' => [],
        // // ]);

        // // return $u;


        // // die('Done');
        // return User::get()->toArray();

        if (!$this->usersRepository->has('admin@rowaad.net', 'email')) {
            $user = $this->usersRepository->create([
                'name' => 'admin',
                'email' => 'admin@rowaad.net',
                'password' => '123123123',
                'group' => 1,
            ]);
        }

        die('OK');


        //     $faker = Factory::create();

        //     // print_r($faker->name);die;

        //    for ($i = 0; $i < 10; $i++) {
        //        $this->citiesRepository->create([
        //            'name' => $faker->city,
        //            'shippingFees' => 10,
        //            'published' => 1
        //        ]);

        //        $this->categoriesRepository->create([
        //            'name' => $faker->name,
        //            'published' => 1
        //        ]);
        //    }

        //    for ($i = 0; $i < 10; $i++) {
        //        $this->bannersRepository->create([
        //            'name' => $faker->name,
        //            'link' => $faker->url,
        //            'title' => $faker->name,
        //            'type' => '',
        //            'published' => 1,
        //        ]);


        //        $this->productsRepository->create([
        //            'name' => $faker->name,
        //            'description' => $faker->text,
        //            'shortDescription' => $faker->text,
        //            'price' => 100,
        //            'finalPrice' => 90,
        //            'published' => 1,
        //            'store' => Store::latest()->first()->id,
        //            'category' => Category::latest()->first()->id
        //        ]);
        //    }
    }

    public function testPayment(Request $request)
    {
        $client = $this->customersRepository->get((int) $request->client);

        $hyperPay = App::make(HyperPay::class);

        $type = $request->type ?? 'VISA';

        $checkOutId = $request->checkOutId ?? $hyperPay->initiate((int) $request->orderId, 1, $type, $client);

        return view('pay', compact('checkOutId', 'type'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function testPaymentConfirm(Request $request)
    {
//        $order = repo('orders')->getByCheckoutId($request->id);
//
//        $checkOut = repo('orders')->confirmPayment($order);
//
//        return response(collect($checkOut)->toJson(), 200);
    }
}
