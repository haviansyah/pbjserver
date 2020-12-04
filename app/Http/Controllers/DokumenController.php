<?php

namespace App\Http\Controllers;

use App\Helpers\PbjHelper;
use App\Http\Resources\ActivityDocument;
use App\Http\Resources\Dokumen as ResourcesDokumen;
use App\Http\Resources\Pengadaan;
use App\Models\ActivityDokumen;
use App\Models\Dokumen;
use App\Models\DokumenDmr;
use App\Models\DokumenPr;
use App\Models\Notification;
use App\Models\Pengadaan as ModelsPengadaan;
use App\Models\Step;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use JenisDokumenConst;
use JenisPengadaanConst;
use JWTAuth;
use RoleConst;
use RoleConstId;
use StateDocumentConst;
use StatusDokumenConst;
use TypeNotificationConst;
use UserIdConst;

class DokumenController extends Controller
{

    private $step;

    private $filterableAttr = [
        "jenis_dokumen_id",
    ];

    function __construct()
    {
        $step_db = Step::get()->groupBy("jenis_dokumen_id");
        $step_arr = [];
        foreach ($step_db as $jenis_dokumen_id => $step_list) {
            $idx = (int) $jenis_dokumen_id;
            $step_arr[$idx] = [];
            foreach ($step_list as $step) {
                $role = $step["role_id"];
                $submit = $step["submit"];
                $revise = $step["revise"];
                $step_arr[$idx][] = [$role, [$submit, $revise]];
            }
        }
        $this->step = $step_arr;
    }


    public function admin_delete($id)
    {
        $data = Dokumen::findOrFail($id);
        try {
            $data->delete();
            return response("OK", 200);
        } catch (Exception $e) {
            return response("NOT OK", 400);
        }
    }

    public function admin_timeline($id)
    {
        $data = Dokumen::findOrFail($id);
        $timeline = $data->activity;
        $activityBaru = new ActivityDokumen([
            "dokumen_id" => $data->id,
            "from_user_id" => $data->created_by_user_id,
            "to_user_id" => $data->created_by_user_id,
            "state" => 1,
            "step" => 0,
            "keterangan" => "Dokumen Dibuat",
            "created_at" => $data->created_at
        ]);
        $timeline->push($activityBaru);

        $grouped = ActivityDocument::collection($timeline)->sortBy("created_at")->groupBy(function ($item) {
            return $item->created_at->format('d M Y');
        });

        return response()->json($grouped, 200);
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $pengadaan = Dokumen::create([
            'pengadaan_id' => $request->get("pengadaan_id"),
            'jenis_dokumen_id' => $request->get("jenis_dokumen_id"),
            'status_dokumen_id' => $request->get("status_dokumen_id"),
            'created_by_user_id' => $user->id,
            'posisi_user_id' => $user->id,
        ]);

        if ($request->get("nomor_pr") != null) {
            $pengadaan->dokumenPr()->save(new DokumenPr(["nomor_pr" => $request->get("nomor_pr")]));
        }

        return response()->json($request->all(), 201);
    }


    function buildAction($id, $action)
    {
        return [
            "action_name" => ucfirst($action),
            "url" => url("api/dokumens/{$id}/{$action}")
        ];
    }

    public function getOne($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = Dokumen::findOrFail($id);

        $status_dokumen = $data->statusDokumen->id;
        $posisi_dokumen = $data->posisi->id;
        $jenis_dokumen = $data->jenisDokumen->id;
        $step_dokumen = $data->step;

        $data_array = new ResourcesDokumen($data);
        $action = [];

        if ($posisi_dokumen == $user->id) {
            if ($status_dokumen == StatusDokumenConst::MASUK) {
                $action = [
                    $this->buildAction($id, "konfirmasi"),
                    $this->buildAction($id, "tidak")
                ];
            } else {
                if (array_key_exists(1, $this->step[$jenis_dokumen][$step_dokumen])) {
                    $actions_list = $this->step[$jenis_dokumen][$step_dokumen][1];
                    foreach ($actions_list as $act) {
                        if ($act != null)
                            array_push($action, $this->buildAction($id, $act));
                    }
                }
            }
        }
        // return $this->step;
        return response()->json([
            "data" => $data_array,
            "aksi" => $action
        ]);
    }


