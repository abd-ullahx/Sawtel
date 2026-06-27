@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header">
                <span class="panel-title">{{ _lang('Add New User') }}</span>
            </div>
            <div class="card-body">
                <form method="post" class="validate" autocomplete="off" action="{{ route('users.store') }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Name') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
                                <div class="col-xl-9">
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                        required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Password') }}</label>
                                <div class="col-xl-9">
                                    <input type="password" class="form-control" name="password" value="" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('User Type') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control auto-select" data-selected="{{ old('user_type') }}"
                                        name="user_type" id="user_type" required>
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
                                    <select class="form-control auto-select" data-selected="{{ old('status', 1) }}"
                                        name="status" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="1">{{ _lang('Active') }}</option>
                                        <option value="0">{{ _lang('In Active') }}</option>
                                    </select>
                                    <a href="" class="mt-3 d-block toggle-optional-fields" data-toggle-title="{{ _lang('Hide Optional Fields') }}">{{ _lang('Show Optional Fields') }}</a>
                                </div>
                            </div>
   
                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Phone') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('City') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('State') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('ZIP') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Address') }}</label>
                                <div class="col-xl-9">
                                    <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row optional-field">
                                <label class="col-xl-3 col-form-label">{{ _lang('Profile Picture') }}</label>
                                <div class="col-xl-9">
                                    <input type="file" class="dropify" name="profile_picture">
                                </div>
                            </div>
    
                            <div class="form-group row mt-4">
                                <div class="col-xl-9 offset-xl-3">
                                    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Create User') }}</button>
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