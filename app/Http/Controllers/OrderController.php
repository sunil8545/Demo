<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Payment\PaymentGateway;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::paginate()
                ->through(fn($order)=>new OrderResource($order));

        return $this->sendSuccessResponse($orders,'Order List');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'=>'nullable|exists:customers,id',
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
        ]);
        $order = Order::create([
            'customer_id'=>$request->customer_id,
            'customer_name'=>$request->name,
            'customer_email'=>$request->email,
            'customer_phone'=>$request->phone
        ]);

        return $this->sendSuccessResponse(new OrderResource($order),'Order Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return $this->sendSuccessResponse(new OrderResource($order->load('products')),'Order Retrieved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
        ]);
        
        $order->customer_name = $request->name;
        $order->customer_email = $request->email;
        $order->customer_phone = $request->phone;
        $order->save();

        return $this->sendSuccessResponse(new OrderResource($order,true),'Order Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return $this->sendSuccessResponse([],'Order Deleted');
    }

    public function addProduct(Request $request, Order $order)
    {
        $request->validate([
            'product_id'=>'required|exists:products,id'
        ]);

        $product = Product::find($request->product_id);

        $order_product = $order->products()->where('product_id',$request->product_id)->first();

        if($order_product){
            $order_product->quantity +=1;
            $order_product->total = $order_product->quantity * $order_product->price;
            $order_product->save();
        }else{
            $order->products()->create([
                'product_id'=>$product->id,
                'name'=>$product->name,
                'price'=>$product->price,
                'quantity'=>1,
                'total'=>$product->price,
            ]);
        }
        
        $order->total = $order->products()->sum('total');
        $order->save();

        return $this->sendSuccessResponse(new OrderResource($order->load('products')),'Product added in order');
    }

    public function pay(Request $request, Order $order, PaymentGateway $paymentGateway)
    {
        $paymentData = [
            'order_id'=>$order->id,
            'customer_email'=>$order->customer_email,
            'value'=>$order->total,
        ];

        $paymentResponse = $paymentGateway->makePayment($paymentData);
        if($paymentResponse['status']){
            $payment = $order->payments()->create([
                'message'=>$paymentResponse['message'],
                'amount'=>$order->total,
                'status'=>'success',
                'response'=>$paymentResponse['data']
            ]);
            $order->payment_id = $payment->id;
            $order->payment_status = 'success';
            $order->status = 'complete';
            $order->save();
            return $this->sendSuccessResponse([],'Order Paid');
        }

        $order->payments()->create([
            'message'=>$paymentResponse['message'],
            'amount'=>$order->total,
            'status'=>'cancel',
            'response'=>$paymentResponse['data']
        ]);
        
        return $this->sendErrorResponse($paymentResponse['message'],'Something went wrong',500);
    }
}