    public function buildNotifBody(int $type, $dari, Dokumen $data)
    {
        switch ($type) {
            case 1:
                return "Dokumen diteruskan dari {$dari} : {$data->jenisDokumen->jenis_dokumen_name} " . PbjHelper::buildJudul($data->pengadaan->judul_pengadaan);
                break;
            case 2:
                return "Dokumen telah direvisi dari {$dari} : {$data->jenisDokumen->jenis_dokumen_name} " . PbjHelper::buildJudul($data->pengadaan->judul_pengadaan);
                break;
            case 3:
                return "Dokumen direvisi dari {$dari} : {$data->jenisDokumen->jenis_dokumen_name} " . PbjHelper::buildJudul($data->pengadaan->judul_pengadaan);
                break;
            case 4:
                return "Dokumen dikembalikan dari {$dari} : {$data->jenisDokumen->jenis_dokumen_name} " . PbjHelper::buildJudul($data->pengadaan->judul_pengadaan);
                break;
        }
    }

    public function dokumenSubmit($id)
    {
        $data = Dokumen::findOrFail($id);

        $status_dokumen = $data->statusDokumen->id;
        $posisi_dokumen = $data->posisi->id;
        $posisi_dokumen_role = $data->posisi->role_id;
        $jenis_dokumen = $data->jenisDokumen->id;

        // Ambil Step Dokumen Dari Database Dan Convert Ke ID Array
        $step_dokumen = $data->step;

        $step_next = $step_dokumen + 1;

        // SAAT PRA PENGADAAN
        if ($data->state_document == 1) {
            // Get Next STEP

            // CEK APAKAH DOKUMEN PR & Jenis Pengadaan adalah JASA MAKA SKIP AMU INVENTORY
            if (
                $data->jenis_dokumen_id == JenisDokumenConst::PR
                && $data->pengadaan->jenis_pengadaan_id == JenisPengadaanConst::JASA
                && $data->step == 0
            ) {
                // LANGSUNG APPROVAL MANAGER BIDANG
                $step_next = 2;
            }

            // Ambil role id selanjutnya
            $role_next = $this->step[$jenis_dokumen][$step_next][0];

            // Cari User dengan role selanjutnya 
            $role_user = User::where("role_id", $role_next)->first();


            // Cari User kalau manager bidang sesuai bidang 
            if ($role_next == RoleConstId::MANAGERBIDANG) {
                $bidang_dokumen = $data->pengadaan->direksi_pengadaan_id;
                $role_user = User::where("role_id", $role_next)->whereHas("managerBidang", function ($query) use ($bidang_dokumen) {
                    return $query->where("bidang_id", (int) $bidang_dokumen);
                })->first();
                // dd($role_user);
                $role_user_id = $role_user->id;
            } else if ($role_next == RoleConstId::MENG) {
                $role_user_id = 18;
            } else {
                $role_user_id = $role_user->id;
            }


            try {
                $data->prev_user_id = $data->posisi_user_id;
                $data->posisi_user_id = $role_user_id;
                $data->step = $step_next;
                $data->last_step =  $step_dokumen;
                $data->status_dokumen_id = StatusDokumenConst::MASUK;


                $notifBody = $this->buildNotifBody(1, User::find($data->prev_user_id)->name, $data);
                $notif = new Notification([
                    "user_id" => $role_user_id,
                    "title" => "Dokumen Di Teruskan",
                    "body" => $notifBody,
                    "data_id" => $data->id,
                    "data_type" => TypeNotificationConst::DOKUMEN
                ]);

                $data->save();
                $notif->save();

                PbjHelper::buildDocumentActivity($data, 1);
                PbjHelper::sendNotification($notif);
            } catch (Exception $e) {
                return $e;
            }

            return response()->json(["status" => "OK"], 200);

            // SAAT DI KEUANGAN
        } else if ($data->state_document == 2) {

            if ($posisi_dokumen_role == RoleConstId::RENDAL) {
                try {
                    $keu_id =  User::where("role_id", RoleConstId::KEUANGAN)->first()->id;
                    $data->prev_user_id = $data->posisi_user_id;
                    $data->posisi_user_id = $keu_id;
                    $data->step = $data->last_step;
                    $data->last_step = 0;
                    $data->status_dokumen_id = StatusDokumenConst::MASUK;
                    $notifBody = $this->buildNotifBody(2, User::find($data->prev_user_id)->name, $data);
                    $notif = new Notification([
                        "user_id" => $keu_id,
                        "title" => "Dokumen telah di revisi",
                        "body" => $notifBody,
                        "data_id" => $data->id,
                        "data_type" => TypeNotificationConst::DOKUMEN
                    ]);

                    $data->save();
                    $notif->save();
                    PbjHelper::buildDocumentActivity($data, 1);
                    PbjHelper::sendNotification($notif);
                } catch (Exception $e) {
                    return $e;
                }
            } else {
                // IF APPROVED BY KEUANGAN
                // $madm_id =  20;
                try {
                    // $data->prev_user_id = $data->posisi_user_id;
                    // $data->posisi_user_id = $madm_id;
                    // $data->step = $step_next;
                    // $data->last_step =  $step_dokumen;
                    $data->status_dokumen_id = StatusDokumenConst::KEU;

                    // $notifBody = $this->buildNotifBody(1, User::find($data->prev_user_id)->name, $data);
                    // $notif = new Notification([
                    //     "user_id" => $madm_id,
                    //     "title" => "Dokumen masuk",
                    //     "body" => $notifBody,
                    //     "data_id" => $data->id,
                    //     "data_type" => TypeNotificationConst::DOKUMEN
                    // ]);
                    // $notif->save();
                    // PbjHelper::sendNotification($notif);

                    $data->save();
                    PbjHelper::buildDocumentActivity($data, 1);
                } catch (Exception $e) {
                    return $e;
                }
            }

            // SAAT DOKUMEN DI PBJ
        } else if ($data->state_document == 3) {

            if ($posisi_dokumen_role == RoleConstId::RENDAL) {
                try {
                    // Move TO PBJ
                    $pbj_id =  User::where("role_id", RoleConstId::PBJ)->first()->id;
                    $data->prev_user_id = $data->posisi_user_id;
                    $data->posisi_user_id = $pbj_id;
                    $data->step = $data->last_step;
                    $data->last_step = 0;
                    $data->status_dokumen_id = StatusDokumenConst::MASUK;
                    $notifBody = $this->buildNotifBody(2, User::find($data->prev_user_id)->name, $data);
                    $notif = new Notification([
                        "user_id" => $pbj_id,
                        "title" => "Dokumen telah di revisi",
                        "body" => $notifBody,
                        "data_id" => $data->id,
                        "data_type" => TypeNotificationConst::DOKUMEN
                    ]);

                    $data->save();
                    $notif->save();
                    PbjHelper::buildDocumentActivity($data, 1);
                    PbjHelper::sendNotification($notif);
                } catch (Exception $e) {
                    return $e;
                }
            } else {
                // IF APPROVED BY PBJ
                try {
                    $data->status_dokumen_id = StatusDokumenConst::APPROVE;
                    $data->save();
                    PbjHelper::buildDocumentActivity($data, 1);
                } catch (Exception $e) {
                    return $e;
                }
            }
        }
    }

