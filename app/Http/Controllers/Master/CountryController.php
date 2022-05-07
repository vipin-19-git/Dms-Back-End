<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Country;
use App\Http\Controllers\Controller;
use DB;
class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data=Country::select()->orderBy('id','desc')->get();
             return response()->json( [
                        'entity' => 'country', 
                        'action' => 'Get', 
                        'result' => $data,
                        'status'=>200,
                        'message'=>'Country fetch successfully !'
            ], 200);

    }
    public function create(Request $request)
    {
     
           $rules=[
            'countryname'=>'required',
            'isdcode' => 'required|numeric|unique:countrymaster,isdcode',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'country', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
            $c = new Country;
            $c->countryname= $request->input('countryname');
            $c->isdcode= $request->input('isdcode');
            $c->save();

            return response()->json( [
                        'entity' => 'country', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Country created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response()->json( [
                       'entity' => 'country', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create company !'
            ], 409);
       }
    }
    public function edit($id)
    {
        try{
            $data=Country::findOrFail($id);
              return response()->json( [
                       'entity' => 'country', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'status'=>200,
                       'message'=>'successfully to get company !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'country', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get company !'
            ], 404);
         }
    }
    
  public function update(Request $request,$id)
    {
          $rules=[
            'countryname'=>'required',
            'isdcode' => 'required|numeric|unique:countrymaster,isdcode,'.$id,
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'country', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Country::findOrFail($id);
            $c->countryname= $request->input('countryname');
            $c->isdcode= $request->input('isdcode');
            $c->save();
              return response()->json( [
                       'entity' => 'country', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'country', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
    
    
}