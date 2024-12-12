<?php

namespace App\Modules\CustomerGroups\Controllers\Admin;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use App\Modules\Customers\Imports\CustomerImport;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CustomerGroupsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'customerGroups',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'conditionType' => 'required|in:totalOrders,totalPurchaseAmount',
                'specialDiscount' => 'required_without_all:freeShipping,freeExpressShipping',
                'freeShipping' => 'required_without_all:specialDiscount,freeExpressShipping',
                'freeExpressShipping' => 'required_without_all:freeShipping,specialDiscount',
            ],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Method getDownload
     *
     * @return void
     */
    public function getDownload()
    {
        $file = base_path() . "/CustomerGroupsExcels/CustomerGroups.xlsx";
        // dd($file);
        $headers = [
            'Content-Type: application/xlsx',
        ];

        return response()->download($file, 'CustomerGroups.xlsx', $headers);
    }

    /**
     * Method customersGroupExport
     *
     * @param Request $request
     *
     * @return void
     */
    public function customersGroupExport(Request $request)
    {

        $extensions = ["xls", "xlsx", "csv", "xlm", "xla", "xlc", "xlt", "xlw"];

        $fileExtension = $request->file('excelFile')->getClientOriginalExtension();

        if (in_array($fileExtension, $extensions)) {
            try {
                Excel::import(new CustomerImport, $request->file('excelFile'));

                return $this->success([
                    'success' => trans('general.Success Upload Excel File.'),
                ]);
            } catch (\Exception $exception) {
                return $this->badRequest($exception->getMessage());
            }
        } else {
            return $this->badRequest(trans('Please Upload Excel File.'));
        }
    }

    // protected function scan(Request $request)
    // {
    //     return Validator::make($request->all(), [
    //         'firstName' => 'required|min:2',
    //         'lastName' => 'required|min:2',
    //         'phoneNumber' => 'required',
    //         'password' => 'confirmed|min:6',
    //     ]);
    // }

    /**
     * It calls the `fix:transaction` command, which is defined in the
     * `App\Console\Commands\FixTransaction` class
     *
     * @return The return value of the Artisan::call() method.
     */
    public function fixTransaction()
    {
        Artisan::call("php artisan fix:transaction");
        // Artisan::call("php artisan fix:transaction --rollback");
        return 'done';
    }
}
