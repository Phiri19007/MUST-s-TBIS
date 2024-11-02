<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run PHP Script</title>
    <script>
        function runScript() {
            // AJAX request to call the PHP script
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "update_cars.php", true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Display result from PHP script
                    document.getElementById("output").innerHTML = xhr.responseText;
                }
            };

            xhr.send();
        }
    </script>
</head>

<body>

    <h1>Run Update Script</h1>
    <button onclick="runScript()">Run Script</button>

    <div id="output" style="margin-top: 20px;">
        <!-- This will display the PHP script output -->
    </div>

</body>

</html>