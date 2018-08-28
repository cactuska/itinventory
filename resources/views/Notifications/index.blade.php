@extends('layout.app')

@section('content')

    <script>
        $(window).load(function(){
            $('#addresses').removeAttr('style');
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
                    url: './Notifications',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'address': $('#address_add').val()
                    },
                    success: function (data) {
                        $('#addresses').append("<tr class='item" + data.id + "'>" +
                            "<td>" + data.address + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-address='"+ data.address + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-address='"+ data.address +"'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // Edit a post
            $(document).on('click', '.edit-modal', function() {
                $('.modal-title').text('Edit');
                $('#id_edit').val($(this).data('id'));
                $('#address_edit').val($(this).data('address'));
                id = $('#id_edit').val();
                $('#editModal').modal('show');
            });
            $('.modal-footer').on('click', '.edit', function() {
                $.ajax({
                    type: 'PUT',
                    url: './Notifications/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'address': $('#address_edit').val()
                    },
                    success: function(data) {
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.address + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-address='"+ data.address + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-address='"+ data.address + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // delete a post
            $(document).on('click', '.delete-modal', function() {
                $('.modal-title').text('Remove');
                $('#id_delete').val($(this).data('id'));
                $('#address_delete').val($(this).data('address'));
                $('#deleteModal').modal('show');
                id = $('#id_delete').val();
            });
            $('.modal-footer').on('click', '.delete', function() {
                $.ajax({
                    type: 'DELETE',
                    url: './Notifications/' + id,
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
            $('#addresses tfoot th').each( function () {
                var title = $(this).text();
                if (title != "Active" && title != "Action"){
                    $(this).html( '<input style="margin: 5px; max-width: 130px;" type="text" placeholder="Search" />' );
                } else {
                    $(this).html( '' );
                }
            } );

            // DataTable
            var table = $('#addresses').DataTable({
                // "columnDefs": [
                //     { className: "dt-body-center", "targets": [ 0, 1, 2, 3, 4, 5, 6 ] }
                // ],
                // "pageLength": 50

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
                            <label class="control-label col-sm-4" for="address">Address:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="address_add">
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
        <div class="modal-dialog">
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
                            <label class="control-label col-sm-4" for="address">Address:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="address_edit">
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
                            <label class="control-label col-sm-8" for="address">Address:</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" id="address_delete" disabled>
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
        <p><h1>Notifications</h1></p>
        <p>&nbsp;</p>

        <a href="#" class="add-modal"><button class="btn btn-block btn-danger"><span class="glyphicon glyphicon-plus"></span>New recipient</button></a>
        <p>&nbsp;</p>
        <div class="container" style="overflow-x: scroll;">
            <table class="table-hover" id="addresses">
                <thead>
                <tr>
                    <th style="text-align: center">Address</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </thead>
                <tfoot style="display: table-header-group;">
                <tr>
                    <th style="text-align: center">Address</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($addresses as $address)
                    <tr class="item{{$address->id}}">
                        <td>{{$address->address}}</td>
                        <td class="text-right">
                            <button class="edit-modal btn btn-info" data-id="{{$address->id}}" data-address="{{$address->address}}">
                                <span class="glyphicon glyphicon-edit"></span> Edit</button>
                            <button class="delete-modal btn btn-danger" data-id="{{$address->id}}" data-address="{{$address->address}}">
                                <span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop