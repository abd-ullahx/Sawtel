<?php

namespace App\Http\Controllers;

use App\Models\CustomerProductPrice;
use App\Models\Product;
use DataTables;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class ProductController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = ['datatable'];
        return view('backend.admin.product.list', compact('assets'));
    }

    /**
     * @throws Exception
     */
    public function get_table_data()
    {
        $products = Product::select('products.*')
            ->with('product_unit')
            ->orderBy("products.name");

        return Datatables::eloquent($products)
            ->editColumn('image', function ($product) {
                if ($product->image) {
                    return '<img src="' . asset('public/uploads/media/' . $product->image) . '" class="thumb-sm img-thumbnail">';
                } else {
                    return '<img src="' . asset('public/uploads/media/default.png') . '" class="thumb-sm img-thumbnail">';
                }
            })
            ->editColumn('bar_code', function ($product) {
                if (!is_null($product->bar_code)) {
                    return '<img src="https://barcode.tec-it.com/barcode.ashx?data=' . $product->bar_code . '%0A&translate-esc=on&code=Code128&translate-esc=on.jpg" 
                        alt="barcode" width="100px"   />';
                }
                return '';
            })
            ->editColumn('item_code', function ($product) {
                return ucwords($product->item_code);
            })
            ->editColumn('type', function ($product) {
                return ucwords($product->type);
            })
            ->editColumn('purchase_cost', function ($product) {
                return '<div class="text-right">' . formatAmount($product->purchase_cost, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('selling_price', function ($product) {
                return '<div class="text-right">' . formatAmount($product->selling_price, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('stock', function ($product) {
                return '<div class="text-center">' . $product->stock . ' ' . $product->product_unit->unit . '</div>';
            })
            ->editColumn('status', function ($product) {
                return '<div class="text-center">' . status($product->status) . '</div>';
            })
            ->addColumn('action', function ($product) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('products.edit', $product['id']) . '"><i class="ti-pencil"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('products.show', $product['id']) . '"><i class="ti-eye"></i>  ' . _lang('View') . '</a>'
                    . '<form action="' . route('products.destroy', $product['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($product) {
                return "row_" . $product->id;
            })
            ->rawColumns(['image', 'purchase_cost', 'selling_price', 'stock', 'status', 'action', 'bar_code'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $alert_col = 'col-lg-10 offset-lg-1';

        if (!$request->ajax()) {
            return view('backend.admin.product.create', compact('alert_col'));
        } else {
            return view('backend.admin.product.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'item_code' => 'nullable',
            'bar_code' => 'nullable',
            'deutsch' => 'required',
            'weight_per_item' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|max:2048',
            'stock' => 'required|numeric',
            'allow_for_selling' => 'required_without_all:allow_for_purchasing',
            'allow_for_purchasing' => 'nullable',
            'purchase_cost' => 'nullable|required_if:allow_for_purchasing,1|numeric',
            'selling_price' => 'nullable|required_if:allow_for_selling,1|numeric',
            'income_category_id' => 'required_if:allow_for_selling,1',
            'expense_category_id' => 'required_if:allow_for_purchasing,1',
            'status' => 'required',
            'stock_management' => 'required',
            'customer_item_code' => 'nullable'
        ], [
            'allow_for_selling.required_without_all' => 'You need to choose at least for selling or purchasing',
            'purchase_cost.required_if' => 'Purchase Cost is required',
            'selling_price.required_if' => 'Selling Cost is required',
            'income_category_id.required_if' => 'Income Category is required',
            'expense_category_id.required_if' => 'Expense Category is required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('products.create')->withErrors($validator)->withInput();
            }
        }

        $image = 'default.png';
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $image);
        }

        $product = new Product();
        $product->name = $request->input('name');
        $product->item_code = $request->input('item_code');
        $product->bar_code = $request->input('bar_code');
        $product->deutsch = $request->input('deutsch');
        $product->weight_per_item = $request->input('weight_per_item');
        $product->type = $request->input('type');
        $product->product_unit_id = $request->input('product_unit_id');
        $product->purchase_cost = $request->allow_for_purchasing == 1 ? $request->input('purchase_cost') : null;
        $product->selling_price = $request->allow_for_selling == 1 ? $request->input('selling_price') : null;
        $product->image = $image;
        $product->descriptions = $request->input('descriptions');
        $product->stock = $request->input('stock');
        $product->allow_for_selling = $request->input('allow_for_selling');
        $product->allow_for_purchasing = $request->input('allow_for_purchasing');
        $product->income_category_id = $request->allow_for_selling == 1 ? $request->income_category_id : null;
        $product->expense_category_id = $request->allow_for_purchasing == 1 ? $request->expense_category_id : null;

        $product->status = $request->input('status');
        $product->stock_management = $request->stock_management;

        $product->save();

        if (!$request->ajax()) {
            return redirect()->route('products.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $product, 'table' => '#products_table']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$request->ajax()) {
            return view('backend.admin.product.view', compact('product', 'id'));
        } else {
            return view('backend.admin.product.modal.view', compact('product', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $alert_col = 'col-lg-10 offset-lg-1';
        $product = Product::find($id);
        return view('backend.admin.product.edit', compact('product', 'id', 'alert_col'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonRespons
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'item_code' => 'nullable',
            'name' => 'required',
            'bar_code' => 'nullable',
            'deutsch' => 'required',
            'weight_per_item' => 'required',
            'type' => 'required',
            'image' => 'nullable|image|max:2048',
            'stock' => 'required|numeric',
            'allow_for_selling' => 'required_without_all:allow_for_purchasing',
            'allow_for_purchasing' => 'nullable',
            'purchase_cost' => 'nullable|required_if:allow_for_purchasing,1|numeric',
            'selling_price' => 'nullable|required_if:allow_for_selling,1|numeric',
            'income_category_id' => 'required_if:allow_for_selling,1',
            'expense_category_id' => 'required_if:allow_for_purchasing,1',
            'status' => 'required',
            'stock_management' => 'required',
            'customer_item_code' => 'nullable'
        ], [
            'allow_for_selling.required_without_all' => 'You need to choose at least for selling or purchasing',
            'purchase_cost.required_if' => 'Purchase Cost is required',
            'selling_price.required_if' => 'Selling Cost is required',
            'income_category_id.required_if' => 'Income Category is required',
            'expense_category_id.required_if' => 'Expense Category is required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('products.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $image);
        }

        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->item_code = $request->input('item_code');
        $product->bar_code = $request->input('bar_code');
        $product->type = $request->input('type');
        $product->product_unit_id = $request->input('product_unit_id');
        $product->purchase_cost = $request->allow_for_purchasing == 1 ? $request->input('purchase_cost') : null;
        $product->selling_price = $request->allow_for_selling == 1 ? $request->input('selling_price') : null;

        if ($request->hasfile('image')) {
            $product->image = $image;
        }

        $product->descriptions = $request->input('descriptions');
        $product->stock = $request->input('stock');
        $product->allow_for_selling = $request->input('allow_for_selling');
        $product->allow_for_purchasing = $request->input('allow_for_purchasing');
        $product->income_category_id = $request->allow_for_selling == 1 ? $request->income_category_id : null;
        $product->expense_category_id = $request->allow_for_purchasing == 1 ? $request->expense_category_id : null;

        $product->status = $request->input('status');
        $product->customer_item_code = $request->input('customer_item_code');
        $product->stock_management = $request->stock_management;

        $product->save();

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
            //return redirect()->route('products.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $product, 'table' => '#products_table']);
        }

    }

    public function getProducts($type)
    {
        if ($type == 'sell') {
            $products = Product::active()->where('allow_for_selling', 1)->get();
        } else if ($type == 'buy') {
            $products = Product::active()->where('allow_for_purchasing', 1)->get();
        }
        return $products;
    }

    public function getProduct($id)
    {
        $product = Product::query()
            ->where('status', 1)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('item_code', $id);
            })->first();

        if ($product && request('customer_id')) {
            $prices = CustomerProductPrice::where('customer_id', request('customer_id'))->where('product_id', $product->id)->first();
            if ($prices) {
                $product->selling_price = $prices->selling_price;
            }
        }

        $decimal_place = get_business_option('decimal_places', 2);
        return response()->json(array('product' => $product, 'decimal_place' => $decimal_place));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', _lang('Deleted Successfully'));
    }

}