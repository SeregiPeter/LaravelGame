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
                @guest <!-- Check if the user is not authenticated -->
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
                <li><a href="{{route('characters.index')}}">Back</a></li>
            </ul>
        </nav>
    </header>
    <h1>Character Details</h1>
    <div>
        <h2>Name: {{ $character->name }}</h2>
        <p>Defence: {{ $character->defence }}</p>
        <p>Strength: {{ $character->strength }}</p>
        <p>Accuracy: {{ $character->accuracy }}</p>
        <p>Magic: {{ $character->magic }}</p>
    </div>
    @if (Auth::user() == $character->user && !$character->enemy)
        <h2>Contests</h2>
        <div>
            <ul>
                @forelse ($character->contests as $contest)
                    <li><a href = "{{route('contests.show', ['contest' => $contest])}}">place: {{ $contest->place->name }} - enemy:
                            {{ $contest->characters->where('id', '!=', $character->id)->first()->name ?? '[unknown]' }}</a></li>
                @empty
                    <p>This character has no contests yet.</p>
                @endforelse
            </ul>
        </div>
        @if (!$character->enemy)
            <form action="{{ route('contests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="character_id" value="{{ $character->id }}">
                <button type="submit" {{ $areThereEnemies ? '' : 'disabled' }}>Start new contest</button>
            </form>
        @endif
    @endif
    <a href="{{ route('characters.edit', ['character' => $character]) }}">Edit character</a>
    <form method="POST" action="{{ route('characters.destroy', $character) }} " id="delete-form">
        @csrf
        @method('DELETE')
        <a href="{{ route('characters.destroy', ['character' => $character]) }}"
            onclick="event.preventDefault();
        document.querySelector('#delete-form').submit();">Delete
            character</a>
    </form>


</body>

</html>
