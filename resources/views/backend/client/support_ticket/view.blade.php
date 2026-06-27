@extends('layouts.client')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header">
				<span class="header-title">{{ _lang('Ticket Details') }}</span>
			</div>

			<div class="card-body">
			    <table class="table table-striped table-bordered">
				    <tr>
						<td><b>{{ _lang('Client') }}</b></td>
						<td>{{ $supportticket->customer->name }} - {{ $supportticket->customer->email }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Priority') }}</td>
						<td>{{ $priorityList[$supportticket->priority] ?? _lang('N/A') }}</td>
					</tr>
					<tr>
						<td><b>{{ _lang('Subject') }}</b></td>
						<td><b>{{ $supportticket->subject }}</b></td>
					</tr>
					<tr>
						<td>{{ _lang('Status') }}</td><td>{!! xss_clean(ticket_status($supportticket->status)) !!}</td>
					</tr>
					@if($supportticket->status == 2)
						<tr><td>{{ _lang('Closed By') }}</td><td>{{ $supportticket->closed_by->name }}</td></tr>
					@endif
					@if($supportticket->is_resolved == 1)
						<tr><td>{{ _lang('Resolved') }}</td><td>{!! xss_clean(show_status(_lang('Yes'), 'success')) !!}</td></tr>
					@endif
			    </table>
			</div>
	    </div>
	</div>

	<div class="{{ $alert_col }} mt-3">
		<div class="card">
		    <div class="card-header d-flex align-items-center justify-content-between">
				<span class="header-title">{{ _lang('Conversations') }}</span>
				@if($supportticket->status == 1)
					<a href="{{ route('client.support_tickets.mark_as_closed', $supportticket->uuid) }}" class="btn btn-danger btn-xs"><i class="fas fa-check-circle"></i> {{ _lang('Mark as Closed') }}</a>
				@endif

				@if($supportticket->status == 2 && $supportticket->is_resolved == 0)
					<a href="{{ route('client.support_tickets.mark_as_resolved', $supportticket->uuid) }}" class="btn btn-success btn-xs"><i class="fas fa-check-circle"></i> {{ _lang('Mark as Resolved') }}</a>
				@endif
			</div>

			<div class="card-body comment-box">
				<!-- Messages-->
				@foreach($supportticket->messages as $message)
				<div class="comment">
					<div class="comment-author-ava"><img src="{{ profile_picture($message->sender->profile_picture) }}" alt="Avatar"></div>
					<div class="comment-body">
						<p class="comment-text">{{ $message->message }}</p>
						@if($message->attachment != null)
						<a href="{{ asset('public/uploads/media/'.$message->attachment) }}" target="_blank"><small><i class="fa-solid fa-paperclip"></i> {{ $message->attachment }}</small></a>
						@endif
						<div class="comment-footer"><span class="comment-meta">{{ $message->sender->name }}</span></div>
					</div>
				</div>
				@endforeach

				@if($supportticket->status == 1)
				<!-- Reply Form-->
				<h5 class="mb-2">{{ _lang('Leave Message') }}</h5>
				<form method="post" action="{{ route('client.support_tickets.reply',$supportticket->uuid) }}" enctype="multipart/form-data">
					@csrf
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<textarea class="form-control form-control-rounded" name="message" rows="6" placeholder="{{ _lang('Write your message here') }}..." required>{{ old('message') }}</textarea>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<input type="file" class="file-uploader" data-placeholder="{{ _lang('Attachment') }}" name="attachment">
							</div>
						</div>
						<div class="col-lg-12 mt-3">
							<div class="text-right">
								<button class="btn btn-outline-primary" type="submit"><i class="fas fa-reply"></i> {{ _lang('Send Message') }}</button>
							</div>
						</div>
					</div>
				</form>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection