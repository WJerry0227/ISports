<?php

namespace App\Http\Controllers;
use App\friend;
use App\User;
use Storage;
use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;

class UserController extends Controller
{
    //
    public function usermanagement(){
        return view('user.usermanagement');
    }

    public function launchmoments(){
        return view('user.launchmoments');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $preg='/^[a-zA-Z\x{4e00}-\x{9fa5}]{6,20}$/u';
        $preg2='/^[a-zA-Z]+$/u';
        $preg3='/^[\x{4e00}-\x{9fa5}]+$/u';

        if(preg_match($preg2,$data) || preg_match($preg3,$data)){
            return redirect('/competition/info',"ERROR");//纯中文或纯英文
        }elseif(preg_match($preg,$data)){
            $this->launchmoments();
        }else{
            return redirect('/competition/info',"ERROR");//纯中文或纯英文
        }

        //英文、数字、下划线6-20位字符
        $preg='/^[\w\_]{6,20}$/u';
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    public function deletefriend($id){
        friend::where('follow_id',$id)->where('user_id',Auth::user()->id)->delete();

        $friend_ids = friend::where('user_id',Auth::user()->id)->get();
        $friends = collect();
        foreach ($friend_ids as $friend_id){
            $friends->push(User::whereId($friend_id->follow_id)->first());
        }

        return redirect('user/friendmanagement')->with('friends',$friends);
    }

    public function addfriend(Request $request){
        $user = User::where('email',$request->get('email'))->first();
        //dd($user);
        $isFriend = friend::where('user_id',Auth::user()->id)->where('follow_id',$user->id)->first();

        if($isFriend!=null)
            return redirect('/user/info')
                ->with('info',"You have followed this user.");
        $friend = new friend();
        $friend->user_id = Auth::user()->id;
        $friend->follow_id = $user->id;
        $friend->save();

        $friend_ids = friend::where('user_id',Auth::user()->id)->get();
        $friends = collect();
        foreach ($friend_ids as $friend_id){
            $friends->push(User::whereId($friend_id->follow_id)->first());
        }
        return redirect('user/friendmanagement')->with('friends',$friends);

    }

    public function friendmanagement(){
        $friend_ids = friend::where('user_id',Auth::user()->id)->get();
        $friends = collect();
        foreach ($friend_ids as $friend_id){
            $friends->push(User::whereId($friend_id->follow_id)->first());
        }
        return view('user.friendmanagement')->with('friends',$friends);
    }

    public function userphoto(Request $request){
        // 文件上传方法
        if ($request->isMethod('post')) {
            $file = $request->file('picture');
            //dd($file);
            // 文件是否上传成功
            if ($file->isValid()) {
                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $type = $file->getClientMimeType();     // image/jpeg
                // 上传文件
                $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
                // 使用我们新建的uploads本地存储空间（目录）
                //dd(explode("/",Auth::user()->user_photo)[2]);
                //Storage::disk('public')->delete(explode("/",Auth::user()->user_photo)[2]);
                Storage::disk('public')->put($filename, file_get_contents($realPath));
                Auth::user()->user_photo = "/storage/".$filename;
                Auth::user()->save();
                return redirect('/user/info')
                    ->with('info',"You have uploaded your head photo successfully.");
            }
        }
        return redirect('/user/info')
            ->with('info',"Submit photo failure.");


    }

    public function info(){
        return view('user.info');
    }

    public function userdata(){
        //dd(request("intro"));
        Auth::user()->name = request("name");
        Auth::user()->intro = request("intro");
        Auth::user()->save();

        return redirect('user/info')->with('info',"You have updated your profile");
    }
}
