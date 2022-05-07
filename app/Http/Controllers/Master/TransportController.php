<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Transport;
use App\Http\Controllers\Controller;
use DB;
class TransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT transportmaster.*,statemaster.statename,districtmaster.districtname,citymaster.cityname
        FROM termsdms.transportmaster
        LEFT JOIN termsdms.statemaster
        ON transportmaster.statecode = statemaster.statecode
        LEFT JOIN termsdms.districtmaster
        ON transportmaster.districtcode = districtmaster.districtcode
        LEFT JOIN termsdms.citymaster
        ON transportmaster.citycode = citymaster.citycode");
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
        $statemaster = DB::select("select * from termsdms.statemaster");
        // $areamaster = DB::select("select * from termsdms.areamaster");

        return response()->json(["status"=>true, "success" => true,"statemaster" => $statemaster],200);
       
    }
    public function save(Request $request)
    {
      
      $rules=[
            'transport_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'transportmaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
                
        try 
        {
           
            $c = new Transport;
         
            $c->transportname= $request->input('transport_name');
            $c->statecode= $request->input('state_nm');
            $c->districtcode = $request->input('district');
            $c->citycode = $request->input('city');

            $c->mobileno= $request->input('mobile_no');
            $c->phone= $request->input('phone_no');
            $c->alternatephoneno = $request->input('alt_phone_no');
            $c->emailid = $request->input('email_id');

            $c->faxno= $request->input('fax_no');
            $c->address= $request->input('address');
            
            $c->save();
         
            return response()->json( [
                        'entity' => 'transportmaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Transporter created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'transportmaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Transporter !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
           
            $statemaster = DB::select("select * from termsdms.statemaster");
         
           
          
            $data=Transport::findOrFail($id);
            $districtmaster = DB::select(" select * from termsdms.districtmaster where statecode = '".$data->statecode."'");
             
            $citymaster = DB::select("select * from termsdms.citymaster where districtcode = '".$data->districtcode."'");
            
            
            return response()->json( [
                       'entity' => 'transportmaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                     
                       'statemaster' => $statemaster,
                       'districtmaster' => $districtmaster,
                       'citymaster' => $citymaster,
                       'status'=>200,
                       'message'=>'successfully to get Transport !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'transportmaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Transport !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
       
          $rules=[
            'transport_name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'transportmaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  Transport::findOrFail($request->input('transporter_id'));
        
            $c->transportname= $request->input('transport_name');
            $c->statecode= $request->input('state_nm');
            $c->districtcode = $request->input('district');
            $c->citycode = $request->input('city');

            $c->mobileno= $request->input('mobile_no');
            $c->phone= $request->input('phone_no');
            $c->alternatephoneno = $request->input('alt_phone_no');
            $c->emailid = $request->input('email_id');

            $c->faxno= $request->input('fax_no');
            $c->address= $request->input('address');
            
           
            $c->save();
              return response()->json( [
                       'entity' => 'transportmaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'transportmaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}