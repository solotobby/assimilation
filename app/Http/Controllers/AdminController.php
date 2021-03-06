<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\HouseholdLocation;
use App\Models\SubGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function allCards(Request $request)
    {
        if(isset($request->start) && isset($request->end)) {
            // $year = Carbon::parse($request->month)->format('Y');
            // $month = Carbon::parse($request->month)->format('m');
            $cards = Card::whereDate('created_at', '>=', $request->start)
            ->whereDate('created_at', '<=', $request->end)->get();//whereYear('created_at', $year)->whereMonth('created_at', $month)->get();            
        }else{
            $cards = Card::orderBy('id', 'desc')->orderBy('created_at', 'desc')->get();
        }
        
        return view('admin.cardlist', ['cards' => $cards]);
    }

    public function contactedCards()
    {
        $cards = Card::where('comment', '!=', null)->orderBy('created_at', 'desc')->get();
        return view('admin.contacted', ['cards' => $cards]);
    }

    public function visitedCards()
    {
        $cards = Card::where('is_visited', true)->orderBy('created_at', 'desc')->get();
        return view('admin.visited', ['cards' => $cards]);
    }

    public function userList()
    {
        $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get();
        return view('admin.user_list', ['users' => $users]);
    }

    public function userInfo($id)
    {
        $user = User::find($id);
        $cards = $user->myCards()->orderBy('created_at', 'desc')->get();
        return view('admin.view_user_activity', ['user' => $user, 'cards' => $cards]);
    }

    public function filter(Request $request)
    {
        // // return $request->month;

        // if(isset($request->month)) {
        //     $request->month;



            
        // }else{
        //     $audits = AuditTrail::orderBy('created_at', 'desc')->take(100)->get();
        // }

        // return Card::whereDate('created_at', $request->month)->get();

    }

    public function uncontacted()
    {
        $uncontacted = Card::where('comment', null)->get();

        return view('admin.uncontacted', ['uncontacted' => $uncontacted]);
    }

    public function manageHousehold()
    {
        $subgroup = SubGroup::all();
        $household = HouseholdLocation::orderBy('created_at', 'desc')->get();
        return view('admin.manage_household', ['subgroups' => $subgroup, 'household' => $household]);
    }

    public function storeSubgroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:sub_groups',
        ]);
        $subgroup = SubGroup::create(['name' => $request->name]);
        return back()->with('success', 'Subgroup Created Successfully'); 
    }

    public function storeHousehold(Request $request)
    {
        $request->validate([
            'household_name' => 'required|string|unique:household_locations',
        ]);
       $household = HouseholdLocation::create([
           'sub_group_id' => $request->sub_group_id, 
           'household_name' => $request->household_name
        ]);
        return back()->with('success', 'Household Created Successfully'); 
    }

    public function viewVisitationList()
    {
        $cards = Card::where('is_visited', true)->orderBy('created_at', 'desc')->get();
        return view('admin.visitation_report', ['cards' => $cards]);
    }
}
