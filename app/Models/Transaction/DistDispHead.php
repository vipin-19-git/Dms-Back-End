<?php

namespace App\Models\Transaction;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Transaction\DistOrderDetails;
use App\Models\Master\StockistMaster;
use App\Models\Master\Transport;
class DistDispHead extends Model 
{
    //DistDispDetails
    protected $table="despatchhead";
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $with=['getDistributor','getTransporter'];
    public function despDetails()
    {
      return  $this->hasMany(DistDispDetails::class, 'head_id','id');
    }
     public function getDistributor()
     {
        return  $this->hasOne(StockistMaster::class, 'distributorcode','distributorcode')->where('stockisttype','Distributor');  
     }
      public function getTransporter()
     {
        return  $this->hasOne(Transport::class, 'transportcode','transportcode');  
     }
}
