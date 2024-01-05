<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Food;
use App\Models\Order; 
use App\Models\Review;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'order_note' => 'required',
            'address' => 'required_if:order_type,delivery',
            //'longitude' => 'required_if:order_type,delivery',
            //'latitude' => 'required_if:order_type,delivery',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $product_price = 0;
    
        
        foreach ($request['cart'] as $c) {
            $product = Food::find($c['id']);
    
            if ($product) {
                $price = $c['price'] * $c['pquantity']; // Calculate total price for this product
                $product_price += $price;
                
                $product->people += 1; 
                $product->save();  
                // Create Order record for this item
                $order = new Order();
                $order->user_id = $request->user()->id;
                $order->order_amount = $c['price'];
                $order->product_id = $c['id'];
                $order->product_img = $c['img'];
                $order->quantity = $c['pquantity'];
                $order->seller_id = $c['breeder'];
                $order->male_quantity = $c['mquantity'];
                $order->female_quantity = $c['fquantity'];
                $order->order_note = $request['order_note'];
                $order->delivery_address = json_encode([
                    'contact_person_name' => $request->contact_person_name ?? $request->user()->f_name . ' ' . $request->user()->f_name,
                    'contact_person_number' => $request->contact_person_number ?? $request->user()->phone,
                    'address' => $request->address,
                    'longitude' => (string) $request->latitude,
                    'latitude' => (string) $request->longitude,
                ]);
                $timezone = 'Asia/Kolkata'; 
                $order->otp = rand(1000, 9999);
                $order->pending = Carbon::now($timezone)->toDateTimeString();  
                $order->created_at = Carbon::now($timezone)->toDateTimeString(); 
                $order->updated_at = Carbon::now($timezone)->toDateTimeString(); 
                $order->save();
    
                // Create OrderDetails record for this item
                $orderDetail = new OrderDetail(); 
                $orderDetail->order_id = $order->id;
                $orderDetail->prodect_id = $c['id'];
                $orderDetail->food_details = json_encode($product);
                $orderDetail->quantity = $c['pquantity'];
                $orderDetail->seller_id = $c['breeder'];
                $orderDetail->male_quantity = $c['mquantity'];
                $orderDetail->female_quantity = $c['fquantity'];
                $orderDetail->price = $c['price'];
                $orderDetail->created_at = Carbon::now($timezone)->toDateTimeString(); 
                $orderDetail->updated_at = Carbon::now($timezone)->toDateTimeString(); 
                $orderDetail->tax_amount = 0.0; 
                $orderDetail->save();

               
               
            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'product', 'message' => 'not found!']
                    ]
                ], 401);
            }
        }
    
        return response()->json([
            'message' => trans('messages.order_placed_successfully'),
            'total_amount' => $product_price,
        ], 200);
    } 


    public function get_order_list(Request $request)
    {
        $orders = Order::withCount('details')->where(['user_id' => $request->user()->id])->get()->map(function ($data) {
            $data['delivery_address'] = $data['delivery_address']?json_decode($data['delivery_address']):$data['delivery_address'];   

            return $data;
        });
        return response()->json($orders, 200);
    }

    public function get_seller_order_list(Request $request)
    {
    // Get the authenticated user's name
        $sellerName = $request->user()->f_name;

    // Retrieve orders where seller_id matches the user's name
        $orders = Order::withCount('details')
                    ->where(['seller_id' => $sellerName])
                    ->get()
                    ->map(function ($data) {
                        // Decode delivery_address if it's a JSON string
                        $data['delivery_address'] = $data['delivery_address'] ? json_decode($data['delivery_address']) : $data['delivery_address'];
                        return $data;
                    });

    // Return the orders as JSON response
        return response()->json($orders, 200);
    }
    
    public function updateAcceptedStatus(Request $request)
    {

        $data = $request->json()->all();

        // Extract order_id and delivery_charge from the request data
        $orderId = $data['order_id'];
        $deliveryCharge = $data['delivery_charge'];    
   
   
        $order = Order::find($orderId);

   
        if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
   
        }
  
        $timezone = 'Asia/Kolkata';

        
        $order->accepted =  Carbon::now($timezone)->toDateTimeString();
  
        $order->order_status = 'Accepted';
  
        $order->delivery_charge = $deliveryCharge;

  
        $order->save();

  
        return response()->json(['message' => 'Order accepted successfully.']);
    } 

      public function updatePackedStatus(Request $request)
    {
        
    $data = $request->json()->all();

    // Extract order_id and delivery_charge from the request data
    $orderId = $data['order_id'];
    $expectedDate = $data['expected_date'];
    $order = Order::find($orderId);
    
    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }
    
    
     

    $timezone = 'Asia/Kolkata';

    $order->processing =  Carbon::now($timezone)->toDateTimeString();
    $order->order_status = 'Processing';
    $order->scheduled = $expectedDate;
    
    $order->save();

    return response()->json(['message' => 'Order On the Way.']);
    }
    
    
    
    public function updateHandoverStatus(Request $request,)
    {
        
        
        $data = $request->json()->all();

    // Extract order_id and delivery_charge from the request data
    $orderId = $data['order_id'];
    $trackId = $data['tracking_id'];
    $deliveryPartner = $data['delivery_partner']; 
    $order = Order::find($orderId);
    
    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }
    

    $timezone = 'Asia/Kolkata';

    $order->handover =  Carbon::now($timezone)->toDateTimeString();
    $order->order_status = 'On the Way';
    $order->tracking_id = $trackId;
    $order->delivery_partner = $deliveryPartner;

    $order->save();

    return response()->json(['message' => 'ok Handover success.']);
    } 
      public function payementDone(Request $request,)
    {
        
        
        $data = $request->json()->all();

    // Extract order_id and delivery_charge from the request data
    $orderId = $data['order_id'];
    $order = Order::find($orderId);
    
  //  $seller=User::find($sellerId);
    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }
    

    $timezone = 'Asia/Kolkata'; 

    
    $order->payment_status = 'done';

    $order->save();

    return response()->json(['message' => 'ok Payment success.']);
    } 
   
    public function updateDeliveryStatus(Request $request,)
    {
        
        
        $data = $request->json()->all();

    // Extract order_id and delivery_charge from the request data
    $orderId = $data['order_id'];
    $instruction = $data['instruction'];
    
    $order = Order::find($orderId);

    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }
    

    $timezone = 'Asia/Kolkata';


    $order->delivered =  Carbon::now($timezone)->toDateTimeString();
    $order->instruction =  $instruction;
    $order->order_status = 'Delivered';
    

    $order->save();
    
    return response()->json(['message' => 'ok Handover success.']);
    } 
    public function updatecancellStatus(Request $request,)
    {
        
        
        $data = $request->json()->all();

    // Extract order_id and delivery_charge from the request data
    $orderId = $data['order_id']; 
    $orderNote = $data['order_note'];
    
    $order = Order::find($orderId);
    
    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }
    

    $timezone = 'Asia/Kolkata';

    $order->canceled =  Carbon::now($timezone)->toDateTimeString();
    $order->order_note =  $orderNote;
    $order->order_status = 'Cancelled';
    

    $order->save();

    return response()->json(['message' => 'ok Handover success.']);
    } 
    public function store_review(Request $request)
{
    $request->validate([
        'product_id' => 'required',
    ]);

    $user = auth()->user();
    $product = Food::findOrFail($request->product_id);

    $review = new Review();
    $review->user_id = $user->id;
    $review->product_id = $product->id;
    $review->rating = $request->rating;
    $review->comment = $request->comment;
    $review->img = $request->img;

    // Handle image upload if provided
    

    $review->save();

    $averageRating = Review::where('product_id', $product->id)->avg('rating') ;

    // Update the product's rating column with the calculated average
    $product->stars = $averageRating; 
    $product->save();
    
    return response()->json(['message' => 'Review submitted successfully.']);
}
public function getAllReviews()
{
    $reviews = Review::with('user')->get();
    return response()->json(['reviews' => $reviews]);
}
    
}
