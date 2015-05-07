<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 08.05.15
 * Time: 0:35
 */

class actionWSP {

    public function __construct() {
    }

    /**
     * @param $disciple
     * @param $competiton
     * @param $mysqlObj
     * @return int
     */
    public function addDsCompRel($disciple, $competiton, $mysqlObj, $itsIdFlag) {
        // test connection
        if($mysqlObj) {
            // it work
            if ($itsIdFlag == 0) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $competiton . '"'];
            } elseif ($itsIdFlag == 1) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => '*', 'exp' => 'id_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competition', 'what' => '*', 'exp' => 'id_comp="' . $competiton . '"'];
            }
            if(count($r1 = $mysqlObj->doSelectMySQL($sc1)) > 0) {
                $id_dis = $r1[0]['id_dis'];
            }
            if(count($r2 = $mysqlObj->doSelectMySQL($sc2)) > 0) {
                $id_comp = $r2[0]['id_comp'];
            }
            if(isset($id_dis) && isset($id_comp)) {
                $i = ['table' => 'mcd_dc', 'id_dis' => $id_dis, 'id_comp' => $id_comp];
                if($r = $mysqlObj->doInsertMySQL($i)) return 1;
                else return 0;
            }
        } else return 0;
    }

    /**
     * @param $mysqlObj
     * @param $disciples
     * @return int
     */
    public function addDiscAndComp($mysqlObj, array $disciples, $relation = null) {
        if(isset($mysqlObj)) {
            foreach($disciples as $dis => $cs) {
                $st = explode(" ", $cs);
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'.$dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);

                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                } else {
                    $i1 = ['table' => 'mcd_disciple', 'name_dis' => $dis];
                    $li1 = $mysqlObj->doInsertMySQL($i1);
                }
                foreach($st as $d => $c){
                    $s2 = ['table'=>'mcd_competition', 'what' => 'id_comp', 'exp' => 'name_comp="' . $c . '"'];
                    $s2res = $mysqlObj->doSelectMySQL($s2);

                    if(count($s2res) > 0) {
                        $id_comp = $s1res[0]['id_comp'];
                    } else {
                        $i2 = ['table' => 'mcd_competition', 'name_comp' =>  $c];
                        $li2 = $mysqlObj->doInsertMySQL($i2);
                    }
                    if ($relation == 1) {
                        $this->addDsCompRel($li1, $li2, $mysqlObj, 1);
                    } else {/* do nothing */ }
                }
            }
            return 1;
        } else return 0;
    }

    public function addWsp($formSt, $codeSt, $specSt, $qualSt, $limitSt, $profileSt) {
        if (!empty($formSt) &&
            !empty($codeSt) &&
            !empty($specSt) &&
            !empty($qualSt) &&
            !empty($limitSt) &&
            !empty($profileSt)) {
            $iPln = [
                'table' => 'mcd_wsp',
                'form_st' => $formSt,
                'qual_st' => $qualSt,
                'code_group' => $codeSt,
                'name_spec' => $specSt,
                'count_year' => $limitSt,
                'profile_st' => $profileSt
            ];
            $iPlnRes = $mysqlObj->doInsertMySQL($iPln);
            $_SESSION['id_wsp'] = $iPlnRes;
            return $iPlnRes;
        } else return 0;
    }

    public function addHourList($idWsp, $hourList, $mysqlObj) {
        if (!empty($hourList) && isset($mysqlObj) && !empty($idWsp)) {
            foreach ($hourList as $dis => $cont) {
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'name_dis ="'. $dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);
                 // select DIS -> id
                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                    foreach ($cont as $term => $hours) {
                        // 'Auditory' == 'Lection' + 'Laboratory' + 'Practice' + 'ControlWork'
                        // 'Study' == 'Auditory' + 'IndepWork'
                        // 'Total' == 'Study' + 'Examine'
                        $i1 = ['table' => 'mcd_wsp_ds',
                            'id_wsp' => $idWsp,
                            'id_ds' => $id_dis,
                            'term' => $term[1],
                            'course' => $term[0],
                            'h_lect' => $hours['Lection'],
                            'h_lab' => $hours['Laboratory'],
                            'h_pract' => $hours['Practice'],
                            'h_control' => $hours['ControlWork'],
                            'h_indep' => $hours['IndepWork'],
                            'h_exam' => $hours['Examiine'],
                            'type_exam' => $hours['TypeExamine']
                        ];
                        if ($mysqlObj->doInsertMySQL($i1)) return 1;
                        else return 0;
                    }
                } else {
                    //disciple does not exist
                    return 0;
                }
            }
        } else return 0;
        //check and add
    }

} 