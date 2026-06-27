@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('My Business') }}</span>
			</div>
			<div class="card-body">
				<table id="business_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Business Name') }}</th>
							<th>{{ _lang('Currency') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th>{{ _lang('Is Default') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($businesss as $business)
					    <tr data-id="row_{{ $business->id }}">
							<td class='name'>
								<div class="d-flex align-items-center">
									<img src="{{ asset('public/uploads/media/' . $business->logo) }}" class="thumb-sm mr-2">
									<span><b>{{ $business->name }}</b></span>
								</div>
							</td>
							<td class='currency'>{{ $business->currency }} ({{ currency_symbol($business->currency) }})</td>							
							<td class='status'>{!! xss_clean(status($business->status)) !!}</td>
							<td class='default'>{!! $business->default == 1 ? show_status(_lang('Yes'), 'success') : show_status(_lang('No'), 'danger') !!}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ $business->default == 0 ? route('business.destroy', $business['id']) : '#' }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('business.edit', $business['id']) }}" class="dropdown-item dropdown-edit dropdown-edit"><i class="ti-pencil mr-2"></i>{{ _lang('Edit') }}</a>
{{--										<a href="{{ route('business.users', $business['id']) }}" class="dropdown-item dropdown-view dropdown-view"><i class="ti-user mr-2"></i>{{ _lang('Manage Users') }}</a>--}}
										<a href="{{ route('business.settings', $business['id']) }}" class="dropdown-item dropdown-view dropdown-view"><i class="ti-settings mr-2"></i>{{ _lang('Settings') }}</a>
										<button class="btn-remove dropdown-item" type="submit" {{ $business->default == 1 ? 'disabled' : '' }}><i class="ti-trash mr-2"></i>{{ _lang('Delete') }}</button>
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