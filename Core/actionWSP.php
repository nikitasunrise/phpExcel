<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 08.05.15
 * Time: 0:35
 */

class actionWSP {
    private $mysqlObj;

    public function __construct($mysqlObj) {
        if($mysqlObj->getDBname()) {
            $this->mysqlObj = $mysqlObj;
        }
    }

    /**
     * @param $disciple
     * @param $competence
     * @param $mysqlObj
     * @return int
     */
    public function addDsCompRel($disciple, $competence, $isIdFlag) {
        $mysqlObj = $this->mysqlObj;
        // test connection
        if($mysqlObj) {
            // it work
            if ($isIdFlag == 0) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'WHERE name_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competence', 'what' => 'id_comp', 'exp' => 'WHERE name_comp="' . $competence . '"'];
            } elseif ($isIdFlag == 1) {
                $sc1 = ['table' => 'mcd_disciple', 'what' => '*', 'exp' => 'WHERE id_dis="' . $disciple . '"'];
                $sc2 = ['table' => 'mcd_competence', 'what' => '*', 'exp' => 'WHERE id_comp="' . $competence . '"'];
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
    public function addDiscAndComp($disciples, $relation = null) {

        $mysqlObj = $this->mysqlObj;
        if(isset($mysqlObj)) {
            foreach($disciples as $dis => $cs) {
                $st = explode(" ", $cs);
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'WHERE name_dis ="'.$dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);
                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                } else {
                    $i1 = ['table' => 'mcd_disciple', 'name_dis' => $dis];
                    //$id_dis = $mysqlObj->doInsertMySQL($i1);
                }
                foreach($st as $d => $c){
                    $s2 = ['table'=>'mcd_competence', 'what' => 'id_comp', 'exp' => 'WHERE name_comp="' . $c . '"'];
                    $s2res = $mysqlObj->doSelectMySQL($s2);
                    if(count($s2res) > 0) {
                        $id_comp = $s2res[0]['id_comp'];
                    } else {
                        $i2 = ['table' => 'mcd_competence', 'name_comp' =>  $c];
                        //$id_comp = $mysqlObj->doInsertMySQL($i2);
                    }
                    if ($relation == 1) {
                        $this->addDsCompRel($id_dis, $id_comp, $mysqlObj, 1);
                    } else {/* do nothing */ }
                }
            }
            return 1;
        } else return 0;
    }

    public function addWsp($formSt, $codeSt, $specSt, $qualSt, $limitSt, $profileSt, $yearSt, $hashPln) {
        $mysqlObj = $this->mysqlObj;

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
                'profile_st' => $profileSt,
                'year_st' => $yearSt,
                'md5_wsp' => $hashPln];
            $iPlnRes = $mysqlObj->doInsertMySQL($iPln);
            $_SESSION['id_wsp'] = $iPlnRes;
            return $iPlnRes;
        } else return 0;
    }

    public function addHourList($idWsp, $hourList, $mysqlObj) {
        $mysqlObj = $this->mysqlObj;
        if (!empty($hourList) && !empty($idWsp)) {
            foreach ($hourList as $dis => $cont) {
                $s1 = ['table' => 'mcd_disciple', 'what' => 'id_dis', 'exp' => 'WHERE name_dis ="'. $dis .'"'];
                $s1res = $mysqlObj->doSelectMySQL($s1);
                 // select DIS -> id
                if(count($s1res) > 0) {
                    $id_dis = $s1res[0]['id_dis'];
                    foreach ($cont as $trm => $hours) {
                        // 'Auditory' == 'Lection' + 'Laboratory' + 'Practice' + 'ControlWork'
                        // 'Study' == 'Auditory' + 'IndepWork'
                        // 'Total' == 'Study' + 'Examine'
                        $audit = $hours['Lection'] + $hours['Laboratory'] + $hours['Practice'] + $hours['ControlWork'];
                        $t = (string)$trm;
                        if (($audit == 0) && ($hours['TypeExamine'] == '')) {
                            // do nothing
                        } else {
                            $i1 = ['table' => 'mcd_wsp_ds',
                                'id_wsp' => $idWsp,
                                'id_ds' => $id_dis,
                                'trm' => $t[1],
                                'course' => $t[0],
                                'h_lect' => $hours['Lection'],
                                'h_lab' => $hours['Laboratory'],
                                'h_pract' => $hours['Practice'],
                                'h_control' => $hours['ControlWork'],
                                'h_indep' => $hours['IndepWork'],
                                'h_exam' => $hours['Examine'],
                                'type_exam' => $hours['TypeExamine']];
                            $mysqlObj->doInsertMySQL($i1);
                        }
                    }
                } else {}
            }
        } else return 0;
        //check and add
    }
}