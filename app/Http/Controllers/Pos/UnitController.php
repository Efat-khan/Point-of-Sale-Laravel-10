<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Auth;
use Illuminate\support\Carbon;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:unit-list|unit-create|unit-edit|unit-delete'], ['only' => ['UnitAll']]);
        $this->middleware(['permission:unit-create'], ['only' => ['UnitStore']]);
        $this->middleware(['permission:unit-edit'], ['only' => ['UnitEdit', 'UnitUpdate']]);
        $this->middleware(['permission:unit-delete'], ['only' => ['UnitDelete']]);
    }
    public function UnitAll()
    {
        $units = Unit::latest()->get();
        return view('backend.unit.unit_all', compact('units'));
    } // End Method

    // Save unit insert form to Database
    public function UnitStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->passes()) {
            Unit::insert([
                'name' => $request->name,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Unit Inserted Successfully',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Fill unit name field.',
                'alert-type' => 'error',
                'errors' => $validator->errors()
            );
        }
        return redirect()->route('unit.all')->with($notification);
    } //End Method

    // Unit Edit form
    public function UnitEdit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('backend.unit.unit_edit', compact('unit'));
    } //End Method

    // Unit edited data save to database
    public function UnitUpdate(Request $request)
    {
        $unit_id = $request->id;
        Unit::findOrFail($unit_id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Unit Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('unit.all')->with($notification);
    } //End Method

    // Unit Delete from database
    public function UnitDelete($id)
    {
        Unit::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Unit Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method
}
