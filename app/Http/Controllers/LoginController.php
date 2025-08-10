<?php

namespace App\Http\Controllers;
use App\Services\SmsService;
use App\Services\PHPMailerService;
use App\Models\SmsmailModel;

use App\Helpers\CryptoHelper;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;


class LoginController extends Controller
{

     public function generatePassword($length = 8) {
    $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower = 'abcdefghijkmnopqrstuvwxyz';
    $numbers = '23456789';
    $special = '$';

    $password = [
        $upper[random_int(0, strlen($upper) - 1)],
        $lower[random_int(0, strlen($lower) - 1)],
        $numbers[random_int(0, strlen($numbers) - 1)],
        $special[random_int(0, strlen($special) - 1)],
    ];

    $all = $upper . $lower . $numbers . $special;
    for ($i = 4; $i < $length; $i++) {
        $password[] = $all[random_int(0, strlen($all) - 1)];
    }

    shuffle($password);

    return implode('', $password);
    }


    protected $smsService;
    protected $mailService;

    // Combine both services in the constructor
    public function __construct(SmsService $smsService, PHPMailerService $mailService)
    {
        $this->smsService = $smsService;
        $this->mailService = $mailService;
    }

    // public function sendTestEmail()
    // {
       
    // }

   


    public function login(Request $request)
    {
    //     $to = 'swathinagarajann99@gmail.com';
    //     $subject = 'Test Subject';
    //     $body = '<h1>This is a test email</h1><p>Sending emails using PHPMailer in Laravel</p>';

    //    print_r($this->mailService->sendEmail($to, $subject, $body));

    //    exit;

        // return response()->json(['result' => $result]);


        //$otp    =   rand(1000,9999);

        // $mobileNumber = "8148958988";
        // $response = $this->smsService->sendSms($mobileNumber,$otp,'login');

        // print_r($response);


        // exit;
        
  
        // Validate user input
        $request->validate([
            'username' => 'required|email',  // Assuming username is an email
            'encryptedPassword' => 'required|min:6',  // Minimum password length
            'captcha' => 'required|string'
        ]);

       if ($request->captcha !== Session::get('captcha_code')) 
        {
            return response()->json([
                "message" => __("validation.captcha"),
                "errors" => [
                    "captcha" => [__("validation.captcha")]
                ]
            ], 400);
            
            
        }

        $username = $request->username;

        $encryptedPassword = $request->encryptedPassword;

        // Manually call the helper function
        $password = CryptoHelper::decryptPassword($encryptedPassword);
    
        // Fetch user from the database (replace with your actual DB query)
        // $user = DB::table(table: 'audit.deptuserdetails')->where('email', $username)->first();
    
        // // Check if user exists and password is correct
        // if ($user && Hash::check($password, $user->pwd)) 

        // Fetch user from the database (replace with your actual DB query)
        $user = DB::table('audit.deptuserdetails')->where('email', $username)
        ->where('statusflag','Y')->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->pwd))  // Compare hashed password'
        // if ($user && $request->password === $user->pwd)
        {  // Compare hashed password

            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', $user->deptuserid)
                ->orderByDesc('loginid')  // Order by ID in descending order
                ->limit(1)           // Limit to 1 record
                ->first();


            $lastlogin_time =   '';
            $deptuserid =   $user->deptuserid;

            if ($lastlogin_userdel) {
                $lastlogin_time =   $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $user->deptuserid) // Condition to identify the record
                    ->update([
                        'activestatus' => 'N',  // Field and new value
                    ]);
            }


            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $user->deptuserid,
                'ipadd' => $request->ip(),  // Get the server's IP address
                'browser' => $request->userAgent(),      // Get the user's browser information
                'machinename' => gethostname(),           // Get the server's hostname
                'logintime' =>  View::shared('get_nowtime'),                     // Current timestamp using Laravel's `now()` helper
                'activestatus' => 'Y'                    // Active status set to 'Y'
            ], 'loginid');  // Specify 'loginid' as the primary key column




            // echo $user->deptuserid;


            if ($lastlogin_time) {
                DB::table('audit.deptuserdetails')
                    ->where('deptuserid', $deptuserid) // Condition to identify the record
                    ->update([
                        'lastlogin' => $lastlogin_time,  // Field and new value
                    ]);
            }

         

            $charge = DB::table('audit.userchargedetails as uc')
                ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid') // Adjust the columns as needed
                ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
                ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'c.distcode')
                ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'c.regioncode')
                ->leftJoin('audit.auditor_instmapping as i', 'i.instmappingcode', '=', 'c.instmappingcode')
                ->leftJoin('audit.auditplanteammember as at', 'at.userid', '=', 'du.deptuserid')
                ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
                ->where('du.deptuserid', $deptuserid)
                ->where('uc.chargeflag', 'P')
                ->where('uc.statusflag','=','Y' )
                ->where('du.statusflag','=','Y' )
                // ->where('uc.statusflag','=','Y' )
                ->select(
                    'uc.chargeid',
                    'uc.userid',
                    'c.chargedescription',
                    'c.deptcode',
                    'c.regioncode',
                    'rtm.usertypecode',
                    'c.distcode',
                    'c.desigcode',
                    'd.desigelname',
                    'd.desigtlname',
                    'de.deptesname',
                    'd.desigesname',
                    'di.distsname',
                    'r.regionename',
                    'r.regiontname',
                    'di.distename',
                    'di.disttname',
                    'd.desigelname',
                    'du.username',
                    'du.usertamilname',
                    'du.email',
                    'du.mobilenumber',
                    'rtm.roletypecode',
		    'rm.roleactioncode',
                    'du.lastlogin',
                    'rm.rolemappingid',
                    'uc.userchargeid',
                    'at.teamhead as auditteamhead',
                    
                ) // Select all columns from both tables
                ->first();

            // print_r($charge);

            // exit;

            if($charge)
            {
                $user = new \stdClass();  // Create a new instance of stdClass
                $user->userid =   $charge->userid;
                $user->username =   $charge->username;
                $user->usertname =   $charge->usertamilname;
                $user->desigtsname =   $charge->desigtlname;
                $user->lastlogin =   $charge->lastlogin;
                $user->email =   $charge->email;
                $user->mobilenumber =   $charge->mobilenumber;

    
                unset($charge->userid);
                unset($charge->username);
                unset($charge->lastlogin);
                unset($charge->email);
                unset($charge->mobilenumber);


                $results = DB::table('audit.rolemapping')
                ->select(DB::raw("jsonb_array_elements_text(menuid->'1') as value"))  // Extract values from the JSON array at key '1'
                ->where('rolemappingid', '=', $charge->rolemappingid)  // Add your condition here
                ->get();
                
//print_r($results);

                $auditarray    =    [9, 14, 19, 24, 29];

                if (in_array($charge->rolemappingid, $auditarray)) {
//echo('hi');
                   $selecttable = DB::table('audit.auditplanteammember')
                        ->where('teamhead', 'Y')
                        ->where('statusflag','Y')
                        ->where('userid', $deptuserid)
                        ->get();
//print_r($selecttable);
//print_r($deptuserid);

                    if ($selecttable->isNotEmpty()) {
                        $results->push((object) ['value' => 12]);
                        $results->push((object) ['value' => 16]);
                        $results->push((object) ['value' => 64]);
                        $results->push((object) ['value' => 67]);
			$results->push((object) ['value' => 69]);
                    }
                }

                    // // After filtering, extract the remaining menu IDs
                $control_menu = $results->pluck('value')->toArray(); // Pluck the 'value' column as an array
 	//print_r($control_menu);





                $user->loginid    =   $insertedId;
                $charge->menu    =   $control_menu;



                session(['user' => $user]);
                session(['charge' => $charge]);
                //dd($charge);


                // Return success response with redirect URL
                return response()->json([
                    'success' => true,
                    'redirect_url' => url('/home'),   // Redirect to dashboard after successful login
                ]);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'you have no charge. Contact administator',
                ]);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }

        // If credentials are invalid, return error response
        // return response()->json([
        //     'success' => false,
        //     'message' => 'Invalid credentials, please try again.',
        //     // 'redirect_url' => url('/')  // Redirect back to login page
        // ], 401);
    }


    public function logout(Request $request)
    {
        $userData = session('user');
        $loginid   =    $userData->loginid;

        DB::table('audit.userlogindetails')
            ->where('loginid', $loginid) // Condition to identify the record
            ->update([
                'activestatus' => 'N',  // Field and new value
                'logouttime'    =>  View::shared('get_nowtime'),
            ]);


        // Check if the user is authenticated
        if (Auth::check()) {

            // // Log out the user
            Auth::logout();
        }

        // Clear all session data
        session()->flush();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token to prevent session fixation
        $request->session()->regenerateToken();

        // Redirect to the login page with a logout success message
        return redirect('/')->with('message', 'You have been logged out successfully.');
    }

    //--------------forget password-------------------
     
    public function forgetpassword(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'email' => 'required|email',
                'user' => 'required|in:auditor,auditee',
                'captcha'  => 'required|string'

            ]);

            if ($request->captcha !== Session::get('captcha_code')) 
            {
                return response()->json([
                    "message" => __("validation.captcha"),
                    "errors" => [
                        "captcha" => [__("validation.captcha")]
                    ]
                ], 400);
                
                
            }
    
            $email = $request->email;
            $userType = $request->user;
    
            // Choose the correct table
            $table = ($userType === 'auditor') ? 'audit.deptuserdetails' : 'audit.audtieeuserdetails';
    

            // Check if user exists
            $userExists = DB::table($table)->where('email', $email)->exists();
    
            if (!$userExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'email_exists' 
                ], 400);
            }
    
            // Update password
            $newPassword = $this->generatePassword(8);
            DB::table($table)->where('email', $email)->update([
                'pwd' => bcrypt($newPassword),
                'profile_update' => 'Y',
            ]);

            $redirectUrl = $request->user === 'auditor' ? url('/login') : url('/auditeelogin');

            $username = DB::table($table)->select('username')->where('email', $email)->first();

            $username =$username->username;


            $data =['email'=>$email,'username'=>$username,'redirect_url'=>$redirectUrl,'newpwd'=>$newPassword];
            // print_r($data);exit;

            $Lang='en';
            $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
            $sentsms = $auditModel->sendforgetpassword($data,$Lang);
           // print_r($sentsms);

    
            return response()->json([
                'success' => true,
                'message' => 'forget_success',
                'redirect' => $redirectUrl

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function auditee_validatelogin(Request $request)
    {

        // Validate user input
        $request->validate([
            'username' => 'required|email',  // Assuming username is an email
            'encryptedPassword' => 'required|min:6',  // Minimum password length
            'captcha' => 'required|string'

        ]);

        if ($request->captcha !== Session::get('captcha_code')) 
        {
            return response()->json([
                "message" => __("validation.captcha"),
                "errors" => [
                    "captcha" => [__("validation.captcha")]
                ]
            ], 400);
            
            
        }

        $username = $request->username;

        $encryptedPassword = $request->encryptedPassword;

        // Manually call the helper function
        $password = CryptoHelper::decryptPassword($encryptedPassword);

        // Fetch user from the database (replace with your actual DB query)
        $user = DB::table('audit.audtieeuserdetails')->where('email', $username)->first();

        // Check if user exists and password is correct
        // if ($user && Hash::check(value: $password, $user->pwd)) 


        // Check if user exists and password is correct
        if ($user && Hash::check($password, $user->pwd))

        // if ($user && $request->password === $user->pwd)
        {  // Compare hashed password



            $lastlogin_userdel = DB::table('audit.userlogindetails')
                ->where('userid', operator: $user->auditeeuserid)
                ->where('usertypecode', operator: 'I')
                ->orderByDesc('loginid')  // Order by ID in descending order
                ->limit(1)           // Limit to 1 record
                ->first();


            $lastlogin_time =   '';
            $auditeeuserid =   $user->auditeeuserid;



            if ($lastlogin_userdel) {
                $lastlogin_time =   $lastlogin_userdel->logintime;
                DB::table('audit.userlogindetails')
                    ->where('userid', $auditeeuserid) // Condition to identify the record
                    ->where('usertypecode', operator: 'I')
                    ->update([
                        'activestatus' => 'N',  // Field and new value
                    ]);
            }

            $insertedId = DB::table('audit.userlogindetails')->insertGetId([
                'userid' => $auditeeuserid,
                'ipadd' => $request->ip(), // Get the server's IP address
                'browser' => $request->userAgent(),      // Get the user's browser information
                'machinename' => gethostname(),           // Get the server's hostname
                'logintime' => View::shared('get_nowtime'),                     // Current timestamp using Laravel's `now()` helper
                'activestatus' => 'Y',
                'usertypecode' => 'I'                    // Active status set to 'Y'
            ], 'loginid');  // Specify 'loginid' as the primary key column




            // echo $user->deptuserid;

            if ($lastlogin_time) {
                DB::table('audit.audtieeuserdetails')
                    ->where('auditeeuserid', $auditeeuserid) // Condition to identify the record
                    ->update([
                        'lastlogin' => $lastlogin_time,  // Field and new value
                    ]);
            }

            // echo $auditeeuserid;

            $charge = DB::table('audit.audtieeuserdetails as au')
                ->Join('audit.mst_institution as in', 'in.instid', '=', 'au.instid') // Adjust the columns as needed
                ->leftJoin('audit.chargedetails as c', 'c.chargeid', '=', 'au.chargeid')
                ->leftJoin('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'in.deptcode')
                ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'in.distcode')
                ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'in.regioncode')
                // ->join('audit.mst_designation as d', 'd.desigcode', '=', 'c.desigcode')
                ->where('au.auditeeuserid', $auditeeuserid)
                // ->where('uc.statusflag','=','Y' )
                ->select('*')
                ->select(
                    'c.chargeid',
                    'au.auditeeuserid',
                    'c.chargedescription',
                    'in.deptcode',
                    'in.regioncode',
                    'in.distcode',
                    'de.deptesname',
                    'di.distsname',
                    'r.regionename',
                    'di.distename',
                    'in.instename',
		            'in.instid',
                    'au.username',
                    'au.email',
                    'au.mobilenumber',
                    'rtm.roletypecode',
                    'au.lastlogin',
                    'rm.rolemappingid',
                    'rtm.usertypecode'
                ) // Select all columns from both tables
                ->first();

            // print_r( $charge);

            $user = new \stdClass();  // Create a new instance of stdClass
            $user->userid =   $charge->auditeeuserid;
            $user->username =   $charge->username;
            $user->usertname =   $charge->username;
            $user->lastlogin =   $charge->lastlogin;
            $user->email =   $charge->email;
            $user->mobilenumber =   $charge->mobilenumber;


            unset($charge->userid);
            unset($charge->username);
            unset($charge->lastlogin);
            unset($charge->email);
            unset($charge->mobilenumber);


            $results = DB::table('audit.rolemapping')
                ->select(DB::raw("jsonb_array_elements_text(menuid->'1') as value"))  // Extract values from the JSON array at key '1'
                ->where('rolemappingid', '=', $charge->rolemappingid)  // Add your condition here
                ->get();



            // Menu
            $control_menu = $results->pluck('value')->toArray(); // Plucks out the values as an array


            $user->loginid    =   $insertedId;
            $charge->menu    =   $control_menu;



            session(['user' => $user]);
            session(['charge' => $charge]);


            // Return success response with redirect URL
            return response()->json([
                'success' => true,
                'redirect_url' => url('/auditeedashboard'),   // Redirect to dashboard after successful login
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Username or Password is incorrect',
            ]);
        }
    }
}
