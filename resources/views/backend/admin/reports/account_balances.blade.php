@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Account Balances') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">
				<div id="report">
					@php $date_format = get_date_format(); @endphp

					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<h5>{{ _lang('Account Balances') }}</h5>
						<p>{{ _lang('Date').': '. date($date_format) }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Account Name') }}</th>
								<th>{{ _lang('Currency') }}</th>
								<th>{{ _lang('Opening Date') }}</th>
								<th class="text-right">{{ _lang('Current Balance') }}</th>
							</thead>
							<tbody>
								@if(isset($accounts))
								@foreach($accounts as $account)
									<tr>
										<td>{{ $account->account_name }}</td>
										<td>{{ $account->currency }} ({{ currency_symbol($account->currency) }})</td>
										<td>{{ date($date_format, strtotime($account->opening_date)) }}</td>
										<td class='balance text-right'>{{ formatAmount($account->balance, currency_symbol($account->currency)) }}</td>											
									</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection