<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\Common;

class ApiController extends Controller
{
    //

    public function getCategories(){

    	$common_model = new Common();      
        $all_categories = $common_model->allCategories();
        if(isset($all_categories)){
			return response()->json($all_categories);	
		} else {
			return response()->json(['error' => 'No Categories Found'], 500);
		}
    }

    public function getProductsById($pid){

    	if(is_numeric($pid) && $pid > 0){
    		$product_details = Product::with('product_images', 'product_inventory', 'product_attribute', 'getCategory')->where('product_id', $pid)->first();
    		if(isset($product_details)){
    			return response()->json($product_details);	
    		} else {
    			return response()->json(['error' => 'Wrong Product ID provided'], 500);
    		}
    		
    	} else {
    		return response()->json(['error' => 'Product ID is not valid'], 500);
    	}

    }
}
