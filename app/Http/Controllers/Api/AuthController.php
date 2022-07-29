<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $TBLUSERS_COL_USER_NAME ='';
    private $TBLUSERS_COL_USER_MIDDLENAME ='';
    private $TBLUSERS_COL_USER_LASTNAME ='';
    private $TBLUSERS_COL_USER_EMAIL ='';
    private $TBLUSERS_COL_USER_IMAGE ='';
    private $TBLUSERS_COL_USER_PASSWORD ='';
    private $MSG_USER_CREATED ='';
    private $SUCCESS_STATUS_CODE ='';
    private $ERROR_STATUS_CODE ='';
    private $SUCCESS_MSG ='';
    private $ERROR_MSG ='';

    public function __construct()
    {
        $this->TBLUSERS_COL_USER_NAME = config("constants.TBLUSERS_COL_USER_NAME");
        $this->TBLUSERS_COL_USER_MIDDLENAME = config("constants.TBLUSERS_COL_USER_MIDDLENAME");
        $this->TBLUSERS_COL_USER_LASTNAME = config("constants.TBLUSERS_COL_USER_LASTNAME");
        $this->TBLUSERS_COL_USER_EMAIL = config("constants.TBLUSERS_COL_USER_EMAIL");
        $this->TBLUSERS_COL_USER_IMAGE = config("constants.TBLUSERS_COL_USER_IMAGE");
        $this->TBLUSERS_COL_USER_PASSWORD = config("constants.TBLUSERS_COL_USER_PASSWORD");
        $this->MSG_USER_CREATED = config("constants.MSG_USER_CREATED");
        $this->SUCCESS_STATUS_CODE = config("constants.SUCCESS_STATUS_CODE");
        $this->ERROR_STATUS_CODE = config("constants.ERROR_STATUS_CODE");
        $this->SUCCESS_MSG = config("constants.SUCCESS_MSG");
        $this->ERROR_MSG = config("constants.ERROR_MSG");
    }
    // print the message
    public function message(){
        return "hello";
    }

    // Registration
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            $this->TBLUSERS_COL_USER_NAME => 'required|string',
            $this->TBLUSERS_COL_USER_MIDDLENAME => 'required|string',
            $this->TBLUSERS_COL_USER_LASTNAME => 'required|string',
            $this->TBLUSERS_COL_USER_EMAIL => 'required|string|email|unique:users',
            $this->TBLUSERS_COL_USER_IMAGE => 'required|mimes:jpg,png,jpeg',
            $this->TBLUSERS_COL_USER_PASSWORD => 'required|string'
        ]);

        if($validator->fails()) 
        {
            return jsonResponseData($this->ERROR_STATUS_CODE , $validator->messages()->first(), null);
        }
       
        if(!empty($request->image))
        {
            $image = $request->file('image');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destination_path = public_path('/images');
            $image->move($destination_path, $name);
            $userimage = 'images/'.$name;
        }

        $user = new User([
            $this->TBLUSERS_COL_USER_NAME => $request->name,
            $this->TBLUSERS_COL_USER_MIDDLENAME => $request->middle_name,
            $this->TBLUSERS_COL_USER_LASTNAME => $request->last_name,
            $this->TBLUSERS_COL_USER_EMAIL => $request->email,
            $this->TBLUSERS_COL_USER_IMAGE => $userimage,
            $this->TBLUSERS_COL_USER_PASSWORD => Hash::make($request->password)
        ]);
       
        $user->save();
       
        $requestData = [$this->TBLUSERS_COL_USER_NAME => $request->name,$this->TBLUSERS_COL_USER_MIDDLENAME => $request->middle_name,$this->TBLUSERS_COL_USER_LASTNAME =>$request->last_name];
        return jsonResponseData($this->SUCCESS_STATUS_CODE , $this->MSG_USER_CREATED, $requestData);
        
    }

    public function notifyUser(Request $request){
 
        $user = User::where('id', $request->id)->first();
      
        $notification_id = $user->notification_id;
        $title = "Greeting Notification";
        $message = "Have good day!";
        $id = $user->id;
        $type = "basic";
      
        $res = send_notification_FCM($notification_id, $title, $message, $id,$type);
      
        if($res == 1){
      
           // success code
           return jsonResponseData($this->SUCCESS_STATUS_CODE , $this->SUCCESS_MSG, null);
      
        }else{
      
          // fail code
          return jsonResponseData($this->ERROR_STATUS_CODE , $this->ERROR_MSG, null);
        }
         
      
     }

}
