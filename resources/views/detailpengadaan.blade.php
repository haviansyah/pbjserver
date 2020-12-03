@extends('layouts.app2')

@section('content')

<!-- Modal -->
<div class="modal fade" id="modalTimeline" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Timeline Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-red">10 Nov 2020</span>
                    </div>

                    <div>
                        <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> 12:05</span>
                            <h3 class="timeline-header"><a href="#">Rendal Har. Unit</a> Dokumen Dibuat</h3>
                        </div>
                    </div>

                    <!-- END timeline item -->
                    <div>
                        <i class="fas fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

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

            <div class="col-md-5">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Data Pengadaan</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Judul Pengadaan</strong>
                                <p class="text-muted">{{$data->judul_pengadaan}}</p>
                            </div>

                            <div class="col-md-6">
                                <strong>Jenis Pengadaan</strong>
                                <p class="text-muted">Pengadaan {{$data->jenisPengadaan->jenis_pengadaan}}</p>

                                <strong>Jenis Anggaran</strong>
                                <p class="text-muted">{{$data->jenisAnggaran->jenis_anggaran}}</p>

                                <strong>Direksi Pekerjaan</strong>
                                <p class="text-muted">{{$data->direksiPengadaan->direksi_pengadaan}}</p>

                                <strong>Status Pengadaan</strong>
                                <p class="text-muted">{{$data->statusPengadaan->status_pengadaan}}</p>
                            </div>

                            <div class="col-md-6">
                                <strong>Dibuat Oleh</strong>
                                <p class="text-muted">{{$data->createdBy->name}}
                                    ({{$data->createdBy->jabatan->jabatan_name}})</p>

                                <strong>Metode Pengadaan</strong>
                                <p class="text-muted">{{$data->metodePengadaan->metode_pengadaan}}</p>

                                <strong>Nomor Kontrak</strong>
                                <p class="text-muted">{{$data->nomor_kontrak}}</p>

                                <strong>Tanggal Selesai Kontrak</strong>
                                <p class="text-muted">
                                    {{$data->tanggal_selesai_kontrak ? $data->tanggal_selesai_kontrak->format("d M Y") : "-"}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
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
                                        <a href="" data-jenis="{{$dokumen->jenisDokumen->jenis_dokumen_name}}"
                                            data-id="{{$dokumen->id}}" class="button-delete btn btn-danger"><i
                                                class="fa fa-trash"></i></a>
                                        <a href="" data-id="{{$dokumen->id}}" class="button-timeline btn btn-success"><i
                                                class="fa fa-eye"></i></a>
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
    $(".button-delete-pengadaan").on("click", function (e) {
        e.preventDefault();
        const id = "{{$data->id}}";
        var konfirmasi = confirm(
            "Anda yakin ingin menghapus pengadaan {{$data->judul_pengadaan}} dan semua dokumen terkait?, karena operasi ini tidak dapat dikembalikan!"
        );
        if (konfirmasi) {
            $.ajax({
                type: "DELETE",
                url: "{{url('api/admin/pengadaan')}}/" + id,
                success: function (e) {
                    window.location.replace("{{url('/admin/pengadaan')}}");
                }
            })
        }
    });

    $(".button-timeline").on("click", function (e) {
        e.preventDefault();
        const modalTimeline = $("#modalTimeline");
        const id = $(e.currentTarget).data("id");

        $.ajax({
            method: "GET",
            url: "{{url('api/admin/dokumen/timeline')}}/" + id,
            success: function (response) {
                buildTimeline(response);
                modalTimeline.modal("show");
            }
        });

    });

    function buildDateTimeline(date) {
        return `
        <div class="time-label">
            <span class="bg-red">${date}</span>
        </div>
        `;
    }

    function buildActivityTimeline(activity) {
        var label = {};

        switch (activity.keterangan) {

            case "Revisi":
                label = {
                    icon: "fa-arrow-circle-left",
                    color: "red"
                };
                break;
            case "Konfirmasi":
                label = {
                    icon: "fa-check",
                    color: "blue"
                };
                break;
            case "Dikembalikan":
                label = {
                    icon: "fa-close",
                    color: "gray"
                };
                break;
           default:
                label = {
                    icon: "fa-arrow-circle-right",
                    color: "green"
                };
                break;
        }

        return `
        <div>
            <i class="fa ${label.icon} bg-${label.color}"></i>
            <div class="timeline-item">
                <span class="time"><i class="fas fa-clock"></i> ${activity.jam}</span>
                <h3 class="timeline-header"><a href="#">${activity.from}</a> </h3>
                <div class="timeline-body">
                    ${activity.keterangan == "Konfirmasi" ? "Konfirmasi untuk <strong>"+activity.step+"</strong>" : activity.keterangan }
                </div>
            </div>
        </div>
        `;
    }

    function buildTimeline(data) {
        timeline = "";
        const entries = Object.entries(data)
        entries.forEach((date) => {
            timeline += buildDateTimeline(date[0]);
            date[1].forEach((activity) => {
                timeline += buildActivityTimeline(activity);
            })
        });

        timeline += `
        <div>
            <i class="fas fa-clock bg-gray"></i>
        </div>`;
        $(".timeline").empty().html(timeline);
        // console.log(timeline);
    }

    $(".button-delete").on("click", function (e) {
        e.preventDefault();
        const id = $(e.currentTarget).data("id");
        const jenis = $(e.currentTarget).data("jenis");
        var konfirmasi = confirm("Anda yakin ingin menghapus dokumen " + jenis +
            " pengadaan {{$data->judul_pengadaan}}?, karena operasi ini tidak dapat dikembalikan!");
        if (konfirmasi) {
            $.ajax({
                type: "DELETE",
                url: "{{url('api/admin/dokumen')}}/" + id,
                success: function (e) {
                    location.reload();
                }
            })
        }
    })

</script>

@endsection
