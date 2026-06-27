<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Vendor;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class VendorController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$assets = ['datatable'];
		return view('backend.admin.vendor.list', compact('assets'));
	}

	public function get_table_data() {

		$vendors = Vendor::select('vendors.*')
			->orderBy("vendors.id", "desc");

		return Datatables::eloquent($vendors)
			->editColumn('profile_picture', function ($vendor) {
				return '<img src="' . profile_picture($vendor->profile_picture) . '" class="thumb-sm img-thumbnail rounded-circle">';
			})
			->editColumn('currency', function ($vendor) {
				return $vendor->currency . ' (' . currency_symbol($vendor->currency) . ')';
			})
			->addColumn('action', function ($vendor) {
				return '<div class="dropdown text-center">'
				. '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
				. '</button>'
				. '<div class="dropdown-menu">'
				. '<a class="dropdown-item" href="' . route('vendors.edit', $vendor['id']) . '"><i class="ti-pencil"></i> ' . _lang('Edit') . '</a>'
				. '<a class="dropdown-item" href="' . route('vendors.show', $vendor['id']) . '"><i class="ti-eye"></i>  ' . _lang('Details') . '</a>'
				. '<form action="' . route('vendors.destroy', $vendor['id']) . '" method="post">'
				. csrf_field()
				. '<input name="_method" type="hidden" value="DELETE">'
				. '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
					. '</form>'
					. '</div>'
					. '</div>';
			})
			->setRowId(function ($vendor) {
				return "row_" . $vendor->id;
			})
			->rawColumns(['profile_picture', 'action'])
			->make(true);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		$alert_col = 'col-lg-10 offset-lg-1';
		if (!$request->ajax()) {
			return view('backend.admin.vendor.create', compact('alert_col'));
		} else {
			return view('backend.admin.vendor.modal.create');
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
			'name' => 'required|max:50',
			'email' => 'nullable|email|unique:vendors|max:191',
			'currency' => 'required',
			'profile_picture' => 'nullable|image|max:2048',
		]);

		if ($validator->fails()) {
			if ($request->ajax()) {
				return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
			} else {
				return redirect()->route('vendors.create')
					->withErrors($validator)
					->withInput();
			}
		}

		$profile_picture = 'default.png';
		if ($request->hasfile('profile_picture')) {
			$file = $request->file('profile_picture');
			$profile_picture = time() . $file->getClientOriginalName();
			$file->move(public_path() . "/uploads/profile/", $profile_picture);
		}

		$vendor = new Vendor();
		$vendor->name = $request->input('name');
		$vendor->company_name = $request->input('company_name');
		$vendor->email = $request->input('email');
		$vendor->password = $request->input('password');
		$vendor->registration_no = $request->input('registration_no');
		$vendor->vat_id = $request->input('vat_id');
		$vendor->mobile = $request->input('mobile');
		$vendor->country = $request->input('country');
		$vendor->currency = $request->input('currency');
		$vendor->city = $request->input('city');
		$vendor->state = $request->input('state');
		$vendor->zip = $request->input('zip');
		$vendor->address = $request->input('address');
		$vendor->remarks = $request->input('remarks');
		$vendor->profile_picture = $profile_picture;

		$vendor->save();

		if (!$request->ajax()) {
			return redirect()->route('vendors.index')->with('success', _lang('Saved Successfully'));
		} else {
			return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $vendor, 'table' => '#vendors_table']);
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$data = array();
		$data['alert_col'] = 'col-lg-10 offset-lg-1';
		$data['vendor'] = Vendor::find($id);

		if (!isset($_GET['tab'])) {
			$data['purchase'] = Purchase::selectRaw('COUNT(id) as total_bill, SUM(grand_total) as total_amount, sum(paid) as total_paid')
				->where('vendor_id', $id)
				->first();
		}

		if (isset($_GET['tab']) && $_GET['tab'] == 'purchases') {
			$data['purchases'] = Purchase::where('vendor_id', $id)
				->orderBy('purchase_date', 'desc')
				->paginate(15);
			$data['purchases']->withPath('?tab=' . $_GET['tab']);
		}

		if (isset($_GET['tab']) && $_GET['tab'] == 'transactions') {
			$data['transactions'] = Transaction::where('ref_id', '!=', null)
				->where('ref_type', 'purchase')
				->whereHas('purchase', function ($query) use ($id) {
					return $query->where('vendor_id', $id);
				})
				->orderBy('trans_date', 'desc')
				->paginate(15);
			$data['transactions']->withPath('?tab=' . $_GET['tab']);
		}

		return view('backend.admin.vendor.view', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
		$alert_col = 'col-lg-10 offset-lg-1';
		$vendor = Vendor::find($id);
		return view('backend.admin.vendor.edit', compact('vendor', 'id', 'alert_col'));
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
			'name' => 'required|max:50',
			'email' => [
				'nullable',
				'email',
				Rule::unique('vendors')->ignore($id),
			],
			'profile_picture' => 'nullable|image|max:2048',
		]);

		if ($validator->fails()) {
			if ($request->ajax()) {
				return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
			} else {
				return redirect()->route('vendors.edit', $id)
					->withErrors($validator)
					->withInput();
			}
		}

		if ($request->hasfile('profile_picture')) {
			$file = $request->file('profile_picture');
			$profile_picture = time() . $file->getClientOriginalName();
			$file->move(public_path() . "/uploads/profile/", $profile_picture);
		}

		$vendor = Vendor::find($id);
		$vendor->name = $request->input('name');
		$vendor->company_name = $request->input('company_name');
		$vendor->email = $request->input('email');
		$vendor->password = $request->input('password');
		$vendor->registration_no = $request->input('registration_no');
		$vendor->vat_id = $request->input('vat_id');
		$vendor->mobile = $request->input('mobile');
		$vendor->country = $request->input('country');
		$vendor->city = $request->input('city');
		$vendor->state = $request->input('state');
		$vendor->zip = $request->input('zip');
		$vendor->address = $request->input('address');
		$vendor->remarks = $request->input('remarks');

		if ($request->hasfile('profile_picture')) {
			$vendor->profile_picture = $profile_picture;
		}

		$vendor->save();

		if (!$request->ajax()) {
			return redirect()->route('vendors.index')->with('success', _lang('Updated Successfully'));
		} else {
			return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $vendor, 'table' => '#vendors_table']);
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$vendor = Vendor::find($id);
		$vendor->delete();
		return redirect()->route('vendors.index')->with('success', _lang('Deleted Successfully'));
	}
}