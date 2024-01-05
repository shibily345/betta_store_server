<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
        
    public function get_popular_products(Request $request){
  
        $list = Food::where('type_id', 2)->take(10)->get();
        
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => 2,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200);
 
    }
        public function get_recommended_products(Request $request){
        $list = Food::where('type_id', 3)->take(10)->get();
        
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => 3,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200); 
    }
    

       public function test_get_recommended_products(Request $request){
  
        $list = Food::skip(5)->take(2)->get();
      
        foreach ($list as $item){
            $item['description']=strip_tags($item['description']);
            $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
        }
        
         $data =  [
            'total_size' => $list->count(),
            'limit' => 5,
            'offset' => 0,
            'products' => $list
        ];
         return response()->json($data, 200);
        // return json_decode($list);
    } 
    public function get_betta_fishes(Request $request){
        $list = Food::where('type_id', 4)->take(10)->orderBy('created_at','DESC')->get();
        
                foreach ($list as $item){
                    $item['description']=strip_tags($item['description']);
                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                    unset($item['selected_people']);
                    unset($item['people']);
                }
                
                 $data =  [
                    'total_size' => $list->count(),
                    'type_id' => 4,
                    'offset' => 0,
                    'products' => $list
                ];
                
         return response()->json($data, 200);
        
        } 
        
        public function upload(Request $request)
        {
            // Get the file from the request.
            $file = $request->file('file'); 
    
            // Save the file to a destination on the server.
            $file->move(public_path('uploads/images'), $file->getClientOriginalName()); 
    
            // Return a success message.
            return response()->json(['message' => 'File uploaded successfully!']);
        } 
        public function uploadVideo(Request $request)
        {
            // Get the file from the request.
            $file = $request->file('file'); 
    
            // Save the file to a destination on the server.
            $file->move(public_path('uploads/files'), $file->getClientOriginalName()); 
    
            // Return a success message.
            return response()->json(['message' => 'File uploaded successfully!']);
        }
        public function store(Request $request)
        {   

          
            $product = [
                'name' => $request->name,
                'breeder' => $request->breeder, 
                'seller_id' => $request->seller_id,  
                'description' => $request->description,   
                'price' => $request->price, 
                'malePrice' => $request->malePrice,
                'femalePrice' => $request->femalePrice, 
                'stars' => $request->stars, 
                'img' => $request->img,
                'video' => $request->video, 
                'type_id' => $request->type_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Insert the product data into the 'foods' table
            DB::table('foods')->insert($product);
        
            return response()->json(['message' => 'Product created successfully'], 201);
        }

        public function get_plants(Request $request){
            $list = Food::where('type_id', 5)->take(10)->orderBy('created_at','DESC')->get();
            
                    foreach ($list as $item){
                        $item['description']=strip_tags($item['description']);
                        $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                        unset($item['selected_people']);
                        unset($item['people']);
                    }
                    
                     $data =  [
                        'total_size' => $list->count(),
                        'type_id' => 5,
                        'offset' => 0,
                        'products' => $list
                    ];
                    
             return response()->json($data, 200);
            
            } 
             public function get_otherfish(Request $request){
                $list = Food::where('type_id', 6)->take(10)->orderBy('created_at','DESC')->get();
                
                        foreach ($list as $item){
                            $item['description']=strip_tags($item['description']);
                            $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                            $item['selected_people'];
                            $item['people'];
                        }
                        
                         $data =  [
                            'total_size' => $list->count(),
                            'type_id' => 6,
                            'offset' => 0,
                            'products' => $list
                        ];
                        
                 return response()->json($data, 200);
                
                } 
                
                public function get_items(Request $request){
                    $list = Food::where('type_id', 7)->take(10)->orderBy('created_at','DESC')->get();
                    
                            foreach ($list as $item){
                                $item['description']=strip_tags($item['description']);
                                $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                                unset($item['selected_people']);
                                unset($item['people']);
                            }
                            
                             $data =  [
                                'total_size' => $list->count(),
                                'type_id' => 7,
                                'offset' => 0,
                                'products' => $list
                            ];
                            
                     return response()->json($data, 200);
                    
                    }
                    
                    public function get_feeds(Request $request){
                        $list = Food::where('type_id', 8)->take(10)->orderBy('created_at','DESC')->get();
                        
                                foreach ($list as $item){
                                    $item['description']=strip_tags($item['description']);
                                    $item['description']=$Content = preg_replace("/&#?[a-z0-9]+;/i"," ",$item['description']); 
                                    unset($item['selected_people']);
                                    unset($item['people']);
                                }
                                
                                 $data =  [
                                    'total_size' => $list->count(),
                                    'type_id' => 8,
                                    'offset' => 0,
                                    'products' => $list
                                ];
                                
                         return response()->json($data, 200);
                        
                        }
                        public function updateProduct(Request $request, $id)
                        {
                            $product = Food::find($id);
                            
                            if (!$product) {
                                return response()->json(['error' => 'Product not found'], 404);
                            }
                            
                            // Validate and update product data
                            $request->validate([
                                'name' => 'required|string',
                                'description' => 'required|string',
                                // Add more validation rules for other fields as needed
                            ]);
                        
                            $product->update($request->all());
                            
                            return response()->json(['message' => 'Product updated successfully'], 200);
                        }
                        
                        
                        
                        public function deleteProduct($id)
                        {
                            $product = Food::find($id);
                            
                            if (!$product) {
                                return response()->json(['error' => 'Product not found'], 404);
                            }
                            
                            $product->delete();
                            
                            return response()->json(['message' => 'Product deleted successfully'], 200);
                        }

}
