<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the SessionService to the container
        // $this->app->singleton(SessionService::class, function ($app) {
        //     return new SessionService();
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('workallocation', 'WA');
        view()->share('datachange', 'CD');
        view()->share('DGA_roletypecode', '04');  // Set and share type1 globally in one line
        view()->share('Ho_roletypecode', '03');  // Set and share type1 globally in one line
        view()->share('Re_roletypecode', '02');  // Set and share type1 globally in one line
        view()->share('Dist_roletypecode', '01');  // Set and share type1 globally in one line
        view()->share('Admin_roletypecode', '05');  // Set and share type1 globally in one line
	    view()->share('NIC_roletypecode', '07');
        view()->share('auditeelogin', 'I');  // Set and share type1 globally in one line
        view()->share('auditorlogin', 'A');  // Set and share type1 globally in one line

        view()->share('get_nowtime', Carbon::now('Asia/Kolkata'));
        view()->share('Reject', 'I');
        view()->share('Forward', 'F');
        view()->share('Approve', 'A');
        view()->share('Insert', 'S');
        view()->share('accepted', 'A');



        view()->share('insert', 'insert');
        view()->share('update', 'update');

        view()->share('savebtn', 'savebtn');
        view()->share('savedraftbtn', 'savedraftbtn');
        view()->share('clearbtn', 'clearbtn');
        view()->share('updatebtn', 'updatebtn');
        view()->share('finalizebtn', 'finalizebtn');
        view()->share('cancelbtn', 'cancelbtn');
        view()->share('approvebtn', 'approvebtn');
        view()->share('forwardbtn', 'forwardbtn');

        view()->share('statecode', 'TN');

        view()->share('Auditeechargeid', '5');
        view()->share('Leavetransactiontypecode', '01');
        view()->share('auditor_roleactioncode', '04');
        view()->share('diversionTransactiontypecode', '07');


        view()->share('Inflag', 'I');
        view()->share('Outflag', 'O');

        view()->share('inactive', 'I');

        view()->share('I', 'Auditee');  // Set and share type1 globally in one line
        view()->share('A', 'Auditor');  // Set and share type1 globally in one line

        view()->share('uploadsfilefoldername', 'uploads');
        view()->share('slipfileuploadpath', 'slipauditor');
        view()->share('auditeefileuploadpath', 'auditeeReply');
        view()->share('alterplan', 'alterplan');
        view()->share('annexturepath', 'report_annexures');



        view()->share('auditeedefaultPass', 'Dgcams@2025');    

        view()->share('transfercode', '06');
        view()->share('transferwithpromocode', '08');
        view()->share('promotioncode', '05'); 
        view()->share('AuditorRoleactioncode', '04');
        view()->share('current_quarter', 'Q1');

        
    }
}
