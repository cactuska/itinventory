@extends('layout.app')

@section('content')

    <script>
        $(window).load(function(){
            $('#dnaddresses').removeAttr('style');
        })
    </script>

    <!-- AJAX CRUD operations -->
    <script type="text/javascript">
        $(document).ready(function() {
            // add a new post
            $(document).on('click', '.add-modal', function () {
                $('.modal-title').text('New');
                $('#addModal').modal('show');
            });
            $('.modal-footer').on('click', '.add', function () {
                $.ajax({
                    type: 'POST',
                    url: './Softwares',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#description_add').val(),
                        'serial': $('#serial_add').val().replace(/\//g, '_'),
                        'invoiceno': $('#invoiceno_add').val(),
                        'purdate': $('#purdate_add').val(),
                        'expdate': $('#expdate_add').val(),
                        'supplyer': $('#supplyer_add').val(),
                        'price': $('#price_add').val()
                    },
                    success: function (data) {
                        $('#softwares').append("<tr class='item" + data.id + "'>" +
                            "<td>" + data.description + "</td>" +
                            "<td>" + data.serial + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td>" + data.deviceserial + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial +
                            "' data-invoiceno='"+ data.incoiceno +
                            "' data-purdate='"+ data.purdate +
                            "' data-expdate='"+ data.expdate +
                            "' data-supplyer='"+ data.supplyer +
                            "' data-price='"+ data.price + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // Edit a post
            $(document).on('click', '.edit-modal', function() {
                $('.modal-title').text('Edit');
                $('#id_edit').val($(this).data('id'));
                $('#description_edit').val($(this).data('description'));
                $('#serial_edit').val($(this).data('serial'));
                $('#invoiceno_edit').val($(this).data('invoiceno'));
                $('#purdate_edit').val($(this).data('purdate'));
                $('#expdate_edit').val($(this).data('expdate'));
                $('#supplyer_edit').val($(this).data('supplyer'));
                $('#price_edit').val($(this).data('price'));
                id = $('#id_edit').val();
                $('#editModal').modal('show');

                $.ajax({
                    type: 'POST',
                    url: './Softwares/' + $(this).data('serial') + '/getdevices',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'serial': $(this).data('serial')
                    },
                    success: function(data) {
                        var assignedtable = "<table id='assignedto'><thead><th>User</th><th>Device</th><th>Action</th></thead><tbody>";
                        $.each(data, function(key, value) {
                            if (value.inventoryid == '1'){
                                var button = '<button type="button" class="btn btn-danger assign">Assign</button>';
                            } else {
                                var button = '<button type="button" class="btn btn-danger unassign" data-id="' + value.softwareid + '" data-inventory_serial="' + value.serial + '" data-dismiss="modal">Unassign</button>';
                            }
                            assignedtable += "<tr class=\"assignedto" + value.softwareid + "\">" +
                                "<td>" + value.networklogonname + "</td>" +
                                "<td>" + value.serial + "</td>" +
                                "<td>" + button +
                                    "<select id='userlist' class='custom-select' style='display: none'></select>" +
                                    "<select id='devicelist' class='custom-select' style='display: none'></select>" +
                                    "<select id='seriallist' class='custom-select' style='display: none'></select>" +
                                    "<button type='button' id='doassign' class='btn btn-danger assign' style='display: none' data-dismiss='modal'>Assign</button>" +
                                "</td>" +
                                "</tr>";
                        });
                        assignedtable += "</tbody></table>";

                        $('#assignedto_edit').html(assignedtable);
                        $('#assignedto').DataTable({
                            "paging":   false,
                            "ordering": false,
                            "info":     false,
                            "searching": false
                        });
                    }
                });
            });

            $('#assignedto_edit').on('click', '.unassign', function(){
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: './Inventory/unassignsoftware',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'id': $(this).data('id'),
                        'inventory_serial': $(this).data('inventory_serial')
                    },
                    success: function(data){
                        setTimeout(function() {
                            $( ".edit-modal[data-id="+id+"]" ).trigger( "click" );
                        }, 500);
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.description + "</td>" +
                            "<td>" + data.serial + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td>" + data.deviceserial + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial +
                            "' data-invoiceno='"+ data.invoiceno +
                            "' data-purdate='"+ data.purdate +
                            "' data-expdate='"+ data.expdate +
                            "' data-supplyer='"+ data.supplyer +
                            "' data-price='"+ data.price + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // Query user list with device in [notebook = 8, pc = 2, Server = 11]

            $('#assignedto_edit').on('click', '.assign', function(){
                $.ajax({
                    type: 'POST',
                    url: "./Softwares/getuserlist",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#description_edit').val(),
                        'serial': $('#serial_edit').val()
                    },
                    success: function(data){
                        $('#userlist').append($("<option></option")
                            .attr("value", "")
                            .text(""));
                        $.each(data, function(key, value) {
                            $('#userlist').append($("<option></option")
                                .attr("value", value.id)
                                .text(value.networklogonname));
                        });
                        $('.assign').hide();
                        $('#userlist').show();
                    }
                });
            });

            // Get devices for employee

            $('#assignedto_edit').on('change', '#userlist', function(){
                $.ajax({
                    type: 'POST',
                    url: "./Softwares/getdeviceperuser",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'id': $('#userlist').val()
                    },
                    success: function(data){
                        $('#devicelist').append($("<option></option")
                            .attr("value", "")
                            .text(""));
                        $.each(data, function(key, value) {
                            $('#devicelist').append($("<option></option")
                                .attr("value", value.description)
                                .text(value.description));
                        });
                        $('#devicelist').show();
                    }
                });
            });

            // Get inventory serials for device on user

            $('#assignedto_edit').on('change', '#devicelist', function(){
                $.ajax({
                    type: 'POST',
                    url: "./Softwares/getserialperdevice",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#devicelist').val(),
                        'employee': $('#userlist').val()
                    },
                    success: function(data){
                        $('#seriallist').append($("<option></option")
                            .attr("value", "")
                            .text(""));
                        $.each(data, function(key, value) {
                            $('#seriallist').append($("<option></option")
                                .attr("value", value.id)
                                .text(value.serial));
                        });
                        $('#seriallist').show();
                    }
                });
            });

            $('#assignedto_edit').on('change', '#seriallist', function(){
                if ($('#seriallist').val()!=""){
                    $('#doassign').show();
                } else {
                    $('#doassign').hide();
                }
            });

            // Do the assign

            $('#assignedto_edit').on('click', '#doassign', function(){
                var id = $('#id_edit').val();
                $.ajax({
                    type: 'POST',
                    url: "./Inventory/assignsoftware",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#description_edit').val(),
                        'serial': $('#serial_edit').val(),
                        'inventory_id': $('#seriallist').val()
                    },
                    success: function(data){
                        $('#doassign').hide();
                        $('#seriallist').hide();
                        $('#devicelist').hide();
                        $('#userlist').hide();

                        setTimeout(function() {
                            $( ".edit-modal[data-id="+id+"]" ).trigger( "click" );
                        }, 500);
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.description + "</td>" +
                            "<td>" + data.serial + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td>" + data.deviceserial + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial +
                            "' data-invoiceno='"+ data.invoiceno +
                            "' data-purdate='"+ data.purdate +
                            "' data-expdate='"+ data.expdate +
                            "' data-supplyer='"+ data.supplyer +
                            "' data-price='"+ data.price + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            $('.modal-footer').on('click', '.edit', function() {
                $.ajax({
                    type: 'PUT',
                    url: './Softwares/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'description': $('#description_edit').val(),
                        'serial': $('#serial_edit').val().replace(/\//g, '_'),
                        'invoiceno': $('#invoiceno_edit').val(),
                        'purdate': $('#purdate_edit').val(),
                        'expdate': $('#expdate_edit').val(),
                        'supplyer': $('#supplyer_edit').val(),
                        'price': $('#price_edit').val()
                    },
                    success: function(data) {
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.description + "</td>" +
                            "<td>" + data.serial + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td>" + data.deviceserial + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial +
                            "' data-invoiceno='"+ data.invoiceno +
                            "' data-purdate='"+ data.purdate +
                            "' data-expdate='"+ data.expdate +
                            "' data-supplyer='"+ data.supplyer +
                            "' data-price='"+ data.price + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-description='"+ data.description +
                            "' data-serial='"+ data.serial + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // delete a post
            $(document).on('click', '.delete-modal', function() {
                $('.modal-title').text('Remove');
                $('#id_delete').val($(this).data('id'));
                $('#description_delete').val($(this).data('description'));
                $('#serial_delete').val($(this).data('serial'));
                $('#deleteModal').modal('show');
                id = $('#id_delete').val();
            });
            $('.modal-footer').on('click', '.delete', function() {
                $.ajax({
                    type: 'DELETE',
                    url: './Softwares/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                    },
                    success: function(data) {
                        $('.item' + data['id']).remove();
                    }
                });
            });

            //DataTable
            // Setup - add a text input to each footer cell
            $('#softwares tfoot th').each( function () {
                var title = $(this).text();
                if (title != "Active" && title != "Action"){
                    $(this).html( '<input style="margin: 5px; max-width: 130px;" type="text" placeholder="Search" />' );
                } else {
                    $(this).html( '' );
                }
            } );

            // DataTable
            var table = $('#softwares').DataTable({
                // "columnDefs": [
                //     { className: "dt-body-center", "targets": [ 0, 1, 2, 3, 4, 5, 6 ] }
                // ],
                "pageLength": 50

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


    <!-- Modal form to add a post -->
    <div id="addModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="description">Description:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="description_add" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="serial">Key / Serial:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="serial_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="invoiceno">Invoice No:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="invoiceno_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="purdate">Purchase date:</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="purdate_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="expdate">Expiration date:</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="expdate_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="supplyer">Supplyer:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="supplyer_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="price">Price:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_add">
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success add" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-check'></span> Add
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <label class="control-label col-sm-4" for="id">ID:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="id_edit" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="description">Description:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="description_edit" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="serial">Key / Serial:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="serial_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="invoiceno">Invoice No:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="invoiceno_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="purdate">Purchase date:</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="purdate_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="expdate">Expiration date:</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="expdate_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="supplyer">Supplyer:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="supplyer_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="price">Price:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="price_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="assignedto_edit">Assigned to:</label>
                            <div class="col-sm-12">
                                <div id="assignedto_edit"></div>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form to delete a form -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <h3 class="text-center">Confirm removal</h3>
                    <br />
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="id">ID:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="id_delete" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="compcode">Description:</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" id="description_delete" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="compcode">Key / Serial:</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" id="serial_delete" disabled>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger delete" data-dismiss="modal">
                            <span id="" class='glyphicon glyphicon-trash'></span> Delete
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <p><h1>Softwares</h1></p>
        <p>&nbsp;</p>

        <a href="#" class="add-modal"><button class="btn btn-block btn-danger"><span class="glyphicon glyphicon-plus"></span>New software</button></a>
        <p>&nbsp;</p>
        <div class="container-fluid" style="overflow-x: scroll;">
            <table class="table-hover" id="softwares">
                <thead>
                <tr>
                    <th style="text-align: center">Description</th>
                    <th style="text-align: center">Key / Serial</th>
                    <th style="text-align: center">Assigned to</th>
                    <th style="text-align: center">Device</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </thead>
                <tfoot style="display: table-header-group;">
                <tr>
                    <th style="text-align: center">Description</th>
                    <th style="text-align: center">Key / Serial</th>
                    <th style="text-align: center">Assigned to</th>
                    <th style="text-align: center">Device</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($softwares as $software)
                    <tr class="item{{$software->id}}">
                        <td>{{$software->description}}</td>
                        <td>{{$software->serial}}</td>
                        <td>{{$software->device->owner->networklogonname}}</td>
                        <td>{{$software->device->serial}}</td>
                        <td class="text-right">
                            <button class="edit-modal btn btn-info" data-id="{{$software->id}}" data-description="{{$software->description}}" data-serial="{{$software->serial}}" data-inventory_id="{{$software->inventory_id}}" data-invoiceno="{{$software->invoiceno}}" data-purdate="{{$software->purdate}}" data-expdate="{{$software->expdate}}" data-supplyer="{{$software->supplyer}}" data-price="{{$software->price}}">
                                <span class="glyphicon glyphicon-edit"></span> Edit</button>
                            <button class="delete-modal btn btn-danger" @if ($software->inventory_id!=1) disabled @endif data-id="{{$software->id}}" data-description="{{$software->description}}" data-serial="{{$software->serial}}">
                                <span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop