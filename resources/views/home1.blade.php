@extends('layouts.app')

@section('content')

    <!-- header begins here -->
    <div class="header">
        <div class="left-side vertical-center border-right">
            <div class="horizontal-center">Chat</div>
            <img id="newMessage" class="pull-right" src="{{URL::asset('img/note.png')}}" title="New message">
        </div>
        <div class="right-side vertical-center border-left">
            <div id="participants" class="horizontal-center participants"><b>{{ $participants }}</b></div>
            <div id="newParticipants">
                To: <div id="friends"></div>
                <input id="search" class="input" type="text" placeholder="Search for recipients..." autocomplete="off"/>
                <div id="results">
                </div>
            </div>
        </div>
    </div>

    <!-- main part begins here -->
    <div class="main">

        <!-- conversations and search -->
        <div class="left-side border-right">
            <div id="conversations">
                @foreach($conversations as $conversation)
                    <div class="conversation @if($conversation->id == $active->id)active @endif" data-href="/c/{{$conversation->id}}" data-chat="{{$conversation->id}}">
                        <div class="conversation-image">
                            <img src="{{URL::asset($conversation->picture())}}" width="50" height="50">
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-title">{{ $conversation->participantsText($conversation->id) }}<div class="time">12:56</div></div>
                            <div class="last-message" data-chat="{{$conversation->id}}">
                                @if($conversation->messages->last()->user->id == Auth::user()->id)
                                    You:
                                @endif
                                {{ $conversation->messages->last()->content }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- chat and settings -->
        <div class="right-side border-left">

            <!-- chat starts here -->
            <div class="chat border-right">

                <!-- box with chat messages -->
                <div id="chat" class="box border-bottom">
                    <div id="chat-box">
                        @include('_chat-box')
                    </div>
                </div>

                <div class="animation text-center">
                    <div>
                        <img src="{{URL::asset('/img/paper-plane.gif')}}" alt="">
                        <form id="firstMessageForm" method="post" action="#">
                            <input id="_token" type="hidden" value="{{csrf_token()}}" />
                            <input id="friend" type="hidden" />
                            <textarea id="firstMessage" cols="25" rows="3">Say hi!</textarea>
                            <button id="createConversation" class="btn btn-success btn-lg disabled" type="submit">Start conversation</button>
                        </form>
                    </div>
                </div>

                <!-- submit form -->
                <div id="input-box" class="input">
                    <form id="sendMessageForm" method="get" action="#">
                        <div class="msg-input vertical-center">
                            <div id="input-field">
                                <input id="p" type="hidden" value="{{URL::asset(Auth::user()->picture)}}" />
                                <input id="m" type="text" placeholder="Write your message..." autocomplete="off"/>
                            </div>
                        </div>
                        <div class="submit">
                            <button id="submit-btn" type="submit">Send</button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- settings part starts here -->
            <div class="settings">
                <div class="peer-info border-bottom vertical-center">
                    <div class="thumb horizontal-center">
                        <img src="{{URL::asset($active->picture())}}" width="50" height="50">
                        <div class="participants" id="name">{{ $active->participantsText($active->id) }}</div>
                    </div>
                </div>
                <div class="user-info vertical-center">
                    <div class="thumb horizontal-center text-center">
                        <img src="{{URL::asset(Auth::user()->picture)}}" width="90%">
                        <h2>{{ Auth::user()->name }}</h2>
                    </div>
                </div>
            </div>

        </div>
    </div>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.min.js"></script>
                <script>
                    var conversation = '{{$active->id}}';
                    var socket =  io.connect('http://localhost:3000', { query: "conversation="+conversation });
                    socket.emit('join-user', 'user{{Auth::user()->id}}');
                    var user = '{{Auth::user()->name}}';
                    scrollToBottom();
                    
                    $(document).ready(function () {

                        $("#sendMessageForm").submit(function() {
                            var message = $('#m').val();
                            var picture = $('#p').val();
                            var data = {
                                '_token' : '{{csrf_token()}}',
                                'message' : message,
                                'user': user,
                                'conversation' : conversation,
                                'picture' : picture
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

                        $("#firstMessageForm").submit(function() {

                            var token = $("#firstMessageForm > input").val();
                            var message = $("#firstMessageForm > textarea").val();
                            var friend = $("#friend").val();
                            var data = {
                                '_token': token,
                                'message': message,
                                'friend': friend
                            };
                            $.ajax({
                                type: "post",
                                url: "/c/create",
                                data: data,
                                success: function(msg) {
                                    console.log(msg);
                                }
                            });
                            return false;
                        });

                        socket.on('chat-message', function(message){
                            //console.log("Received message:"+ message);
                            var chat_message = JSON.parse(message);
                            if(chat_message.user == user) {
                                $('#chat-box').append("<div class='message sent'><div class='content received'>"+chat_message.message+"</div></div>");
                                $(".last-message[data-chat='"+chat_message.conversation+"']").text("You: "+chat_message.message);
                            }
                            else {
                                $('#chat-box').append("<div class='message received'><img src='"+ chat_message.picture +"' width='32' height='32' alt=''> <div class='content received'>" + chat_message.message + "</div> </div>");
                                $(".last-message[data-chat='"+chat_message.conversation+"']").text(chat_message.message);
                            }

                            scrollToBottom();
                        });

                        socket.on('message-received', function (message) {
                            if(message.conversation != conversation){
                                console.log("Unread message!");
                                $(".last-message[data-chat='"+message.conversation+"']").text(message.message);
                                $("div[data-chat='"+message.conversation+"']").addClass('unread');
                            }
                        });

                        socket.on('conversation-created', function (message) {
                            console.log('conversation created');
                            $("#conversations").prepend(message);
                            hideSearch();
                        });

                        $('#conversations').on('click', '.conversation', function (event) {
                            hideSearch();
                            var url = $(this).attr('data-href');
                            $(".active").removeClass('active');
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
                                    conversation = '' + msg.conversation.id + ''; // update connection
                                    socket.emit('join', conversation);
                                    $(".participants b").text(msg.participants);
                                }
                            });
                            //event.preventDefault();
                        });

                        $("#newMessage").click(function () {
                            if(conversation == -1) return;
                            else {
                                conversation = -1;
                                $(".animation").css('display', 'flex');
                                $("#participants").css('display', 'none');
                                $(".active").removeClass('active');
                                $("#chat-box").empty();
                                $("#conversations").prepend("<div id='newConversation' class='conversation active'><div class='conversation-image'><img src='{{URL::asset('img/user.png')}}' width='50' height='50'></div><div class='conversation-info'><div class='conversation-title'>New message<span id='to'></span><span id='removeNew'>x</span></div></div></div>");
                                $("#newParticipants").css('visibility', 'visible');
                                $(".box").css('display', 'none');
                                $("#input-box").css('display', 'none');
                                $("#search").css('visibility', 'visible');
                            }
                        });

                        $('#search').keyup(function () {
                            $('#results').css('visibility', 'visible');
                            var input = $('#search').val();
                            $('#results').empty();
                            $("#results").append("<img src='{{URL::asset('img/loading.gif')}}' width='30px' height='30px' /> <span style='font-size: 14px;'>Searching...</span>");
                            $.ajax({
                                type: "get",
                                url: "/search?user=" + input,
                                data: '',
                                success: function(msg) {
                                    $('#results').empty();
                                    $('#results').append(msg.results);
                                }
                            });
                        });

                        $('#results').on('click', '.result', function() {
                            var id = $(this).data('id');
                            var name = $(this).data('name');

                            $.ajax({
                                type: "get",
                                url: "/c/check/" + id,
                                data: '',
                                success: function(msg) {
                                    if(msg.length != 0) $("div[data-chat="+msg[0].id+"]").click();
                                    else{
                                        $("#friends").append("<span>" + name + "<div class='remove'>x</div></span>");
                                        $("#search").css('visibility', 'hidden');
                                        $("#to").append(' to ' + name);
                                        $("#friend").val(id);
                                        $("#createConversation").removeClass('disabled');
                                    }
                                }
                            });
                        });

                        $('body').click(function () {
                            $('#results').css('visibility', 'hidden');
                            $('#search').val('');
                        });

                        $("#friends").on("mouseover", "span", function () {
                            $(this).children().eq(0).css('visibility', 'visible');
                        });

                        $("#friends").on("mouseout", "span", function () {
                            $(this).children().eq(0).css('visibility', 'hidden');
                        });

                        $("#friends").on('click', '.remove', function () {
                            $(this).parent().remove();
                            $("#to").empty();
                            $("#search").css('visibility', 'visible');
                            $("#createConversation").addClass('disabled');
                        });

                        $("#conversations").on('mouseover', '#newConversation', function () {
                            $('#removeNew').css('visibility', 'visible');
                        });

                        $("#conversations").on('mouseout', '#newConversation', function () {
                            $('#removeNew').css('visibility', 'hidden');
                        });

                        $("#conversations").on('click', '#removeNew', function () {
                            hideSearch();
                        });
                    });

                    function connectSocket(id){
                        socket.connect('http://localhost:3000', { query: "conversation="+conversation });
                        socket.emit('join-user', 'user{{Auth::user()->id}}');
                    }

                    function scrollToBottom() {
                        $("#chat").scrollTop($("#chat")[0].scrollHeight);
                    }

                    function hideSearch() {
                        $(".animation").css('display', 'none');
                        $("#newParticipants").css('visibility', 'hidden');
                        $("#search").css('visibility', 'hidden');
                        $("#participants b").empty();
                        $("#participants").css('display', 'initial');
                        $('#friends').empty();
                        $(".box").css('display', 'block');
                        $("#input-box").css('display', 'block');
                        $("#newConversation").remove();
                        conversation = -2;
                    }
                </script>
            </div>
        </div>
    </div>
@endsection
