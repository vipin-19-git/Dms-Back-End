<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Tehsil;
use App\Http\Controllers\Controller;
use DB;
class TehsilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT tehsilmaster.*,citymaster.cityname
        FROM termsdms.tehsilmaster
        LEFT JOIN termsdms.citymaster
        ON tehsilmaster.citycode = citymaster.citycode");
        return response()->json($data);
    }
    public function getUserData(Request $request,$id)
    {
        $user_type = $id;
        if($user_type == '1')
        {
            
            $user_data = DB::select("select employeecode as stockistcode,employeename as name from employeemaster");
           
        }
        else if($user_type == '2')
        {
            
            $user_data = DB::select("select * from termsdms.stockistmaster where stockisttype='Dealer'");
        }
        else if($user_type == '3')
        {
            $user_data = DB::select("select * from termsdms.stockistmaster where stockisttype='Distributor'");
        }

        return response()->json($user_data);

    }
    public function create(Request $request)
    {
        // $countries = DB::table('countrymaster')->get();
        $citymaster = DB::select("select * from termsdms.citymaster");
        // $areamaster = DB::select("select * from termsdms.areamaster");

        return response()->json(["status"=>true, "success" => true,"citymaster" => $citymaster],200);
       
    }
    public function save(Request $request)
    {
      
      $rules=[
            'tehsil_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'tehsilmaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
                
        try 
        {
           
            $c = new Tehsil;
         
            $c->citycode= $request->input('city');
            $c->tehsilname= $request->input('tehsil_name');
         

        
            
            $c->save();
         
            return response()->json( [
                        'entity' => 'tehsilmaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Tehsil created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'vehiclemaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Vehicle !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
           
            $citymaster = DB::select("select * from termsdms.citymaster");
           
          
            $data=Tehsil::findOrFail($id);
            
          
            
            return response()->json( [
                       'entity' => 'tehsilmaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'citymaster' => $citymaster,
                       'status'=>200,
                       'message'=>'successfully to get Tehsil !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'tehsilmaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Tehsil !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'tehsil_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'tehsilmaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Tehsil::findOrFail($request->input('tehsil_id'));
        
            $c->citycode= $request->input('city');
            $c->tehsilname= $request->input('tehsil_name');
           
            $c->save();
              return response()->json( [
                       'entity' => 'tehsilmaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'vehiclemaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}