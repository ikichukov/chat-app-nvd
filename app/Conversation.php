<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Participant;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    public function messages(){
        return $this->hasMany('App\Message', 'conversation_id', 'id');
    }

    public function sender(){
        return $this->hasOne('App\User', 'id', 'user1');
    }

    public function receiver(){
        return $this->hasOne('App\User', 'id', 'user2');
    }

    public static function participantsInfo($id){
        return Conversation::where('id', $id)->with('sender', 'receiver')->first();
    }

    public static function participantsText($id){
        $c = Conversation::participantsInfo($id);
        return ($c->sender->name === Auth::user()->name)?$c->receiver->name:$c->sender->name;
    }

    public function picture(){
        $sender = $this->sender;
        $receiver = $this->receiver;

        if($sender->id == Auth::user()->id) return $receiver->picture;
        else return $sender->picture;
    }

}
