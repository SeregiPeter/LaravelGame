<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        body {
            background-image: url({{ $contest->place->image === null ? url('https://img.pikbest.com/wp/202344/winter-wonderland-a-fresh-snowfall-blanketing-the-landscape-in-white_9916756.jpg!sw800') : Storage::url('images/' . $contest->place->image) }});
            background-size: cover;
        }
    </style>
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
                    <li><a href="{{route('characters.index')}}">Characters</a></li>
                @endguest
                @if(Auth::user()->admin)
                    <li><a href="{{route('places.index')}}">Places</a></li>
                @endif
                <li><a href="{{route('characters.show', ['character' => $character])}}">Back</a></li>
            </ul>
        </nav>
    </header>
    <h1>{{ $contest->place->name }}</h1>
    <h2>Characters:</h2>
    <h3>Your character: {{ $character->name }}</h3>
    <ul>
    <li>HP: {{ $character_hp }}</li>
    <li>Defence: {{ $character->defence }}</li>
    <li>Strength: {{ $character->strength }}</li>
    <li>Accuracy: {{ $character->accuracy }}</li>
    <li>Magic: {{ $character->magic }}</li>
    </ul>

    <h3>Enemy character: {{ $enemy->name }}</h3>
    <ul>
    <li>HP: {{ $enemy_hp }}</li>
    <li>Defence: {{ $enemy->defence }}</li>
    <li>Strength: {{ $enemy->strength }}</li>
    <li>Accuracy: {{ $enemy->accuracy }}</li>
    <li>Magic: {{ $enemy->magic }}</li>
    </ul>

    @if ($contest->win !== null)
        <h2>Outcome:</h2>
        @if ($contest->win)
            <p>Win!</p>
        @else
            <p>Lose!</p>
        @endif
    @else
        <h2>Choose attack:</h2>
        <a href="{{ route('contests.attack', ['id' => $contest->id, 'attackType' => 'melee']) }}">Melee</a>
        <a href="{{ route('contests.attack', ['id' => $contest->id, 'attackType' => 'ranged']) }}">Ranged</a>
        <a href="{{ route('contests.attack', ['id' => $contest->id, 'attackType' => 'special']) }}">Special</a>
    @endif

    <h2>Match history</h2>
    <p>{!! str_replace("\n", "<br>", $contest->history) !!}</p>

</body>

</html>
