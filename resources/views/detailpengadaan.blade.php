@extends('layouts.app2')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-6">
                <h1 class="m-0 text-dark">Detail Pengadaan</h1>
            </div><!-- /.col -->
            <div class="col-6">
                <a href="#" class="btn btn-danger button-delete-pengadaan float-right">Hapus Pengadaan</a>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Data Pengadaan</h3>
                    </div>
                    <div class="card-body">
                        <strong>Judul Pengadaan</strong>
                        <p class="text-muted">{{$data->judul_pengadaan}}</p>

                        <strong>Jenis Pengadaan</strong>
                        <p class="text-muted">Pengadaan {{$data->jenisPengadaan->jenis_pengadaan}}</p>

                        <strong>Jenis Anggaran</strong>
                        <p class="text-muted">{{$data->jenisAnggaran->jenis_anggaran}}</p>

                        <strong>Direksi Pekerjaan</strong>
                        <p class="text-muted">{{$data->direksiPengadaan->direksi_pengadaan}}</p>

                        <strong>Status Pengadaan</strong>
                        <p class="text-muted">{{$data->statusPengadaan->status_pengadaan}}</p>

                        <strong>Dibuat Oleh</strong>
                        <p class="text-muted">{{$data->createdBy->name}} ({{$data->createdBy->jabatan->jabatan_name}})</p>

                        <strong>Metode Pengadaan</strong>
                        <p class="text-muted">{{$data->metodePengadaan->metode_pengadaan}}</p>

                        <strong>Nomor Kontrak</strong>
                        <p class="text-muted">{{$data->nomor_kontrak}}</p>

                        <strong>Tanggal Selesai Kontrak</strong>
                        <p class="text-muted">{{$data->tanggal_selesai_kontrak ? $data->tanggal_selesai_kontrak->format("d M Y") : "-"}}</p>

                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Dokumen</h3>
                    </div>
                    <div class="card-body">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>Jenis Dokumen</th>
                                  <th>Posisi</th>
                                  <th>Status</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach($data->dokumen as $dokumen)
                              <tr>
                                  <td>{{$dokumen->jenisDokumen->jenis_dokumen_name}}</td>
                                  <td>{{$dokumen->posisi->name}} ({{$dokumen->posisi->jabatan->jabatan_name}})</td>
                                  <td>{{$dokumen->statusDokumen->status_dokumen_name}}</td>
                                  <td>
                                      <a href="" data-jenis="{{$dokumen->jenisDokumen->jenis_dokumen_name}}" data-id="{{$dokumen->id}}" class="button-delete btn btn-danger"><i class="fa fa-trash"></i></a>
                                  </td>
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

    $(".button-delete-pengadaan").on("click",function(e){
        e.preventDefault();
        const id = "{{$data->id}}";
        var konfirmasi = confirm("Anda yakin ingin menghapus pengadaan {{$data->judul_pengadaan}} dan semua dokumen terkait?, karena operasi ini tidak dapat dikembalikan!");
        if(konfirmasi){
            $.ajax({
                type : "DELETE",
                url : "{{url('api/admin/pengadaan')}}/"+id,
                success : function(e){
                    window.location.replace("{{url('/admin/pengadaan')}}");
                }
            })
        }
    });
    $(".button-delete").on("click",function(e){
        e.preventDefault();
        const id = $(e.currentTarget).data("id");
        const jenis = $(e.currentTarget).data("jenis");
        var konfirmasi = confirm("Anda yakin ingin menghapus dokumen "+jenis+" pengadaan {{$data->judul_pengadaan}}?, karena operasi ini tidak dapat dikembalikan!");
        if(konfirmasi){
            $.ajax({
                type : "DELETE",
                url : "{{url('api/admin/dokumen')}}/"+id,
                success : function(e){
                    location.reload();
                }
            })
        }
    })
</script>

@endsection