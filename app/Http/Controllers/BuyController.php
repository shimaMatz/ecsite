<?php

namespace App\Http\Controllers;

use App\CartItem; 
use Illuminate\Http\Request;
use App\Mail\Buy;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth; 

class BuyController extends Controller
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

        if (!$subtotal) {
            return redirect(route('cartitem.index'));
        }
        return view('buy/index', ['cartitems' => $cartitems, 'subtotal' => $subtotal]);
    }

    public function store(Request $request)
    {
        $cartitems = CartItem::select('cart_items.*', 'items.name', 'items.amount')
        ->where('user_id', Auth::id())
        ->join('items', 'items.id','=','cart_items.item_id')
        ->get();
        $subtotal = 0;
        foreach($cartitems as $cartitem){
            $subtotal += $cartitem->amount * $cartitem->quantity;
        }

        if (!$subtotal) {
            return redirect(route('cartitem.index'));
        }

        $line_items = [];
        foreach ($cartitems as $cartitem) {
            $line_item = [
                'name'        => $cartitem->name,
                'amount'      => $cartitem->amount,
                'currency'    => 'jpy',
                'quantity'    => $cartitem->quantity,
            ];
            array_push($line_items, $line_item);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [$line_items],
            'success_url'          => route('cartitem.index'),
            'cancel_url'           => route('cartitem.index'),
        ]);

        CartItem::where('user_id', Auth::id())->delete();
        Mail::to(Auth::user()->email)->send(new Buy());

        return view('buy.checkout', [
            'session' => $session,
            'publicKey' => env('STRIPE_PUBLIC_KEY')
        ]);

        return view('buy/complete');

        $request->flash();
        return $this->index();
    }
}
