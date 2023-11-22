@extends('layouts.app')

@section('title', 'All Users')

@section('content')
    <h2>All Users</h2>

    <form method="GET" action="{{ route('show.users', ['pageNr' => 1]) }}">
        <input type="text" name="query" placeholder="Search users..." value="{{ $query }}">
        <button type="submit">Search</button>
    </form> 

    @if ($users->isEmpty())
        <p>No users found.</p>
    @else
        <ul>
            @foreach ($users as $user)
                <li>
                    <a href="{{ route('show', ['id' => $user->id]) }}">
                        <strong>{{ $user->username }}</strong>
                    </a>
                    <br>
                    <strong>First Name:</strong> {{ $user->first_name }}<br>
                    <strong>Last Name:</strong> {{ $user->last_name }}<br>
                    <strong>Email:</strong> {{ $user->email }}<br>
                    <strong>Rating:</strong> {{ $user->rating ?? 'No rating yet' }}<br>
                    <strong>Role:</strong> {{ $user->role }}<br>
                </li>
            @endforeach
        </ul>

        <div class="pagination">
            @if ($users->currentPage() > 1)
                <a href="{{ route('show.users', ['pageNr' => $users->currentPage() - 1]) }}?query={{ $query }}" class="prev">Previous</a>
            @endif

            @for ($i = 1; $i <= $users->lastPage(); $i++)
                <a href="{{ route('show.users', ['pageNr' => $i]) }}" class="{{ $i == $users->currentPage() ? 'active' : '' }}">{{ $i }}</a>
            @endfor

            @if ($users->currentPage() < $users->lastPage())
            <a href="{{ route('show.users', ['pageNr' => $users->currentPage() + 1]) }}?query={{ $query }}" class="next">Next</a>
            @endif
        </div>
    @endif
@endsection
