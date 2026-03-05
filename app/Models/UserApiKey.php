<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApiKey extends Model
{

    protected $fillable = ['user_id', 'provider', 'api_key', 'is_active', 'label', 'priority', 'is_primary'];

}
