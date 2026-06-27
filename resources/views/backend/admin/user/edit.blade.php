@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                <span class="panel-title">{{ _lang('Update User') }}</span>
            </div>
            <div class="card-body">
                <form method="post" class="validate" autocomplete="off"
                    action="{{ route('users.update', $id) }}" enctype="multipart/form-data">
                    {{ csrf_field()}}
                    <input name="_method" type="hidden" value="PATCH">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Name') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
                                <div class="col-xl-9">
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Password') }}</label>
                                <div class="col-xl-9">
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('User Type') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control auto-select"
                                        data-selected="{{ $user->user_type }}" name="user_type" id="user_type" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="admin">{{ _lang('Admin') }}</option>
                                        <option value="user">{{ _lang('User') }}</option>
                                    </select>
                                    <small class="text-primary"><i class="ti-info-alt"></i> <i>{{ _lang('Admin will get full access and user will get role based access only.') }}</i></small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control auto-select" data-selected="{{ $user->status }}"
                                        name="status" required>
                                        <option value="1">{{ _lang('Active') }}</option>
                                        <option value="0">{{ _lang('In Active') }}</option>
                                    </select>
                                    <a href="" class="mt-3 d-block toggle-optional-fields" data-toggle-title="{{ _lang('Hide Optional Fields') }}">{{ _lang('Show Optional Fields') }}</a>
                                </div>
                            </div>
   
                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Phone') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('City') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="city" value="{{ $user->city }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('State') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="state" value="{{ $user->state }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('ZIP') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="zip" value="{{ $user->zip }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Address') }}</label>
                                <div class="col-xl-9">
                                    <textarea class="form-control" name="address">{{ $user->address }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Profile Picture') }}</label>
                                <div class="col-xl-9">
                                    <input type="file" class="dropify" default="{{ $user->profile_picture }}" name="profile_picture">
                                </div>
                            </div>
    
                            <div class="form-group row mt-4">
                                <div class="col-xl-9 offset-xl-3">
                                    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update User') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection