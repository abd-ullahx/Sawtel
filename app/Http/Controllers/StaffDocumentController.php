<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffDocumentController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $assets         = ['datatable'];
        $staffDocuments = EmployeeDocument::where('employee_id', $id)->orderBy('id', 'desc')->get();
        return view('backend.admin.staff_documents.list', compact('staffDocuments', 'id', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id, Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.staff_documents.modal.create', compact('id'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'name'        => 'required',
            'document'    => 'required|mimes:png,jpg,jpeg,pdf|max:10000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('staff_documents.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $document = '';
        if ($request->hasfile('document')) {
            $file     = $request->file('document');
            $document = time() . uniqid() . '-' . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/documents/", $document);
        }

        $staffDocument              = new EmployeeDocument();
        $staffDocument->employee_id = $request->input('employee_id');
        $staffDocument->name        = $request->input('name');
        $staffDocument->document    = $document;

        $staffDocument->save();

        //Prefix Output
        $staffDocument->document = '<a target="_blank" href="' . asset('public/uploads/documents/' . $staffDocument->document) . '">' . $staffDocument->document . '</a>';

        if (!$request->ajax()) {
            return redirect()->route('staff_documents.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $staffDocument, 'table' => '#staff_documents_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $staffDocument = EmployeeDocument::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.staff_documents.modal.edit', compact('staffDocument', 'id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'name'        => 'required',
            'document'    => 'nullable|mimes:png,jpg,jpeg,pdf|max:10000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('staff_documents.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('document')) {
            $file     = $request->file('document');
            $document = time() . uniqid() . '-' . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/documents/", $document);
        }

        $staffDocument              = EmployeeDocument::find($id);
        $staffDocument->employee_id = $request->input('employee_id');
        $staffDocument->name        = $request->input('name');
        if ($request->hasfile('document')) {
            $staffDocument->document = $document;
        }

        $staffDocument->save();

        //Prefix Output
        $staffDocument->document = '<a target="_blank" href="' . asset('public/uploads/documents/' . $staffDocument->document) . '">' . $staffDocument->document . '</a>';

        if (!$request->ajax()) {
            return back()->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $staffDocument, 'table' => '#staff_documents_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $document = EmployeeDocument::find($id);
        unlink(public_path('uploads/documents/' . $document->document));
        $document->delete();
        return back()->with('success', _lang('Deleted Successfully'));
    }

}