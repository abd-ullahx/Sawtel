@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Departments') }}</span>
				<div class="d-sm-flex align-items-center mt-2 mt-sm-0">
					<a class="btn btn-info btn-xs" href="{{ route('designations.index') }}"><i class="fas fa-list-ul"></i> {{ _lang('Designations') }}</a>
					<a class="btn btn-primary btn-xs ajax-modal ml-0 ml-sm-1" data-title="{{ _lang('Add Department') }}" href="{{ route('departments.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="departments_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Descriptions') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($departments as $department)
					    <tr data-id="row_{{ $department->id }}">
							<td class='name'>{{ $department->name }}</td>
							<td class='descriptions'>{{ $department->descriptions }}</td>					
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('departments.destroy', $department['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('departments.edit', $department['id']) }}" data-title="{{ _lang('Update Department') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<a href="{{ route('departments.show', $department['id']) }}" data-title="{{ _lang('Department Details') }}" class="dropdown-item dropdown-view ajax-modal"><i class="fas fa-eye"></i> {{ _lang('Details') }}</a>
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