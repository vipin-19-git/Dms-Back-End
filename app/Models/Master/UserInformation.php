<?php

namespace App\Models\Master;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Master\StockistMaster;
class UserInformation extends Model 
{
    
    protected $table="userinformation";
    protected $primarykey = 'userid';

  public function getStockist()
    {
     return $this->hasOne(StockistMaster::class,'distributorcode','employeecode');
    }
    public function getEmpName()
    {
       return $this->hasOne(EmployeeMaster::class,'employeecode','employeecode');
    }
    
    
}
