<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use DB;
use Hash;
use App\User;
use App;
use App\SetupValue;
use App\Log;
use Intervention\Image\Facades\Image;
use App\Yearbook;
use App\Question;


class Member extends Controller
{
    protected $agreement = null;
    protected $classTeacher = null;
    protected $titleName = null;
    protected $studenListLimit = null;

    public function __construct(){
        //$agreement = SetupValue::where('slug','AGREEMENT')->first()->value;
        //$this->agreement = $agreement;
        $this->classTeacher = SetupValue::where('slug','like','CLASS-TEACHER-%')->orderby('list')->get();
        $this->titleName = SetupValue::where('slug','like','TITLE-NAME-%')->orderby('list')->get();
        $this->studenListLimit = SetupValue::where('slug','STUDEN-LIST-LIMIT')->first()->value;

    }




    public function register(Request $request) 
    {
        //dd($request->get('step'));
        switch ($request->get('step')) {
            case '2':
                $number = preg_replace('/( )||(-)/', '', trim($request->get('number')));
                $detail = User::where('studenNo',$number)->orwhere('idCardNo',$number);
                if($detail->count() == 1){
                    $memberDetail = $detail->first();
                    //dd($memberDetail );
                    if(
                        $memberDetail->username == '' ||
                        $memberDetail->password == '' ){
                        return View('users.regisStep.step2')
                            ->with('agreement',$this->agreement)
                            ->with('request',$request->all())
                            ->with('memberDetail',$memberDetail)
                            ->with('titleNames',$this->titleName)
                            ;
                    }else{
                        return redirect(route('register'))
                                ->with('agreement',$this->agreement)
                                ->withInput()
                                ->withErrors('คุณได้ลงทะเบียนแล้ว! หากพบปัญหาการใช้งานกรุณาติดต่อผู้ดูแลระบบ')
                                ;
                    }                    
                }else{
                    return redirect(route('register'))
                                ->with('agreement',$this->agreement)
                                ->withInput()
                                ->withErrors('ข้อมูลไม่ถูกต้อง! กรุณาติดต่อผู้ดูแลระบบ')
                                ;
                }
                
                break;
            case '3':
                $messages = [
                    'required' => ':attribute จำเป็นต้องระบุข้อมูล!',
                    'date_format' => 'รูปแบบวันที่ไม่ถูกต้อง! กรุณาระบุ ปีคศ-เดือน-วัน เท่านั้น!',
                    'email' => 'Email ไม่ถูกต้อง!',
                    'unique' => ':attribute มีผู้ใช้งานแล้ว!',
                    'alpha_num' => ':attribute กรุณาระบุตัวเลขหรือภาษาอังกฤษเท่านั้น!',
                    'between' => ':attribute ต้องอยู่ระหว่าง :min ถึง :max ตัวอักษรเท่านั้น!',
                    'confirmed' => 'รหัสผ่านไม่ตรงกัน!',
                    'min' => ':attribute วันที่ไม่ถูกต้อง!',
                ];
                $validator = Validator::make($request->all(),[
                    'titleName' => 'required',
                    'name' => 'required',
                    'lastname' => 'required',
                    'address' => 'required',
                    'birthday' => 'required|date_format:Y-m-d',
                    //'contact' => 'required',
                    //'email' => 'required|email|unique:member,email',
                    'username' => 'required|alpha_num|between:4,200|unique:member,username',
                    'password' => 'required|between:4,200|confirmed',
                    'password_confirmation' => 'required|between:4,200',

                ],$messages);
                if ($validator->fails()) {
                    return redirect(route('register',['step' =>'2','number'=>$request->get('number')]))
                                    ->withInput()
                                    ->withErrors($validator->errors());
                                    ;
                }else{
                    $updateMember = User::find(trim($request->get('id')));
                    $updateMember->titleName = trim($request->get('titleName'));
                    $updateMember->name = trim($request->get('name'));
                    $updateMember->lastname = trim($request->get('lastname'));
                    $updateMember->nickname = trim($request->get('nickname'));
                    $updateMember->birthday = trim($request->get('birthday'));
                    $updateMember->address = trim($request->get('address'));
                    $updateMember->tel = trim($request->get('tel'));
                    $updateMember->contact = trim($request->get('contact'));
                    $updateMember->email = trim($request->get('email'));
                    $updateMember->username = trim($request->get('username'));
                    $updateMember->password = Hash::make(trim($request->get('password')));
                    $updateMember->active = true;
                    $updateMember->save();
                    $log = new Log;
                    $log->memberId = $updateMember->id;
                    $log->detail = 'Register,'.$updateMember;
                    $log->save();
                    return Redirect::route('login');
                }
                break;
            default:
                return View('users.register')->with('agreement',$this->agreement);
                break;
        }
        
    }

   
    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function loginForm()
    {
        return View('users.login'); 
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|alpha_num|min:3|max:32',
            'password' => 'required|min:3'
        ]);

        if($validator->fails()){
            return Redirect::to('login')->withErrors($validator->errors())->withInput();
        }

        if(Auth::attempt(['username' => $request->get('username'), 'password' => $request->get('password')], $request->get('remember'))){
            if($request->get('redirect') != ''){
                return Redirect::to($request->get('redirect'));
            }else{
                return Redirect::route('member');
            }
        }else{
            $errors['username'] =  'ชื่อผู้ใช้หรือรหัสผ่านผิดพลาด';
            $errors['password'] =  ' ';

            return Redirect::to('login')->withErrors($errors)->withInput(); 
        }
    }

    public function forgetpass(){
        if(Auth::check()){
            Auth::logout();
        }
        return view('users.forgetpass');
    }

    public function resetpass(Request $request){
        $messages = [
            'required' => ':attribute จำเป็นต้องระบุข้อมูล!',
            'exists' => ':attribute ไม่พบข้อมูลในฐานข้อมูล!',
        ];
        $validator = Validator::make($request->all(),[
            'input1' => 'required|exists:member,'.$request->get('column1'),
            'input2' => 'required|exists:member,'.$request->get('column2')
        ],$messages);

        if($validator->fails()){
            return Redirect::to('forgetpass')->withErrors($validator->errors());
        }

        $resetpass = User::where($request->get('column1'),trim($request->get('input1')))
                        ->where($request->get('column2'),trim($request->get('input2')))->first();
        $resetpass->username = '';
        $resetpass->password = '';
        $resetpass->email = '';
        $resetpass->active = false;
        $resetpass->save();

        $log = new Log;
        $log->memberId = $resetpass->id;
        $log->detail = 'Reset Password,'.$resetpass;
        $log->save();

        return Redirect::route('register');


    }

    
    public function studenList(){
       
        
        
        return view('admin.studenList')
                    ->with('classTeachers',$this->classTeacher)
                    ->with('titleNames',$this->titleName)
                    ->with('studenListLimit',$this->studenListLimit)
        ;
    }

    public function studenListView(Request $request){
        $key = $request->get('key');
        $column = $request->get('column');
        $limit = ($request->get('limit') != '')?$request->get('limit'):$this->studenListLimit;
 
        if($key != ''){
            if(preg_match('/[0-9]\/[0-9]/',$key)){
                $classRoomExplode = explode('/',$key);
                $studenLists = User::where('class',$classRoomExplode[0])
                            ->where('room',$classRoomExplode[1])
                            ->orderBy('class', 'asc')
                            ->orderBy('room', 'asc')
                            ->orderBy('CRNo', 'asc')
                            ->paginate($limit);
            }else{
                $studenLists = User::where($column,$key)
                                ->orderBy('class', 'asc')
                                ->orderBy('room', 'asc')
                                ->orderBy('CRNo', 'asc')
                                ->paginate($limit);
            }

        }else{
         $studenLists = User::orderBy('class', 'asc')
                        ->orderBy('room', 'asc')
                        ->orderBy('CRNo', 'asc')
                        ->paginate($limit);
        }
        //dd($studenLists);
        $page = $studenLists->currentPage();
        $totalPage = $studenLists->lastPage();
        if($request->get('page') > $totalPage){
            return redirect()->route('studenView',['page'=>$totalPage,'limit'=>$limit,'column'=>$column,'key'=>$key]);
        }

        return view('admin.studenListView',compact('studenLists','limit','key','page','totalPage','column'));

    }


    public function yearbook(){
            $checkYB = Yearbook::where('memberId',Auth::user()->id)->first();
        return view('users.yearbook')->with(compact('checkYB'));
    }

    public function studenUpdate(Request $request){
        
        $validator = Validator::make($request->all(),[
            'classRoom'     => 'required',
            'CRNo'          => 'required',
            'studenNo'      => 'required',
            'idCardNo'      => 'required|min:13|max:13',
            'titleName'     => 'required',
            'name'          => 'required',
            'lastname'      => 'required',
        ]);

        if($validator->fails()){
            return 'false';
        }


        if($request->get('id') != ''){
            $updateMember = User::find($request->get('id'));
        }else{
            $updateMember = new User;
        }

        if($request->get('admin')){
            $admin = 1;
        }else{
            $admin = 0;
        }

        $classRoom = explode(',', $request->get('classRoom'));
        $updateMember->class = trim($classRoom[0]);
        $updateMember->room = trim($classRoom[1]);
        $updateMember->CRNo = trim($request->get('CRNo'));
        $updateMember->gradYear = trim($request->get('gradYear'));
        $updateMember->studenNo = trim($request->get('studenNo'));
        $updateMember->idCardNo = trim($request->get('idCardNo'));
        $updateMember->titleName = trim($request->get('titleName'));
        $updateMember->name = trim($request->get('name'));
        $updateMember->lastname = trim($request->get('lastname'));
        $updateMember->admin = trim($admin);
        $updateMember->save();


        $log = new Log;
        $log->memberId = Auth::user()->id;
        if($request->get('id') != ''){
            $log->detail = 'Update Member,'.$updateMember;
        }else{
            $log->detail = 'Insert Member,'.$updateMember;
        }
        
        $log->save();

        return 'true';
        
    }


    public function index(){
        $date = strtotime(Auth::user()->birthday);
        $birthMonth = SetupValue::where('slug','M-'.date('n',$date))->first();
        $checkYB = Yearbook::where('memberId',Auth::user()->id)->first();
        $questions = Question::where('subId','')->orderBy('id')->get();
        return view('users.index')
                ->with('classTeachers',$this->classTeacher)
                ->with('titleNames',$this->titleName)
                ->with(compact('birthMonth'))
                ->with(compact('checkYB'))
                ->with(compact('questions'))
                ;
    }

    public function studenDel(Request $request){
        $id = $request->get('id');
        if($id != ''){
            $idArray = explode(',', $id);
            User::destroy($idArray);
            $log = new Log;
            $log->memberId = Auth::user()->id;
            $log->detail = 'Delete Member id = '.$id;
            $log->save();
            return 'true';
        }
        return 'false';
    }

    public function uploadPic(Request $request)
    {

        $filePic = $request->get('picture');
        $filePicExplode = explode(',', $filePic);
        $filePic = $filePicExplode[1];
        $filePic = base64_decode($filePic);

        $user_update = User::find(Auth::user()->id)->first();
        if($user_update->picture != 'picture/yearbook/ubr.jpg'){
            if(file_exists($user_update->picture)){
                unlink($user_update->picture);
            }
            
        }
        

        
        $dir = 'uploads/class-'.Auth::user()->class.'/room-'.Auth::user()->room;

        if (!file_exists($dir)){
            mkdir($dir, 0777, true);
        }
        $millitime = round(microtime(true) * 1000);
        $filename = 'pic-'.Auth::user()->class.Auth::user()->room.'-'.Auth::user()->CRNo.'-'.Auth::user()->id.'-'.$millitime.".jpg";

        $file_put_contents = file_put_contents($dir.'/'.$filename, $filePic);

        if($file_put_contents != 0 | $file_put_contents != 1){
            $user_update = User::find(Auth::user()->id);
            $user_update->picture = $dir.'/'.$filename;
            $user_update->save();
            
            $log = new Log;
            $log->memberId = Auth::user()->id;
            $log->detail = 'Edit Picture,'.$user_update;
            $log->save();
            return $dir.'/'.$filename;
        }else{
            return 'false';
        }
        
    }
    public function yeaBooGen(Request $request){
        //dd($request->all());
        if(Auth::user()->CRNo != '00'){
        $date = strtotime(Auth::user()->birthday);
        $birthMonth = SetupValue::where('slug','M-'.date('n',$date))->first();
        $yearThai = date("Y",$date)+543;
        $birthday = date('j',$date).' '.$birthMonth->title.' '.$yearThai;

        $name = '#'.Auth::user()->CRNo.' '.Auth::user()->name.' '.Auth::user()->lastname;
        $proPic = Image::make(Auth::user()->picture)->resize(140,140);
        $proPic = Image::make(Auth::user()->picture)->resize(140,140);

        if((Auth::user()->CRNo%2) == 0){
            $bg = 'picture/yearbook/y2.jpg';
        }
        else{
            $bg = 'picture/yearbook/y1.jpg';
        }


        $img = Image::make($bg)
                ->insert($proPic,'top-left',100,188)
                ->text($name,260,180,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(34);
                    $font->color('#000');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text(Auth::user()->studenNo,237,335,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#000');
                    $font->align('right');
                    $font->valign('top');
                })
                ->text(Auth::user()->nickname,315,335,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#000');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($birthday,445,334,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#000');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('aboutMe1'),270,240,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(30);
                    $font->color('#000');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('aboutMe2'),270,270,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(30);
                    $font->color('#000');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('likeColor'),167,389,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('likeSubject'),187,419,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('myfriend'),187,447,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('myTeacher'),239,475,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('tellFriend'),250,505,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('tellTeacher'),268,533,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('tellSchool'),268,563,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(24);
                    $font->color('#1100b7');
                    $font->align('left');
                    $font->valign('top');
                })
                ->text($request->get('motto'),339.5,632,function($font){
                    $font->file('fonts/ThaiSansNeue-Bold.ttf');
                    $font->size(36);
                    $font->color('#FFF');
                    $font->align('center');
                    $font->valign('top');
                })
                ;
            }

        if($request->get('action') == 'save'){

            if(Auth::user()->CRNo != '00'){

                $dir = 'yearbook/class-'.Auth::user()->class.'/room-'.Auth::user()->room;
                if (!file_exists($dir)){
                    mkdir($dir, 0777, true);
                }
                $millitime = round(microtime(true) * 1000);
                $filename = 'yearbook-'.Auth::user()->class.Auth::user()->room.'-'.Auth::user()->CRNo.'-'.Auth::user()->id.'-'.$millitime.".jpg";
                $img->save($dir.'/'.$filename);
            }

            $checkYB = Yearbook::where('memberId',Auth::user()->id)->first();
            if(count($checkYB) != 0){
                if($checkYB->link != ''){
                     if(file_exists($checkYB->link)){
                        unlink($checkYB->link);
                    }
                }
            }else{
                $checkYB = new Yearbook;
            }

             if(Auth::user()->CRNo == '00'){
                $checkYB->memberId = Auth::user()->id;
                $checkYB->aboutMe1 = trim($request->get('textTeacher'));
                $checkYB->link = 'none';
                $checkYB->save();

             }else{
                $checkYB->memberId = Auth::user()->id;
                $checkYB->aboutMe1 = $request->get('aboutMe1');
                $checkYB->aboutMe2 = $request->get('aboutMe2');
                $checkYB->likeSubject = $request->get('likeSubject');
                $checkYB->likeColor = $request->get('likeColor');
                $checkYB->myFriend = $request->get('myfriend');
                $checkYB->myTeacher = $request->get('myTeacher');
                $checkYB->tellFriend = $request->get('tellFriend');
                $checkYB->tellTeacher = $request->get('tellTeacher');
                $checkYB->tellSchool = $request->get('tellSchool');
                $checkYB->motto = $request->get('motto');
                $checkYB->link = $dir.'/'.$filename;
                $checkYB->save();
            }

            if(count($checkYB) != 0){
                $log = new Log;
                $log->memberId = Auth::user()->id;
                $log->detail = 'Update Yearbook,'.$checkYB;
                $log->save();
            }else{
                $log = new Log;
                $log->memberId = Auth::user()->id;
                $log->detail = 'Create Yearbook,'.$checkYB;
                $log->save();

            }
            $updateYstatus = User::find(Auth::user()->id);
            $updateYstatus->yearbook = true;
            $updateYstatus->save();
            return Redirect::route('yearbook');

        }


        return $img->response('jpg');
    }

    public function update(Request $request)
    {            
                //dd($request->all());
                $niceNames = array(
                    'gradYear' => 'ปีจบการศึกษา',
                    'classRoom' => 'สายชั้น',
                    'CRNo' => 'เลขที่',
                    'studenNo' => 'รหัสนักเรียน',
                    'idCardNo' => 'รหัสประชาชน',
                    'titleName' => 'คำนำหน้าชื่อ',
                    'name' => 'ชื่อ',
                    'lastname' => 'นามสกุล',
                    'nickname' => 'ชื่อเล่น',
                    'birthday' => 'วันเกิด',
                    'address' => 'ที่อยู่',
                    'tel' => 'เบอร์โทรศัพท์',
                );
                $messages = [
                    'required' => ':attribute จำเป็นต้องระบุข้อมูล!',
                    'date_format' => 'รูปแบบวันที่ไม่ถูกต้อง! กรุณาระบุ ปีคศ-เดือน-วัน เท่านั้น!',
                    'email' => 'Email ไม่ถูกต้อง!',
                    'unique' => ':attribute มีผู้ใช้งานแล้ว!',
                    'alpha_num' => ':attribute กรุณาระบุตัวเลขหรือตัวอักษรเท่านั้น!',
                    'between' => ':attribute ต้องอยู่ระหว่าง :min ถึง :max ตัวอักษรเท่านั้น!',
                    'confirmed' => 'รหัสผ่านไม่ตรงกัน!',
                    'date_format' => ':attribute วันที่ไม่ถูกต้อง!',
                    'min' => ':attribute ข้อมูลต้องมี :max ตัว!',
                    'max' => ':attribute ข้อมูลต้องมี :max ตัว!',
                ];
                $validator = Validator::make($request->all(),[
                    'gradYear' => 'required',
                    'classRoom' => 'required',
                    'CRNo' => 'required',
                    'studenNo' => 'required',
                    'idCardNo' => 'required|max:13|min:13',
                    'titleName' => 'required',
                    'name' => 'required',
                    'lastname' => 'required',
                    'nickname' => 'required',
                    'address' => 'required',
                    'birthday' => 'required|date_format:Y-m-d',
                    'tel' => 'required'
                ],$messages);
                $validator->setAttributeNames($niceNames); 
                if ($validator->fails()) {
                    return redirect(route('member',['action' =>'2']))
                                    ->withErrors($validator->errors())
                                    ->withInput($request->all())
                                    ;
                }

                $classRoom = explode(',', $request->get('classRoom'));
                //dd($classRoom);

                $memberUpdate = User::find(Auth::user()->id);
                $memberUpdate->gradYear =   trim($request->get('gradYear'));
                $memberUpdate->class =      trim($classRoom[0]);
                $memberUpdate->room =       trim($classRoom[1]);
                $memberUpdate->CRNo =       trim($request->get('CRNo'));
                $memberUpdate->studenNo =   trim($request->get('studenNo'));
                $memberUpdate->idCardNo =   trim($request->get('idCardNo'));
                $memberUpdate->titleName =  trim($request->get('titleName'));
                $memberUpdate->name =       trim($request->get('name'));
                $memberUpdate->lastname =   trim($request->get('lastname'));
                $memberUpdate->nickname =   trim($request->get('nickname'));
                $memberUpdate->birthday =   trim($request->get('birthday'));
                $memberUpdate->address =    trim($request->get('address'));
                $memberUpdate->tel =        trim($request->get('tel'));
                $memberUpdate->contact =    trim($request->get('contact'));
                $memberUpdate->save();




                $log = new Log;
                $log->memberId = Auth::user()->id;
                $log->detail = 'Update Member,'.$memberUpdate;
                $log->save();
                return redirect(route('member',['action' =>'2']));


    }


   



}
