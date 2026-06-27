@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-signin my-5 p-3">              
				<div class="card-body">
				    <img class="logo" src="{{ get_logo() }}">
					
					<h5 class="text-center py-4">{{ _lang('Create Your Account') }}</h4> 

                    @if(Session::has('error'))
                        <div class="alert alert-danger">
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(Session::has('success'))
                        <div class="alert alert-success mb-4">
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif	
					
                    <form method="POST" class="form-signup" autocomplete="off" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
							<div class="col-lg-12">
                                <input id="name" type="text" placeholder="{{ _lang('Name') }}" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input id="email" type="email" placeholder="{{ _lang('E-Mail Address') }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group row">
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <select class="form-control{{ $errors->has('country_code') ? ' is-invalid' : '' }} select2" name="country_code" required>
                                    <option value="">{{ _lang('Country Code') }}</option>
                                    @foreach(get_country_codes() as $key => $value)
                                    <option value="{{ $value['dial_code'] }}" {{ old('country_code') == $value['dial_code'] ? 'selected' : '' }}>{{ $value['country'].' (+'.$value['dial_code'].')' }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('country_code'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('country_code') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-lg-6">
                                <input id="mobile" type="text" placeholder="{{ _lang('Mobile') }}" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{ old('mobile') }}" required>

                                @if ($errors->has('mobile'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <input id="password" type="password" placeholder="{{ _lang('Password') }}" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                           <div class="col-lg-6">
                                <input id="password-confirm" type="password" class="form-control" placeholder="{{ _lang('Confirm Password') }}" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-12">
                                <input type="hidden" class="{{ $errors->has('g-recaptcha-response') ? ' is-invalid' : '' }}" name="g-recaptcha-response" id="recaptcha">
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group row mt-4">
							<div class="col-lg-12 text-center">
								<button type="submit" class="btn btn-primary btn-login">
								{{ _lang('Create My Account') }}
                                </button>
							</div>
						</div>
                        <div class="form-group row mt-4">
							<div class="col-lg-12 text-center">
							   {{ _lang('Already Have An Account?') }} 
                               <a href="{{ route('login') }}">{{ _lang('Log In Here') }}</a>
							</div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(get_option('enable_recaptcha', 0) == 1)
<script src="https://www.google.com/recaptcha/api.js?render={{ get_option('recaptcha_site_key') }}"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ get_option('recaptcha_site_key') }}', {action: 'register'}).then(function(token) {
        if (token) {
            document.getElementById('recaptcha').value = token;
        }
        });
    });
</script>
@endif
@endsection
