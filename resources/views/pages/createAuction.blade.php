@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('createAuction', ['id' => $user->id]) }}">
    @csrf
    @method('POST')

    <!-- Auction fields -->
    <label for="title">Auction Title</label>
    <input id="title" type="text" name="title" value="{{ old('title') }}" required autofocus>
    @error('title')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="description">Auction Description</label>
    <textarea id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
    @error('description')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="name">Item Name</label>
    <input id="name" type="text" name="name" value="{{ old('title') }}" required autofocus>
    @error('name')
    <span class="error">{{ $message }}</span>
    @enderror

    <label for="category">Category</label>
    <input id="category" type="text" name="category" value="{{ old('category') }}" required>
    @error('category')
    <span class="error">{{ $message }}</span>
    @enderror

    
    <label for="brand">Brand</label>
    <input id="brand" type="text" name="brand" value="{{ old('brand') }}" required>
    @error('brand')
    <span class="error">{{ $message }}</span>   
    @enderror

    <label for="color">Color</label>
    <input id="color" type="text" name="color" value="{{ old('color') }}" required>
    @error('color')
    <span class="error">{{ $message }}</span>   
    @enderror

    <label for="condition">Choose the state of the item:</label>
    <select name="condition" class="form-select" id="condition" required>
    <option value="" selected>Please Select</option>
        <option value="NEW">New</option>
        <option value="LIKE NEW">Like new</option>
        <option value="EXCELLENT">Excellent</option>
        <option value="GOOD">Good</option>
        <option value="USED">Used</option>
    </select>
    <br>
    @error('condition')
    <span class="error">{{ $message }}</span>
    @enderror


    <label for="starting_price">Starting Price</label>
    <input id="starting_price" type="text" name="starting_price" value="{{ old('starting_price') }}" required>
    @error('starting_price')
    <span class="error">{{ $message }}</span>   
    @enderror

    <label for="current_price">Current Price</label>
    <input id="current_price" type="text" name="current_price" value="{{ old('current_price') }}" required>
    @error('current_price')
    <span class="error">{{ $message }}</span>   
    @enderror


    <label for="end_date">End Date</label>
    <input id="end_date" type="datetime-local" name="end_date" value="{{ old('end_date') }}" required>
    @error('end_date')
    <span class="error">{{ $message }}</span>
    @enderror

    <button type="submit">
        Create Auction
    </button>

</form>
@endsection
