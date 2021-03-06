<?php

namespace App\Http\Controllers;
use App\Competition_user;
use App\User;
use Request;
use Illuminate\Support\Collection;
use App\Http\Requests\CompetitionCreateRequest;
use App\Competition;
use Carbon\Carbon;
use Auth;

class CompetitionController extends Controller
{
    protected $fields = [
        'name' => '',
        'time' => '',
        'type' => '',
        'discription' => '',
        'id'=>'',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function notAllowed(){
        return redirect('/competition/info')
            ->with('info',"You do not have the permission to view this Competition.");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function single()
    {
        $competitions = Competition::where('endtime','>', Carbon::now())->Where('comType','single')
            ->orderBy('published_at', 'desc')
            ->paginate(config('competition.competition_per_page'));

        return view('competition.singlecompetition',compact('competitions'));
    }

    public function showComp($id){
        $competition = Competition::whereId($id)->firstOrFail();
        $user = $competition->user;
        $usersList = collect();
        $userLists= Competition_user::where('competition_id',$id)->get();
        foreach($userLists as $partuser){
            $usersList->push(User::whereId($partuser->user_id)->first());
        }
        $isJoin = Competition_user::where('competition_id',$id)->where('user_id',Auth::user()->id)->first();
        if($isJoin == null)
            $isJoin = false;
        else
            $isJoin = true;
        //dd($userLists);
        return view('competition.competitiondetail')->withCompetition($competition)->with('user',$user)->with('userLists',$userLists)->with('isJoin',$isJoin)->with('usersList',$usersList);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 返回我参与的活动
     */
    public function myCompetition(){
        $competition_users = Competition_user::where('user_id',Auth::user()->id)
            ->orderBy('published_at', 'desc')->get();


        $competitions = collect();

        foreach ($competition_users as $competition_user){
            $competitions->push(Competition::whereId($competition_user->competition_id)->first());
        }

        //dd($competitions);
        return view('competition.mycompetition')->with(compact('competitions'))->with(compact('competition_users'));

    }


    public function create(){
        return view('competition.launchcompetition');
    }

    public function store(CompetitionCreateRequest $request){

        $competition = new Competition();
        $competition->user_id = Auth::user()->id;
        $competition->name = $request->get("comName");
        $competition->limit_exp = $request->get('comLimit');
        $competition->comType = $request->get("comType");
        $competition->endtime = $request->get("comTime");
        $competition->description = $request->get("comDesc");
        $competition->money = intval($request->get("comMoney"));
        $competition->total = intval($request->get("comMoney"));
        //dd($competition->money);
        //dd(Auth::user()->wealth);
        if($competition->money>intval(Auth::user()->wealth))
            return redirect('/competition/info')
                ->with('info',"The competition '$competition->name' was not created Because you do not have enough money!");

        $competition->save();
        Auth::user()->wealth-=$request->get("comMoney");
        Auth::user()->experience+=config('competition.experience_per_launch');
        Auth::user()->save();

        $competition_user = new Competition_user();
        $competition_user->user_id = Auth::user()->id;
        $competition_user->user_name = Auth::user()->name;
        $competition_user->competition_id = $competition->id;
        $competition_user->path = 0;
        $competition_user->save();


        return redirect('/competition/info')
            ->with('info',"The competition '$competition->name' was created successfully. You have gained 5 exp.");
    }

    public function join(){
        $competition = Competition::whereId(request("competition_id"))->first();
        //dd($competition);
        if($competition->money>intval(Auth::user()->wealth))
            return redirect('/competition/info')
                ->with('info',"You cannot join the '$competition->name' Because you do not have enough money!");

        $competition->total+=intval($competition->money);
        $competition->save();
        Auth::user()->wealth -= intval($competition->money);
        Auth::user()->save();

        $competition_user = new Competition_user();
        $competition_user->user_id = Auth::user()->id;
        $competition_user->user_name = Auth::user()->name;
        $competition_user->competition_id = request("competition_id");
        $competition_user->path = 0;
        $competition_user->save();
        $competition = Competition::whereId(request("competition_id"))->firstOrFail();
        //dd($competition);
        return redirect('/competition/info')
            ->with('info',"You joined the competition '$competition->name' successfully.");
    }

    public function info(){
        return view('competition.info');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function group()
    {
        $competitions = Competition::where('endtime','<', Carbon::now())->Where('comType','group')
            ->orderBy('published_at', 'desc')
            ->paginate(config('competition.competition_per_page'));

        return view('competition.groupcompetition',compact('competitions'));
    }

    public function target()
    {
        $competitions = Competition::where('id','<', 100)->Where('comType','target')
            ->orderBy('published_at', 'desc')
            ->paginate(config('competition.competition_per_page'));

        return view('competition.targetcompetition',compact('competitions'));
    }
}
