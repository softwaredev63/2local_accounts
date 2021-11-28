<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\Importable;

/**
 * Class UsersImport
 *
 * @package App\Imports
 * @author Bojte Szabolcs
 */
class UsersImport implements WithHeadingRow, WithProgressBar, WithValidation, ToModel
{
    use Importable;

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        return new User([
            "name" => $row["name"],
            "email" => $row["email"],
            "password" => $row["password"],
            "locked" => $row["locked"] || false,
            "l2l" => $row["l2l"],
            "has_changed_password" => false
        ]);
    }

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function customValidationMessages()
    {
        return [
            'name.*required' => "Name is required",
            'email.*required' => "Email is required",
            'password.*required' => "Password is required",
        ];
    }

}
