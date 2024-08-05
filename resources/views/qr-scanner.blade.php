<!DOCTYPE html>
<html lang="en">

<head>
    <title>QR Scanner</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script type="text/javascript" src="js/instascan.js"></script> -->
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous">
    </script>
</head>

<body>
    <video id="preview"
    class="w-full lg:w-2/3 xl:1/2 mx-auto"></video>
    <div class="btn-group btn-group-toggle mb-5" data-toggle="buttons">
        <label class="btn btn-primary active">
            <input type="radio" name="options" value="1" autocomplete="off" checked> Front Camera
        </label>
        <label class="btn btn-secondary">
            <input type="radio" name="options" value="2" autocomplete="off">
                Back Camera 1
        </label>
    <label class="btn btn-secondary">
            <input type="radio" name="options" value="3" autocomplete="off">
            Back Camera 2
        </label>
    </div>
    <script type="text/javascript">
        var scanner = new Instascan.Scanner({
            video: document.getElementById('preview'),
            scanPeriod: 3,
            mirror: false
        });
        scanner.addListener('scan', function (content) {
            alert(content);
            window.location.href=content;
        });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
        alert('Camaras: '+ cameras.length);
                scanner.start(cameras[0]);
                $('[name="options"]').on('change', function () {
                    if ($(this).val() == 1) {
                        if (cameras[0] != "") {
                            scanner.start(cameras[0]);
                        } else {
                            alert('Camara delantera no encontrada!');
                        }
                    } else if ($(this).val() == 2) {
                        if (cameras[1] != "") {
                            scanner.start(cameras[1]);
                        } else {
                            alert('No hay camara posterior 1!');
                        }
                    } else if ($(this).val() == 3) {
                        if (cameras[2] != "") {
                            scanner.start(cameras[2]);
                        } else {
                            alert('No hay camara posterior 2!');
                        }
                    }
                });
            } else {
                console.error('No se encontraron camaras.');
                alert('No se encontraron camaras.');
            }
        }).catch(function (e) {
            console.error(e);
            alert(e);
        });
    </script>
</body>
</html>
