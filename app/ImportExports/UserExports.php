<?php
namespace App\ImportExports;

use Maatwebsite\Excel\Concerns\FromCollection;

class UserExports implements FromCollection
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }
    public function collection()
    {
        return $this->users;
    }
}
?>