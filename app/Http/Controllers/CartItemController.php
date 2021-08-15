<?php

namespace App\Http\Controllers;

use App\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class CartItemController extends Controller
{
    public function index()
    {
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
            ->where('user_id', Auth::id())
            ->join('items', 'items.id','=','cart_items.item_id')
            ->get();
            $subtotal = 0;
            foreach($cartitems as $cartitem){
                $subtotal += $cartitem->amount * $cartitem->quantity;
            }
            return view('cartitem/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
    }

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

    public function destroy(CartItem $cartItem){
        $cartItem->delete();
        return redirect('cartitem')->with('flash_message', 'カートから削除しました');
    }

    public function update(Request $request, CartItem $cartItem){
        $cartItem->quantity = $request->post('quantity');
        $cartItem->save();
        return redirect('cartitem')->with('flash_message', 'カートを更新しました');
    }
}
