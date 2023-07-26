<!doctype html>
<head>
    <title>
    </title>

    <style>
        @page { margin: 0; }
    </style>


    @vite('resources/css/app.css')
    @vite('resources/css/styles.scss')

</head>

<body style="word-spacing:normal;background-color:#F4F4F4;padding-top: 40px;padding-bottom: 40px">
{{$slot}}
</body>

</html>
