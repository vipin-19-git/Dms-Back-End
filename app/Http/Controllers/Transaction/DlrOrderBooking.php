<?php

namespace App\Http\Controllers\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Transaction\DlrOrderDetails;
use App\Models\Transaction\DlrOrderHead;
use App\Http\Controllers\Controller;
use App\Models\Master\State;
use App\Models\Master\Model_master;
use App\Models\Master\StockistMaster;
use Illuminate\Support\Str;
use DB;
class DlrOrderBooking extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
      $data=DlrOrderHead::with(['getDealer','getDistributor','getState'])->get();
       return response()->json([
                           'entity' => 'dealerorderhead', 
                           'action' => 'index', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    
    public function getDlrDist($stcode)
    {
         $dealers=StockistMaster::where('stockisttype','Dealer')->where('statecode',$stcode)->get();
         $distributor=StockistMaster::where('stockisttype','Distributor')->where('statecode',$stcode)->get();
         $data=['dealers'=>$dealers,'distributors'=>$distributor];
         return response()->json([
                           'entity' => 'dealerorderhead', 
                           'action' => 'create', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
    }
    public function store(Request $request)
    {
          $rules=[
             'order_date'=>'required',
             'state_nm'=>'required',
             'dealer'=>'required',
             'distributor'=>'required',
             'status'=>'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required',
             'line_item.*.amt'=>'required'
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }

        DB::beginTransaction();
      try
      {
       /* $orderno=Str::random(8);
        $head=new DlrOrderHead();
        $head->orderno=$orderno;
        $head->bookingdate=$request->order_date;
        $head->dealercode=$request->dealer;
        $head->distributorcode=$request->distributor;
        $head->preparedby=$request->prep_by;
        $head->remarks=$request->remarks;
        $head->status=$request->status;
        $head->statecode=$request->state_nm;
        $head->save();
        $head_id=$head->id;*/
        $data=[null,null,$request->order_date,$request->dealer,$request->distributor,$request->prep_by,$request->remarks,$request->status,$request->state_nm];
        $head=DB::select('call termsdms.create_dealer_order(?,?,?,?,?,?,?,?,?)',$data);
    
        $head_id=$head[0]->row_id; $orderno=$head[0]->order_num;
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
         
          $dtls= new  DlrOrderDetails();
          $dtls->orderno=$orderno;
          $dtls->head_id=$head_id;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->status=$request->status;
          $dtls->price=$line_item[$i]['amt'];
          $dtls->save();
        }
         
        DB::commit();
         return response()->json( [
                        'entity' => 'dealerorderhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order booked successfully !'
            ], 200);
     }
       catch (\Exception $e) 
       {
         DB::rollback();
          return response()->json( [
                        'entity' => 'dealerorderhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to booked order !'
            ], 200);
       }
    }
    public function edit($id)
    {
       $data=DlrOrderHead::where('id',$id)->with('orderDetails')->first();
        return response()->json([
                           'entity' => 'dealerorderhead', 
                           'action' => 'Edit', 
                           'result' => $data,
                           'message'=>'Data Fetch successfully !']);  
       
    }
   public function update(Request $request,$head_id)
   {
     
    $rules=[
             'order_date'=>'required',
             'state_nm'=>'required',
             'dealer'=>'required',
             'distributor'=>'required',
             'status'=>'required',
             'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required',
             'line_item.*.amt'=>'required'
             ];

            $validator = \Validator::make($request->all(), $rules);
           if ($validator->fails()) {
                       return response()->json([
                           'entity' => 'orderhead', 
                           'action' => 'create', 
                           'result' => 'failed',
                           'message'=>$validator->errors()->first()]);
                 }

        DB::beginTransaction();
      try
      {
        $orderno=Str::random(8);
        $head=DlrOrderHead::find($head_id);
        $head->orderno=$orderno;
        $head->bookingdate=$request->order_date;
        $head->dealercode=$request->dealer;
        $head->distributorcode=$request->distributor;
        $head->preparedby=$request->prep_by;
        $head->remarks=$request->remarks;
        $head->status=$request->status;
        $head->statecode=$request->state_nm;
        $head->save();
        DlrOrderDetails::where('head_id',$head_id)->delete();
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
         
          $dtls= new  DlrOrderDetails();
          $dtls->orderno=$orderno;
          $dtls->head_id=$head_id;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->status=$request->status;
          $dtls->price=$line_item[$i]['amt'];
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'dealerorderhead', 
                        'action' => 'update', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order updated successfully !'
            ], 200);
     }
       catch (\Exception $e) 
       {
         DB::rollback();
          return response()->json( [
                        'entity' => 'dealerorderhead', 
                        'action' => 'update', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to update order !'
            ], 200);
       }
 
   }
    
}