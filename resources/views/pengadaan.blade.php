@extends('layouts.app2')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Data Pengadaan</h1>

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
                        <h3 class="card-title">Data Pengadaan</h3>
                    </div>
                    <div class="card-body">

                        <table id="dataTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Judul Pengadaan</th>
                                    <th>Jenis Pengadaan</th>
                                    <th>Status Pengadaan</th>
                                    <th>Jenis Anggaran</th>
                                    <th>Direksi Pekerjaan</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>

                            </thead>
                            <tbody>
                                @foreach($data as $pengadaan)
                                <tr>
                                    <td>{{$pengadaan["judul_pengadaan"]}}</td>
                                    <td>{{$pengadaan->jenisPengadaan->jenis_pengadaan}}</td>
                                    <td>{{$pengadaan->statusPengadaan->status_pengadaan}}</td>
                                    <td>{{$pengadaan->jenisAnggaran->jenis_anggaran}}</td>
                                    <td>{{$pengadaan->direksiPengadaan->direksi_pengadaan}}</td>
                                    <td>{{$pengadaan->createdBy->name}}</td>
                                    <td>{{$pengadaan->id}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
                    console.log();

                    if(column.index() == 6) return;

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
                    responsivePriority: 1,
                    "targets": 6,
                    "searchable": false,
                    "sortable" : false,
                    "render": function(data, type, row, meta) {
                        // return '<a class="btn-edit btn btn-default" href="#" data-id="'+data+'"><i class="fas fa-pencil-alt"></i></a> <a class="btn btn-danger ml-1" href="admin/users/delete/'+data+'"><i class="fas fa-trash"></i></a>';
                        return `
                        <div class="d-flex">
                            <a href="{{url("admin/pengadaan")}}/${data}" class="btn btn-primary btn-sm mx-1"><i class="fa fa-eye"> </i></a>
                        </div>
                        `;
                    }
                }
            ],

        });

      
    });
</script>

@endsection