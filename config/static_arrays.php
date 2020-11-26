<?php
class RoleConst{
    const ADMIN = "admin";
    const RENDAL = "rendal";
    const SEKERTARISGM = "sekertarisgm";
    const MANAGERBIDANG = "managerbidang";
    const GM = "gm";
    const KEUANGAN = "keuangan";
    const LIM = "lim";
    const MENG = "meng";
    const AMUINVENTORY = "amuinventory";
    const MADM = "madm";
    const PBJ = "pbj";
}

class RoleConstId{
    const ADMIN = 0;
    const RENDAL = 1;
    const SEKERTARISGM = 2;
    const MANAGERBIDANG = 3;
    const GM = 4;
    const KEUANGAN = 5;
    const LIM = 7;
    const MENG = 8;
    const AMUINVENTORY = 9;
    const MADM = 10;
    const PBJ = 11;
}

class StatusPengadaanConst{
    const PRAPENGADAAN = 1;
    const DPHPS = 2;
    const AANWIZJING = 3;
    const PPH = 4;
    const SKP = 5;
    const KONTRAK = 6;
}

class JenisPengadaanConst{
    const BARANG = 2;
    const JASA = 1;
}

class JenisAnggaranConst{
    const AO = 1;
    const APO = 2;
    const AI = 3;
}

class MetodePengadaanConst{
    const PENGADAAN_LANGSUNG = 1;
    const LELANG = 2;
    const PENUNJUKAN_LANSUNG = 3;
}

class DireksiPekerjaanConst{
    const MHAR = 1;
    const MOPN = 2;
    const MENG = 3;
    const MLEP = 4;
    const MADM = 5;
}

class JenisDokumenConst{
    const TOR = 1;
    const DMR = 2;
    const PR  = 3;
}

class StatusDokumenConst{
    const BARU = 1;
    const MASUK = 2;
    const REVIEW = 3;
    const KEU = 4;
    const PBJ = 5;
    const REVISE = 6;
    const APPROVE = 7;
}

class StateDocumentConst{
    const PRA = 1;
    CONST KEU = 2;
    CONST PBJ = 3;
}


class UserIdConst{
    const KEUANGAN = 15;
    const MADM = 20;
    const PBJ = 22;
}

class TypeNotificationConst{
    const DOKUMEN = 1;
    const PENGADAAN = 2;
}