<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{

    public function index()
    {
        $assets = ['datatable'];
        return view('backend.admin.customer.list', compact('assets'));
    }

    public function get_table_data()
    {
        $customers = Customer::select('customers.*')
            ->orderBy("customers.id", "desc");

        return Datatables::eloquent($customers)
            ->editColumn('profile_picture', function ($customer) {
                return '<img src="' . profile_picture($customer->profile_picture) . '" class="thumb-sm img-thumbnail rounded-circle">';
            })
            ->editColumn('currency', function ($customer) {
                return $customer->currency . ' (' . currency_symbol($customer->currency) . ')';
            })
            ->addColumn('action', function ($customer) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('client.add-product-prices', $customer['id']) . '"><i class="ti-plus mr-1"></i> ' . _lang('Add prices') . '</a>'
                    . '<a class="dropdown-item" href="' . route('clients.edit', $customer['id']) . '"><i class="ti-pencil"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('clients.show', $customer['id']) . '"><i class="ti-eye"></i>  ' . _lang('Details') . '</a>'
                    . '<form action="' . route('clients.destroy', $customer['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($customer) {
                return "row_" . $customer->id;
            })
            ->rawColumns(['profile_picture', 'action'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $alert_col = 'col-lg-8 offset-lg-2';

        if (!$request->ajax()) {
            return view('backend.admin.customer.create', compact('alert_col'));
        } else {
            return view('backend.admin.customer.modal.create', compact('alert_col'));
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'nullable|required_if:login_status,1|email|unique:customers|max:191',
            'currency' => 'required',
            'profile_picture' => 'nullable|image|max:2048',
        ], [
            'email.required_if' => _lang('Email address is required'),
            'password.required_if' => _lang('Password field is required'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('clients.create')
                ->withErrors($validator)
                ->withInput();
        }

        $profile_picture = 'default.png';

        if ($request->hasfile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profile_picture = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/profile/", $profile_picture);
        }

        $customer = new Customer();
        $customer->name = $request->input('name');
        $customer->company_name = $request->input('company_name');
        $customer->email = $request->input('email');

        if ($request->password != null) {
            $customer->password = Hash::make($request->password);
        }

        $customer->mobile = $request->input('mobile');
        $customer->country = $request->input('country');
        $customer->currency = $request->input('currency');
        $customer->vat_id = $request->input('vat_id');
        $customer->reg_no = $request->input('reg_no');
        $customer->city = $request->input('city');
        $customer->state = $request->input('state');
        $customer->zip = $request->input('zip');
        $customer->address = $request->input('address');
        $customer->remarks = $request->input('remarks');
        $customer->profile_picture = $profile_picture;
        $customer->login_status = false;

        $customer->save();

        if (!$request->ajax()) {
            return redirect()->route('clients.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $customer, 'table' => '#customers_table']);
        }

    }

    public function show($id)
    {
        $data = array();
        $data['alert_col'] = 'col-lg-8 offset-lg-2';
        $data['customer'] = Customer::find($id);

        if (!isset($_GET['tab'])) {
            $data['invoice'] = Invoice::selectRaw('COUNT(id) as total_invoice, SUM(grand_total) as total_amount, sum(paid) as total_paid')
                ->where('customer_id', $id)
                ->where('is_recurring', 0)
                ->where('status', '!=', 0)
                ->where('status', '!=', 99)
                ->first();
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'invoices') {
            $data['invoices'] = Invoice::where('customer_id', $id)
                ->where('is_recurring', 0)
                ->orderBy('invoice_date', 'desc')
                ->paginate(15);
            $data['invoices']->withPath('?tab=' . $_GET['tab']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'quotations') {
            $data['quotations'] = Quotation::where('customer_id', $id)
                ->orderBy('quotation_date', 'desc')
                ->paginate(15);
            $data['quotations']->withPath('?tab=' . $_GET['tab']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'transactions') {
            $data['transactions'] = Transaction::where('ref_id', '!=', null)
                ->where('ref_type', 'invoice')
                ->whereHas('invoice', function ($query) use ($id) {
                    return $query->where('customer_id', $id);
                })
                ->orderBy('trans_date', 'desc')
                ->paginate(15);

            $data['transactions']->withPath('?tab=' . $_GET['tab']);
        }

        return view('backend.admin.customer.view', $data);
    }

    public function edit(Request $request, $id)
    {
        $alert_col = 'col-lg-8 offset-lg-2';
        $customer = Customer::find($id);
        return view('backend.admin.customer.edit', compact('customer', 'id', 'alert_col'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => [
                'nullable',
                'required_if:login_status,1',
                'email',
                'max:191',
                Rule::unique('customers')->ignore($id),
            ],
            'password' => 'nullable|min:6',
            'profile_picture' => 'nullable|image|max:2048',
        ], [
            'email.required_if' => _lang('Email address is required'),
            'password.required_if' => _lang('Password field is required'),
        ], [

        ]);

        if ($validator->fails()) {
            return redirect()->route('clients.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasfile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profile_picture = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/profile/", $profile_picture);
        }

        $customer = Customer::find($id);
        $customer->name = $request->input('name');
        $customer->company_name = $request->input('company_name');
        $customer->email = $request->input('email');

        if ($request->password != null) {
            $customer->password = Hash::make($request->password);
        }

        if ($request->login_status == 1 && $request->password == null && $customer->password == null) {
            $validator->errors()->add('password', _lang('Password field is required'));
            return redirect()->route('clients.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $customer->mobile = $request->input('mobile');
        $customer->country = $request->input('country');
        $customer->vat_id = $request->input('vat_id');
        $customer->reg_no = $request->input('reg_no');
        $customer->city = $request->input('city');
        $customer->state = $request->input('state');
        $customer->zip = $request->input('zip');
        $customer->address = $request->input('address');
        $customer->remarks = $request->input('remarks');

        if ($request->hasfile('profile_picture')) {
            $customer->profile_picture = $profile_picture;
        }

        $customer->login_status = $request->login_status;

        $customer->save();

        if (!$request->ajax()) {
            return redirect()->route('clients.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $customer, 'table' => '#customers_table']);
        }

    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect()->route('clients.index')->with('success', _lang('Deleted Successfully'));
    }

    public function addProductPrices($id)
    {
        $client = Customer::query()
            ->with([
                'customerProductPrices' => fn($query) => $query->with(['product' => fn($query) => $query->select(['id', 'name', 'item_code'])])->latest('id')
            ])
            ->where('business_id', request()->activeBusiness->id)
            ->findOrFail($id);

        $existingProducts = $client->customerProductPrices()->pluck('product_id');

        $products = Product::query()
            ->select(['id', 'name'])->where('business_id', $client->business_id)
            ->whereNotIn('id', $existingProducts)
            ->orderBy('name')
            ->get();

        return view('backend.admin.customer.add-product-prices', compact('client', 'products'));
    }

    public function storeProductPrices(Request $request, $id)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id']
        ]);

        $client = Customer::query()
            ->where('business_id', request()->activeBusiness->id)
            ->findOrFail($id);

        $product = Product::query()
            ->where('business_id', request()->activeBusiness->id)
            ->where('id', $request->product_id)
            ->firstOrFail();

        $product->customerProductPrices()->updateOrCreate([
            'customer_id' => $client->id,
            'product_id' => $product->id,
        ], [
            'selling_price' => number_format($product->selling_price, 2),
            'customer_item_code' => null,
        ]);

        return redirect()->route('client.add-product-prices', $client->id)->with('success', _lang('Successfully added'));
    }

    public function storeSingleProductPrice(Request $request, $customerId, $priceId)
    {
        $request->validate([
            'customer_item_code' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        $customerProductPrice = CustomerProductPrice::query()
            ->where('customer_id', $customerId)
            ->findOrFail($priceId);

        $customerProductPrice->update([
            'selling_price' => $request->input('selling_price'),
            'customer_item_code' => $request->input('customer_item_code'),
        ]);

        return redirect()->route('client.add-product-prices', $customerProductPrice->customer_id)->with('success', _lang('Successfully updated'));
    }

    public function deleteSingleProductPrice($customerId, $priceId)
    {
        CustomerProductPrice::query()
            ->where('customer_id', $customerId)
            ->findOrFail($priceId)
            ->delete();

        return redirect()->route('client.add-product-prices', $customerId)->with('success', _lang('Successfully deleted'));
    }

}
