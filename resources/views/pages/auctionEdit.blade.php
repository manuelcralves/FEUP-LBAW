@extends('layouts.app')

@section('title', 'Edit Auction')

@section('content')
    <form action="{{ route('auction.update', $auction->id) }}" method="POST">
        @csrf
        @method('POST')
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Title" value="{{ $auction->title }}">

        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Description">{{ $auction->description }}</textarea>
        
        <button type="submit">Update Auction</button>
    </form>
@endsection
