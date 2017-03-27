<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use App\Conversation;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Message;
use App\Events\MessageReceived;
use App\Events\NewConversationCreated;

Route::get('/test', function(){
    return view('main');
});

Route::get('/search', function (Request $request){
    sleep(1);
    $users = User::where('name', 'LIKE', '%'.$request->input('user').'%')->get();
    $results = View::make('ajax.ssection')->with(['users' => $users])->renderSections()['search'];

    return ['results' => $results];
});

Route::get('/', function() {
    return view('welcome');
});

Route::get('/users', function () {
   return User::all();
});

Route::post('/c/create', function (Request $request){
    $sender = Auth::user()->id;
    $receiver = $request->input('friend');
    $content = $request->input('message');
    $user1 = Auth::user();
    $user2 = User::where('id', $receiver)->first();

    $conversation = new Conversation();
    $conversation->user1 = $sender;
    $conversation->user2 = $receiver;
    $conversation->save();

    $message = new Message();
    $message->content = $content;
    $message->user_id = $sender;
    $message->conversation_id = $conversation->id;
    $message->save();

    $c1 = View::make('ajax.csection')->with(['conversation' => $conversation, 'user' => $user2])->renderSections()['conversation'];
    $c2 = View::make('ajax.csection')->with(['conversation' => $conversation, 'user' => $user1])->renderSections()['conversation'];
    event(new NewConversationCreated($conversation->id, $c1, $c2, 'user'.$sender, 'user'.$receiver));

    return 'true';
});

Route::get('/c/{c}/simple', function($id){
    $conversation = Conversation::where('id', $id)->first();
    return ['c' => View::make('ajax.csection')->with(['conversation' => $conversation])->renderSections()['conversation']];
});

Route::get('/c/{conversation}', function ($id){
    $conversation = Conversation::where('id', $id)->first();
    $participants = Conversation::participantsText($conversation->id);
    $messages = $conversation->messages()->orderBy('created_at')->with('user')->get();
    $box = View::make('ajax.chat')->with([
        'active' => $conversation,
        'participants' => $participants,
        'messages' => $messages
    ])->renderSections()['chat-box'];

    return ['box'=>$box, 'conversation'=>$conversation, 'participants'=>$participants];
});

Route::get('/c/check/{user}', function ($user){
    $s = Auth::user()->id;
    $r = User::where('id', $user)->first()->id;

    return Conversation::whereIn('user1', [$s, $r])->whereIn('user2', [$s, $r])->get();

});

Route::post('/c/{conversation}/m/store', function(Request $request, $id){

    $message = new Message();
    $message->content = $request->input('message');
    $message->conversation_id = (int) $request->input('conversation');
    $message->user_id = Auth::user()->id;
    $message->save();

    event(new MessageReceived(Auth::user()->name, $message->content, $message->conversation_id));

    return 'true';
});

Route::auth();

Route::get('/home', function() {
    $conversations = User::conversations(Auth::user()->id);
    if(!$conversations->isEmpty()) {
        $active = $conversations->first();
        $messages = $active->messages()->orderBy('created_at')->with('user')->get();
        $participants = $active->participantsText($active->id);
    }
    else{
        $active = null;
        $messages = [];
        $participants = null;
    }

    return view('home1')->with([
            "conversations" => $conversations,
            "active" => $active,
            "participants" => $participants,
            "messages" => $messages
    ]);
});


Route::get('test', function(Request $request){
    return view('test');
});