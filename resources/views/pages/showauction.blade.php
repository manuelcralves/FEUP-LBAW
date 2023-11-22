@extends('layouts.app')

@section('title', 'Auction Details')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($customError))
        <div class="alert alert-danger">
            {{ $customError }}
        </div>
    @endif
    
    <h2>Auction Details</h2>

    <p><strong>Title:</strong> {{ $auction->title }}</p>
    <p><strong>Owner:</strong> {{ $auction->authenticatedUser->username}}</p>
    <p><strong>Current Price:</strong> {{ $auction->current_price }}</p>
    <p><strong>Start Date:</strong> {{ $auction->start_date->format('Y-m-d H:i:s') }}</p>
    <p><strong>End Date:</strong> {{ $auction->end_date->format('Y-m-d H:i:s') }}</p>
    <p><strong>Status:</strong> {{ $auction->status }}</p>
    <p><strong>Description:</strong> {{ $auction->description }}<br></p>
    @if (Auth::check() && Auth::user()->id == $auction->owner && $auction->status == 'ACTIVE')
        <a href="{{ route('auction.edit', $auction->id) }}" class="button">Edit Auction</a>
        <form method="POST" action="{{ route('auction.cancel', $auction->id) }}" style="display: inline;">
            @csrf
            @method('PUT')
            <button type="submit" class="button" onclick="return confirm('Are you sure you want to cancel this auction (You can only cancel an auction if it has 0 bids)?')">Cancel Auction</button>
        </form>
    @endif

    <!-- Table to display bid information -->
    <table id="bidTable">
        <thead>
            <tr>
                <th>Place</th>
                <th>Rating</th>
                <th>Username</th>
                <th>Bid Amount</th>
                <th>Creation Date</th>
            </tr>
        </thead>
        <tbody id="bidTableBody">
            @forelse ($auction->bids as $bid)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $bid->authenticatedUser->rating ?? '(No rating yet)' }}</td>
                    <td>{{ $bid->authenticatedUser->username }}</td>
                    <td>{{ $bid->value }}</td>
                    <td>{{ $bid->creation_date->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">There are no bids yet, be the first!</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Bid Form -->
    @if(Auth::user()->role != 'ADMIN' && Auth::user() != $auction->authenticatedUser)
        <form id="bidForm" method="POST" action="/auction/{{ $auction->id }}/bid">
            @csrf
            <label for="bid_amount">Bid Amount:</label>
            <input type="number" id="bid_amount" name="bid_amount" min="{{ $auction->current_price }}" step="1" required>
            <button type="submit" class="button">Bid</button>
        </form>
    @endif

@endsection
