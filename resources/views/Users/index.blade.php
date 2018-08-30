@extends('layout.app')

@section('content')

    <script>
        $(window).load(function(){
            $('#users').removeAttr('style');
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
            $('#users').on('click', '.renew_api', function(){
                $.ajax({
                    type: 'POST',
                    url: './Users/renew_api/'+$(this).data('id'),
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'id': $(this).data('id')
                    },
                    success: function (data) {
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.username + "</td>" +
                            "<td>" + data.name + "</td>" +
                            "<td>" + data.email + "</td>" +
                            "<td>" + data.api_token + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });
            $('.modal-footer').on('click', '.add', function () {
                $.ajax({
                    type: 'POST',
                    url: './Users',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'username': $('#username_add').val(),
                        'name': $('#name_add').val(),
                        'email': $('#email_add').val()
                    },
                    success: function (data) {
                        $('#users').append("<tr class='item" + data.id + "'>" +
                            "<td>" + data.username + "</td>" +
                            "<td>" + data.name + "</td>" +
                            "<td>" + data.email + "</td>" +
                            "<td>" + data.api_token + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email +"'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // Edit a post
            $(document).on('click', '.edit-modal', function() {
                $('.modal-title').text('Edit');
                $('#id_edit').val($(this).data('id'));
                $('#username_edit').val($(this).data('username'));
                $('#name_edit').val($(this).data('name'));
                $('#email_edit').val($(this).data('email'));
                id = $('#id_edit').val();
                $('#editModal').modal('show');
            });
            $('.modal-footer').on('click', '.edit', function() {
                $.ajax({
                    type: 'PUT',
                    url: './Users/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'username': $('#username_edit').val(),
                        'name': $('#name_edit').val(),
                        'email': $('#email_edit').val()
                    },
                    success: function(data) {
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.username + "</td>" +
                            "<td>" + data.name + "</td>" +
                            "<td>" + data.email + "</td>" +
                            "<td>" + data.api_token + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-username='"+ data.username +
                            "' data-name='"+ data.name +
                            "' data-email='"+ data.email + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // delete a post
            $(document).on('click', '.delete-modal', function() {
                $('.modal-title').text('Remove');
                $('#id_delete').val($(this).data('id'));
                $('#username_delete').val($(this).data('username'));
                $('#deleteModal').modal('show');
                id = $('#id_delete').val();
            });
            $('.modal-footer').on('click', '.delete', function() {
                $.ajax({
                    type: 'DELETE',
                    url: './Users/' + id,
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
            $('#users tfoot th').each( function () {
                var title = $(this).text();
                if (title != "Active" && title != "Action"){
                    $(this).html( '<input style="margin: 5px; max-width: 130px;" type="text" placeholder="Search" />' );
                } else {
                    $(this).html( '' );
                }
            } );

            // DataTable
            var table = $('#users').DataTable({
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
                            <label class="control-label col-sm-4" for="username">Username:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="email">E-mail:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email_add">
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
                            <label class="control-label col-sm-4" for="username">Username:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="name">Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="email">E-mail:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email_edit">
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
                            <label class="control-label col-sm-8" for="username">Username:</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" id="username_delete" disabled>
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
        <p><h1>IT Users</h1></p>
        <p>&nbsp;</p>

        <a href="#" class="add-modal"><button class="btn btn-block btn-danger"><span class="glyphicon glyphicon-plus"></span>New user</button></a>
        <p>&nbsp;</p>
        <div class="container-fluid" style="overflow-x: scroll;">
            <table class="table-hover" id="users">
                <thead>
                <tr>
                    <th style="text-align: center">Username</th>
                    <th style="text-align: center">Name</th>
                    <th style="text-align: center">E-mail</th>
                    <th style="text-align: center">Apikey</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </thead>
                <tfoot style="display: table-header-group;">
                <tr>
                    <th style="text-align: center">Username</th>
                    <th style="text-align: center">Name</th>
                    <th style="text-align: center">E-mail</th>
                    <th style="text-align: center">Apikey</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($users as $user)
                    <tr class="item{{$user->id}}">
                        <td>{{$user->username}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->api_token}} @if ($user->api_token != "***") <button class="renew_api btn btn-info" data-id="{{$user->id}}">
                                <span class="glyphicon glyphicon-edit"></span> Renew</button>@endif</td>
                        <td class="text-right">
                            <button class="edit-modal btn btn-info" data-id="{{$user->id}}" data-username="{{$user->username}}" data-name="{{$user->name}}" data-email="{{$user->email}}">
                                <span class="glyphicon glyphicon-edit"></span> Edit</button>
                            <button class="delete-modal btn btn-danger" data-id="{{$user->id}}" data-username="{{$user->username}}" data-name="{{$user->name}}" data-email="{{$user->email}}">
                                <span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop