<html>
    <head>
        <title>Test Ajax</title>
    </head>
    <body>
        <form action="" method="post">
            <p>
                <label for="url">Paste the URL of the image:</label>
                <input type="text" name="url" />
            </p>
            <p>
                <input type="submit" name="submit" value="Encode" />
                <input type="button" name="ajax" value="Send Ajax" />
            </p>
        </form>
        
        <h4>The code is: </h4>
        <div id="code"><?php print ($_SERVER['REQUEST_METHOD'] == "POST") ? "data:image/jpeg;base64," . base64_encode(file_get_contents($_POST['url'])) : "No data" ; ?></div>
        
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("input[name='ajax']").on('click', function() {
                    var code = $("div#code").text();
                    if( code == "No data") {
                        alert("Please type an url of the image to encode.");
                        return false;
                    }
                    
                    $.ajax({ 
                        type: "POST", 
                        url: "images.php",
                        dataType: 'json',
                        data: {
                            data : code,
                            filename : "filename.jpg"
                        },
                        beforeSend : function() {
                            $("input[name='ajax']").val('Sending ajax...');
                        },
                        complete : function( data ) {
                            $("input[name='ajax']").val('Send Ajax');
                            
                            if( data.responseJSON == "true") {
                                $("div#code").text('File stored!!');
                            } else {
                                $("div#code").text('Error while storing the file...');
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>