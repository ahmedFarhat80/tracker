<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\Admin\QuotessResource;
use App\Http\Requests\Admin\QuotesRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quote;

class QuotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Quote::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return QuotessResource::collection($data);        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuotesRequest $request){
        try{
            $quote = $this->process(new Quote , $request);
            return $this->show($quote->id);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $data = Quote::selection()->findOrFail($id);
            return new QuotessResource($data);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(QuotesRequest $request, $id){
        try{
            $data = Quote::findOrFail($id);
            $quote = $this->process($data , $request);
            return $this->show($quote->id);
        } catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            $data = Quote::findOrFail($id);
            
            $data->delete();
            
            $data = [
                'status'    => 'success',
                'message'   => __('quotes.delete_success'),
            ];
            
            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }



    /**
     * store / update patients fields
     *
     * @param  quote Object , Request
     * @return quote
     */
    protected function process(Quote $quote , Request $request){
        
        $quote->en_title            = $request->en_title;
        $quote->ar_title            = $request->ar_title;
        $quote->en_desc             = $request->en_desc;
        $quote->ar_desc             = $request->ar_desc;
        $quote->cost                = $request->cost;
        $quote->months              = $request->months;
        $quote->sequence            = $request->sequence;
        $quote->status              = $request->status ? 1 : 0;
        $quote->drivers_count       = $request->drivers_count;
        
        $quote->save();

        return $quote;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

    public function status(Request $request , $id)
    {
        try {
            $data = Quote::selection()->findOrFail($id);
            
            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status ]);

            return new QuotessResource($data);
        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }
}
