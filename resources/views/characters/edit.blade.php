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
                <li><a href="{{route('characters.show', ['character' => $character])}}">Back</a></li>
            </ul>
        </nav>
    </header>
    <h1>Edit Character</h1>
    <form action="{{ route('characters.update', $character) }}" method="POST">
        @csrf
        @method('PUT')
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') ?? $character->name }}">
        @error('name')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>

        <label for="defence">Defence:</label>
        <input type="number" id="defence" name="defence" value="{{ old('defence') ?? $character->defence }}">
        @error('defence')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>

        <label for="strength">Strength:</label>
        <input type="number" id="strength" name="strength" value="{{ old('strength') ?? $character->strength }}">
        @error('strength')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>

        <label for="accuracy">Accuracy:</label>
        <input type="number" id="accuracy" name="accuracy" value="{{ old('accuracy') ?? $character->accuracy }}">
        @error('accuracy')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>

        <label for="magic">Magic:</label>
        <input type="number" id="magic" name="magic" value="{{ old('magic') ?? $character->magic }}">
        @error('magic')
            <p style="color: red">{{ $message }}</p>
        @enderror
        <br>

        @error('totalError')
            <p style="color: red">{{ $message }}</p>
        @enderror


        <button type="submit">Update Character</button>
    </form>

</body>

</html>
