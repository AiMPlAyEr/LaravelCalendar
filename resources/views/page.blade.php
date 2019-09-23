<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap" rel="stylesheet">

        <!-- Bootstrap -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Roboto';
                font-weight: 300;
                height: 100vh;
                margin: 0;
                font-size: 14px;
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
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .calendar-title {
                font-size: 24px;
                font-weight: bold;
            }

            .greyedOut {
                background-color: #eee;
            }
            
            td {
                width: 60px !important;
                height: 60px !important;
                max-width: 60px !important;
                min-width: 40px !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .navigation {
                display: flex;
                justify-content: space-between;
            }

            .event {
                display: block;
                background-color: gray;
                min-height: 20px;
                width: 100%;
                margin-bottom: 5px;
                color: #fff;
                font-size: 14px;
                padding: 0px 5px;
            }

            .hiddenDescription {
                display: none;
            }

            .type-1 {
                background-color: orangered;
            }

            .type-2 {
                background-color: royalblue;
            }

            .type-3 {
                background-color: teal;
            }

            .start {
                border-top-left-radius: 10px;
                border-bottom-left-radius: 10px;
                position: relative;
                left: 11px;
            }

            .center {

            }

            .end {
                border-top-right-radius: 10px;
                border-bottom-right-radius: 10px;
                position: relative;
            }

            .single {
                border-radius: 11px;
            }

            .legendPreview {
                width: 40px;
            }

            .btn-flat {
                border-radius: 0px;
            }

            .table-custom {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container" style="margin-top: 50px;">
            <div class="row">
                <div class="col-lg-12">
                    {!! $calendar !!}
                </div>
            </div>
        </div>
    </body>
</html>
