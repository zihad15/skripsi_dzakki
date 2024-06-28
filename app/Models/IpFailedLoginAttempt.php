<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpFailedLoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'ip_failed_login_attempt';

    protected $fillable = [
        'input_username',
        'ip',
        'failed_attempt',
    ];
}
