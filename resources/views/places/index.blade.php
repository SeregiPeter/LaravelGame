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
                    <li><a href="{{route('characters.index')}}">Characters</a></li>
                @endguest
                @if(Auth::user()->admin)
                    <li><a href="{{route('places.index')}}">Places</a></li>
                @endif
            </ul>
        </nav>
    </header>

    <h1>Places</h1>

    @if(Session::has('success'))
    <p style="background-color: lightgreen">
        {{ Session::get('success') }}
    </p>
    @endif

    <table>
        <tr>
            <th>Name</th>
            <th>Image</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    @forelse ($places as $place)
        <tr>
            <td><?= $place->name ?></td>
            <td><img src="{{ $place->image === null ? url('https://img.pikbest.com/wp/202344/winter-wonderland-a-fresh-snowfall-blanketing-the-landscape-in-white_9916756.jpg!sw800') : Storage::url('images/' . $place->image) }}" alt="{{$place->image}}" width="200"></td>
            <td><a href="{{route('places.edit', ['place' => $place])}}">Edit</a></td>
            <td><form method="POST" action="{{ route('places.destroy', $place) }} " id="delete-form-{{$place->id}}">
                @csrf
                @method('DELETE')
                <a href="{{ route('places.destroy', ['place' => $place]) }}"
                    onclick="event.preventDefault();
                document.querySelector('#delete-form-{{$place->id}}').submit();">Delete</a>
            </form></td>
        </tr>
    @empty
        <p>There are no places yet.</p>
    @endforelse
    </table>

    <a href="{{route('places.create')}}">Create new place</a>
</body>
</html>