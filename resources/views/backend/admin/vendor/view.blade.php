@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<ul class="nav nav-tabs business-settings-tabs mb-4">
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) ? '' : 'active'  }}" href="{{ route('vendors.show', $vendor->id) }}"><i class="fas fa-tools mr-2"></i><span>{{ _lang('Overview') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'purchases' ? 'active' : ''  }}" href="{{ route('vendors.show', $vendor->id) }}?tab=purchases"><i class="fas fa-receipt mr-2"></i><span>{{ _lang('Purchases') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'transactions' ? 'active' : ''  }}" href="{{ route('vendors.show', $vendor->id) }}?tab=transactions"><i class="fab fa-paypal mr-2"></i><span>{{ _lang('Transactions') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" href="{{ route('vendors.edit', $vendor->id) }}"><i class="far fa-edit mr-2"></i><span>{{ _lang('Edit Details') }}</span></a></li>
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
										<h5>{{ _lang('Total Bill') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ $purchase->total_bill }}</b></h4>
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
										<h5>{{ _lang('Total Amount') }}</h5>
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($purchase->total_amount, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
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
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($purchase->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
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
										<h4 class="pt-1 mb-0"><b>{{ formatAmount($purchase->total_amount - $purchase->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			    <table class="table table-bordered">
					<tr>
                        <td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ profile_picture($vendor->profile_picture) }}"></td>
                    </tr>
					<tr><td>{{ _lang('Name') }}</td><td>{{ $vendor->name }}</td></tr>
					<tr><td>{{ _lang('Company Name') }}</td><td>{{ $vendor->company_name }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $vendor->email }}</td></tr>
					<tr><td>{{ _lang('Registration No') }}</td><td>{{ $vendor->registration_no }}</td></tr>
					<tr><td>{{ _lang('Vat ID') }}</td><td>{{ $vendor->vat_id }}</td></tr>
					<tr><td>{{ _lang('Mobile') }}</td><td>{{ $vendor->mobile }}</td></tr>
					<tr><td>{{ _lang('Country') }}</td><td>{{ $vendor->country }}</td></tr>
					<tr><td>{{ _lang('Currency') }}</td><td>{{ $vendor->currency }}</td></tr>
					<tr><td>{{ _lang('City') }}</td><td>{{ $vendor->city }}</td></tr>
					<tr><td>{{ _lang('State') }}</td><td>{{ $vendor->state }}</td></tr>
					<tr><td>{{ _lang('ZIP') }}</td><td>{{ $vendor->zip }}</td></tr>
					<tr><td>{{ _lang('Address') }}</td><td>{{ $vendor->address }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $vendor->remarks }}</td></tr>
			    </table>
			</div>
	    </div>
		@else		
			@include('backend.admin.vendor.tabs.'.$_GET['tab'])	
		@endif
	</div>
</div>
@endsection


