<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use KDuma\PassphraseGenerator\Facades\Passphrase;

class SecretPhraseService
{
    public function generateSecretPhrase()
    {
        return Passphrase::setSeparators(" ")
            ->useEnglishWordList()
            ->dontUseModifiers()
            ->get();
    }
}
