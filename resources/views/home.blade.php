@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h2>Conversations</h2>
            <div class="list-group text-center">
                @foreach($conversations as $conversation)
                    <a href="/c/{{$conversation->id}}" data-chat="{{$conversation->id}}" class="list-group-item
                        @if($conversation->id == $active->id)
                            active
                        @endif">
                    {{$conversation->participantsText($conversation->id)}}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-md-9">

            <div id="chat-box">
                @include('_chat-box')
            </div>
                <form class="form-group" method="get" action="test">
                    <input id="m" class="form-control" autocomplete="off"/>
                    <button class="form-control btn btn-primary" type="submit">Send</button>
                </form>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.min.js"></script>

            <script>
                var conversation = '{{$active->id}}';
                var socket =  io.connect('http://localhost:3000', { query: "conversation="+conversation });
                var user = '{{Auth::user()->name}}';
                $(document).ready(function () {

                    $('form').submit(function() {
                        var message = $('#m').val();
                        var data = {
                            '_token' : '{{csrf_token()}}',
                            'message' : message,
                            'user': user,
                            'conversation' : conversation
                        };
                        socket.emit('message',JSON.stringify(data));

                        $.ajax({
                            type: "post",
                            url: "/c/"+conversation+"/m/store",
                            data: data,
                            success: function(msg) {
                                console.log(msg);
                            }
                        });

                        $('#m').val('');
                        return false;
                    });

                    socket.on('chat-message:PeerSentMessage', function(message){
                        //console.log("Received message:"+ message);
                        var chat_message = JSON.parse(message);
                        if(chat_message.conversation != conversation){

                            //
                        }
                        else{
                            if(chat_message.user == user) {
                                $('#chat').append("<div class='message sent'>" + chat_message.message + "</div>");
                            }
                            else $('#chat').append("<div class='message received'><b>"+chat_message.user+" says:</b><br/>" + chat_message.message + "</div>");
                        }
                    });

                    socket.on('message-received', function (message) {
                        if(message.conversation != conversation){
                            console.log("Unread message!");
                            $("a[data-chat='"+message.conversation+"']").addClass('unread');
                        }
                    });

                    $('a').click(function (event) {
                        var url = $(this).attr('href');
                        $("a[data-chat='"+conversation+"']").removeClass('active');
                        $(this).addClass('active');
                        $(this).removeClass('unread');
                        console.log(url);
                        $.ajax({
                            type: "get",
                            url: url,
                            data: [],
                            success: function(msg) {
                                $('#chat-box').replaceWith(msg.box);
                                socket.emit('leave', conversation);
                                conversation = '' + msg.conversation + ''; // update connection
                                socket.emit('join', conversation);
                                //history.pushState(null, null, '/c/'+conversation);
                            }
                        });
                        event.preventDefault();
                    });

                });

                function connectSocket(id){
                    socket.connect('http://localhost:3000', { query: "conversation="+conversation });
                }

            </script>
        </div>
    </div>
</div>
@endsection
