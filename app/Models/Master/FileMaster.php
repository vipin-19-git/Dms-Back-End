<?php

namespace App\Models\Master;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Master\FileMasterInternal;
class FileMaster extends Model 
{
    
    protected $table="filemaster";
    protected $primarykey = 'fileid';

    public function getMenuList(){  
       return $this->hasMany(self::class, 'menuid','fileid')->orderBy('menuseq','ASC');
      }

    public function  getSubMenuList() {
        return $this->hasMany(FileMasterInternal::class,'file_sub_route_id','fileid');
     }

     public function userMenuAuth()
     {
         return $this->hasOne(UserAauthorization::class,'fileid','fileid')->where('userid',auth()->User()->username);
         
     }
  
    public function getAuthMenu()
    {
      return $this->hasMany(self::class, 'menuid','fileid')->wherehas('userMenuAuth');
    }


}
