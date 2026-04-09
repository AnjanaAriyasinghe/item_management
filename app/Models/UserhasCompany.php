<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserhasCompany extends Model
{
    protected $fillable = [
     'user_id',
     'company_id',
    ];

    /**
     * Get the user that owns the UserhasCompany
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
