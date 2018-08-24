@extends('layout.app')

@section('content')

    <script>
        $(window).load(function(){
            $('#employees').removeAttr('style');
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
                    url: './Employees',
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'code': $('#code_add').val(),
                        'firstname': $('#firstname_add').val(),
                        'lastname': $('#lastname_add').val(),
                        'networklogonname': $('#networklogonname_add').val()
                    },
                    success: function (data) {
                        if (data.status) {data.status="<input type=\"checkbox\" class=\"status\" data-id=" + data.id + " checked >";}
                        else {data.status="<input type=\"checkbox\" class=\"status\" data-id=" + data.id + " >";}
                        $('#employees').append("<tr class='item" + data.id + "'>" +
                            "<td>" + data.code + "</td>" +
                            "<td>" + data.firstname + "</td>" +
                            "<td>" + data.lastname + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td class='text-center'>" +  data.status + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-code='"+ data.code +
                            "' data-firstname='"+ data.firstname +
                            "' data-lastname='"+ data.lastname +
                            "' data-networklogonname='"+ data.networklogonname + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-code='"+ data.code +
                            "' data-firstname='"+ data.firstname +
                            "' data-lastname='"+ data.lastname +
                            "' data-networklogonname='"+ data.networklogonname +"'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // Edit a post
            $(document).on('click', '.edit-modal', function() {
                $('.modal-title').text('Edit');
                $('#id_edit').val($(this).data('id'));
                $('#code_edit').val($(this).data('code'));
                $('#firstname_edit').val($(this).data('firstname'));
                $('#lastname_edit').val($(this).data('lastname'));
                $('#networklogonname_edit').val($(this).data('networklogonname'));
                id = $('#id_edit').val();
                $('#editModal').modal('show');
            });
            $('.modal-footer').on('click', '.edit', function() {
                $.ajax({
                    type: 'PUT',
                    url: './Employees/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'code': $('#code_edit').val(),
                        'firstname': $('#firstname_edit').val(),
                        'lastname': $('#lastname_edit').val(),
                        'networklogonname': $('#networklogonname_edit').val()
                    },
                    success: function(data) {
                        if (data.status) {data.status="<input type=\"checkbox\" class=\"status\" data-id=" + data.id + " checked >";}
                        else {data.status="<input type=\"checkbox\" class=\"status\" data-id=" + data.id + " >";}
                        $('.item' + data.id).replaceWith("<tr class='item" + data.id + "'>" +
                            "<td>" + data.code + "</td>" +
                            "<td>" + data.firstname + "</td>" +
                            "<td>" + data.lastname + "</td>" +
                            "<td>" + data.networklogonname + "</td>" +
                            "<td class='text-center'>" +  data.status + "</td>" +
                            "<td class='text-right'>" +
                            "<button class='edit-modal btn btn-info' " +
                            "data-id='"+ data.id +
                            "' data-code='"+ data.code +
                            "' data-firstname='"+ data.firstname +
                            "' data-lastname='"+ data.lastname +
                            "' data-networklogonname='"+ data.networklogonname + "'>" +
                            "<span class='glyphicon glyphicon-edit'></span> Edit</button> " +
                            "<button class='delete-modal btn btn-danger' " +
                            "data-id='"+ data.id +
                            "' data-code='"+ data.code +
                            "' data-firstname='"+ data.firstname +
                            "' data-lastname='"+ data.lastname +
                            "' data-networklogonname='"+ data.networklogonname + "'>" +
                            "<span class='glyphicon glyphicon-trash'></span> Delete</button>" +
                            "</td></tr>");
                    }
                });
            });

            // delete a post
            $(document).on('click', '.delete-modal', function() {
                $('.modal-title').text('Remove');
                $('#id_delete').val($(this).data('id'));
                $('#networklogonname_delete').val($(this).data('networklogonname'));
                $('#deleteModal').modal('show');
                id = $('#id_delete').val();
            });
            $('.modal-footer').on('click', '.delete', function() {
                $.ajax({
                    type: 'DELETE',
                    url: './Employees/' + id,
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                    },
                    success: function(data) {
                        $('.item' + data['id']).remove();
                    }
                });
            });

            // Status Changer
            $('.status').on('click', function(event){
                id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: "{{ URL::route('Employees.changeStatus') }}",
                    data: {
                        '_token': $('meta[name="_token"]').attr('content'),
                        'id': id
                    },
                    success: function(data) {
                        // empty
                    },
                });
            });

            //DataTable
            // Setup - add a text input to each footer cell
            $('#employees tfoot th').each( function () {
                var title = $(this).text();
                if (title != "Active" && title != "Action"){
                    $(this).html( '<input style="margin: 5px; max-width: 130px;" type="text" placeholder="Search" />' );
                } else {
                    $(this).html( '' );
                }
            } );

            // DataTable
            var table = $('#employees').DataTable({
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
                            <label class="control-label col-sm-4" for="code">Code:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="code_add" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="firstname">Firstname:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="firstname_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="lastname">Lastname:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="lastname_add">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="networklogonname">Network Logon Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="networklogonname_add">
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
                            <label class="control-label col-sm-4" for="code">Code:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="code_edit" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="firstname">Firstname:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="firstname_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="lastname">Lastname:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="lastname_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4" for="networklogonname">Network Logon Name:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="networklogonname_edit">
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
                            <label class="control-label col-sm-8" for="networklogonname">Network Logon Name:</label>
                            <div class="col-sm-10">
                                <input type="name" class="form-control" id="networklogonname_delete" disabled>
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
        <p><h1>Registered Employees</h1></p>
        <p>&nbsp;</p>

        <a href="#" class="add-modal"><button class="btn btn-block btn-danger"><span class="glyphicon glyphicon-plus"></span>New employee</button></a>
        <p>&nbsp;</p>
        <div class="container" style="overflow-x: scroll;">
            <table class="table-hover" id="employees">
                <thead>
                <tr>
                    <th style="text-align: center">Code</th>
                    <th style="text-align: center">Firstname</th>
                    <th style="text-align: center">Lastname</th>
                    <th style="text-align: center">Network Logon Name</th>
                    <th style="text-align: center">Active</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </thead>
                <tfoot style="display: table-header-group;">
                <tr>
                    <th style="text-align: center">Code</th>
                    <th style="text-align: center">Firstname</th>
                    <th style="text-align: center">Lastname</th>
                    <th style="text-align: center">Network Logon Name</th>
                    <th style="text-align: center">Active</th>
                    <th style="text-align: right">Action</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($employees as $employee)
                    <tr class="item{{$employee->id}}">
                        <td>{{$employee->code}}</td>
                        <td>{{$employee->firstname}}</td>
                        <td>{{$employee->lastname}}</td>
                        <td>{{$employee->networklogonname}} <small>({{$employee->tools->count()}})</small></td>
                        <td class="text-center"><input type="checkbox" class="status" data-id="{{$employee->id}}" @if ($employee->status) checked @endif></td>
                        <td class="text-right">
                            <button class="edit-modal btn btn-info" data-id="{{$employee->id}}" data-code="{{$employee->code}}" data-firstname="{{$employee->firstname}}" data-lastname="{{$employee->lastname}}" data-networklogonname="{{$employee->networklogonname}}">
                                <span class="glyphicon glyphicon-edit"></span> Edit</button>
                            <button class="delete-modal btn btn-danger" @if ($employee->tools->count()!=0) disabled @endif data-id="{{$employee->id}}" data-code="{{$employee->code}}" data-firstname="{{$employee->firstname}}" data-lastname="{{$employee->lastname}}" data-networklogonname="{{$employee->networklogonname}}">
                                <span class="glyphicon glyphicon-trash"></span> Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop