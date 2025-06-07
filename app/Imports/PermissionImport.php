<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class PermissionImport implements ToModel
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Permission([
           'name'     => $row[0],
           'guard_name'    => $row[1], 
           'group_name' => $row[2],
        ]);
    }
}
