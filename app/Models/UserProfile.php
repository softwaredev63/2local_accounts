<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'users_profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birthday',
        'country',
        'country_code',
        'city',
        'state',
        'post_code',
        'address',
        'mobile_number',
        'mobile_verification',
        'mobile_verified_at',
        'image',
        'business_name',
        'website',
        'hope',
        'notes',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'mobile_verified_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
