<?php

namespace App\Http\Controllers\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction\DistOrderHead;
use App\Models\Transaction\DistOrderDetails;
use App\Models\Master\State;
use App\Models\Master\StockistMaster;
use App\Models\Master\Transport;
use App\Models\Master\Vehicle;
use App\Models\Transaction\DistDispHead;
use App\Models\Transaction\DistDispDetails;
use Illuminate\Support\Str;
use DB;
class DistributorDispatchReceiving extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
      $data=DistDispHead::all();
      return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function getDistributors()
    {
           $data=StockistMaster::where('stockisttype','Distributor')->get();
            return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    
    public function getInvoices($distcode)
    {
         $data=DistDispHead::where('distributorcode',$distcode)->get();
         return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
  public function getDispDtls($invoice)
    {
         $head=DistDispHead::where('despinvoiceno',$invoice)->first();
         $dtls=DistDispDetails::where('despinvoiceno',$invoice)->get();
         $data=['head'=>$head,'details'=>$dtls];
         return response()->json([
                           'entity' => 'despatchdetails', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }

   
   public function update(Request $request)
   {
 
          $rules=[
              'distributor'=>'required',
              'invoice_no'=>'required',
              'order_no'=>'required',
              'gr_no'=> 'required',
              'received_date'=>'required',
              'received_by'=>'required',
              'remarks'=> 'required', 
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }

        DB::beginTransaction();
      try
       {
         $disp_head= DistDispHead::where('distributorcode',$request->distributor)->where('despinvoiceno',$request->invoice_no)->where('orderno',$request->order_no);
         $disp_head->update(['receiveddate'=>$request->received_date,'receivedby'=>$request->received_by,'receivestatus'=>5,'dispatchstatus'=>5]);
         $order_head=DistOrderHead::where('orderno',$request->order_no)->where('distributorcode',$request->distributor);
         $order_head->update(['status'=>6]);
        DB::commit();
         return response()->json( [
                        'entity' => 'despatchhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order received successfully !'
            ], 200);
     }
       catch (\Exception $e) 
       {
         DB::rollback();
          return response()->json( [
                        'entity' => 'despatchhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to received order !'
            ], 200);
       }
   }
    
}