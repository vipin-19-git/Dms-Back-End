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
class DistOrderHead extends Model 
{
    
    protected $table="orderhead";
    protected $primarykey = 'orderno';
    public $timestamps = false;

     public function orderDetails()
     {
         return  $this->hasMany(DistOrderDetails::class, 'orderno','orderno');
     }
/*     public function getStatusAttribute($value)
      {
        if($value==1)
        {
          return 'Confirmed';
        }
        else
        {
          return 'Booked';
        }
    
    }*/
   /* public function getDistributorcodeAttribute($value)
    {
     return @StockistMaster::where('stockisttype','Distributor')->where('distributorcode',$value)->first()->name;
    }
 */


}