    // Konfirmasi Dokumen
    public function dokumenKonfirmasi($id)
    {
        try {
            $data = Dokumen::findOrFail($id);
            $data->prev_user_id = $data->posisi_user_id;
            $data->status_dokumen_id = StatusDokumenConst::REVIEW;

            if ($data->posisi->role_id == RoleConstId::KEUANGAN) {
                $data->state_document = StateDocumentConst::KEU;
            }
            if ($data->posisi->role_id == RoleConstId::PBJ) {
                $data->state_document = StateDocumentConst::PBJ;
            }

            $data->confirmed_at = new Carbon();
            $data->save();
            PbjHelper::buildDocumentActivity($data, 3);
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
        return response()->json(["status" => "OK"], 200);
    }


    public function dokumenRevise($id)
    {
        try {
            $data = Dokumen::findOrFail($id);
            $data->posisi_user_id = $data->created_by_user_id;
            $data->last_step = $data->step;
            $data->step = 0;
            $data->status_dokumen_id = StatusDokumenConst::MASUK;

            $notifBody = $this->buildNotifBody(3, User::find($data->prev_user_id)->name, $data);
            $notif = new Notification([
                "user_id" => $data->created_by_user_id,
                "title" => "Dokumen direvisi",
                "body" => $notifBody,
                "data_id" => $data->id,
                "data_type" => TypeNotificationConst::DOKUMEN
            ]);

            $data->save();
            $notif->save();
            PbjHelper::buildDocumentActivity($data, 2);
            PbjHelper::sendNotification($notif);
        } catch (Exception $e) {
            return response()->json($e, 400);
        }
        return response()->json(["status" => "OK"], 200);
    }

    // Kembalikan Tolak Dokumen
    public function dokumenBack($id)
    {
        $data = Dokumen::findOrFail($id);
        try {
            $user_id = $data->posisi_user_id;
            $ls = $data->last_step;
            $data->posisi_user_id = $data->prev_user_id;
            $data->last_step = $data->step;
            $data->step = $ls;
            $data->status_dokumen_id = StatusDokumenConst::REVIEW;

            $notifBody = $this->buildNotifBody(4, User::find($user_id)->name, $data);
            $notif = new Notification([
                "user_id" => $data->posisi_user_id,
                "title" => "Dokumen dikembalikan",
                "body" => $notifBody,
                "data_id" => $data->id,
                "data_type" => TypeNotificationConst::DOKUMEN
            ]);

            $data->save();
            $notif->save();
            PbjHelper::buildDocumentActivity($data, 4);
            PbjHelper::sendNotification($notif);
        } catch (Exception $e) {
            return response()->json($e, 400);
        }

        return response()->json(["status" => "OK"], 200);
    }

    public function input($id, Request $request)
    {
        $data = Dokumen::findOrFail($id);
        // var_dump($request->all());
        if ($request->get("nomorDMR") != null) {
            $data->dokumenDmr()->save(new DokumenDmr(["nomor_dmr" => $request->get("nomorDMR")]));
            $this->dokumenSubmit($id);
        }

        echo response()->json(["status" => "OK", 200]);
    }

    public function get(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $auth_role = $user->role->role_name;

        switch ($auth_role) {
            case RoleConst::PBJ:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id)->orWhere("state_document", 3);
                });
                break;
            case RoleConst::KEUANGAN:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id)->orWhere("state_document", 2);
                });
                break;
            default:
                $data = Dokumen::where(function ($query) use ($user) {
                    return $query->where("posisi_user_id", $user->id)->orWhere("created_by_user_id", $user->id);
                });
        }

        // KALAU MADM HIDE DOKUMEN YANG SUDAH DI APPROVE SAMA KEUANGAN
        if ($user->id == UserIdConst::MADM) {
            $data = $data->where(function ($q) {
                return $q->where("state_document", "!=", StateDocumentConst::KEU);
            });
        }

        if ($request->term && $request->term != "") {
            $term = $request->term;
            $data =  $data->whereHas("pengadaan", function ($query) use ($term) {
                return $query->where("judul_pengadaan", "LIKE", "%" . $term . "%");
            });
        }


        foreach ($this->filterableAttr as $filter) {
            if ($request->get($filter) && count($request->get($filter)) != 0) {
                $status_pengadaans = $request->get($filter);
                if (!in_array(null, $status_pengadaans)) {
                    $data = $data->whereIn($filter, $status_pengadaans);
                }
            }
        }

        if ($request->get("status_dokumen_id") && count($request->get("status_dokumen_id")) != 0) {
            $status_dokumen = $request->get("status_dokumen_id");
            if (!in_array(null, $status_dokumen)) {
                $data = $data->where(function ($q) use ($user, $status_dokumen) {
                    if (in_array(3, $status_dokumen)) array_push($status_dokumen, 1);
                    return $q->where("posisi_user_id", $user->id)->whereIn("status_dokumen_id", $status_dokumen);
                });
            }
        }

        $data = $data->where("status_dokumen_id", "!=", StatusDokumenConst::APPROVE);
        return ResourcesDokumen::collection($data->get()->sortBy("sla"));
        // return response()->json($dokumens,200);
    }


    public function showStep()
    {
        $data = Step::all();
        return view("step", compact("data"));
    }

    public function editStep($id, Request $request)
    {
        $step = Step::findOrFail($id);
        try {
            $sla_days = $request->get("sla");
            $sla_hours = $sla_days * 24;
            $step->sla = $sla_hours;
            $step->save();
        } catch (Exception $e) {
            return response()->json(["error" => $e], 400);
        }

        return response()->json(["status" => "ok"], 200);
    }
}
