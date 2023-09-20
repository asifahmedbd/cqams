<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute;

class AttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $all_attributes = Attribute::all();
        //dd($all_attributes);
        return view('admin.attributes.index', compact('all_attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'attribute_name' => 'required',
        ]);

        $admin_info = session("admin_info");

        $attribute_model = new Attribute();
        $attribute_model->attribute_name = $request->input('attribute_name');
        $attribute_model->attribute_value = json_encode($request->input('attribute_values'));
        $attribute_model->created_by = $admin_info['id'];

        $attribute_model->save();

        // Alert::success('Attribute Created Successfully!', 'success');
        return redirect()->route('attributes.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
