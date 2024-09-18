<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $filePath = 'products.json';

    public function index()
    {
        $products = $this->getProducts();
        return view('products', compact('products'));
    }

    public function store(Request $request)
    {
        $products = $this->getProducts();
        $newProduct = [
            'id' => count($products) + 1,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'datetime' => now()->toDateTimeString(),
        ];
        $products[] = $newProduct;
        $this->saveProducts($products);
        return response()->json($newProduct);
    }

    public function update(Request $request, $id)
    {
        $products = $this->getProducts();
        $index = array_search($id, array_column($products, 'id'));
        if ($index !== false) {
            $products[$index] = [
                'id' => $id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'datetime' => $products[$index]['datetime'],
            ];
            $this->saveProducts($products);
            return response()->json($products[$index]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    private function getProducts()
    {
        if (Storage::exists($this->filePath)) {
            return json_decode(Storage::get($this->filePath), true) ?? [];
        }
        return [];
    }

    private function saveProducts($products)
    {
        Storage::put($this->filePath, json_encode($products));
    }
}
