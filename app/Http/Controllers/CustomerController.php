<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Supplier;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:customers',
            'nic' => 'required|min:3|',
            'address' => 'required|min:3',
            'mobile' => 'required|min:3|digits:10',
            'email' => 'unique:customers',
            'vnumber' => '',
            'previous_balance' => '',

        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->nic = $request->nic;
        $customer->address = $request->address;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->vnumber = $request->vnumber;
        $customer->previous_balance = $request->previous_balance;
        $customer->save();

        return redirect()->back()->with('message', 'Customer added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'nic' => 'required|min:3|',
            'address' => 'required|min:3',
            'mobile' => 'required|min:3|digits:10',
            'vnumber' => '',
            'previous_balance' => '',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->name = $request->name;
        $customer->nic = $request->nic;
        $customer->address = $request->address;
        $customer->mobile = $request->mobile;
        $customer->vnumber = $request->vnumber;
        $customer->previous_balance = $request->previous_balance;
        $customer->save();

        // return redirect()->back()->with('message', 'Customer Updated Successfully');
        return redirect()->route('invoice.create', ['customer_id' => $customer->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect()->back();

    }
}
