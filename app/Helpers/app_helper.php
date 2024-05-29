<?php 

function date_iso($date){
    return $date ? date('c', strtotime($date)) : null;
}

function model_date_iso($data){
    $fields = ['created_at', 'updated_at', 'deleted_at', 'expired_at', 'archor_date'];

    if($data['data'][0] ?? false){
        foreach($data['data'] as $i => $fin){
            foreach($fields as $fi){
                if(!isset($fin[$fi])) continue;
                $data['data'][$i][$fi] = date_iso($fin[$fi]);
            }
        }
    }

    if($data['data'] ?? false){
        foreach($fields as $fi){
            if(!isset($data['data'][$fi])) continue;
            $data['data'][$fi] = date_iso($data['data'][$fi]);
        }
    }
    return $data;
}

function getOneError($errors){
    if(!$errors) return null;
    $return = "";
    foreach($errors as $key => $err){
        if($return){
            $return .= " ";
        }
        $return .= $err;
    }
    return $return;
}

function validatePhone($dial_code, $phone){
    if(!(@$dial_code)) return null;
    $phone = trim(preg_replace("/[^0-9]/", "", $phone));

    // ex: +628xxx
    if($dial_code == substr($phone, 0, strlen($dial_code))){
        return $phone;
    }

    // ex: 628xxx
    if(is_numeric($phone) && $dial_code == substr('+'.$phone, 0, strlen($dial_code))){
        return '+'.$phone;
    }

    // ex: 08xxx
    if(is_numeric($phone) && '0' == substr($phone, 0, 1)){
        return $dial_code.substr($phone, 1);
    }

    // ex: 8xxxx
    if(is_numeric($phone)){
        return $dial_code.$phone;
    }

    // invalid
    return null;
}