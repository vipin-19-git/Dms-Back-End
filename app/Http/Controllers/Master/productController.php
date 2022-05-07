<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Product;
use App\Http\Controllers\Controller;
use DB;
class productController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT * FROM termsdms.product order by product.id desc");
        return response()->json($data);
    }
   
    public function save(Request $request)
    {
      
      $rules=[
            'product_nm'=>'required',
            'prod_desc'=>'required'
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'product', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new Product;
          
            $c->productname= $request->input('product_nm');
            $c->description= $request->input('prod_desc');
           
            $c->status = 'Y';
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'product', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Product created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'product', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Product !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $data = DB::table('product')->get();
         
           
          
            $data=Product::findOrFail($id);
              return response()->json( [
                       'entity' => 'product', 
                       'action' => 'Edit', 
                       'result' => $data,
                      
                   
                       'status'=>200,
                       'message'=>'successfully to get Products !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'product', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Products !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'product_nm'=>'required',
            'prod_desc'=>'required'
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'product', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Product::findOrFail($request->input('product_cd'));
        
            $c->productname= $request->input('product_nm');
            $c->description= $request->input('prod_desc');
           
            $c->status = 'Y';

           
            $c->save();
              return response()->json( [
                       'entity' => 'product', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'product', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}