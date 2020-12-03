@extends('layouts.app2')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Setting SLA</h1>

            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List LSA</h3>
                    </div>
                    <div class="card-body">

                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Posisi Role</th>
                                    <th>Status Dokumen</th>
                                    <th>SLA (Hari)</th>
                                </tr>
                               
                            </thead>
                            <tbody>
                                @foreach($data as $step)
                                <tr>
                                    <td>{{$step->id}}</td>
                                    <td>{{$step->jenisDokumen->jenis_dokumen_name}}</td>
                                    <td>{{$step->role->role_name}}</td>
                                    <td>{{$step->step_name}}</td>
                                    <td>{{$step->sla/24}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Jenis Dokumen</th>
                                    <th>Posisi Role</th>
                                    <th>Status Dokumen</th>
                                    <th>SLA (Hari)</th>
                                </tr>
                            </tfoot>
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
        var table = $("#dataTable").DataTable({
            responsive: true,
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if(column.index() == 4) return;
                    var select = $('<select class="form-control"><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            

                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "responsivePriority": -1,
                },
                {
                    "targets": [2],
                    "responsivePriority": 102,
                },
                {
                    "targets": [3],
                    "responsivePriority": 1,
                },
                {
                    responsivePriority: 1,
                    "searchable" : false,
                    "sortable" : false,
                    "targets": 4,
                    "render": function(data, type, row, meta) {
                        // return '<a class="btn-edit btn btn-default" href="#" data-id="'+data+'"><i class="fas fa-pencil-alt"></i></a> <a class="btn btn-danger ml-1" href="admin/users/delete/'+data+'"><i class="fas fa-trash"></i></a>';
                        return `
                    <div class="input-group input-group-sm">
                        <input type="number" id="sla-${row[0]}" class="form-control" value="${data}">
                        <span class="input-group-append">
                            <button type="button" data-id="${row[0]}" class="btn-change btn btn-success btn-flat">Edit</button>
                        </span>
                    </div>
                    `;
                    }
                }
            ],

        });

        $(document).on("click",".btn-change",function(e){
            var id = $(e.currentTarget).data("id");
            var inputForm = $("#sla-"+id);
            var newInput = inputForm.val();
            console.log(newInput);

            $.ajax({
              url:  `{{url("/api/step/")}}/${id}`,
              method : "POST",
              data : {
                  "sla" : newInput
              },
              success: function(response){
                Swal.fire(
                    'Berhasil',
                    'SLA Berhasil diubah',
                    'success'
                ).then(function() {
                    location.reload();
                });
                setTimeout(function() {
                    location.reload();
                }, 2000)
              },
              error: function(error){
                  console.error(error);
                  location.reload();
              }
            })
        });

        $(".btn-edit").click(function(e) {
            var id = $(e.currentTarget).data("id");

            $.ajax({
                method: "GET",
                url: "api/users/" + id
            }).done(function(msg) {
                console.log(msg.name);
                $("#name").val(msg.name);
                $("#email").val(msg.email);
                $("#role_id").val(msg.role_id);
                $("#jabatan_id").val(msg.jabatan_id);
                $("#btn-edit-save").data("id", id);
                $("#modal-edit").modal("toggle");
            });


        });

        $("#btn-edit-save").click(function(e) {
            var id = $(e.currentTarget).data("id");
            name = $("#name").val();
            email = $("#email").val();
            role_id = $("#role_id").val();
            jabatan_id = $("#jabatan_id").val();
            password1 = $("#password1").val();
            password2 = $("#password2").val();

            console.log([
                name, email, role_id, jabatan_id, password1, password2
            ]);
            data = {
                name: name,
                email: email,
                role_id: role_id,
                jabatan_id: jabatan_id,

            };
            if (password1 != "") {
                data["password"] = password1
                data["password_confirmation"] = password2
            }
            $.ajax({
                method: "PUT",
                url: "api/users/" + id,
                data: data
            }).done(function(msg) {
                console.log(msg);

                Swal.fire(
                    'Berhasil',
                    'Data User Berhasil Diubah',
                    'success'
                ).then(function() {
                    location.reload();

                });
                $("#modal-edit").modal("toggle");
                setTimeout(function() {
                    location.reload();
                }, 2000)

            }).fail(function(msg) {
                alert(msg.responseText);
                console.log(msg.responseText);
                // location.reload();
            });
        });
    });
</script>

@endsection