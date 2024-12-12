<?php

namespace App\Modules\Customers\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomerImport implements ToCollection, WithStartRow
{
    /**
     * Method model
     *
     * @param array $row
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        repo('customers')->customersGroupExport($rows);
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
