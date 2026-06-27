@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Product Units') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal-2" data-title="{{ _lang('Add Product Unit') }}" href="{{ route('product_units.create') }}"><i class="ti-plus mr-1"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="product_units_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Unit Name') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($productunits as $productunit)
					    <tr data-id="row_{{ $productunit->id }}">
							<td class='unit'>{{ $productunit->unit }}</td>					
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('product_units.destroy', $productunit['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('product_units.edit', $productunit['id']) }}" data-title="{{ _lang('Update Product Unit') }}" class="dropdown-item dropdown-edit ajax-modal-2"><i class="ti-pencil"></i> {{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
							</td>
					    </tr>
					    @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection