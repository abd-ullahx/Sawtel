<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Mail\ContactUs;
use App\Models\EmailSubscriber;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Page;
use App\Models\Post;
use App\Models\Team;
use App\Models\Testimonial;
use App\Utilities\Overrider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class WebsiteController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        if (env('APP_INSTALLED', true) == true) {
            date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
            $this->middleware(function ($request, $next) {
                if (isset($_GET['language'])) {
                    session(['language' => $_GET['language']]);
                    return back();
                }
                if (get_option('website_enable', 1) == 0) {
                    return redirect()->route('login');
                }
                return $next($request);
            });
        }
    }

    /**
     * Display website's home page
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug = '') {
        echo 'Frontend Website';
    }

}