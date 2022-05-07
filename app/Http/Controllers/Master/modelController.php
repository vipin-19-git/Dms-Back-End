<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Model_master;
use App\Http\Controllers\Controller;
use DB;
class modelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT modelmaster.*,product.productname
        FROM termsdms.modelmaster
        LEFT JOIN termsdms.product
        ON modelmaster.productcode = product.productcode order by modelmaster.id desc");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $products = DB::table('product')->get();
   

        return response()->json(["status"=>true, "success" => true, "products" => $products],200);
       
    }
    public function save(Request $request)
    {
      $rules=[
            'product'=>'required',
            'model_name'=>'required',
            'cylinder'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'modelmaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new Model_master;
            $c->productcode= $request->input('product');
            $c->modelname= $request->input('model_name');
          
            $c->cylinder= $request->input('cylinder');
            $c->status = 'Y';
          
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'modelmaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Model created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
           
            return response()->json( [
                       'entity' => 'modelmaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Model !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $products = DB::table('product')->get();
            $data=Model_master::findOrFail($id);
              return response()->json( [
                       'entity' => 'modelmaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'products' => $products,
                      
                       'status'=>200,
                       'message'=>'successfully to get State !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'modelmaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Model !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
             'product'=>'required',
            'model_name'=>'required',
            'cylinder'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'modelmaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Model_master::findOrFail($request->input('model_code'));
        
            $c->productcode= $request->input('product');
            $c->modelname= $request->input('model_name');
          
            $c->cylinder= $request->input('cylinder');
            $c->status = 'Y';

           
            $c->save();
              return response()->json( [
                       'entity' => 'modelmaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'modelmaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}