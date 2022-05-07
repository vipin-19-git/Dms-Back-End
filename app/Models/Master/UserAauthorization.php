<?php

namespace App\Models\Master;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Master\FileMaster;
use App\Models\Master\FileMasterInternal;
class UserAauthorization extends Model 
{
    
    protected $table="user_authorization";
    protected $primarykey = 'uid';
    protected $timestamp=false;
}
