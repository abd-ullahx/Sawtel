@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Leave Types') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal-2" data-title="{{ _lang('Add Leave Type') }}" href="{{ route('leave_types.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="leave_types_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Title') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($leavetypes as $leavetype)
					    <tr data-id="row_{{ $leavetype->id }}">
							<td class='title'>{{ $leavetype->title }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('leave_types.destroy', $leavetype['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('leave_types.edit', $leavetype['id']) }}" data-title="{{ _lang('Update Leave Type') }}" class="dropdown-item dropdown-edit ajax-modal-2"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
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