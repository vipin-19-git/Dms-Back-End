<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Zone;
use App\Http\Controllers\Controller;
use DB;
class zoneController extends Controller
{
   
    public function index()
    {
       
         $data=Zone::select()->orderBy('id','desc')->get();
             return response()->json( [
                        'entity' => 'zonemaster', 
                        'action' => 'Get', 
                        'result' => $data,
                        'status'=>200,
                        'message'=>'Zone fetch successfully !'
            ], 200);

    }
    public function create(Request $request)
    {
        
     
           $rules=[
            'zonename'=>'required',
           
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'zonemaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new Zone;
            $c->zonename= $request->input('zonename');
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'zonemaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Zone created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response()->json( [
                       'entity' => 'zonemaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Zone !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $data=Zone::findOrFail($id);
              return response()->json( [
                       'entity' => 'zonemaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'status'=>200,
                       'message'=>'successfully to get zone !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'zonemaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get zone !'
            ], 404);
         }
    }
    public function update(Request $request,$id)
    {
       
          $rules=[
            'zonename'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'zonemaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Zone::findOrFail($id);
        
            $c->zonename= $request->input('zonename');
            $c->save();
              return response()->json( [
                       'entity' => 'zonemaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'zonemaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}