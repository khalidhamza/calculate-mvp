<?php
    /**
     * Author : Khalid Hamza
     * Mobile : 94771608
     * Email : khalid_hamza2015@yahoo.com
     * Task : Calculate MVP
     * Progarmming Language : PHP
     * Date : 2019-12-18
     * Task Time : 2:15
     */
    
    /**
     * Find Key 
     * @param array $array
     * @param string|int $keySearch
     * @return bool
     */ 
    function findKey($array, $keySearch)
    {
        foreach ($array as $key => $item) {
            if ($key == $keySearch) {
                return true;
            } elseif (is_array($item) && findKey($item, $keySearch)) {
                return true;
            }
        }
        return false;
    }


    /**
     *  SETTING
     *  contains sports, postions, rates and number of information in each row
     */

    $setting    = [
        'basketball'    => [
            'row_info'  => 8,
            'positions' => [
                ['name' => 'G' , 'rates' => ['score' => 2, 'rebound' => 3, 'assist' => 1]],
                ['name' => 'F' , 'rates' => ['score' => 2, 'rebound' => 2, 'assist' => 2]],
                ['name' => 'C' , 'rates' => ['score' => 2, 'rebound' => 1, 'assist' => 3]],
            ], 
        ],
        'handball'      => [
            'row_info'  => 7,
            'positions' => [
                ['name' => 'G' , 'rates' => ['initial' => 50, 'made' => 5, 'received' => -2]],
                ['name' => 'F' , 'rates' => ['initial' => 20, 'made' => 1, 'received' => -1]],
            ], 
        ],
    ];

    // print_r($setting);die();

    // STRORED FILES
    $files  = ['files/basketball.txt', 'files/handball.txt'];

    $content    = file($files[1]);
    if(is_array($content) && count($content) > 0){
        
        // get sport name
        $sport  = trim(strtolower($content[0]));
        
        // check if the sport already mentioned in setting
        if(findKey($setting, $sport)){
            
            // get row info
            $row_info   = $setting[$sport]['row_info'];
            $players    = [];
            $teams      = [];
            for ($i=1; $i < count($content); $i++) { 
                $palayerStat    = explode(';', $content[$i]);
                
                // check the players row info
                if(count($palayerStat) == $row_info){
                    
                    // CALCULTE RATE
                    if($sport == 'basketball'){
                        $name       = $palayerStat[0];
                        $nickName   = $palayerStat[1];
                        $number     = $palayerStat[2];
                        $team       = $palayerStat[3];
                        $position   = $palayerStat[4];
                        $score      = (int) $palayerStat[5];
                        $rebound    = (int) $palayerStat[6];
                        $assist     = (int) $palayerStat[7];
                    }
                    elseif($sport == 'handball'){
                        $name       = $palayerStat[0];
                        $nickName   = $palayerStat[1];
                        $number     = $palayerStat[2];
                        $team       = $palayerStat[3];
                        $position   = $palayerStat[4];
                        $made       = (int) $palayerStat[5];
                        $received   = (int) $palayerStat[6];
                    }

                    $positionExsist = false;
                    $targetRates    = [];
                    
                    // check if the postions is correct
                    foreach ($setting[$sport]['positions'] as $positionRec) {
                        if($positionRec['name'] == $position){
                            $positionExsist     = true;
                            $targetRates        = $positionRec['rates'];
                        }
                    }
                    
                    // check if the postions is correct
                    if($positionExsist){
                        
                        // clc rate
                        $rate       = 0;
                        if($sport == 'basketball'){
                            $rate   = ($score * $targetRates['score']) + ($rebound * $targetRates['rebound']) + ($assist * $targetRates['assist']);
                        }
                        
                        elseif($sport == 'handball'){
                            $rate   = $targetRates['initial'] + ($made * $targetRates['made']) + ($received * $targetRates['received']);
                        }
                        
                        // add team to teams
                        if(! in_array($team, $teams)){
                            $teams[]    = $team;
                            $teams[$team]['players']    = [];
                            $teams[$team]['rate_sum']   = 0;
                            $teams[$team]['name']       = $team;
                        }

                        // add player to his team
                        $teams[$team]['players'][$nickName] = $rate;

                        // sum the rate
                        $teams[$team]['rate_sum'] += $rate;

                        // add palyer to players array
                        $players[$nickName] = $name;
                    }else{
                        echo "Incorretc Postion name {$position}";exit();
                        break;
                    }
                    
                }else{
                    echo "Incorrect File format in {$sport} file, Player number {$i}";exit();
                    break;
                }
            }

            // here
            // print_r($teams);
            $winerTeam  = '';
            $teamRate   = 0;
            $mvp        = 0;
            $payerName  = '';
            foreach ($teams as $teamArr) {
                if(!empty($teamArr['rate_sum']) && $teamArr['rate_sum'] > $teamRate){
                    $teamRate   = $teamArr['rate_sum'];
                    $winerTeam  = $teamArr['name'];
                }
            }

            if(!empty($teams[$winerTeam]['players'])){
                foreach ($teams[$winerTeam]['players'] as $key => $value) {
                    if($value > $mvp){
                        $mvp        = $value;
                        $payerName  = $key;
                    }
                }
            }
            $mvp    +=10;
            echo "Winer Team => {$winerTeam} \t\nMVP => {$mvp} \t\nPlayer Name => {$players[$payerName] }";
            exit();
        }else{
            echo "Incorrect Sport # {$sport}";exit();
        }

    }else{
        echo "Incorrect File format";exit();
    }