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
class Dispatch extends Controller
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
    
    public function getDistOrder($code)
    {
         $orders=DistOrderHead::where('distributorcode',$code)->get();
         $distributor=StockistMaster::where('stockisttype','Distributor')->where('distributorcode',$code)->first();
         $transporters=Transport::where('statecode',$distributor->statecode)->get();
         $data=['orders'=>$orders,'transporters'=>$transporters];
         return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
  public function geVehicle($code)
    {
         $data=Vehicle::where('transportcode',$code)->get();
         return response()->json([
                           'entity' => 'vehiclemaster', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function getDistOrderDtls($order_no)
    {
       $data=DistOrderDetails::where('orderno',$order_no)->with(['getProductName','getModelName'])->get();
       return response()->json([
                           'entity' => 'orderdetails', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function store(Request $request)
    {
   
          $rules=[
             'invoice_no'=>'required',
             'invoice_date'=>'required',
             'prep_by'=>'required',
             'distributor'=>'required',
             'order_no'=>'required',
             'transporter'=>'required',
             'vehicle'=>'required',
             'gr_no'=> 'required',
             'status'=> 'required',
             'remarks'=> 'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required',
             'line_item.*.amt'=>'required'
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
        $head=new DistDispHead();
        $head->despinvoiceno=$request->invoice_no;
        $head->orderno=$request->order_no;
        $head->preparedby=$request->prep_by;
        $head->invoicedate=$request->invoice_date;
        $head->dispatchstatus=$request->status;
        $head->remarks=$request->remarks;
        $head->transportcode=$request->transporter;
        $head->vehicle=$request->vehicle;
        $head->grno=$request->gr_no;
        $head->distributorcode=$request->distributor;
        $head->save();
        $head_id=$head->id;
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
        
          $dtls= new  DistDispDetails();
          $dtls->despinvoiceno=$request->invoice_no;
          $dtls->orderno=$request->order_no;
          $dtls->head_id=$head_id;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->price=$line_item[$i]['amt'];
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'despatchhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order dispatched successfully !'
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
                        'message'=>'Failed to dispatch order !'
            ], 200);
       }
    }
    public function edit($id)
    {
       $data=DistDispHead::where('id',$id)->with('despDetails')->first();
        return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'Edit', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
       
    }
   public function update(Request $request,$head_id)
   {

          $rules=[
             'invoice_no'=>'required|unique:despatchhead,despinvoiceno,'.$head_id,
             'invoice_date'=>'required',
             'prep_by'=>'required',
             'distributor'=>'required',
             'order_no'=>'required',
             'transporter'=>'required',
             'vehicle'=>'required',
             'gr_no'=> 'required',
             'status'=> 'required',
             'remarks'=> 'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required',
             'line_item.*.amt'=>'required'
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
        $head= DistDispHead::find($head_id);
        $head->despinvoiceno=$request->invoice_no;
        $head->orderno=$request->order_no;
        $head->preparedby=$request->prep_by;
        $head->invoicedate=$request->invoice_date;
        $head->dispatchstatus=$request->status;
        $head->remarks=$request->remarks;
        $head->transportcode=$request->transporter;
        $head->vehicle=$request->vehicle;
        $head->grno=$request->gr_no;
        $head->distributorcode=$request->distributor;
        $head->save();
        DistDispDetails::where('head_id',$head_id)->delete();
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
        
          $dtls= new  DistDispDetails();
          $dtls->despinvoiceno=$request->invoice_no;
          $dtls->orderno=$request->order_no;
          $dtls->head_id=$head_id;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->price=$line_item[$i]['amt'];
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'despatchhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order dispatch upadted successfully !'
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
                        'message'=>'Failed to dispatch order !'
            ], 200);
       }
   }
    
}