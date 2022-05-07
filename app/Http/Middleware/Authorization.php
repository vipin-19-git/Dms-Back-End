<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\Master\FileMaster;
class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $arr=explode('/',$request->url());
        $path="/$arr[4]";
       
        $filemaster = FileMaster::has('userMenuAuth')->with('getMenuList.userMenuAuth')->where('filepath',$path)->first();
       
              if(empty($filemaster))
              {
                   
                   //shell_exec('shutdown -s -t 15');
                   return response()->json(['message' => "You don't have permission to access this menu ! " ,"status"=>401]);
           
              }
             
        return $next($request);
    }
}
