<?php

namespace App\Exports;

use App\Services\UserWalletService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class ActiveUsersExport
 * @package App\Exports
 */
class ActiveUsersExport implements FromCollection, WithHeadings, WithMapping
{
    private $headers = ['Name', 'Email', '2LC', 'Address', 'Locked', 'Affiliate no.', 'Affiliate by'];
    private $userWalletService;

    /**
     * ActiveUsersExport constructor.
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService)
    {
        $this->userWalletService = $userWalletService;
    }

    public function collection() : Collection
    {
        return $this->userWalletService->getActiveUsers();
    }

    public function map($user) : array
    {
        return [
            $user->name,
            $user->email,
            $user->l2l,
            $user->wallet->address,
            $user->locked ? 'Yes' : 'No',
            $user->affiliate_code,
            $user->affiliate_by ? $user->affiliateBy->affiliate_code : '-',
        ];
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
