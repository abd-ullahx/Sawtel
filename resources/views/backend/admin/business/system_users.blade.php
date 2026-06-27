@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-sm-flex align-items-center justify-content-between">
                <h4 class="header-title">{{ _lang('Business Users') }}</h4>
                <div class="mt-2 mt-sm-0">
                    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Assign New User') }}"
                        href="{{ route('system_users.invite', $business->id) }}"><i class="ti-plus mr-1"></i>{{ _lang('Assign User') }}</a>
                </div>
            </div>

            <div class="card-body">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Business') }}</th>
                            <th>{{ _lang('Role') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($business->users as $user)
                        <tr data-id="row_{{ $user->id }}">
                            <td class='name'>
                                <div class="d-flex align-items-center">
									<img src="{{ profile_picture($user->profile_picture) }}" class="thumb-sm img-thumbnail rounded-circle mr-2">
                                    <div><span class="d-block text-height-0"><b>{{ $user->name }}</b></span><span class="d-block">{{ $user->email }}</span></div>
								</div>
                            </td>
                            <td class='business'>{{ $business->name }}</td>
                            <td class='role'>{{ $user->pivot->role_id == NULL ? _lang('Admin') : $user->roles->find($user->pivot->role_id)->name }}</td>
                            <td class='status'>{!! xss_clean(user_status($user->status)) !!}</td>
                            <td class="text-center">
                                @if($user->pivot->role_id != NULL)
                                <form action="{{ route('system_users.destroy_user', $user->id) }}" method="post">
                                    {{ csrf_field() }}
                                    <input name="_method" type="hidden" value="DELETE">
                                        <a class="btn btn-primary btn-xs ajax-modal" href="{{ route('system_users.change_role', [$user->id, $business->id]) }}" data-title="{{ _lang('Change Role') }}"><i class="ti-pencil mr-1"></i>{{ _lang('Change Role') }}</a>
                                        <button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-trash mr-1"></i>{{ _lang('Delete') }}</button>
                                    </div>
                                </form>
                                @else
                                    <a class="btn btn-primary btn-xs disabled" href="#"><i class="ti-pencil mr-1"></i>{{ _lang('Change Role') }}</a>
                                    <button class="btn btn-danger btn-xs" type="button" disabled><i class="ti-trash mr-1"></i>{{ _lang('Delete') }}</button>
                                @endif
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