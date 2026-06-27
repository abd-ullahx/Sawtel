@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Transaction Methods') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('New Method') }}" href="{{ route('transaction_methods.create') }}"><i class="ti-plus"></i>{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="transaction_methods_table" class="table data-table">
					<thead>
					    <tr>
							<th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($transactionmethods as $transactionmethod)
					    <tr data-id="row_{{ $transactionmethod->id }}">
							<td class='name'>{{ $transactionmethod->name }}</td>
							<td class='status'>{!! xss_clean(status($transactionmethod->status)) !!}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('transaction_methods.destroy', $transactionmethod['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('transaction_methods.edit', $transactionmethod['id']) }}" data-title="{{ _lang('Update Method') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="ti-pencil mr-1"></i>{{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash mr-1"></i>{{ _lang('Delete') }}</button>
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