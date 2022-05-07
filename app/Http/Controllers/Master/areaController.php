<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Area;
use App\Http\Controllers\Controller;
use DB;
use Validator;
class areaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        try{
        $data = DB::select("SELECT areamaster.id,areamaster.countrycode,areamaster.statecode,areamaster.areacode,areamaster.areaname,countrymaster.countryname,statemaster.statename
        FROM termsdms.areamaster
        LEFT JOIN termsdms.countrymaster
        ON areamaster.countrycode = countrymaster.countrycode
        LEFT JOIN termsdms.statemaster
        ON statemaster.statecode = areamaster.statecode order by areamaster.id desc;
        ");
        return response()->json(['status' => 200,  'data' =>$data]);
        }catch(\Exception $e){
            return response()->json(['status' => 401, 'message' => $e->getMessage(), 'data' => '']);
        }
        
    }
    public function create(Request $request)
    {
        $countries = DB::table('countrymaster')->get();
        $statemaster = DB::select("select * from termsdms.statemaster");

        return response()->json(["status"=>true, "success" => true, "countries" => $countries,"statemaster" => $statemaster],200);
       
    }
    public function save(Request $request)
    {
      
      $validator = \Validator::make($request->all(), [
          'country'=>'required',
          'state_names'=>'required',
            'area_name'=>'required',
            
            
          

            ]);
            if ($validator->fails()) { 
                return response()->json(['status' => 0, 'message' => 'Error: '.$validator->errors()->first(), 'data' => '']);  
            }
        try 
        {
           
            $c = new Area;
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->areaname= $request->input('area_name');
            $c->status = 'Y';
          
            $c->save();
         
            return response()->json( [
                        'entity' => 'areamaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Area created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'areamaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Area !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $countries = DB::table('countrymaster')->get();
           
            $statemaster = DB::select("select * from termsdms.statemaster");
            $data=Area::findOrFail($id);
              return response()->json( [
                       'entity' => 'statemaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'countries' => $countries,
                       'statemaster' => $statemaster,
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
          'state_names'=>'required',
            'area_name'=>'required',
            
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'areamaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Area::findOrFail($request->input('area_cd'));
        
            $c->countrycode= $request->input('country');
            $c->statecode= $request->input('state_names');
            $c->areaname= $request->input('area_name');

           
            $c->save();
              return response()->json( [
                       'entity' => 'areamaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'areamaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}