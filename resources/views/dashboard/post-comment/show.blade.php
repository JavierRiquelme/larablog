@extends('dashboard.master')

@section('content')

    <div class="form-group">
        <label for="title">Título</label>
            <input readonly class="form-control" type="text" name="title" id="title" value="{{$postComment->title}}">

    </div>
    <div class="form-group">
        <label for="user">Usuario</label>
            <input readonly class="form-control" type="text" name="user" id="user" value="{{$postComment->user->name}}">

    </div>
    <div class="form-group">
        <label for="approved">Aprovado</label>
            <input readonly class="form-control" type="text" name="approved" id="approved" value="{{$postComment->approved}}">

    </div>
    <div class="form-group">
        <label for="content">Contenido</label>
            <textarea readonly class="form-control" name="content" id="content" cols="30" rows="10" >{{$postComment->message}}</textarea>
    </div>
    
@endsection