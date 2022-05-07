<?php

namespace App\Models\Transaction;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Master\Product;
use App\Models\Master\Model_master;
class DistOrderDetails extends Model 
{
    
    protected $table="orderdetails";
    protected $primarykey = 'id';
    public $timestamps = false;
    public function getProductName()
    {
         return  $this->hasOne(Product::class, 'productcode','productcode'); 
    }
   public function getModelName()
     {
         return  $this->hasOne(Model_master::class, 'modelcode','modelcode'); 
     }
    
}
