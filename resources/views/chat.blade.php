@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1>Chat room #{{ $conversation->name }}</h1>
                <div id="chat">
                </div>
                <form class="form-group" method="get" action="test">
                    <input id="m" class="form-control" autocomplete="off"/>
                    <button class="form-control btn btn-primary" type="submit">Send</button>
                </form>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.min.js"></script>

                <script>
                    var socket = io.connect('http://localhost:3000', { query: "conversation={{$conversation->id}}" });

                    $(document).ready(function () {

                        $('form').submit(function() {
                            socket.emit('message', $('#m').val());
                            $('#m').val('');
                            return false;
                        });

                        socket.on('chat-message:PeerSentMessage', function(message){
                            $('#chat').append($("<div class='message'> <span>").text(message));
                        });

                    });
                </script>
            </div>
        </div>
    </div>
@endsection
