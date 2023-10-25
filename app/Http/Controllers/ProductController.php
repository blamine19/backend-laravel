<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpParser\Node\NullableType;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::select('id', 'title', 'description', 'image')->get();

        return $products;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image'
        ]);

        $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);
        Product::create($request->post() + ['image' => $imageName]);
        return response()->json([
            'message' => 'Item added successfully'
        ]);

        /*
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required'
        ]);

       

        Product::create($request->post() );


        return response()->json(
            [
                'message' => 'Item added successfully '
            ]
        );*/
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable'
        ]);


        $product->fill($request->post())->update();

     
        if( $request->hasFile('image') ) {                    

            if($product->image) {          
                if( Storage::disk('public')->exists("product/image/{$product->image}") ) {   
                   
                    Storage::disk('public')->delete("product/image/{$product->image}");                    
                }
            }           

            
            $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
            Storage::disk('public')->putFileAs('product/image', $request->image, $imageName);

                                  
            $product->image = $imageName;

            $product->save();
        }


        return response()->json(
            [
                'message' => 'Item updated successfully '
            ]
        );
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

        if ($product->image) {
            if (Storage::disk('public')->exists("product/image/{$product->image}")) {
                Storage::disk('public')->delete("product/image/{$product->image}");
            }
        }

        $product->delete();

        return response()->json(
            [
                'message_retour' => 'Item deleted successfully '
            ]
        );
    }
}
