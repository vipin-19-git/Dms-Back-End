<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Vehicle;
use App\Http\Controllers\Controller;
use DB;
class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT vehiclemaster.*,transportmaster.transportname
        FROM termsdms.vehiclemaster
        LEFT JOIN termsdms.transportmaster
        ON transportmaster.transportcode = vehiclemaster.transportcode");
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
        $transportmaster = DB::select("select * from termsdms.transportmaster");
        // $areamaster = DB::select("select * from termsdms.areamaster");

        return response()->json(["status"=>true, "success" => true,"transportmaster" => $transportmaster],200);
       
    }
    public function save(Request $request)
    {
      
      $rules=[
            'vehicle_no'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'vehiclemaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
                
        try 
        {
           
            $c = new Vehicle;
         
            $c->transportcode= $request->input('transporter_code');
            $c->vehicleno= $request->input('vehicle_no');
            $c->vehiclename = $request->input('vehicle_name');
            $c->drivername = $request->input('driver_name');

        
            
            $c->save();
         
            return response()->json( [
                        'entity' => 'vehiclemaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Vehicle created successfully !'
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
           
            $transportmaster = DB::select("select * from termsdms.transportmaster");
         
           
          
            $data=Vehicle::findOrFail($id);
            
          
            
            return response()->json( [
                       'entity' => 'vehiclemaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'transportmaster' => $transportmaster,
                       'status'=>200,
                       'message'=>'successfully to get Transport !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'vehiclemaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Vehicle !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'vehicle_no'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'vehiclemaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Vehicle::findOrFail($request->input('vehicle_id'));
        
            $c->transportcode= $request->input('transporter_code');
            $c->vehicleno= $request->input('vehicle_no');
            $c->vehiclename = $request->input('vehicle_name');
            $c->drivername = $request->input('driver_name');
           
            $c->save();
              return response()->json( [
                       'entity' => 'vehiclemaster', 
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