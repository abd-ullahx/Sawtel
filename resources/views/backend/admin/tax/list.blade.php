@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Taxes') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Tax') }}" href="{{ route('taxes.create') }}"><i class="ti-plus mr-1"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="taxes_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Tax Rate') }}</th>
							<th>{{ _lang('Tax Number') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($taxs as $tax)
					    <tr data-id="row_{{ $tax->id }}">
							<td class='name'>{{ $tax->name }}</td>
							<td class='rate'>{{ $tax->rate }} %</td>
							<td class='tax_number'>{{ $tax->tax_number }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('taxes.destroy', $tax['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('taxes.edit', $tax['id']) }}" data-title="{{ _lang('Update Tax Rate') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="ti-pencil"></i> {{ _lang('Edit') }}</a>
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