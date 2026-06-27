@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Accounts') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Account') }}" href="{{ route('accounts.create') }}"><i class="ti-plus mr-2"></i>{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="accounts_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Account Name') }}</th>
							<th>{{ _lang('Currency') }}</th>
							<th>{{ _lang('Opening Date') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($accounts as $account)
					    <tr data-id="row_{{ $account->id }}">
							<td class='account_name'>{{ $account->account_name }}</td>
							<td class='currency'>{{ $account->currency }} ({{ currency_symbol($account->currency) }})</td>
							<td class='opening_date'>{{ $account->opening_date }}</td>

							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('accounts.destroy', $account->id) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('accounts.edit', $account->id) }}" data-title="{{ _lang('Update Account') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="ti-pencil mr-1"></i> {{ _lang('Edit') }}</a>
										<a href="{{ route('accounts.show', $account->id) }}" data-title="{{ _lang('Account Details') }}" class="dropdown-item dropdown-view ajax-modal"><i class="ti-eye mr-1"></i> {{ _lang('View') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash mr-1"></i> {{ _lang('Delete') }}</button>
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