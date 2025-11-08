<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PetCare</title>
</head>

<h1>Pets List</h1>

<ul>
    @forelse ($pets as $pet)
    <li>{{ $pet->name }}</li>
    @empty
    <li>No pets found</li>
    @endforelse
</ul>

</html>