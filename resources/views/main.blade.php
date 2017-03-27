<!doctype html>
<html>
    <head>
        <title>Testing partial refresh</title>
        <style>
            html, body{
                width: 100%;
            }
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    </head>
    <body>
        <div style="width: 50%;">
            <h1>Овој дел ќе остане стат.</h1>
            <a href="/change" onclick="changeURL()">Смени го вториот дел.</a>
        </div>

        <script>
            $(document).ready(function() {
                $('a').click(function (e) {
                    window.history.pushState('page2', 'Title', '/page2.php');
                    e.preventDefault();
                });
            });
        </script>

    </body>
</html>