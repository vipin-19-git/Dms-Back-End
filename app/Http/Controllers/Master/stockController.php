<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\StockistMaster;
use App\Http\Controllers\Controller;
use DB;
class stockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT termsdms.stockistmaster.*,statemaster.statename
                FROM termsdms.stockistmaster
                LEFT JOIN termsdms.statemaster
                ON stockistmaster.statecode = statemaster.statecode");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $departments = DB::table('department')->get();
        $designations = DB::table('designationmaster')->get();

        $dealers = DB::select("select stockistcode,name from termsdms.stockistmaster where stockisttype ='Dealer'");
        $countries = DB::table('countrymaster')->get();
        $states = DB::table('statemaster')->get();
        $districts = DB::table('districtmaster')->get();
        $cities = DB::table('citymaster')->get();
        $tehsils = DB::table('tehsilmaster')->get();

        
        
        $distributors = DB::select("select stockistcode,name from termsdms.stockistmaster where stockisttype ='Distributor'");
        return response()->json(["status"=>true, "success" => true, "departments" => $departments,"designations" => $designations,"dealers"=>$dealers,"distributors"=>$distributors,"countries"=>$countries,"states"=>$states,'districts'=>$districts,'cities'=>$cities,'tehsils'=>$tehsils],200);
       
    }
    public function getdealerdata(Request $request)
    {
        $dealers = DB::select("select stockistcode,name from termsdms.stockistmaster where stockisttype ='Dealer'");
       
        return response()->json(["status"=>true, "success" => true, "dealers" => $dealers],200);
    }
    public function getdistributordata(Request $request)
    {
        $distributors = DB::select("select stockistcode,name from termsdms.stockistmaster where stockisttype ='Distributor'");
      
        return response()->json(["status"=>true, "success" => true, "distributors" => $distributors],200);
    }
    public function getstatesdata($id)
    {
        $states_data = DB::select("select * from termsdms.statemaster where countrycode='".$id."'");
      
        return response()->json(["status"=>true, "success" => true, "states_data" => $states_data],200);
    }
    public function getdistrictData($id)
    {
        $district_datas = DB::select("select * from termsdms.districtmaster where statecode='".$id."'");
      
        return response()->json(["status"=>true, "success" => true, "district_datas" => $district_datas],200);
       
    }
    public function getcityData($id)
    {
        $city_datas = DB::select("select * from termsdms.citymaster where districtcode='".$id."'");
      
        return response()->json(["status"=>true, "success" => true, "city_datas" => $city_datas],200);
    }
    public function getTehsilData($id)
    {
        $tehsil_datas = DB::select("select * from termsdms.tehsilmaster where citycode='".$id."'");
      
      
        return response()->json(["status"=>true, "success" => true, "tehsil_datas" => $tehsil_datas],200);

    }
    public function save(Request $request)
    {
  
      $rules=[
            'name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'stockistmaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new StockistMaster;
            $c->stockisttype= $request->input('stockist_type');
            $c->distributorcode= $request->input('distributor');
            $c->name= $request->input('name');
            $c->countrycode = $request->input('country');
            $c->statecode = $request->input('state_nm');
            $c->districtcode = $request->input('district');
            $c->citycode = $request->input('city');
            $c->tehsilcode = $request->input('tehsil');
            $c->mobile = $request->input('mobile_no');
            $c->emailid = $request->input('email_id');
            $c->phoneno = $request->input('phone_no');
            $c->altphoneno = $request->input('alt_phone_no');
            $c->faxno = $request->input('fax_no');
            $c->status = $request->input('status');
            $c->concernperson = $request->input('concern_person');
            $c->appointdate = $request->input('apt_date');
            $c->address = $request->input('address');
            $c->area = $request->input('area');

            $c->save();
         
            return response()->json( [
                        'entity' => 'stockistmaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'stockistmaster created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response($e);
           
            return response()->json( [
                       'entity' => 'stockistmaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create stockistmaster !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
           
          //2022-04-19 17:14:00
            //2022-04-12T17:17
            $data=StockistMaster::findOrFail($id);
            
            $distributors = DB::select("select stockistcode,name from termsdms.stockistmaster where stockisttype ='".$data->stockisttype."'");
            $countries = DB::table('countrymaster')->get(); 
            $states_data = DB::select("select * from termsdms.statemaster where countrycode='".$data->countrycode."'");
            $district_datas = DB::select("select * from termsdms.districtmaster where statecode='".$data->statecode."'");
            $city_datas = DB::select("select * from termsdms.citymaster where districtcode='".$data->districtcode."'");
            $tehsil_datas = DB::select("select * from termsdms.tehsilmaster where citycode='".$data->citycode."'");
            return response()->json( [
                       'entity' => 'stockistmaster', 
                       'action' => 'Edit', 
                       'distributors' =>$distributors,
                       'countries' =>$countries,
                       'states_data'=>$states_data,
                       'district_datas'=>$district_datas,
                       'city_datas'=>$city_datas,
                       'tehsil_datas'=>$tehsil_datas,

                       'result' => $data,
                     
                       'status'=>200,
                       'message'=>'successfully to get stockistmaster !'
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
            'name'=>'required',
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'stockistmaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  StockistMaster::findOrFail($request->input('stock_id'));
        
            $c->stockisttype= $request->input('stockist_type');
            $c->distributorcode= $request->input('distributor');
            $c->name= $request->input('name');
            $c->countrycode = $request->input('country');
            $c->statecode = $request->input('state_nm');
            $c->districtcode = $request->input('district');
            $c->citycode = $request->input('city');
            $c->tehsilcode = $request->input('tehsil');
            $c->mobile = $request->input('mobile_no');
            $c->emailid = $request->input('email_id');
            $c->phoneno = $request->input('phone_no');
            $c->altphoneno = $request->input('alt_phone_no');
            $c->faxno = $request->input('fax_no');
            $c->status = $request->input('status');
            $c->concernperson = $request->input('concern_person');
            $c->appointdate = $request->input('apt_date');
            $c->address = $request->input('address');
            $c->area = $request->input('area');
           
            $c->save();
              return response()->json( [
                       'entity' => 'stockistmaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'stockistmaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}