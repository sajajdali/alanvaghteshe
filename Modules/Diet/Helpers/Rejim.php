<?php
namespace Modules\Diet\Helpers;

use Modules\Diet\Entities\Food;

class Rejim {
    public function makeRejim(){
        return Food::first();
    }

    function calculateWrist($userId = null) {
        if ($userId === null) {
            $user = auth()->user();
        } else {
            $user = User::find($userId);
        }

        $gender = $user->gender;
        $wrist = $user->wrist;

        if($gender == UserMeta::GENDER_MALE){
            switch ($wrist){
                case 1:
                    return 17;
                case  2:
                    return 19;
                    break;
                case 3:
                    return 21;
                    break;
            }
        } elseif($gender == UserMeta::GENDER_FEMALE){
            switch ($wrist){
                case 1:
                    return 14;
                case  2:
                    return 16;
                    break;
                case 3:
                    return 18;
                    break;
            }
        }
    }
    function typeBody($vazn = '', $ghad = '', $doremoch = 17, $sen = '',
                      $jensiat = '', $rejimBody = '1', $rejimLevel = 'none',$activityRate = '')
    {
        if($jensiat != 1 && $jensiat != 2){
            $jensiat = ($jensiat == 'Male' ? 1 : 2);
        }
        $result = array();

        /*$vazn = $vazn;
        $ghad = $ghad;
        $doremoch = $doremoch;
        $sen = $sen;
        $jensiat = $jensiat;*/

        $JOSE = round($ghad / $doremoch); // andaze jose meghdare (r)
        $BMI = round(	(($vazn / ($ghad * $ghad)) * 10000) , 1); // BMI badan dar safhe 23
        $result['BMI'] = $BMI;
        if($BMI <= 18.5){
            $result['bodyType'] = 'کم وزنی';
            $result['bodyTypeNumber'] = 1;
        }elseif($BMI >= 18.5 && $BMI <= 24.9){
            $result['bodyType'] = 'طبیعی';
            $result['bodyTypeNumber'] = 2;
        }elseif($BMI >= 25 && $BMI <= 29.9){
            $result['bodyType'] = 'اضافه وزن';
            $result['bodyTypeNumber'] = 3;
        }elseif($BMI >= 30 && $BMI <= 34.9){
            $result['bodyType'] = 'چاقی درجه I';
            $result['bodyTypeNumber'] = 4;
        }elseif($BMI >= 35 && $BMI <= 39.9){
            $result['bodyType'] = 'چاقی درجه II';
            $result['bodyTypeNumber'] = 5;
        }elseif($BMI >= 40){
            $result['bodyType'] = 'چاقی درجه III';
            $result['bodyTypeNumber'] = 6;
        }
        // end of mohasebe namye tode badan
        /////******* END MHASEBAT **********/////////////
        /////******* START NAMAYESHI**********/////////////
        // start taeen andazs jose bar hasbe $JOSE
        switch($jensiat){
            case '1'; {
                if($JOSE > 10.4){
                    $JOSEKOL = 'کوچک';
                }elseif($JOSE >= 9.6 && $JOSE <= 10.4){
                    $JOSEKOL = 'متوسط';
                }elseif($JOSE < 9.6){
                    $JOSEKOL = 'بزرگ';
                }
                break;
            }
            case '2'; {
                if($JOSE > 11){
                    $JOSEKOL = 'کوچک';
                }elseif($JOSE >= 10.1 && $JOSE <= 11){
                    $JOSEKOL = 'متوسط';
                }elseif($JOSE < 10.1){
                    $JOSEKOL = 'بزرگ';
                }
                break;
            }

        }
        $result['jose'] = isset($JOSEKOL) ? $JOSEKOL : '';
        $JOSE; // adade size jose
        // end taeen andazs jose bar hasbe $JOSE
        // start mohasebe IBW badan va vazne ideall
        switch($jensiat){
            case '1'; {
                $IBW = 48 + ($ghad - 150);
                break;
            }
            case '2'; {
                $IBW = 45 + ($ghad - 150);
                break;
            }
        }
        // end mohasebe IBW badan va vazne ideall
        // start IBW base jose bozorg 10% va base jose kocka -10%
        //        switch($JOSEKOL){
        //            case 'بزرگ';{
        //                $darsade_IBW = (10 * $IBW)/100;
        //                //$gIBW = $IBW + $darsade_IBW;
        //                break;
        //            }
        //            case 'کوچک';{
        //                $darsade_IBW = (10 * $IBW)/100;
        //                //$IBW = $IBW - $darsade_IBW;
        //
        //                break;
        //            }
        //        }
        if($vazn >= $IBW){
            $EZAFE_VAZN = $vazn - $IBW;
            $result['EZAFE_VAZN_type'] = 1;
            $result['EZAFE_VAZN_result'] = $EZAFE_VAZN;
        }else{
            $EZAFE_VAZN = '0';
            $result['EZAFE_VAZN_type'] = 2;
            $result['EZAFE_VAZN_result'] = $IBW-$vazn;
        }
        $result['EZAFE_VAZN'] = $EZAFE_VAZN;
        if($EZAFE_VAZN > 0){
            $CURRECT_VAZN = $vazn - $EZAFE_VAZN;
        }else{
            $CURRECT_VAZN = $IBW;
        }
        // ENd IBW base jose bozorg 10% va base jose kocka -10%
        $result['CURRECT_VAZN'] = abs($CURRECT_VAZN); // وزن ایده عال شما
        $result['Jensiat'] = $jensiat; // وزن ایده عال شما
        // start mohasebe darsade IBW
        $D_IBW = round(($vazn / $IBW) * 100); // darsade vazne ide all
        if($D_IBW >= 200){
            $D_IBW = 'چاقی شدید';
        }elseif($D_IBW > 130 && $D_IBW < 199){
            $D_IBW = 'چاقی';
        }elseif($D_IBW > 110 && $D_IBW < 129){
            $D_IBW = 'اضافه وزن';
        }elseif($D_IBW > 91 && $D_IBW < 109){
            $D_IBW = 'طبیعی';
        }elseif($D_IBW > 80 && $D_IBW < 90){
            $D_IBW = 'سوء تغذیه خفیف';
        }elseif($D_IBW > 70 && $D_IBW < 79){
            $D_IBW = 'سوء تغذیه متوسط';
        }elseif($D_IBW <= 69){
            $D_IBW = 'سوء تغذیه شدید';
        }
        $result['vaziat_badan'] = $D_IBW;
        // end mohasebe darsade IBW
        // start estefade az bmi
        /* agar BMI < 40 bod az vazne feli estefade mishavad va agar boaorgtar az 40 bood az vazne ideal*/
        //	if($BMI >= 40){
        //		$vazn_badan = $IBW;
        //	}else{
        //		$vazn_badan = $vazn;
        //	}
        // END start estefade az bmi
        // mohasebe enerjo va kallery (BEE)
        $vazn_badan = $vazn;
        switch($jensiat){
            case '1'; {
                $BEE = round(((66.47 + (13.75 * $vazn_badan)) + (5 * $ghad)) - (6.76 * $sen)); // mohasebe enerjo va kallery
                break;
            }
            case '2'; {
                $BEE = round(655.1 + (9.56 * $vazn_badan) + (1.85 * $ghad) - (4.68 * $sen)); // mohasebe enerjo va kallery
                break;
            }
        }

        switch($JOSEKOL){
            case 'بزرگ';{
                $darsade_IBW = (10 * $BEE)/100;
                $BEE = $BEE + $darsade_IBW;
                break;
            }
            case 'کوچک';{
                $darsade_IBW = (10 * $BEE)/100;
                $BEE = $BEE - $darsade_IBW;
                break;
            }
        }

        if($activityRate == ""){
            $BEE *= 1.45;
        }else{
            switch($activityRate){

                case 1:
                    $BEE *= 1;
                    break;
                case 2:
                    $BEE *= 1;
                    break;
                case 3:
                    $BEE *= 1.2;
                    break;
                case 4:
                    $BEE *= 1.45;
                    break;
                case 5:
                    $BEE *= 1.65;
                    break;
                default:
                    $BEE *= 1.45;
                    break;

                //            case 1:
                //                $BEE *= 1.2;
                //                break;
                //            case 2:
                //                $BEE *= 1.45;
                //                break;
                //            case 3:
                //                $BEE *= 1.65;
                //                break;
                //            case 4:
                //                $BEE *= 1.85;
                //                break;
                //            case 5:
                //                $BEE *= 2.2;
                //                break;
                //            default:
                //                $BEE *= 1.65;
                //                break;
            }
        }
        //echo $BEE;
        // START zarayebe faaleiat
        /*
            switch ($faaliat){
                case 5;{
                    $BEE *= 1.2;
                break;
                }
                case 2;{
                    $BEE *=  1.45;
                break;
                }
                case 3;{
                    $BEE *=1.65;
                break;
                }
                case 4;{
                    $BEE *=1.85;
                break;
                }
                case 5;{
                    $BEE *=2.2;
                break;
                }
            }
            */
        // در صورتی که درخواست رژیم کاهش وزن داشته باشد
        if($rejimBody == 1){
            if($rejimLevel == 'none'){
                if($BEE < 1500){
                    $BEE = $BEE - 500;
                }else{
                    $BEE = $BEE - 800;
                }
                if($BEE <= 1000){
                    $BEE = 1000;
                }
            }else{
                switch($rejimLevel){
                    case '3':
                        $BEE = $BEE - 1000;
                        break;
                    case '2':
                        $BEE = $BEE - 800;
                        break;
                    case '1':
                        $BEE = $BEE - 500;
                        break;
                    default:
                        $BEE = $BEE - 800;
                        break;
                }

            }
            if($BEE <= 800){
                $BEE = 800;
            }
        }
        // در صورتی که درخواست رژیم کاهش وزن داشته باشد
        // در صورتی که درخواست رژیم افزایش وزن داشته باشد
        elseif($rejimBody == 2){
            if($BEE < 1500){
                $BEE = $BEE + 500;
            }else{
                $BEE = $BEE + 1000;
            }
        }else{
            $BEE -= 300;
            if($BEE <= 800){
                $BEE = 800;
            }
        }
        // در صورتی که درخواست رژیم افزایش وزن داشته باشد
        $result['type'] = 'success';
        $result['BEE'] = $BEE;

        return $result;
    }
}
