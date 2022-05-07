<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\City;
use App\Http\Controllers\Controller;
use DB;
class cityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT citymaster.*,districtmaster.districtname
        FROM termsdms.citymaster
        LEFT JOIN termsdms.districtmaster
        ON citymaster.districtcode = districtmaster.districtcode order by citymaster.id desc");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $countries = DB::table('countrymaster')->get();
        $statemaster = DB::select("select * from termsdms.statemaster");
        $districtmaster = DB::select("select * from termsdms.districtmaster");

        return response()->json(["status"=>true, "success" => true, "countries" => $countries,"statemaster" => $statemaster,"districtmaster" => $districtmaster],200);
       
    }
    public function save(Request $request)
    {
      
      $rules=[
            'country'=>'required',
            'state_names'=>'required',
            'district_code'=>'required',
            'city_name'=>'required',
            'std_code'=>'required|min:6|max:6',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'citymaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new City;
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->districtcode= $request->input('district_code');
            $c->cityname = $request->input('city_name');
            $c->stdcode = $request->input('std_code');
            
            $c->status = 'Y';
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'citymaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'City created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'citymaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create City !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $countries = DB::table('countrymaster')->get();
            $statemaster = DB::select("select * from termsdms.statemaster");
            $districtmaster = DB::select("select * from termsdms.districtmaster");
          
            $data=City::findOrFail($id);
              return response()->json( [
                       'entity' => 'citymaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'countries' => $countries,
                       'statemaster' => $statemaster,
                       'districtmaster' => $districtmaster,
                       'status'=>200,
                       'message'=>'successfully to get District !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'citymaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get District !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'country'=>'required',
            'state_names'=>'required',
            'district_code'=>'required',
            'city_name'=>'required',
            'std_code'=>'required|min:6|max:6',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'districtmaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  City::findOrFail($request->input('city_cd'));
        
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->districtcode= $request->input('district_code');
            $c->cityname = $request->input('city_name');
            $c->stdcode = $request->input('std_code');

           
            $c->save();
              return response()->json( [
                       'entity' => 'citymaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'citymaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}