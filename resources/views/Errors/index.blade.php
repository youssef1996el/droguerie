<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
{{--     <link rel="stylesheet" href="./style.css"> --}}
    <style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    font-family: "Montserrat";
    color: rgb(56,56,56);
}
.wrapper {
    display: flex;
    align-items: center;
    flex-direction: column;
}
.wrapper h1 {
    font-size: 3rem;
    margin-top: 20px;
}
.wrapper .message {
    font-size: 1.5rem;
    padding: 20px;
    width: 60%;
    text-align: center;
}
.wrapper .btn {
    background: #5249fe;
    padding: 20px;
    font-size: 1.5rem;
    text-decoration: none;
    color: #fff;
    border-radius: 10px;
}
.wrapper .btn:hover {background: #8b85f8;}
.wrapper .copyRights {margin-top: 50px;}

    </style>
</head>
<body>
    <img src="{{asset('images/404.svg')}}" alt="">
    <div class="wrapper">
        <h1>{{$title}}</h1>
       <p class="message">
       {{$body}}
       </p>
       <a href="{{url('/home')}}  {{--  {{ url()->previous() }} --}}" class="btn">Retourner à la page précédente</a>

    </div>
</body>
</html>
