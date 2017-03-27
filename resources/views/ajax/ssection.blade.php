@extends('ajax.slayout')

@section('search')
    @if(!$users->isEmpty())
        @foreach($users as $user)
            <div class="result" data-name="{{$user->name}}" data-id="{{$user->id}}">
                <img src='{{URL::asset($user->picture)}}' height='30' width='30'>
                <div class='username'>{{$user->name}}</div>
            </div>
        @endforeach
    @else
        <div class="result">
            No results found.
        </div>
    @endif
@stop