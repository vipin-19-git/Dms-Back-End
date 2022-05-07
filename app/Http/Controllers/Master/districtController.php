<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\District;
use App\Http\Controllers\Controller;
use DB;
class districtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT districtmaster.*,statemaster.statename
        FROM termsdms.districtmaster
        LEFT JOIN termsdms.statemaster
        ON districtmaster.statecode = statemaster.statecode order by districtmaster.id desc");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $countries = DB::table('countrymaster')->get();
        $statemaster = DB::select("select * from termsdms.statemaster");
        $areamaster = DB::select("select * from termsdms.areamaster");

        return response()->json(["status"=>true, "success" => true, "countries" => $countries,"statemaster" => $statemaster,"areamaster" => $areamaster],200);
       
    }
    public function save(Request $request)
    {
      
      $rules=[
        'country'=>'required',
        'state_names'=>'required',
            'area_code'=>'required',
            'district_name'=>'required',
            
            

           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'districtmaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new District;
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->areacode= $request->input('area_code');
            $c->districtname = $request->input('district_name');
            $c->status = 'Y';
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'districtmaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'District created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'districtmaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create District !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $countries = DB::table('countrymaster')->get();
            $statemaster = DB::select("select * from termsdms.statemaster");
            $areamaster = DB::select("select * from termsdms.areamaster");
           
          
            $data=District::findOrFail($id);
              return response()->json( [
                       'entity' => 'districtmaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'countries' => $countries,
                       'statemaster' => $statemaster,
                       'areamaster' => $areamaster,
                       'status'=>200,
                       'message'=>'successfully to get District !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'districtmaster', 
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
            'area_code'=>'required',
            'district_name'=>'required',
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
            $c =  District::findOrFail($request->input('district_cd'));
        
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->areacode= $request->input('area_code');
            $c->districtname = $request->input('district_name');

           
            $c->save();
              return response()->json( [
                       'entity' => 'districtmaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'districtmaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}