<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdListController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('ad-lists/all', [AdListController::class, 'getAllRecords']);  
Route::group(['namespace' => 'Api\V1'], function () {
    
       Route::group(['prefix' => 'products'], function () {
        Route::get('popular', 'ProductController@get_popular_products');
         Route::get('recommended', 'ProductController@get_recommended_products');
          Route::get('betta', 'ProductController@get_betta_fishes'); 
          Route::get('otherfish', 'ProductController@get_otherfish');
          Route::get('items', 'ProductController@get_items');
          Route::get('feeds', 'ProductController@get_feeds');
          Route::get('plant', 'ProductController@get_plants');
          Route::post('add-product', 'ProductController@store');
          Route::post('add-file', 'ProductController@upload');
          Route::post('add-video', 'ProductController@uploadVideo');
          Route::delete('products/{id}', 'ProductController@deleteProduct');
          Route::patch('products/{id}', 'ProductController@updateProduct');


          
    }); 
        Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('register', 'CustomerAuthController@register');
        Route::post('login', 'CustomerAuthController@login');
        Route::get('users', 'CustomerAuthController@index');
        Route::put('/user/{id}/update', 'CustomerAuthController@update'); 
        Route::put('reset-psd', 'CustomerAuthController@changePasswordByPhoneNumber');
        });
   
        
        Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
            Route::get('notifications', 'NotificationController@get_notifications');
            Route::get('info', 'CustomerController@info');
            Route::post('update-profile', 'CustomerController@update_profile');
            Route::post('update-interest', 'CustomerController@update_interest');
            Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
            Route::get('suggested-foods', 'CustomerController@get_suggested_food');

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', 'CustomerController@address_list');
            Route::post('add', 'CustomerController@add_new_address');
            Route::put('update/{id}', 'CustomerController@update_address');
            Route::delete('delete', 'CustomerController@delete_address');
        });
                Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'OrderController@get_order_list');
            Route::get('running-orders', 'OrderController@get_running_orders');
            Route::get('details', 'OrderController@get_order_details');
            Route::get('to-seller', 'OrderController@get_seller_order_list');
            Route::post('place', 'OrderController@place_order');
            Route::put('cancel', 'OrderController@cancel_order');
            Route::put('refund-request', 'OrderController@refund_request');
            Route::get('track', 'OrderController@track_order');
            Route::put('payment-method', 'OrderController@update_payment_method');
            Route::put('accept', 'OrderController@updateAcceptedStatus');
            Route::put('pay', 'OrderController@payementDone');
            Route::put('processing', 'OrderController@updatePackedStatus');
            Route::put('handover', 'OrderController@updateHandoverStatus');
            Route::put('delivered', 'OrderController@updateDeliveryStatus');
            Route::put('cancelled', 'OrderController@updatecancellStatus');
            Route::post('add-review', 'OrderController@store_review');
            Route::get('reviews', 'OrderController@getAllReviews');


        });
            });
            
        Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
        Route::get('/get-zone-id', 'ConfigController@get_zone');
        Route::get('place-api-autocomplete', 'ConfigController@place_api_autocomplete');
        Route::get('distance-api', 'ConfigController@distance_api');
        Route::get('place-api-details', 'ConfigController@place_api_details');
        Route::get('geocode-api', 'ConfigController@geocode_api');
    });
});
