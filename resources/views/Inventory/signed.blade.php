@extends('layout.app')

@section('content')
<div class="container" style="width: 800px;">
    <embed src="{{url('/signed/')."/".$user}}.pdf" type='application/pdf' width="800px" height="1120px">
</div>
@endsection
