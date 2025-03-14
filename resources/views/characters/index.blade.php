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
            </ul>
        </nav>
    </header>
    @if(Session::has('success'))
    <p style="background-color: lightgreen">
        {{ Session::get('success') }}
    </p>
    @endif
    <h1>List of Characters</h1>
    @if (!$characters->isEmpty())
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Enemy</th>
                    <th>Defence</th>
                    <th>Strength</th>
                    <th>Accuracy</th>
                    <th>Magic</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($characters as $character)
                    <tr>
                        <td><a href="{{ route('characters.show', $character) }}">{{ $character->name . (Auth::id() != $character->user_id ? " {belongs to other user}" : "")}}</a></td>
                        <td>{{ $character->enemy ? 'Yes' : 'No' }}</td>
                        <td>{{ $character->defence }}</td>
                        <td>{{ $character->strength }}</td>
                        <td>{{ $character->accuracy }}</td>
                        <td>{{ $character->magic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>You don't have characters yet.</p>
    @endif

    <br><br>
    <a href="{{ route('characters.create') }}">Create a character</a>
</body>

</html>
