<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                @guest
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </nav>
    </header>
    <h1>Welcome to the game!</h1>
    <p>This game is an arcade game where you can fight with enemies.</p>

    <h2>Statistics</h2>
    <p>Number of characters: {{ $characterCount }}</p>
    <p>Number of contests: {{ $contestCount }}</p>
</body>
</html>