<?php

namespace App\Http\Controllers\Bills;

use Auth;
use App\Models\Bills\Bill;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BillController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = Auth::user()->bills;
        // dd($bills);
        return $this->showAll($bills);
    }
}
