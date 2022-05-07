<?php

namespace App\Http\Controllers\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction\DistDlrDispHead;
use App\Models\Transaction\DistDlrDispDetails;

use App\Models\Transaction\DlrOrderHead;
use App\Models\Transaction\DlrOrderDetails;

use App\Models\Master\State;
use App\Models\Master\StockistMaster;
use App\Models\Master\Transport;
use App\Models\Master\Vehicle;
use App\Models\Transaction\DistDispHead;
use App\Models\Transaction\DistDispDetails;
use Illuminate\Support\Str;
use DB;
class DistributorDispatch extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
      $data=DistDlrDispHead::all();
      return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function getDistributors()
    {
         $data=DB::table('dealerorderhead')->join('stockistmaster','stockistmaster.stockistcode','=','dealerorderhead.distributorcode')
              ->select('stockistmaster.*')->distinct('dealerorderhead.distributorcode')->get();
            return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function getDealers($code)
    {
      $data=StockistMaster::where('stockisttype','Dealer')->where('distributorcode',$code)->get();
            return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    
    public function getDlrOrdrs($code)
    {
         $data=DlrOrderHead::where('dealercode',$code)->get();
        return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }

    public function getDlrOrdrdtls($order_no)
    {
       $data=DlrOrderDetails::where('orderno',$order_no)->with(['getProductName','getModelName'])->get();
       return response()->json([
                           'entity' => 'orderdetails', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function store(Request $request)
    {
   

          $rules=[
             'invoice_no'=>'required|unique:distributordispatchhead,despinvoiceno',
             'invoice_date'=>'required',
             'prep_by'=>'required',
             'distributor'=>'required',
             'order_no'=>'required',
             'dealer'=>'required',
             'status'=> 'required',
             'remarks'=> 'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'distributordispatchhead', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }

    DB::beginTransaction();
      try
      {
        $head=new DistDlrDispHead();
        $head->despinvoiceno=$request->invoice_no;
        $head->orderno=$request->order_no;
        $head->preparedby=$request->prep_by;
        $head->invoicedate=$request->invoice_date;
        $head->dispatchstatus=$request->status;
        $head->remarks=$request->remarks;
        $head->dealercode=$request->dealer;
        $head->distributorcode=$request->distributor;
        $head->save();
        $head_id=$head->id;
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
          $dtls= new  DistDlrDispDetails();
          $dtls->despinvoiceno=$request->invoice_no;
          $dtls->orderno=$request->order_no;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->qty=$line_item[$i]['qty'];
          $dtls->distributorcode=$request->distributor;
          $dtls->head_id=$head_id;
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'distributordispatchhead', 
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
                        'entity' => 'distributordispatchhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to dispatch order !'
            ], 200);
       }
    }
    public function edit($id)
    {
       $data=DistDlrDispHead::where('id',$id)->with('despDetails')->first();
        return response()->json([
                           'entity' => 'despatchhead', 
                           'action' => 'Edit', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
       
    }
   public function update(Request $request,$head_id)
   {

  $rules=[
             'invoice_no'=>'required|unique:distributordispatchhead,despinvoiceno,'.$head_id,
             'invoice_date'=>'required',
             'prep_by'=>'required',
             'distributor'=>'required',
             'order_no'=>'required',
             'dealer'=>'required',
             'status'=> 'required',
             'remarks'=> 'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'distributordispatchhead', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }

    DB::beginTransaction();
      try
      {
        $head= DistDlrDispHead::find($head_id);
        $head->despinvoiceno=$request->invoice_no;
        $head->orderno=$request->order_no;
        $head->preparedby=$request->prep_by;
        $head->invoicedate=$request->invoice_date;
        $head->dispatchstatus=$request->status;
        $head->remarks=$request->remarks;
        $head->dealercode=$request->dealer;
        $head->distributorcode=$request->distributor;
        $head->save();
        $head_id=$head->id;
        $n=count($request->line_item);
        $line_item=$request->line_item;
        DistDlrDispDetails::where('head_id',$head_id)->delete();
        for($i=0;$i<$n;$i++)
        {
          $dtls= new  DistDlrDispDetails();
          $dtls->despinvoiceno=$request->invoice_no;
          $dtls->orderno=$request->order_no;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->qty=$line_item[$i]['qty'];
          $dtls->distributorcode=$request->distributor;
          $dtls->head_id=$head_id;
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'distributordispatchhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Dispatch updated successfully !'
            ], 200);
     }
       catch (\Exception $e) 
       {
         DB::rollback();
          return response()->json( [
                        'entity' => 'distributordispatchhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to updated dispatch !'
            ], 200);
       }
         
   }
    
}