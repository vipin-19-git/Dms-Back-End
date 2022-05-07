<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\UserInformation;
use App\Http\Controllers\Controller;
use DB;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = User::all();
        return response()->json($data);
    }
    public function getUserData($id)
    {
        $user_type = $id;$user_data= array();
        if($user_type == 'EMPLOYEE')
        {
            $user_data = DB::select("select employeecode as stockistcode,employeename as name from employeemaster");
           
        }
        else if($user_type == 'DEALER')
        {
            
            $user_data = DB::select("select * from termsdms.stockistmaster where stockisttype='Dealer'");
        }
        else if($user_type == 'DISTRIBUTOR')
        {
            $user_data = DB::select("select * from termsdms.stockistmaster where stockisttype='Distributor'");
        }

        return response()->json($user_data);

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
            'user_name'=>'required|unique:userinformation,username',
            'emp_code'=>'required',
            'user_password'=>'required',
            'confirm_password' => 'required|same:user_password'
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'userinformation', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
             
        try 
        {
           
            $c = new UserInformation;
            $c->employeecode= $request->input('emp_code');
            $c->username= $request->input('user_name');
            $c->password = Hash::make($request->user_password);
            $c->user_type = $request->input('user_type');
            $c->isactive = $request->input('status');
            $c->save();
           return response()->json( [
                        'entity' => 'userinformation', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'User Information  created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response()->json( [
                       'entity' => 'userinformation', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create userinformation !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
         
           $data=UserInformation::with('getEmpName')->with('getStockist')->findOrFail($id);
              return response()->json( [
                       'entity' => 'userinformation', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'status'=>200,
                       'message'=>'successfully to get user !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'userinformation', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get user !'
            ], 404);
         }
    }
    public function update(Request $request,$id)
    {
      
           
      $rules=[
            'user_name'=>'required|unique:userinformation,username,'.$id,
            'emp_code'=>'required',
           ];
          if($request->user_password!='')
          {
              $rules['user_password']='required';
              $rules['confirm_password']='required|same:user_password';

          }
            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'userinformation', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
             
        try 
        {
           
            $c =  UserInformation::findOrFail($id);
            $c->employeecode= $request->input('emp_code');
            $c->username= $request->input('user_name');
             if($request->user_password!='')
             {
              $c->password = Hash::make($request->user_password);
              }
            $c->user_type = $request->input('user_type');
            $c->isactive = $request->input('status');
            $c->save();
           return response()->json( [
                        'entity' => 'userinformation', 
                        'action' => 'update', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'User Information  updated successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response()->json( [
                       'entity' => 'userinformation', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to update userinformation !'
            ], 409);
       }
    }
  
    
    
}