@extends('layout.app')

@section('content')

    <script>
        $(window).load(function(){
            $('#inventory').removeAttr('style');
        })
    </script>

    <!-- AJAX CRUD operations -->
    <script type="text/javascript">

        function nl2br (str, is_xhtml) {
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
        }

        $(document).ready(function() {

            // Edit a post
            $(document).on('click', '.edit-modal', function() {
                $('.modal-title').text('Szerkesztés');
                $('#id_edit').val($(this).data('id'));
                $('#description_edit').val($(this).data('description'));
                et = $(this).data('equtype');
                $('#type_edit option:contains(' + et + ')').prop("selected", true);
                $('#serial_edit').val($(this).data('serial'));
                serial = $('#serial_edit').val();
                $('#owner_edit').val($(this).data('owner'));
                site = $(this).data('location');
                $('#location_edit option:contains(' + site + ')').prop("selected", true);
                $('#pin_edit').val($(this).data('pin'));
                $('#puk_edit').val($(this).data('puk'));
                $('#invoiceno_edit').val($(this).data('invoiceno'));
                $('#purdate_edit').val($(this).data('purdate'));
                $('#supplyer_edit').val($(this).data('supplyer'));
                $('#price_edit').val($(this).data('price'));
                $('#warranty_edit').val($(this).data('warranty'));
                $('#note_edit').val($(this).data('note'));
                id = $('#id_edit').val();
                $('#editModal').modal('show');

                $.ajax({
                    type: 'POST',
                    url: './Inventory/' + serial + '/logs',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'serial': $(this).data('serial')
                    },
                    success: function(data) {

                        var logtable = "<table id='logs'><th>Date</th><th>User</th><th>Description</th>";
                        for (var key in data) {
                            if (data.hasOwnProperty(key)) {
                                logtable += "<tr>";
                                logtable += "<td>" + data[key]["created_at"] + "</td>";
                                logtable += "<td>" + data[key]["user"] + "</td>";
                                logtable += "<td>" + data[key]["description"] + "</td>";
                                logtable += "</tr>";
                            }
                        }
                        logtable += "</table>";
                        logtable = nl2br(logtable);

                        $('.logs').html(logtable);
                        $('#logs').DataTable({
                            "aaSorting": []
                        });
                     }
                });


            });

            $('.modal-footer').on('click', '.edit', function() {
                $.ajax({
                    type: 'PUT',
                    url: './Inventory/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#description_edit').val(),
                        'type': $('#type_edit').val(),
                        'serial': $('#serial_edit').val(),
                        'location': $('#location_edit').val(),
                        'pin': $('#pin_edit').val(),
                        'puk': $('#puk_edit').val(),
                        'invoiceno': $('#invoiceno_edit').val(),
                        'purdate': $('#purdate_edit').val(),
                        'supplyer': $('#supplyer_edit').val(),
                        'price': $('#price_edit').val(),
                        'warranty': $('#warranty_edit').val(),
                        'note': $('#note_edit').val()

                    },
                    success: function(data) {
                        if (data.employee=='0' || data.employee!='158'){disable_handover = "disabled";} else {disable_handover="";}
                        if (data.employee==158 || data.employee==0){disable_vissza = "disabled";} else {disable_vissza="";}
                        if (data.employee==0 || data.employee!=158){disable_selejt = "disabled";} else {disable_selejt="";}
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.description + "</td>" +
                            "<td>" + data.equtype.EquipmentType + "</td>" +
                            "<td>" + data.serial + "</td>" +
                            "<td><button class='personal_inventory btn btn-link' data-owner='" + data.owner.networklogonname + "'><span class='glyphicon glyphicon-edit'></span>" + data.owner.networklogonname + "</button></td>" +
                            "<td>" + data.loc.compcode + "</td>" +
                            "<td class='text-center'>" +
                                "<input type='checkbox' class='handover' data-id='" + data.id +"' " + disable_handover + "> &nbsp;" +
                                "<input type='checkbox' class='visszavesz' data-id='" + data.id +"' " + disable_vissza + "> &nbsp;" +
                                "<input type='checkbox' class='selejtez' data-id='" + data.id +"' " + disable_selejt + "> &nbsp;" +
                            "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                                "data-id='" + data.id + "'" +
                                "data-description='" + data.description + "'" +
                                "data-equtype='" + data.equtype.EquipmentType + "'" +
                                "data-serial='" + data.serial + "'" +
                                "data-owner='" + data.owner.networklogonname + "'" +
                                "data-pin='" + data.pin + "'" +
                                "data-puk='" + data.puk + "'" +
                                "data-invoiceno='" + data.invoiceno + "'" +
                                "data-purdate='" + data.purdate + "'" +
                                "data-supplyer='" + data.supplyer + "'" +
                                "data-price='" + data.price + "'" +
                                "data-warranty='" + data.warranty + "'" +
                                "data-note='" + data.note + "'" +
                                "data-location='" + data.loc.compcode + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "</td></tr>");
                    }
                });
            });

            //Scrap
            jsonObj = [];
            $('.scrap').on('click', function(event){
                id=$(this).data('id');
                buttoninfo=$('button[data-id=' + id + ']');
                serial=(buttoninfo.data('serial'));
                description=(buttoninfo.data('description'));
                tipus=(buttoninfo.data('equtype'));
                $('td.dataTables_empty').parent().remove();
                $(this).attr('disabled', true);
                $('#handoverselect').val(0);
                $('.dohandover').text('Scrap');
                $('#handoverselect').prop('disabled', true);
                $('#handoverdiv').show();
                $('#handovertable').append('<tr class="handoverid' + id + '">' +
                    '<td>' + description + '</td>' +
                    '<td>' + tipus + '</td>' +
                    '<td>' + serial + '</td>' +
                    '<td class="text-right"><button class="btn btn-danger handover-cancel" data-id="'+id+'">' +
                    '<span class="glyphicon glyphicon-edit"></span> Cancel' +
                    '</button></td>' +
                    '</tr>');

                jsonObj.push(id);

            });

            // Handover table display
            jsonObj = [];
            $('.handover').on('click', function(event){
                id=$(this).data('id');
                buttoninfo=$('button[data-id=' + id + ']');
                serial=(buttoninfo.data('serial'));
                description=(buttoninfo.data('description'));
                tipus=(buttoninfo.data('equtype'));
                $('td.dataTables_empty').parent().remove();
                $(this).attr('disabled', true);
                $('#handoverdiv').show();
                $('#handovertable').append('<tr class="handoverid' + id + '">' +
                        '<td>' + description + '</td>' +
                        '<td>' + tipus + '</td>' +
                        '<td>' + serial + '</td>' +
                        '<td class="text-right"><button class="btn btn-danger handover-cancel" data-id="'+id+'">' +
                            '<span class="glyphicon glyphicon-edit"></span> Cancel' +
                        '</button></td>' +
                    '</tr>');

                jsonObj.push(id);

            });

            // Handover record cancel

            $('#handoverdiv').on('click', '.handover-cancel', function(){
                index = jsonObj.indexOf($(this).data('id'));
                jsonObj.splice(index, 1);
                $('.handoverid' + $(this).data('id')).remove();
            });

            // Handover ajax call

            $('#handoverdiv').on('click', '.dohandover', function(){
                jsonString=JSON.stringify(jsonObj);
                $("#handoverdiv :button").attr("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "./Inventory/handover",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'whom'  : $('#handoverselect').val(),
                        data: jsonString
                    },
                    cache: false,

                    success: function () {
                        window.location.reload(true);
                    }
                });

            });

            //Handover table style

            $('#handovertable').DataTable({
                "aaSorting": [],
                "paging":   false,
                "info":     false,
                "searching":false,
                "ordering":  false
            });

            // TakeBack table display
            jsonObj = [];
            $('.takeback').on('click', function(event){
                id=$(this).data('id');
                buttoninfo=$('button[data-id=' + id + ']');
                serial=(buttoninfo.data('serial'));
                owner=(buttoninfo.data('owner'));
                description=(buttoninfo.data('description'));
                tipus=(buttoninfo.data('equtype'));
                $('td.dataTables_empty').parent().remove();
                $(this).attr('disabled', true);
                $('#takebackdiv').show();
                $('#takebacktable').append('<tr class="takbackid' + id + '">' +
                    '<td>' + description + '</td>' +
                    '<td>' + tipus + '</td>' +
                    '<td>' + serial + '</td>' +
                    '<td>' + owner + '</td>' +
                    '<td class="text-right"><button class="btn btn-danger takeback-cancel" data-id="'+id+'">' +
                    '<span class="glyphicon glyphicon-edit"></span> Cancel' +
                    '</button></td>' +
                    '</tr>');

                jsonObj.push(id);

            });

            // TakeBack record cancel

            $('#takebackdiv').on('click', '.takeback-cancel', function(){
                index = jsonObj.indexOf($(this).data('id'));
                jsonObj.splice(index, 1);
                $('.takebackid' + $(this).data('id')).remove();
            });

            // TakeBack ajax call

            $('#takebackdiv').on('click', '.dotakeback', function(){
                jsonString=JSON.stringify(jsonObj);
                $("#takebackdiv :button").attr("disabled", true);
                window.open('./Inventory/takebackdoc/'+ owner + '/' + encodeURIComponent(jsonString),'_blank');
                $.ajax({
                    type: "POST",
                    url: "./Inventory/takeback",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        data: jsonString
                    },
                    cache: false,

                    success: function () {
                        window.location.reload(true);
                    }
                });

            });

            //TakeBack table style

            $('#takebacktable').DataTable({
                "aaSorting": [],
                "paging":   false,
                "info":     false,
                "searching":false,
                "ordering":  false
            });

            // Personal inventory report

            $('#inventory').on('click', '.personal_inventory', function(){
                owner=$(this).data('owner');
                window.open('./Inventory/personal/'+ owner,'_blank');
            });



            //DataTable
            // Setup - add a text input to each footer cell
            $('#inventory tfoot th').each( function () {
                var title = $(this).text();
                if (title != "Handover - TakeBack - Scrap" && title != "Action"){
                    $(this).html( '<input style="margin: 5px; max-width: 130px;" type="text" placeholder="Search" />' );
                } else {
                    $(this).html( '' );
                }
            } );

            // DataTable
            var table = $('#inventory').DataTable({
                "dom": 'Bfrtip',
                "buttons": [
                    'copy', 'csv', 'excel', 'pdf'
                ],
                "pageLength": 50,
                "aaSorting": []
            });

            // Apply the search
            table.columns().every( function () {
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        });


    </script>

    <!-- Modal form to edit a form -->
    <div id="editModal" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="id">ID:</label>
                                    <input type="text" class="form-control" id="id_edit" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="pin">PIN:</label>
                                    <input type="text" class="form-control" id="pin_edit">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="description">Description:</label>
                                    <input type="text" class="form-control" id="description_edit" autofocus>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="puk">PUK:</label>
                                    <input type="text" class="form-control" id="puk_edit" autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="type">Type:</label>
                                    <select name="type" class="form-control" id="type_edit">
                                        @foreach($types as $type)
                                            <option value="{{$type->id}}">{{$type->EquipmentType}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="invoice">Invoice:</label>
                                    <input type="text" class="form-control" id="invoice_edit" autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="serial">Serial:</label>
                                    <input type="text" class="form-control" id="serial_edit">
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="purdate">Purchase Date:</label>
                                    <input type="date" class="form-control" id="purdate_edit">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="owner">Owner:</label>
                                    <input type="text" class="form-control" id="owner_edit" disabled>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="supplyer">Supplyer:</label>
                                    <input type="text" class="form-control" id="supplyer_edit">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="location">Location:</label>
                                    <select name="location" class="form-control" id="location_edit">
                                        @foreach($sites as $site)
                                            <option value="{{$site->id}}">{{$site->compcode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="price">Price:</label>
                                    <input type="text" class="form-control" id="price_edit">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="warranty">Warranty:</label>
                                    <input type="date" class="form-control" id="warranty_edit">
                                </div>
                                <div class="col-sm-6">
                                    <label class="control-label" for="note">Note:</label>
                                    <input type="text" class="form-control" id="note_edit">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary edit" data-dismiss="modal">
                            <span class='glyphicon glyphicon-check'></span> Edit
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                    <div class="logs">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <p><h1>Registered devices</h1></p>
        <p>&nbsp;</p>

        <a href="#" class="add-modal"><button class="btn btn-block btn-danger"><span class="glyphicon glyphicon-plus"></span>New device</button></a>
        <p>&nbsp;</p>

        <div class="container" id="handoverdiv" style="display: none;">
            <table id="handovertable">
                <thead>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Serial</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <p></p>
            <div class="row">
                <div class="col-md-8">
                    <select id="handoverselect">
                        @foreach($employees as $employee)
                        <option value="{{$employee->id}}">{{$employee->networklogonname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="float-right">
                        <button class="btn btn-success dohandover">
                            <span class="glyphicon glyphicon-edit"></span> Handover
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" id="takebackdiv" style="display: none;">
            <table id="takebacktable">
                <thead>
                <th>Description</th>
                <th>Type</th>
                <th>Serial</th>
                <th>Owner</th>
                <th>&nbsp;</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <p></p>
            <div class="row">
                <div class="col-md-12">
                    <div class="float-right">
                        <button class="btn btn-success dotakeback">
                            <span class="glyphicon glyphicon-edit"></span> TakeBack
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="overflow-x: scroll;">
            <table class="table-hover" id="inventory">
                <thead>
                <tr>
                    <th style="text-align: left">Description</th>
                    <th style="text-align: left">Type</th>
                    <th style="text-align: left">Serial</th>
                    <th style="text-align: left">Network Logon Name</th>
                    <th style="text-align: left">Location</th>
                    <th style="text-align: left">Handover - TakeBack - Scrap</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </thead>
                <tfoot style="display: table-header-group;">
                <tr>
                    <th style="text-align: left">Description</th>
                    <th style="text-align: left">Type</th>
                    <th style="text-align: left">Serial</th>
                    <th style="text-align: left">Network Logon Name</th>
                    <th style="text-align: left">Location</th>
                    <th style="text-align: left">Handover - TakeBack - Scrap</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($records as $record)
                    <tr class="item{{$record->id}}">
                        <td>{{$record->description}}</td>
                        <td>{{$record->equtype->EquipmentType}}</td>
                        <td>{{$record->serial}}</td>
                        <td><button class="personal_inventory btn btn-link"
                                    data-owner="{{$record->owner->networklogonname}}">
                                <span class="glyphicon glyphicon-edit"></span>
                                {{$record->owner->networklogonname}}
                            </button></td>
                        <td>{{$record->loc->compcode}}</td>
                        <td class="text-center">
                            <input type="checkbox" class="handover" data-id="{{$record->id}}" @if ($record->employee==0 or $record->employee<>158) disabled @endif>&nbsp;
                            <input type="checkbox" class="takeback" data-id="{{$record->id}}" @if ($record->employee==158 or $record->employee==0) disabled @endif>&nbsp;
                            <input type="checkbox" class="scrap" data-id="{{$record->id}}" @if ($record->employee==0 or $record->employee<>158) disabled @endif>&nbsp;
                        </td>
                        <td class="text-right">
                            <button class="edit-modal btn btn-info"
                                    data-id="{{$record->id}}"
                                    data-description="{{$record->description}}"
                                    data-equtype="{{$record->equtype->EquipmentType}}"
                                    data-serial="{{$record->serial}}"
                                    data-owner="{{$record->owner->networklogonname}}"
                                    data-pin="{{$record->pin}}"
                                    data-puk="{{$record->puk}}"
                                    data-invoiceno="{{$record->invoiceno}}"
                                    data-purdate="{{$record->purdate}}"
                                    data-supplyer="{{$record->supplyer}}"
                                    data-price="{{$record->price}}"
                                    data-warranty="{{$record->warranty}}"
                                    data-note="{{$record->note}}"
                                    data-location="{{$record->loc->compcode}}"
                            >
                                <span class="glyphicon glyphicon-edit"></span> Edit</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop