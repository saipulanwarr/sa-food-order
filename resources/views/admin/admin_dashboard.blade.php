<html>
    <title>Admin Dashboard Page</title>
    <body>
        <h1>Admin Dashboard</h1>

        @if(Session::has('error'))
            <li>{{Session::get('error')}}</li>
        @endif

        @if(Session::has('success'))
            <li>{{Session::get('success')}}</li>
        @endif

        <a href="">Logout</a>
    </body>
</html>