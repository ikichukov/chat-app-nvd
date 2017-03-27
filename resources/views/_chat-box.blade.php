<div id="chat-box">
    @foreach($messages as $message)
        <div class="message @if($message->user->id == Auth::user()->id)sent @else received @endif">
            <img src="{{URL::asset($message->user->picture)}}" width="32" height="32" alt="">
            <div class="content received">
                {{ $message->content }}
            </div>
        </div>
    @endforeach
</div>