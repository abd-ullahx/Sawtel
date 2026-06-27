@extends('layouts.client')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('New Support Ticket') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('client.support_tickets.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Subject') }}</label>						
                                <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Description') }}</label>						
                                <textarea class="form-control" rows="5" name="description" required>{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Priority') }}</label>						
                                <select class="form-control auto-select" name="priority" data-selected="{{ old('priority', 1) }}">
                                    <option value="1">{{ _lang('Low') }}</option>
                                    <option value="2">{{ _lang('Medium') }}</option>
                                    <option value="3">{{ _lang('High') }}</option>
                                    <option value="4">{{ _lang('Critical') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Attachment') }}</label>						
                                <input type="file" class="dropify" name="attachment">
                            </div>
                        </div>
						
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
</div>
@endsection