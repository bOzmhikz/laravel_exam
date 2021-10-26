<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
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
            'name' => 'required',
            'available_stock' => 'required'
        ]);
        return Product::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Product::destroy($id);
    }

    /**
     * Search for a name
     *
     * @param  int  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Product::where('name', 'like', '%'. $name . '%')->get();
        //return Product::where('name', $name)->get();
    }

     /**
     * Request Order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {
        //Validate Fields
        $fields = $request->validate([
            'product_id' => 'required|integer',
            'quantity' =>'required|numeric|between:0,99999.99'
        ]);

        //Get Product Information
        $product = Product::where('id', '=', $request->input('product_id'))->get();

        //Check if Product Exist
        if($product->count() == 0){
            $response = [
                'message' => 'No Product Found'
            ];
            return response($response, 401);
        }
        //Validate Product Quantity
        else if( $product[0]['available_stock'] < $request->input('quantity') ) {
            $response = [
                'message' => 'Failed to order this product due to unavailability of the stock'
            ];
            return response($response, 400);
        }
        //Proceed Orders
        else 
        {

            $newStocks = $product[0]['available_stock'] - $request->input('quantity');
            Product::where("id", $request->input('product_id'))->update(["available_stock" => $newStocks]);

            $response = [
                'message' => 'You have successfully ordered this product.'
            ];
            return response($response, 201);
        }

    }

}
