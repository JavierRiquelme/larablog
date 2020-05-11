@extends('dashboard.master')

@section('content')

<form action="{{route('post.update', $post->id)}}" method="POST">
    @method('PUT')
    @include('dashboard.post._form')
</form>
<br>
<form action="{{route('post.image', $post)}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col">
            <input type="file" name="image" class="form-control">
        </div>
        <div class="col">
            <input type="submit" class="btn btn-primary" value="Subir">
        </div>
    </div>
</form>

<div class="row mt-3">
    @foreach ($post->images as $image)
    <div class="col-3">
        <img class="w-100" src="{{ $image->getImageUrl() }}">
        <a href="{{ route('post.image-download',$image->id) }}" class="float-left btn btn-success btn-sm mt-1">Descargar</a>

        <form action="{{ route('post.image-delete',$image->id) }}" method="POST">
            @method("DELETE")
            @csrf
            <button class="float-right btn btn-danger btn-sm mt-1" type="submit">Borrar</button>
        </form>

    </div>
    @endforeach
</div>
@endsection




