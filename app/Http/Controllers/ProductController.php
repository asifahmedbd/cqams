<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Common;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\ProductImage;
use App\Models\ProductInventory;
use App\Models\ProductAttribute;
use Image;
Use Alert;
use DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all_products = Product::with('product_images', 'product_inventory', 'product_attribute', 'getCategory')->get();
        //dd($all_products);
        return view('admin.products.index', compact('all_products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $common_model = new Common();      
        $all_categories = $common_model->allCategories();
        $all_attributes = $common_model->allAttributes();
        //dd($all_attributes);
        return view('admin.products.create', compact('all_categories', 'all_attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request);
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'product_name' => 'required',
                'parent_id' => 'required',
                'product_sku' => 'required'
            ]);
            $admin_info = session('admin_info');
            //dd($admin_info);
            $product_db = new Product();
            $product_db->product_title = $request->product_name;
            $product_db->category_id = $request->parent_id;
            $product_db->short_description = $request->short_description ? $request->short_description : null;
            $product_db->long_description = $request->long_description ? $request->long_description : null;
            $product_db->brand_id = $request->brand_id ? $request->brand_id : null;
            $product_db->product_tags = $request->product_tags ? $request->product_tags : null;
            $product_db->product_model = $request->product_model ? $request->product_model : null;
            $product_db->product_sku = $request->product_sku;
            $product_db->product_price = $request->product_price;
            $product_db->product_unit = $request->product_unit;

            $product_db->is_featured = $request->is_featured ? 1 : 0;
            $product_db->top_selling = $request->top_selling ? 1 : 0;
            $product_db->is_refundable = $request->is_refundable ? 1 : 0;
            $product_db->created_by = $admin_info->id;

            $product_db->save();
            $pid = $product_db->product_id;

            //upload feature & gallery images
            if($pid && isset($request->feature_image)){
                $feature_image   = $request->file('feature_image');
                $filename        = time().'_'.$feature_image->getClientOriginalName();
                $product_image = new ProductImage();
                $product_image->product_id      = $pid;
                $product_image->feature_image   = $filename;

                $feature_image->move(public_path('uploads/products/').$pid.'/original/',$filename);
                $image_resize = Image::make(public_path('uploads/products/').$pid.'/original/'.$filename);
                $image_resize->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $save_path = public_path('uploads/products/').$pid.'/thumbnail/';
                if (!file_exists($save_path)) {
                    mkdir($save_path, 777, true);
                }
                $image_resize->save(public_path('uploads/products/').$pid.'/thumbnail/'.$filename);

                //upload gallery images
                if(isset($request->gallery_images)){
                    $gallery_images = $request->gallery_images;
                    //dd($gallery_images);
                    $galleryimg = array();
                    foreach ($gallery_images as $gdata) {
                        $gfilename         = time().'_'.$gdata->getClientOriginalName();
                        $galleryimg[]      = $gfilename;
                        $gdata->move(public_path('uploads/products/').$pid.'/gallery_images/',$gfilename);
                    }
                    $product_image->gallery_images = json_encode($galleryimg);
                }
                $product_image->save();
            }

            //upload product attributes
            if($pid && isset($request->attr_price)){
                $total_quantity = 0;
                $attribute_price = $request->attr_price;
                $attribute_quantity = $request->attr_quantity;
                foreach ($attribute_price as $attribute_title => $value) {
                    $total_quantity += $attribute_quantity[$attribute_title];
                    $product_attribute = new ProductAttribute();
                    $product_attribute->product_id = $pid;
                    $product_attribute->attribute_title = $attribute_title;
                    $product_attribute->attribute_price = $value;
                    $product_attribute->attribute_quantity = $attribute_quantity[$attribute_title];
                    $product_attribute->save();
                }

                //insert/update stock quantity
                $product_inventory = new ProductInventory();
                $product_inventory->product_id = $pid;
                $product_inventory->stock_amount = $total_quantity;
                $product_inventory->save();
            }


            Alert::success('Product Added Successfully!', 'success');    
            return redirect()->route('products.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getProductDetails(Request $request){
        $pid = $request->pid;
        $product_details = Product::with('product_images', 'product_inventory', 'product_attribute', 'getCategory')->where('product_id', $pid)->first();
        $feature_image_url = asset('/uploads/products/'.$pid.'/original/'.$product_details->product_images->feature_image);

        $product_attributes = $product_details->product_attribute;
        //dd($product_attributes);
        $size_array = array();
        $color_array = array();

        foreach ($product_attributes as $pakey => $pavalue) {
            $parray = explode("+", $pavalue['attribute_title']);
            
        }
        //exit();

        $gallery_images = json_decode($product_details->product_images->gallery_images, true);
        //dd($gallery_images);
        $gallery_html = '';
        foreach ($gallery_images as $key => $value) {
            $gallery_image_url = asset('/uploads/products/'.$pid.'/gallery_images/'.$value);
            $gallery_html .= '<div class="product-image-thumb"><img src="'.$gallery_image_url.'" alt="Product Image"></div>';
        }

        $html ='<section class="content">
      <div class="card card-solid">
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-sm-6">
              <h3 class="d-inline-block d-sm-none">'.$product_details->product_title.'</h3>
              <div class="col-12">
                <img src="'.$feature_image_url.'" class="product-image" alt="Product Image">
              </div>
              <div class="col-12 product-image-thumbs">
                <div class="product-image-thumb active"><img src="'.$feature_image_url.'" alt="Product Image"></div>
                '.$gallery_html.'
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <h3 class="my-3">'.$product_details->product_title.'</h3>
              <p>'.$product_details->short_description.'</p>

              <hr>
              <h4>Available Colors</h4>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-default text-center active">
                  <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked>
                  Green
                  <br>
                  <i class="fas fa-circle fa-2x text-green"></i>
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_a2" autocomplete="off">
                  Blue
                  <br>
                  <i class="fas fa-circle fa-2x text-blue"></i>
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_a3" autocomplete="off">
                  Purple
                  <br>
                  <i class="fas fa-circle fa-2x text-purple"></i>
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_a4" autocomplete="off">
                  Red
                  <br>
                  <i class="fas fa-circle fa-2x text-red"></i>
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_a5" autocomplete="off">
                  Orange
                  <br>
                  <i class="fas fa-circle fa-2x text-orange"></i>
                </label>
              </div>

              <h4 class="mt-3">Size <small>Please select one</small></h4>
              <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                  <span class="text-xl">S</span>
                  <br>
                  Small
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_b2" autocomplete="off">
                  <span class="text-xl">M</span>
                  <br>
                  Medium
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_b3" autocomplete="off">
                  <span class="text-xl">L</span>
                  <br>
                  Large
                </label>
                <label class="btn btn-default text-center">
                  <input type="radio" name="color_option" id="color_option_b4" autocomplete="off">
                  <span class="text-xl">XL</span>
                  <br>
                  Xtra-Large
                </label>
              </div>

              <div class="bg-gray py-2 px-3 mt-4">
                <h2 class="mb-0">
                  $'.$product_details->product_price.'
                </h2>
                <h4 class="mt-0">
                  <small>Ex Tax: $00.00 </small>
                </h4>
              </div>

              </div>

            </div>
          </div>
          <div class="row mt-4">
            <nav class="w-100">
              <div class="nav nav-tabs" id="product-tab" role="tablist">
                <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Comments</a>
                <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">Rating</a>
              </div>
            </nav>
            <div class="tab-content p-3" id="nav-tabContent">
              <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vitae condimentum erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed posuere, purus at efficitur hendrerit, augue elit lacinia arcu, a eleifend sem elit et nunc. Sed rutrum vestibulum est, sit amet cursus dolor fermentum vel. Suspendisse mi nibh, congue et ante et, commodo mattis lacus. Duis varius finibus purus sed venenatis. Vivamus varius metus quam, id dapibus velit mattis eu. Praesent et semper risus. Vestibulum erat erat, condimentum at elit at, bibendum placerat orci. Nullam gravida velit mauris, in pellentesque urna pellentesque viverra. Nullam non pellentesque justo, et ultricies neque. Praesent vel metus rutrum, tempus erat a, rutrum ante. Quisque interdum efficitur nunc vitae consectetur. Suspendisse venenatis, tortor non convallis interdum, urna mi molestie eros, vel tempor justo lacus ac justo. Fusce id enim a erat fringilla sollicitudin ultrices vel metus. </div>
              <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab"> Vivamus rhoncus nisl sed venenatis luctus. Sed condimentum risus ut tortor feugiat laoreet. Suspendisse potenti. Donec et finibus sem, ut commodo lectus. Cras eget neque dignissim, placerat orci interdum, venenatis odio. Nulla turpis elit, consequat eu eros ac, consectetur fringilla urna. Duis gravida ex pulvinar mauris ornare, eget porttitor enim vulputate. Mauris hendrerit, massa nec aliquam cursus, ex elit euismod lorem, vehicula rhoncus nisl dui sit amet eros. Nulla turpis lorem, dignissim a sapien eget, ultrices venenatis dolor. Curabitur vel turpis at magna elementum hendrerit vel id dui. Curabitur a ex ullamcorper, ornare velit vel, tincidunt ipsum. </div>
              <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab"> Cras ut ipsum ornare, aliquam ipsum non, posuere elit. In hac habitasse platea dictumst. Aenean elementum leo augue, id fermentum risus efficitur vel. Nulla iaculis malesuada scelerisque. Praesent vel ipsum felis. Ut molestie, purus aliquam placerat sollicitudin, mi ligula euismod neque, non bibendum nibh neque et erat. Etiam dignissim aliquam ligula, aliquet feugiat nibh rhoncus ut. Aliquam efficitur lacinia lacinia. Morbi ac molestie lectus, vitae hendrerit nisl. Nullam metus odio, malesuada in vehicula at, consectetur nec justo. Quisque suscipit odio velit, at accumsan urna vestibulum a. Proin dictum, urna ut varius consectetur, sapien justo porta lectus, at mollis nisi orci et nulla. Donec pellentesque tortor vel nisl commodo ullamcorper. Donec varius massa at semper posuere. Integer finibus orci vitae vehicula placerat. </div>
            </div>
          </div>
        </div>
      </div>

    </section>';

        return $html;
    }
}
