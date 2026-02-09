<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, string $id): View
    {
        $product = SubscriptionProduct::where('id', $id)->where('is_active', true)->firstOrFail();
        return view('account.products.show', ['product' => $product]);
    }
}
