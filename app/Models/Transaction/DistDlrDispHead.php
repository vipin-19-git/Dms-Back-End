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
class DistDlrDispHead extends Model 
{
    //DistDispDetails
    protected $table="distributordispatchhead";
    protected $primarykey = 'id';
    public $timestamps = false;
    protected $with=['getDistributor','getDealer'];
    public function despDetails()
    {
      return  $this->hasMany(DistDlrDispDetails::class, 'head_id','id');
    }
     public function getDistributor()
     {
        return  $this->hasOne(StockistMaster::class, 'stockistcode','distributorcode')->where('stockisttype','Distributor');  
     }
      public function getDealer()
     {
        return  $this->hasOne(StockistMaster::class, 'stockistcode','dealercode')->where('stockisttype','Dealer');  
     }
     
}
