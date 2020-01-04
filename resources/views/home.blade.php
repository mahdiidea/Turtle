@extends('layouts.app')

@section('content')
    <activities :auth_id="{{ auth()->id() }}"></activities>
@endsection
