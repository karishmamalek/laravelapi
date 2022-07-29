<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        
        });
    }

   /* public function render($request, Exception $e)
    {
        if ($e instanceof API\APIError)
            return Response::json(['code' => '204', 'msg' => 'hiii..']);
        return parent::render($request, $e);
    }*/

   /* public function render($request, Exception $exception)
    {
        if ($request->wantsJson()) {   //add Accept: application/json in request
            return $this->handleApiException($request, $exception);
        } else {
            $retval = parent::render($request, $exception);
        }

        return $retval;
    }*/

    function send_notification_FCM($notification_id, $title, $message, $id,$type) {
 
        $accesstoken = env('FCM_KEY');
     
        $URL = 'https://fcm.googleapis.com/fcm/send';
     
     
            $post_data = '{
                "to" : "' . $notification_id . '",
                "data" : {
                  "body" : "",
                  "title" : "' . $title . '",
                  "type" : "' . $type . '",
                  "id" : "' . $id . '",
                  "message" : "' . $message . '",
                },
                "notification" : {
                     "body" : "' . $message . '",
                     "title" : "' . $title . '",
                      "type" : "' . $type . '",
                     "id" : "' . $id . '",
                     "message" : "' . $message . '",
                    "icon" : "new",
                    "sound" : "default"
                    },
     
              }';
            // print_r($post_data);die;
     
        $crl = curl_init();
     
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: ' . $accesstoken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
     
        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
     
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
     
        $rest = curl_exec($crl);
     
        if ($rest === false) {
            // throw new Exception('Curl error: ' . curl_error($crl));
            //print_r('Curl error: ' . curl_error($crl));
            $result_noti = 0;
        } else {
     
            $result_noti = 1;
        }
     
        //curl_close($crl);
        //print_r($result_noti);die;
        return $result_noti;
    }
    
}
