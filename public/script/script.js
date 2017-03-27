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
                $('#chat').append("<div class='message sent'><div class='content received'>"+chat_message.message+"</div></div>");
            }
            else $('#chat').append("<div class='message received'><img src='{{URL::asset('img/img.png')}}' width='32' height='32' alt=''> <div class='content received'>"+chat_message.message+"</div> </div>");
        }
    });

    socket.on('message-received', function (message) {
        if(message.conversation != conversation){
            console.log("Unread message!");
            $("div[data-chat='"+message.conversation+"']").addClass('unread');
        }
    });

    $('.conversation').click(function (event) {
        var url = $(this).attr('data-href');
        $("div[data-chat='"+conversation+"']").removeClass('active');
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
        //event.preventDefault();
    });

});

function connectSocket(id){
    socket.connect('http://localhost:3000', { query: "conversation="+conversation });
}