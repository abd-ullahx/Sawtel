@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Staff Documents') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Document') }}" href="{{ route('staff_documents.create', $id) }}"><i class="ti-plus mr-2"></i>{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="staff_documents_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Employee ID') }}</th>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Document Name') }}</th>
							<th>{{ _lang('Document') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($staffDocuments as $staffDocument)
					    <tr data-id="row_{{ $staffDocument->id }}">
							<td class='staff_id'>{{ $staffDocument->staff->employee_id }}</td>
							<td class='staff_name'>{{ $staffDocument->staff->first_name.' '.$staffDocument->staff->last_name }}</td>
							<td class='name'>{{ $staffDocument->name }}</td>
							<td class='document'><a target="_blank" href="{{ asset('public/uploads/documents/'.$staffDocument->document) }}">{{ $staffDocument->document }}</a></td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('staff_documents.destroy', $staffDocument['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('staff_documents.edit', $staffDocument['id']) }}" data-title="{{ _lang('Update Document') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="ti-pencil-alt"></i>&nbsp;{{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash"></i>&nbsp;{{ _lang('Delete') }}</button>
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