@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Salary Scales') }}</span>
				<div class="d-sm-flex align-items-center mt-2 mt-sm-0">
					<div class="dropdown">
						<a class="btn btn-outline-primary btn-xs dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
						{{ $department_id == '' ? _lang('All Department') : $departments->find($department_id)->name }}
						</a>

						<div class="dropdown-menu">
							<a class="dropdown-item {{ $department_id == '' ? 'active' : '' }}" href="{{ route('salary_scales.index') }}">{{ _lang('All Department') }}</a>
							@foreach($departments as $department)
							<a class="dropdown-item {{ $department_id == $department->id ? 'active' : '' }}" href="{{ route('salary_scales.filter_by_department', $department->id) }}">{{ $department->name }}</a>
							@endforeach
						</div>
					</div>
					<a class="btn btn-primary btn-xs ml-0 ml-sm-1" href="{{ route('salary_scales.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
				</div>
			</div>
			<div class="card-body">
				<table id="salary_scales_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Department') }}</th>
							<th>{{ _lang('Designation') }}</th>
							<th>{{ _lang('Grade') }}</th>
							<th>{{ _lang('Basic Salary') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($salaryscales as $salaryscale)
					    <tr data-id="row_{{ $salaryscale->id }}">
							<td class='department_id'>{{ $salaryscale->department->name }}</td>
							<td class='designation_id'>{{ $salaryscale->designation->name }}</td>
							<td class='grade_number'>{{ _lang('Grade').' '.$salaryscale->grade_number }}</td>
							<td class='basic_salary'>{{ formatAmount($salaryscale->basic_salary, currency_symbol(request()->activeBusiness->currency)) }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  	{{ _lang('Action') }}
								  </button>
								  <form action="{{ route('salary_scales.destroy', $salaryscale['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('salary_scales.edit', $salaryscale['id']) }}" class="dropdown-item dropdown-edit dropdown-edit"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<a href="{{ route('salary_scales.show', $salaryscale['id']) }}" class="dropdown-item dropdown-view dropdown-view"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
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