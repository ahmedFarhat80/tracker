<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\QuotessResource;
use Illuminate\Http\Request;
use App\Models\Quote;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Quote::selection()->active()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return QuotessResource::collection($data);        
    }
}
