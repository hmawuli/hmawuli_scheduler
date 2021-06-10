<html>
    <head>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <!-- View Components -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
        <script defer src="{{ mix('js/app.js') }}"></script>
    </head>

    <body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8">
                <div id="app">
                    <br><br>
                    <h4>Token Management Portal</h4>
                    <br>
                    <passport-clients></passport-clients>
                    <passport-authorized-clients></passport-authorized-clients>
                    <passport-personal-access-tokens></passport-personal-access-tokens>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
    
    </body>
</html>