<?php

namespace App\Http\Controllers\Master;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Master\Tehsil;
use App\Models\Master\FileMaster;
use App\Models\Master\FileMasterInternal;
use App\Models\Master\UserAauthorization;
use App\Models\Master\UserInformation;
use App\Http\Controllers\Controller;
use DB;
class UserPrivilageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        $usernames = DB::select("select employeecode,username from termsdms.userinformation");
        return response()->json($usernames);
    }
   
 
    public function populate (Request $request)
    {
                  
           $rules=[
            'user_name'=>'required',
            'module' => 'required',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'filemaster', 
                           'action' => 'populate', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
                      
        $filemaster = FileMaster::with('getMenuList.getSubMenuList')
                     ->with('getMenuList.userMenuAuth')
                     ->where('menuid',0)
                     ->where('fileid',$request->module)
                     ->orderBy('fileid','ASC')
                     ->first();
                  $files=$filemaster->getMenuList;
                foreach($files as $file)
                {
                   
                   if(isset($file->userMenuAuth))
                    {
                      $file->user_menu_status=true;
                      
                    }
                    else
                    {
                         $file->user_menu_status=false;
                    } 
                    
                }  
           
        return response()->json(["status"=>true, "success" => true,"filemaster" => $filemaster],200);
     
    }

  public function save(Request $request)
  {
    $rules=[
            'user_name'=>'required',
            'module' => 'required',
            'auth_menu.*'=>'required',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'user_authorization', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
        try {
            $res=UserAauthorization::where('moduleid',$request->module)->where('userid',$request->user_name)->delete();
            $n=count($request->auth_menu);
            $usr=UserInformation::where('username',$request->user_name)->first();
            $ustatus= $usr->isactive;
            for($i=0;$i<$n;$i++)
            {
                $auth=new UserAauthorization;
                $auth->userid=$request->user_name;
                $auth->fileid=$request->auth_menu[$i];
                $auth->moduleid=$request->module;
                $auth->userstatus=$ustatus;
                $auth->save();
            }
             return response()->json( [
                       'entity' => 'user_authorization', 
                       'action' => 'create', 
                       'result' => 'success',
                        'status'=>200,
                       'message'=>'User privillage created successfully !'
            ], 200);
          }
          catch (\Exception $e) 
           {
            return response()->json( [
                       'entity' => 'user_authorization', 
                       'action' => 'create', 
                       'result' => 'failed',
                        'status'=>409,
                       'message'=>'Failed to create user privillage !'
            ], 409);
        }


  }
  
    
    
}