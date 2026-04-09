<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenderHasAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'account_number',
        'mobile',
    ];
    protected $table = 'vender_has_accounts';
}
