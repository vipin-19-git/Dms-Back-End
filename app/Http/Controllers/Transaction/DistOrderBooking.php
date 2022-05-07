<?php

namespace App\Http\Controllers\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Transaction\DistOrderDetails;
use App\Models\Transaction\DistOrderHead;
use App\Http\Controllers\Controller;
use App\Models\Master\State;
use App\Models\Master\Model_master;
use App\Models\Master\StockistMaster;
use Illuminate\Support\Str;
use DB;
class DistOrderBooking extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function index()
    {
        return response()->json(DistOrderHead::get());
    }
    public function getState()
    {
        return response()->json(State::all());
    }
    public function getModel($pcode)
    {
       $data=Model_master::where('productcode',$pcode)->get();
       return response()->json($data); 
    }
    public function getDistributor($stcode)
    {
         $data=StockistMaster::where('stockisttype','Dealer')->where('statecode',$stcode)->get();
         return response()->json($data);  
    }
    public function store(Request $request)
    {
          $rules=[
            'order_date'=>'required',
            'state_nm'=>'required',
            'distributor'=>'required',
            'status'=>'required',
            'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required'
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
     /*   $orderno=Str::random(8);
        $head=new DistOrderHead();
        $head->orderno=$orderno;
        $head->bookingdate=$request->order_date;
        $head->distributorcode=$request->distributor;
        $head->preparedby=$request->prep_by;
        $head->remarks=$request->remarks;
        $head->status=$request->status;
        $head->statecode=$request->state_nm;
        $head->save();*/
          $data=[null,null,$request->order_date,$request->distributor,$request->prep_by,$request->remarks,
                $request->status,$request->state_nm];
        $head=DB::select('call termsdms.create_distributor_order(?,?,?,?,?,?,?,?)',$data);
       $head_id=$head[0]->row_id; $orderno=$head[0]->order_num;
        $n=count($request->line_item);
        $line_item=$request->line_item;
        for($i=0;$i<$n;$i++)
        {
         
          $dtls= new  DistOrderDetails();
          $dtls->orderno=$orderno;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->status=$request->status;
          $dtls->price=0.0;
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'orderhead', 
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
                        'entity' => 'orderhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to booked order !'
            ], 200);
       }
    }
    public function edit($id)
    {
       $data=DistOrderHead::where('id',$id)->with('orderDetails')->first();
        return response()->json($data);  
    }
   public function update(Request $request)
   {
    $rules=[
            'order_date'=>'required',
            'state_nm'=>'required',
            'distributor'=>'required',
            'status'=>'required',
            'line_item.*.product'=>'required',
             'line_item.*.model'=>'required',
             'line_item.*.qty'=>'required'
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
        $orderno=$request->order_no;
        $data=['bookingdate'=>$request->order_date,
               'distributorcode'=>$request->distributor,
               'preparedby'=>$request->prep_by,
               'remarks'=>$request->remarks,
               'status'=>$request->status,
               'statecode'=>$request->state_nm
              ];
        $head=DistOrderHead::where('orderno',$orderno)->update($data);
        $n=count($request->line_item);
        $line_item=$request->line_item;
        DistOrderDetails::where('orderno',$orderno)->delete();
        for($i=0;$i<$n;$i++)
        {
          $dtls= new  DistOrderDetails();
          $dtls->orderno=$orderno;
          $dtls->productcode=$line_item[$i]['product'];
          $dtls->modelcode=$line_item[$i]['model'];
          $dtls->quantity=$line_item[$i]['qty'];
          $dtls->status=$request->status;
          $dtls->price=0.0;
          $dtls->save();
        }
        DB::commit();
         return response()->json( [
                        'entity' => 'orderhead', 
                        'action' => 'create', 
                        'result' => 'success',
                        'status'=>200,
                        'message'=>'Order upadted successfully !'
            ], 200);
     }
       catch (\Exception $e) 
       {
         DB::rollback();
          return response()->json( [
                        'entity' => 'orderhead', 
                        'action' => 'create', 
                        'result' => 'Failed',
                        'status'=>409,
                        'message'=>'Failed to update order !'
            ], 200);
       }
   }
    
}