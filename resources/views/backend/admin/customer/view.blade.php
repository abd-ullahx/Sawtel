@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<ul class="nav nav-tabs business-settings-tabs mb-4">
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) ? '' : 'active'  }}" href="{{ route('clients.show', $customer->id) }}"><i class="fas fa-tools mr-2"></i><span>{{ _lang('Overview') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'invoices' ? 'active' : ''  }}" href="{{ route('clients.show', $customer->id) }}?tab=invoices"><i class="fas fa-receipt mr-2"></i><span>{{ _lang('Invoices') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'quotations' ? 'active' : ''  }}" href="{{ route('clients.show', $customer->id) }}?tab=quotations"><i class="fas fa-receipt mr-2"></i><span>{{ _lang('Quotations') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'transactions' ? 'active' : ''  }}" href="{{ route('clients.show', $customer->id) }}?tab=transactions"><i class="fab fa-paypal mr-2"></i><span>{{ _lang('Transactions') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" href="{{ route('clients.edit', $customer->id) }}"><i class="far fa-edit mr-2"></i><span>{{ _lang('Edit Details') }}</span></a></li>
		</ul>

		@if(! isset($_GET['tab']))
		<div class="card">
			<div class="card-body">

				<div class="row">
					<div class="col-xl-3 col-md-6">
						<div class="card mb-4 primary-card dashboard-card">
							<div class="card-body">
								<div class="d-flex">
									<div class="flex-grow-1">
										<h5>{{ _lang('Total Invoices') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ $invoice->total_invoice }}</b></h4>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-md-6">
						<div class="card mb-4 warning-card dashboard-card">
							<div class="card-body">
								<div class="d-flex">
									<div class="flex-grow-1">
										<h5>{{ _lang('Total Invoice Amount') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_amount, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-md-6">
						<div class="card mb-4 success-card dashboard-card">
							<div class="card-body">
								<div class="d-flex">
									<div class="flex-grow-1">
										<h5>{{ _lang('Total Paid') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-xl-3 col-md-6">
						<div class="card mb-4 danger-card dashboard-card">
							<div class="card-body">
								<div class="d-flex">
									<div class="flex-grow-1">
										<h5>{{ _lang('Due Amount') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_amount - $invoice->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<table class="table table-bordered">
					<tr>
						<td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ profile_picture($customer->profile_picture) }}"></td>
					</tr>
					<tr><td>{{ _lang('Name') }}</td><td>{{ $customer->name }}</td></tr>
					<tr><td>{{ _lang('Company Name') }}</td><td>{{ $customer->company_name }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $customer->email }}</td></tr>
					<tr><td>{{ _lang('Mobile') }}</td><td>{{ $customer->mobile }}</td></tr>
					<tr><td>{{ _lang('Country') }}</td><td>{{ $customer->country }}</td></tr>
					<tr><td>{{ _lang('Currency') }}</td><td>{{ $customer->currency }} ({{ currency_symbol($customer->currency) }})</td></tr>
					<tr><td>{{ _lang('Vat ID') }}</td><td>{{ $customer->vat_id }}</td></tr>
					<tr><td>{{ _lang('Reg No') }}</td><td>{{ $customer->reg_no }}</td></tr>
					<tr><td>{{ _lang('City') }}</td><td>{{ $customer->city }}</td></tr>
					<tr><td>{{ _lang('State') }}</td><td>{{ $customer->state }}</td></tr>
					<tr><td>{{ _lang('ZIP') }}</td><td>{{ $customer->zip }}</td></tr>
					<tr><td>{{ _lang('Address') }}</td><td>{{ $customer->address }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $customer->remarks }}</td></tr>
				</table>
			</div>
		</div>
		@else
			<div id="nav-transactions">
				@include('backend.admin.customer.tabs.'.$_GET['tab'])
			</div>
		@endif

	</div>
</div>
@endsection


