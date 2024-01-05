<?php

namespace App\CentralLogics;

use App\Models\BusinessSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }
    public static function send_order_notification($order, $token) {
        try {
            $status = $order->order_status;
            $value = self::order_status_update_message($status);
    
            if ($value) {
                $data = [
                    "title" => trans('messages.order_push_title'),
                    "description" => $value,
                    "orderID" => $order->id,
                    "image" => '',
                    "type" => 'order_status',
                ];
    
                self::send_push_notification_to_device($token, $data);
    
                try {
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'user_id' => $order->user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    return response()->json([$e], 403);
                }
            }
        } catch (\Exception $e) {
            return response()->json([$e], 403);
        }
    
        return false;
    }
    
    public static function send_push_notification_to_device($fcm_token, $data) {
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
    
        $url = "https://fcm.googleapis.com/fcm/send";
    
        $header = array(
            "authorization: key=AAAAv2S7Nvk:APA91bFrfxdl60hhHs4_1xV8k_ps_jIGrpWo0xpxTG2G0vaaybW3qUsnxLAcm_FCorYn-PYRARqVQVvL7DhMS5ZuSY2uZBjv-syXqGjQz1WCJz6Wr4ceBrIjOQV9jnFPz-1_JO82_da7",
            "content-type: application/json"
        );
    
        $postData = '{
            "to": ["' . $fcm_token . '"],
            "mutable_content": true,
            "data": {
                "title":"' . $data["title"] . '",
                "body":"' . $data["description"] . '",
                "order_id":"' . $data["order_id"] . '",
                "type":"' . $data["type"] . '",
                "is_read": 0
            },
            "notification": {
                "title":"' . $data["title"] . '",
                "body":"' . $data["description"] . '",
                "order_id":"' . $data["order_id"] . '",
                "title_loc_key":"' . $data["type"] . '",
                "type":"' . $data["type"] . '",
                "is_read": 0,
                "icon":"new",
                "android_channel_id":"BettaStore"
            }
        }';
    
        $ch = curl_init();
        $timeout = 120;
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
        $result = curl_exec($ch);
    
        if ($result === FALSE) {
            dd(curl_error($ch));
        }
    
        curl_close($ch);
    
        return $result;
    }
    
    public static function order_status_update_message($status) {
        switch ($status) {
            case 'pending':
                $data = BusinessSetting::where('key', 'order_confirmation_msg')->first();
                break;
            case 'accepted':
                $data = BusinessSetting::where('key', 'order_accepted_msg')->first();
                break;
            case 'processing':
                $data = BusinessSetting::where('key', 'order_processing_msg')->first();
                break;
            case 'handover':
                $data = BusinessSetting::where('key', 'order_handover_msg')->first();
                break;
            case 'delivered':
                $data = BusinessSetting::where('key', 'order_delivered_msg')->first();
                break;
            case 'payed':
                $data = BusinessSetting::where('key', 'order_payed_msg')->first();
                break;
            case 'cancelled':
                $data = BusinessSetting::where('key', 'order_cancelled_msg')->first();
                break;
            case 'review_added':
                $data = BusinessSetting::where('key', 'order_review_added_msg')->first();
                break;
            default:
                $data = '{"status":"0","message":""}';
                break;
        }
    
        return isset($data['value']['message']) ? $data['value']['message'] : '';
    }        
    
    // public static function send_push_notification_to_driver($drivers,$title,$body) {
    //     foreach ($drivers as $driver) { 
    //         $fcm_token=UserDevice::where('user_id',$driver)->pluck('fcm_token');
    //         // dd(env("FIREBASE_API_ACCESS_KEY"));
    //         $SERVER_API_KEY = env("FIREBASE_API_ACCESS_KEY");
    //         $data = [
    //             "registration_ids" => $fcm_token,
    //             "notification" => [
    //                 "title"=>$title,
    //                 "body"=>$body,
    //                 ],
    //             ];
    //         }
    //     }
       
}