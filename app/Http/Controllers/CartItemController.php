<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class CartItemController extends Controller
{
    public function store(Request $request){
        CartItem::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'item_id' => $request->post('item_id'),
            ],
            [
                'quantity' => \DB::raw('quantity + ' . $request->post('quantity') ),
            ]
        );
        return redirect('/')->with('flash_message', 'カートに追加しました');
    }
}
