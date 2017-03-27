var server = require('http').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('message-received');
redis.subscribe('conversation-created');

io.on('connection', function (socket) {
    var conversation = socket.request._query['conversation'];
    socket.join(conversation);
    //console.log('User connected to conversation #' + conversation);
    //console.log(io.nsps['/'].adapter.rooms[conversation])

    socket.on('message', function(message) {
        console.log(message);
        io.to(conversation).emit('chat-message', message);
    });

    socket.on('leave', function (message) {
        socket.leave(message);
    });

    socket.on('join', function (message) {
        socket.join(message);
        conversation = message;
    });

    socket.on('join-user', function (message) {
        //console.log('user joined', message);
        socket.join(message);
    });

    socket.on('disconnect', function(){
        socket.leave(conversation);
    })
});

redis.on('message', function(channel, message) {
    //console.log('REDIS: ', channel, message);
    var message = JSON.parse(message);
    if(channel == 'message-received'){
        io.emit(channel, message.data);
    }
    else if(channel = 'conversation-started'){
        //console.log('users',message.data.user1, message.data.user2);
        io.to(message.data.user1).emit('conversation-created', message.data.conversation1);
        io.to(message.data.user2).emit('conversation-created', message.data.conversation2);
    }

});

server.listen(3000, function(){
    console.log('listening on *:3000');
});