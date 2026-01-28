<!DOCTYPE html>
<html>
<head>
    <title>Telnet Web Terminal</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #terminal {
            background: black;
            color: #0f0;
            padding: 10px;
            height: 300px;
            overflow-y: auto;
            font-family: monospace;
        }
    </style>
</head>
<body>

<div id="terminal"></div>
<input type="text" id="command" placeholder="Enter command..." autofocus>

<script>
$("#command").keypress(function(e){
    if(e.which === 13){
        let cmd = $(this).val();
        $(this).val("");

        $.post("telnet.php", {command: cmd}, function(res){
            $("#terminal").append("<div>> " + cmd + "</div>");
            $("#terminal").append("<div>" + res + "</div>");
            $("#terminal").scrollTop($("#terminal")[0].scrollHeight);
        });
    }
});
</script>

</body>
</html>
