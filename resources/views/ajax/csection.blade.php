@extends('ajax.clayout')

@section('conversation')
    <div class="conversation" data-href="/c/{{$conversation->id}}" data-chat="{{$conversation->id}}">
        <div class="conversation-image">
            <img src="{{URL::asset($user->picture)}}" width="50" height="50">
        </div>
        <div class="conversation-info">
            <div class="conversation-title">{{ $user->name }}<div class="time">12:56</div></div>
            <div class="last-message" data-chat="{{$conversation->id}}">
                @if($conversation->messages->last()->user->id == Auth::user()->id)
                    You:
                @endif
                {{ $conversation->messages->last()->content }}
            </div>
        </div>
    </div>
@stop