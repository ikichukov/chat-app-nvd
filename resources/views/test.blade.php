<!doctype html>
<html>
    <head>
        <title>Some title</title>
        <style>
            input, #results{
                width: 200px;
            }

            #results{
                height: 100px;
                border: 1px solid black;
                visibility: visible;
                background-color: white ;
                position: absolute;
            }

            .result{
                width: 100%;
                display: block;
                background-color: #dddddd;
            }

            .result > img{
                border-radius: 40px;
            }
        </style>
    </head>
    <body>

    <input type="text"> <br/>
    <div id="results">
    </div>
    <span>Some text goes here.</span>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        var tmp = 0;
        $(document).ready(function () {
            $('input').keyup(function () {
                $('#results').css('visibility', 'visible');
                var input = $('input').val();
                $.ajax({
                    type: "get",
                    url: "/search?user=" + input,
                    data: '',
                    success: function(msg) {
                        $('#results').empty();
                        for(var i=0; i<msg.length; i++){
                            tmp = msg;
                            $('#results').append(msg[i].name + '<br/>');
                        }
                    }
                });
            });

            $('input').focusout(function () {
                $('#results').css('visibility', 'hidden');
            });
        });
    </script>
    </body>
</html>