<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistPrintController extends Controller
{
    public function print(Wishlist $wishlist)
    {
        $wishlist->load('items');

        return view('prints.wishlist', [
            'wishlist' => $wishlist,
        ]);
    }
}
