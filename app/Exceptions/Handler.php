<?php

namespace App\Exceptions;

use Throwable;
use InvalidArgumentException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
	/**
	 * A list of exception types with their corresponding custom log levels.
	 *
	 * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
	 */
	protected $levels = [
		//
	];

	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<\Throwable>>
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed to the session on validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void {
		$this->reportable(function (Throwable $e) {
			//
		});
	}

	public function render($request, Throwable $e) {
		if ($e instanceof TransportException) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => 'SMTP Configuration is incorrect !'], 500);
            } else {
                return redirect()->route('login')->with('error', 'SMTP Configuration is incorrect !');
            }
        }

        if ($e instanceof InvalidArgumentException) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $e->getMessage()], 500);
            } else {
                return redirect()->route('login')->with('error', $e->getMessage());
            }
        }

        if ($e instanceof PostTooLargeException) {
            return redirect()->route('413')->withInput();
        }

		return parent::render($request, $e);
	}
}
