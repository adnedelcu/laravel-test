<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}" />
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h1>Shopworks Staffing</h1>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <th>Staff ID</th>
                        @foreach ($shifts as $dayNumber => $shiftInfo)
                        <th>Day {{$dayNumber}}</th>
                        @endforeach
                        </thead>

                        <tbody class="text-center">
                        @foreach ($staffIds as $staffId)
                            <tr>
                                <td>{{ $staffId }}</td>

                                @foreach ($shifts as $dayNumber => $shiftInfo)
                                <td>
                                    @if(isset($shiftInfo[$staffId]))
                                        {{$shiftInfo[$staffId]['start_time']}} - {{$shiftInfo[$staffId]['end_time']}}
                                    @else
                                        {{ '-' }}
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        @endforeach

                        <tr>
                            <td>Total Hours</td>
                            @foreach ($totalHours as $dayNumber => $workedHours)
                            <td>{{$workedHours}}</td>
                            @endforeach
                        </tr>

                        <tr>
                            <td>Minutes worked alone</td>
                            @foreach ($minutesAlone as $dayNumber => $workedAlone)
                            <td>{{$workedAlone}}</td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
