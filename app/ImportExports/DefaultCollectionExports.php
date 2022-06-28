<?php
namespace App\ImportExports;

use Maatwebsite\Excel\Concerns\FromCollection;

class DefaultCollectionExports implements FromCollection
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }
    public function collection()
    {
        return $this->collection;
    }
}
?>