<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\EmployeeMaster;
use App\Http\Controllers\Controller;
use DB;
class employeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $data = DB::select("SELECT employeemaster.*,designationmaster.designationname,department.departmentname
        FROM termsdms.employeemaster
        LEFT JOIN termsdms.designationmaster
        ON employeemaster.designationcode = designationmaster.designationcode
         LEFT JOIN termsdms.department
        ON employeemaster.departmentid = department.departmentid order by employeemaster.id desc");
        return response()->json($data);
    }
    public function create(Request $request)
    {
        $departments = DB::table('department')->get();
        $designations = DB::table('designationmaster')->get();

        
   

        return response()->json(["status"=>true, "success" => true, "departments" => $departments,"designations"=>$designations],200);
       
    }
    public function save(Request $request)
    {
      $rules=[
            'emp_name'=>'required',
            'department'=>'required',
            'designation'=>'required',
            'phone_no'=>'required|min:10|max:10',
            'mobile_no'=>'required|min:10|max:10',
            'email_id'=>'required|email',
            'date_of_joining'=>'required',
            'reporting_designation'=>'required',
            'isGoing'=>'required'
           ];

            $validator = \Validator::make($request->all(), $rules);
           if($validator->fails()) {
                       return response()->json([
                           'entity' => 'employeemaster', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try 
        {
           
            $c = new EmployeeMaster;
          //  $c->employeecode = 'SWE236';
            $c->employeename= $request->input('emp_name');
            $c->departmentid= $request->input('department');
            $c->designationcode= $request->input('designation');
            $c->phoneno= $request->input('phone_no');

            $c->mobileno= $request->input('mobile_no');
            $c->emailid= $request->input('email_id');
            $c->dateofjoining= $request->input('date_of_joining');
            $c->reportingdesgination= $request->input('reporting_designation');
            $c->status= $request->input('isGoing');

            $c->save();
         
            return response()->json( [
                        'entity' => 'employeemaster', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Employee created successfully !'
            ], 200);

        } 
        catch (\Exception $e) 
        {
           return response($e);
            return response()->json( [
                       'entity' => 'employeemaster', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create Employee Master !'
            ], 409);
       }
    }

    public function edit($id)
    {
        try{
            $departments = DB::table('department')->get();
            $designations = DB::table('designationmaster')->get();

            $data=EmployeeMaster::findOrFail($id);
              return response()->json( [
                       'entity' => 'employeemaster', 
                       'action' => 'Edit', 
                       'result' => $data,
                       'departments' => $departments,
                       'designations' => $designations,
                      
                       'status'=>200,
                       'message'=>'successfully to get Employee !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
            return response()->json( [
                       'entity' => 'employeemaster', 
                       'action' => 'Edit', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to get Employee !'
            ], 404);
         }
    }
    public function update(Request $request)
    {
      
      
       
          $rules=[
            'emp_name'=>'required',
            'department'=>'required',
            'designation'=>'required',
            'phone_no'=>'required|min:10|max:10',
            'mobile_no'=>'required|min:10|max:10',
            'email_id'=>'required|email',
            'date_of_joining'=>'required',
            'reporting_designation'=>'required',
            'isGoing'=>'required'
           ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'employeemaster', 
                           'action' => 'update', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try{
            $c =  EmployeeMaster::findOrFail($request->input('emp_id'));
        
            $c->employeecode = 'SWE234';
            $c->employeename= $request->input('emp_name');
            $c->departmentid= $request->input('department');
            $c->designationcode= $request->input('designation');
            $c->phoneno= $request->input('phone_no');

            $c->mobileno= $request->input('mobile_no');
            $c->emailid= $request->input('email_id');
            $c->dateofjoining= $request->input('date_of_joining');
            $c->reportingdesgination= $request->input('reporting_designation');
            $c->status= $request->input('isGoing');

           
            $c->save();
         
              return response()->json( [
                       'entity' => 'employeemaster', 
                       'action' => 'update', 
                       'result' => 'success',
                       'status'=>200,
                       'message'=>'Record updated successfully !'
            ], 200);
          
           } 
        catch (\Exception $e) 
         {
         
            
            return response()->json( [
                       'entity' => 'employeemaster', 
                       'action' => 'update', 
                       'result' => 'failed',
                       'status'=>404,
                       'message'=>'Failed to update record !'
            ], 404);
         }
    }
  
    
    
}