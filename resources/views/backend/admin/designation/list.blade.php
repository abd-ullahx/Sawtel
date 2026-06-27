@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Designations') }}</span>
				<div>
					<a class="btn btn-danger btn-xs" href="{{ route('departments.index') }}"><i class="fas fa-list-ul"></i> {{ _lang('Departments') }}</a>
					<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Designation') }}" href="{{ route('designations.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="designations_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Designation') }}</th>
							<th>{{ _lang('Department') }}</th>
							<th>{{ _lang('Descriptions') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($designations as $designation)
					    <tr data-id="row_{{ $designation->id }}">
							<td class='name'>{{ $designation->name }}</td>
							<td class='department_id'>{{ $designation->department->name }}</td>
							<td class='descriptions'>{{ $designation->descriptions }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('designations.destroy', $designation['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('designations.edit', $designation['id']) }}" data-title="{{ _lang('Update Designation') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
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