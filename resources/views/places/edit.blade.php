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
                <li><a href="{{route('places.index')}}">Back</a></li>
            </ul>
        </nav>
    </header>

    <h1>Edit place</h1>
    <form action="{{ route('places.update', $place) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="{{old('name') ?? $place->name}}">
        @error('name')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>
        
        <label for="image">Image:</label><br>
        <input type="file" id="image" name="image">
        @error('image')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br><br>
        
        <input type="submit">
    </form>
</body>
</html>