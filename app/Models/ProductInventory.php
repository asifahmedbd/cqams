<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductInventory extends Model
{
   protected $primaryKey = 'product_stock_id';
   protected $table = 'product_inventory';
}
