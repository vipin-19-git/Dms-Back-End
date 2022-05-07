<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\Master\FileMaster;
class AuthController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['login','register']]);
      
        
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'employeecode' => 'required',
            'userpwd'=>'required',
        ]);

        try 
        {
            $user = new User;
            $user->username= $request->input('username');
            $user->employeecode= $request->input('employeecode');
            $user->userpwd= app('hash')->make($request->input('userpwd'));
            $user->save();

            return response()->json( [
                        'entity' => 'users', 
                        'action' => 'create', 
                        'result' => 'success'
            ], 200);

        } 
        catch (\Exception $e) 
        {
            return response()->json( [
                       'entity' => 'users', 
                       'action' => 'create', 
                       'result' => 'failed'
            ], 409);
        }
    }
    
     /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */  
    public function login(Request $request)
    {
       
      $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
    $credentials = $request->only(['username', 'password']);
       if (! $token = Auth::attempt($credentials)) {            
         return response()->json(['message' => 'Unauthorized','status'=>'failed']);
        }else{
            return $this->respondWithToken($token);
        }
  }

  public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function me(Request $request)
    {
     return response()->json(auth()->User());
    }
    
    public function logout(Request $request){
        auth()->logout();
        return response()->json(['message'=> 'User logout!',"status"=>200],200);
    }
    public function getAllMenus(Request $request)
    {
        
            $rules=[
            'user_name'=>'required',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'country', 
                           'action' => 'populate', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }
  
        $filemaster = FileMaster::with('getMenuList.userMenuAuth')->where('menuid',0)->get();
                     $selected = [];              
          for($i=0;$i<count($filemaster);$i++)
          {
            $files=$filemaster[$i]->getMenuList;
              $selected = []; 
            foreach($files as $file)
            {  
             if($file->userMenuAuth!=null)
                {
                  $selected[]=$file;
                }
            }
           unset($filemaster[$i]->getMenuList);
           $filemaster[$i]->getMenuList =$selected;
            }
     
          return response()->json(["status"=>200, "success" => true,"menus" => $filemaster],200);
    }
    
    
}