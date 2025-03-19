<?php

namespace App\Http\Controllers\Pms;

use App\Models\PmsModels\PaymentTerm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Payment Terms';
        $status=[
            PaymentTerm::ACTIVE=>PaymentTerm::ACTIVE,
            PaymentTerm::INACTIVE=>PaymentTerm::INACTIVE
        ];

        $type = [
            'paid' => "Paid",
            'due' => "Due",
        ];

        $paymentTerms=PaymentTerm::get();

        return view('pms.backend.pages.payment-term.index',compact('title','status','type','paymentTerms'));
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'term' => 'required|max:200',
            'percentage' => 'required|integer|max:100',
            'days' => 'required|integer|max:9999',
            'type' => 'required',
            'remarks' => 'nullable|max:300'
        ]);

        //$percentage = PaymentTerm::sum('percentage')+$request->percentage;
        $percentage = $request->percentage;
        if($percentage > 100){
            return $this->backWithError("Percentage limit Exceeded!");
        }

        $input = $request->except('_token');

        try{
            PaymentTerm::create($input);
            return $this->backWithSuccess('Payment Term created successfully');
        }catch (\Exception $e){

            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentTerm $paymentTerm)
    {
        $title='Edit Payment Term Information';
        $status=[
            PaymentTerm::ACTIVE=>PaymentTerm::ACTIVE,
            PaymentTerm::INACTIVE=>PaymentTerm::INACTIVE
        ];
        $type = [
            'paid' => "Paid",
            'due' => "Due",
        ];
        return view('pms.backend.pages.payment-term.show',compact('title','status','type','paymentTerm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentTerm $paymentTerm)
    {
        return response()->json($paymentTerm);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $this->validate($request, [
            'term' => 'required|max:200',
            'percentage' => 'required|integer|max:100',
            'days' => 'required|integer|max:9999',
            'type' => 'required',
            'remarks' => 'nullable|max:300'
        ]);

        // $percentage = PaymentTerm::whereNotIn('id', [$paymentTerm->id])->sum('percentage')+$request->percentage;
        $percentage = $request->percentage;
        if($percentage > 100){
            return $this->backWithError("Percentage limit Exceeded!");
        }

        $input = $request->except('password', '_token');

        try {
            $paymentTerm->update($input);

            return $this->backWithSuccess('Payment Term Data Update successfully');

        } catch (\Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentTerm $paymentTerm)
    {
        try{
            $paymentTerm->delete();
            return $this->backWithSuccess('Payment Term Data Update successfully');
        }catch(\Exception $e){
            return $this->backWithError($e->getMessage());

        }
    }
}
