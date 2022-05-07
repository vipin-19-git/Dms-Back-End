<?php

namespace App\Models\Transaction;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\State;
use App\Models\Master\StockistMaster;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class DlrOrderHead extends Model 
{
    
     protected $table="dealerorderhead";
     protected $primarykey = 'id';
     public $timestamps = false;
     public function orderDetails()
     {
         return  $this->hasMany(DlrOrderDetails::class, 'head_id','id');
     }
     public function getState()
     {
       return  $this->hasOne(State::class, 'statecode','statecode'); 
     }
     public function getDealer()
     {
        return  $this->hasOne(StockistMaster::class,'stockistcode','dealercode' )->where('stockisttype','Dealer');  
     }
      public function getDistributor()
     {
        return  $this->hasOne(StockistMaster::class, 'stockistcode','distributorcode')->where('stockisttype','Distributor');  
     }
    
}
