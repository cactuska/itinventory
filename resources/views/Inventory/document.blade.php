@extends('layout.app')

@section('content')
<script src="{{url('lib/jSignature/jSignature.min.js')}}"></script>
<div class="container" style="width: 800px;">
    <embed src="data:application/pdf;base64,{{$pdfContent}}" type='application/pdf' width="800px" height="1120px">
    <div id="signature" style="border: 2px; border-style: solid; border-radius: 16px; width: 800px;"></div>
    <div style="text-align: center; margin-top: 10px;width: 800px;"><button class="sign btn btn-success">Aláír</button></div>
    <script>
    $(document).ready(function() {
        $("#signature").jSignature()


            $('.sign').on('click', '', function () {
                console.log('Futok');
                $.ajax({
                    type: 'POST',
                    url: './sign',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'user': window.location.pathname.split("/")[4],
                        'signature': $("#signature").jSignature("getData")
                    },
                    success: function (data) {window.location.replace("{{url('/Inventory/personal/signed/')}}/"+window.location.pathname.split("/")[4]);}
              });
         });


    })
    </script>
</div>
@endsection

