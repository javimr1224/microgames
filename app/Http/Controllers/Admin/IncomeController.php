<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Stripe\Charge;
use Stripe\Stripe;

class IncomeController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(config('stripe.secret'));
        $charges = Charge::all(['limit' => 100]);
        
        return view('admin.incomes', compact('charges'));
    }
}
