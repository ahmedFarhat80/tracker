<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Resources\Admin\CurrencyResource;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data = Currency::selection()->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);
        return CurrencyResource::collection($data);        
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
    public function store(CurrencyRequest $request){
        try{
            $currency = $this->process(new Currency , $request);
            return $this->show($currency->id);
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
            $data = Currency::selection()->findOrFail($id);
            return new CurrencyResource($data);
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
    public function update(CurrencyRequest $request, $id){
        try{
            $data = Currency::findOrFail($id);
            $currency = $this->process($data , $request);
            return $this->show($currency->id);
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
            $data = Currency::findOrFail($id);
            
            $data->delete();
            
            $data = [
                'status'    => 'success',
                'message'   => __('currency.delete_success'),
            ];
            
            return response($data, 200);
        }catch(\Exception $ex){
            return errorMessage($ex->getMessage(), 500);
        }
    }



    /**
     * store / update patients fields
     *
     * @param  Currency Object , Request
     * @return Currency
     */
    protected function process(Currency $currency , Request $request){
        
        $currency->en_title            = $request->en_title;
        $currency->ar_title            = $request->ar_title;
        $currency->symbol              = $request->symbol;
        $currency->sequence            = $request->sequence;
        $currency->status              = $request->status ? 1 : 0;
        $currency->exchange_rate       = $request->exchange_rate;
        
        $currency->save();

        return $currency;
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
            $data = Currency::selection()->findOrFail($id);
            
            $status =  $data->status  == 0 ? 1 : 0;

            $data->update(['status' => $status ]);

            return new CurrencyResource($data);            

        } catch (\Exception $ex) {
            return errorMessage($ex->getMessage(), 500);
        }
    }

    /**
     * Display a filter listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request){
        $search  = $request->search;
        $currencies  = Currency::selection()->where('en_title','like','%'.$search.'%')
                        ->orWhere('ar_title', 'like', "%{$search}%")
                        ->orWhere('symbol', 'like', "%{$search}%")
                        ->orWhere('exchange_rate', 'like', "%{$search}%")
                        ->orderBy('id', 'DESC')->paginate(PAGINATION_COUNT);

        // if($currencies->count() == 0)
        //     return noDataFound(__('currency.no_data'));

        return CurrencyResource::collection($currencies);
    }
}
