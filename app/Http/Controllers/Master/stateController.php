<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\State;
use App\Http\Controllers\Controller;
use DB;
class stateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT statemaster.id, statemaster.statecode,statemaster.zonecode,statemaster.countrycode,statemaster.statename,statemaster.status,countrymaster.countryname,zonemaster.zonename
        FROM termsdms.statemaster
        LEFT JOIN termsdms.countrymaster
        ON statemaster.countrycode = countrymaster.countrycode
        LEFT JOIN termsdms.zonemaster
        ON zonemaster.zonecode = statemaster.zonecode order by statemaster.id desc;");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $countries = DB::table('countrymaster')->get();
        $zones = DB::select("select * from termsdms.zonemaster");

        return response()->json(["status"=>true, "success" => true, "countries" => $countries,"zones" => $zones],200);
       
    }
    public function save(Request $request)
    {
      $rules=[
            'country'=>'required',

            'zone_names'=>'required',
            'state_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'statemaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new State;
            $c->countrycode= $request->input('country');
            $c->zonecode= $request->input('zone_names');
          
            $c->statename= $request->input('state_name');
          
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'statemaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'State created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
           
            return response()->json( [
                       'entity' => 'statemaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create State !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $countries = DB::table('countrymaster')->get();
            $zones = DB::select("select * from termsdms.zonemaster");
            $data=State::findOrFail($id);
              return response()->json( [
                       'entity' => 'statemaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'countries' => $countries,
                       'zones' => $zones,
                       'status'=>200,
                       'message'=>'successfully to get State !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'statemaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get State !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'country'=>'required',

            'zone_names'=>'required',
            'state_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'statemaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  State::findOrFail($request->input('state_id'));
        
            $c->countrycode= $request->input('country');
            $c->zonecode= $request->input('zone_names');
            $c->statename= $request->input('state_name');

           
            $c->save();
              return response()->json( [
                       'entity' => 'statemaster', 
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