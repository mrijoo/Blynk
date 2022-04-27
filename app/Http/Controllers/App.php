<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataButton;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class App extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $databuttons = DataButton::where('user_id', Auth::user()->id)->get();
        if (Auth::user()->token == null && Auth::user()->server == null) {
            return view('layouts.app');
        } else {
            $Projectinfo = Http::get(Auth::user()->server.'/'.Auth::user()->token.'/project')->json();
            $isHardwareConnected = Http::get(Auth::user()->server.'/'.Auth::user()->token.'/isHardwareConnected')->body();
            $this->value = [];
            foreach ($databuttons as $item) {
                if (Http::get(Auth::user()->server.'/'.Auth::user()->token.'/get/'.$item->pin)->body() == "Requested pin doesn't exist in the app."){
                    $this->value[$item->id] = 0;
                } elseif (Http::get(Auth::user()->server.'/'.Auth::user()->token.'/get/'.$item->pin)->body() == "Wrong pin format."){
                    $this->value[$item->id] = 0;
                } else {
                    $value = Http::get(Auth::user()->server.'/'.Auth::user()->token.'/get/'.$item->pin)->json()[0];
                    if($item->type == 'Button') {
                        if ($value == '1') {
                            $this->value[$item->id] = 'ON';
                        } elseif($value == '0') {
                            $this->value[$item->id]= 'OFF';
                        } else {
                            $this->value[$item->id] = 'on';
                        }
                    } else {
                        $this->value[$item->id] = $value;
                    }
                }
            }
            return view('layouts.app', compact('databuttons', 'Projectinfo'), ['isHardwareConnected' => $isHardwareConnected, 'value' => $this->value]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DataButton::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'pin' => $request->pin,
            'type' => $request->type
        ]);
        return back()->with('toast_success', 'Saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = User::find(Auth::user()->id);
        if ($request->Server == null) {
            $Valitdate = Http::get('blynk-cloud.com/'.$request->token.'/isAppConnected')->body();
            if ($Valitdate == "Invalid token.") {
                return redirect('/')->with('toast_error','Invalid token.');                ;
            } else {
                $data->update([
                    'token' => $request->token,
                    'server' => 'blynk-cloud.com'
                ]);
                return back()->with('toast_success', 'Saved successfully!');
            }
        } else {
            $Valitdate = Http::get($request->Server.'/'.$request->token.'/isAppConnected')->body();
            if ($Valitdate == "Invalid token.") {
                return back()->with('toast_error','Invalid token.');
            } else {
                $data->update([
                    'token' => $request->token,
                    'server' => $request->Server
                ]);
                return back()->with('toast_success', 'Saved successfully!');
            }
        }
        // $data = DataButton::finderfail(Auth::user()->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DataButton::find($id)->delete();
        return back()->with('toast_success', 'Successfully deleted!');
    }

    public function API(Request $request, $action) {
        if ($action == 'get') {
            if($request->type == 'Button') {
                $value = Http::get(Auth::user()->server.'/'.Auth::user()->token.'/get/'.$request->pin)->json()[0];
                if ($value == '1') {
                    $value = 'ON';
                } elseif($value == '0') {
                    $value = 'OFF';
                } else {
                    $value = 'Error';
                }
            } else {
                $value = Http::get(Auth::user()->server.'/'.Auth::user()->token.'/get/'.$request->pin)->json()[0];
            }
        } elseif ($action == 'update') {
            $value = Http::get(Auth::user()->server.'/'.Auth::user()->token."/update/".$request->pin."?value=".$request->value)->body();
        }
        return $value;
    }
}
