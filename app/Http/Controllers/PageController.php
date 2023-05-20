<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function notfound() { 
    
        if(Auth::guard('customerlogin')){
            echo 'customr';
        }
        else{
            echo 'visitor';
        }
    }
}
