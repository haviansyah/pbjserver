@extends('layouts.app2')

@section('content')
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('store.user') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required name="email" type="email" class="form-control" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputName">Name</label>
                        <input required name="name" type="text" class="form-control" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input required name="password" type="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password Confirmation</label>
                        <input required name="password_confirmation" type="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select required class="form-control" name="role_id">
                            @foreach(\App\Models\Role::all() as $role)
                            <option value="{{$role->id}}">{{$role->role_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select required class="form-control" name="jabatan_id">
                            @foreach(\App\Models\Jabatan::all() as $jabatan)
                                @if($jabatan->jabatan_name != "Manager Bidang")
                                <option value="{{$jabatan->id}}">{{$jabatan->jabatan_name}}</option>
                                @else
                                    @foreach(\App\Models\DireksiPengadaan::all() as $bidang)
                                        <option value="{{$jabatan->id}}-{{$bidang->id}}">{{$jabatan->jabatan_name}} {{$bidang->direksi_pengadaan}}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save changes"></input>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit User</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- <form action="{{ route('store.user') }}" method="post">
                @csrf -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required name="email" type="email" class="form-control" id="email" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputName">Name</label>
                        <input required name="name" type="text" class="form-control" id="name" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input  name="password" type="password" class="form-control" id="password1" placeholder="Password">
                        <small>Kosongkan Jika Tidak Ada Perubahan</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password Confirmation </label>
                        
                        <input  name="password_confirmation" type="password" class="form-control" id="password2" placeholder="Password">
                        <small>Kosongkan Jika Tidak Ada Perubahan</small>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select required class="form-control" id="role_id" name="role_id">
                            @foreach(\App\Models\Role::all() as $role)
                            <option value="{{$role->id}}">{{$role->role_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select required class="form-control" name="jabatan_id" id="jabatan_id">
                            @foreach(\App\Models\Jabatan::all() as $jabatan)
                                @if($jabatan->jabatan_name != "Manager Bidang")
                                <option value="{{$jabatan->id}}">{{$jabatan->jabatan_name}}</option>
                                @else
                                    @foreach(\App\Models\DireksiPengadaan::all() as $bidang)
                                        <option value="{{$jabatan->id}}-{{$bidang->id}}">{{$jabatan->jabatan_name}} {{$bidang->direksi_pengadaan}}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="button" id="btn-edit-save" class="btn btn-primary" value="Save changes"></input>
                </div>
            <!-- </form> -->
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-6">
                <h1 class="m-0 text-dark">List User</h1>

            </div><!-- /.col -->
            <div class="col-6 ">
                <button href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal-lg">
                    Insert User
                </button>
            </div>
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List User</h3>
                    </div>
                    <div class="card-body">
                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Jabatan</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->jabatan->jabatan_name}} @if($user->role->id == RoleConstId::MANAGERBIDANG) {{$user->managerBidang->bidang->direksi_pengadaan}} @endif</td>
                                    <td>{{$user->role->role_name}} </td>
                                    <td>{{$user->id}}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section("javascripts")
<script>
    $(function() {
        $("#dataTable").DataTable({ 
            responsive: true,
            "columnDefs": [ {
                responsivePriority: 1,
                "targets": 4,
                "data": "download_link",
                "render": function ( data, type, row, meta ) {
                // return '<a class="btn-edit btn btn-default" href="#" data-id="'+data+'"><i class="fas fa-pencil-alt"></i></a> <a class="btn btn-danger ml-1" href="admin/users/delete/'+data+'"><i class="fas fa-trash"></i></a>';
                return '<a class="btn-edit btn btn-default" href="#" data-id="'+data+'"><i class="fas fa-pencil-alt"></i></a>';
                }
            } ]
        });

        $(document).on("click",".btn-edit",function(e){
            var id = $(e.currentTarget).data("id");

            $.ajax({
                method : "GET",
                url : "api/users/"+id
            }).done(function(msg){
                console.log(msg.name);
                $("#name").val(msg.name);
                $("#email").val(msg.email);
                $("#role_id").val(msg.role_id);
                $("#jabatan_id").val(msg.jabatan_id);
                $("#btn-edit-save").data("id",id);
                $("#modal-edit").modal("toggle");
            });

            
        });

        $("#btn-edit-save").click(function(e){
            var id = $(e.currentTarget).data("id");
            name = $("#name").val();
            email = $("#email").val();
            role_id = $("#role_id").val();
            jabatan_id = $("#jabatan_id").val();
            password1 = $("#password1").val();
            password2 = $("#password2").val();

            console.log([
                name,email,role_id,jabatan_id,password1,password2
            ]);
            data = {
                    name : name,
                    email : email,
                    role_id : role_id,
                    jabatan_id : jabatan_id,
                    
            };
            if(password1 != ""){
                data["password"] = password1
                data["password_confirmation"] = password2
            }
            $.ajax({
                method : "PUT",
                url : "api/users/"+id,
                data : data
            }).done(function(msg){
                console.log(msg);
                
                Swal.fire(
                    'Berhasil',
                    'Data User Berhasil Diubah',
                    'success'
                    ).then(function(){
                    location.reload();
                        
                    });
                $("#modal-edit").modal("toggle");
                setTimeout(function(){
                    location.reload();
                },2000)
                
            }).fail(function(msg){
                alert(msg.responseText);
                console.log(msg.responseText);
                // location.reload();
            });
        });
    });
</script>

@endsection