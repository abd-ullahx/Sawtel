@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Account Statement') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">

				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.account_statement') }}" autocomplete="off">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Account') }}</label>
									<select class="form-control auto-select" data-selected="{{ isset($account_id) ? $account_id : old('account_id') }}" name="account_id" required>
										<option value="">{{ _lang('Select One') }}</option>
										@foreach(\App\Models\Account::all() as $acc)
											<option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->currency }})</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-xl-2 col-lg-4">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter mr-1"></i>{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<h5>{{ isset($account) ? $account->account_name : '' }}</h5>
						<p>{{ _lang('Account Statement') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Description') }}</th>
								<th class="text-right">{{ _lang('DEBIT') }}</th>
								<th class="text-right">{{ _lang('CREDIT') }}</th>
								<th class="text-right">{{ _lang('Balance') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@php $date_format = get_date_format(); @endphp
								@foreach($report_data as $transaction)
									@if($transaction->balance == 0)
										@continue
									@endif
									<tr>
										<td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
										<td>{{ $transaction->description != '' ? $transaction->description : $transaction->category }}</td>
										<td class="text-right">{{ formatAmount($transaction->debit, currency_symbol($account->currency)) }}</td>
										<td class="text-right">{{ formatAmount($transaction->credit, currency_symbol($account->currency)) }}</td>
										<td class="text-right">{{ formatAmount($transaction->balance, currency_symbol($account->currency)) }}</td>							
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