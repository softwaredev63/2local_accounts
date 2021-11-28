<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

/**
 * Class ResetPasswordFromNovaResponse
 * @package App\Exports
 */
class ResetPasswordFromNovaResponse implements FromCollection
{
    /**
     * @var Collection
     */
    protected $responseCollection;

    /**
     * ActiveUsersExport constructor.
     * @param $collection
     */
    public function __construct(array $collection)
    {
        $this->responseCollection = new Collection($collection);
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return  $this->responseCollection;
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return ["email", "status"];
    }
}
