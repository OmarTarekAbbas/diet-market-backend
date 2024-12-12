<?php

namespace App\Modules\General\Controllers\Site;

use HZ\Illuminate\Mongez\Managers\ApiController;

class PrivacyPolicyController extends ApiController
{
    /**
     * Get Home Data
     *
     * @return Response
     */
    public function index()
    {
        return $this->success([
            'record' => $this->pagesRepository->getContentByName('privacy-policy'),
        ]);

        // return $this->success([
        //     'records' => '
        //     <h1>سياسة الاستخدام</h1>
        //     <div style="color: red">
        //         <ul>
        //             <li>ايتم 1</li>
        //             <li>ايتم 2</li>
        //             <li>ايتم 3</li>
        //             <li>ايتم 4</li>
        //             <li>ايتم 5</li>
        //         </ul>
        //     </div>
        //     <p>سياسة استخدام  المطعم</p>
        //     ',
        // ]);

        // return $this->success([
        //     'record' => $this->pagesRepository->getContentByName('privacy-policy'),
        // ]);
    }
}
