<?php

/*
     Welcome to my Unserialize Oriented Programming System:

         __    __________       _____       __      _      
        / /   / ____/ __ \     / ___/____ _/ /   __(_)___ _
       / /   /___ \/ / / /_____\__ \/ __ `/ / | / / / __ `/
      / /_______/ / /_/ /_____/__/ / /_/ / /| |/ / / /_/ / 
     /_____/_____/_____/     /____/\__,_/_/ |___/_/\__,_/  
                                                      

 */


set_time_limit(10);

$arr = Array();
$tmp_val = NULL;
$tmp_arr = Array();
$op = 'O:3:"NOP":0:{}';
$res = NULL;
$arg1;
$arg2;
$forloop_option = Array(0,0,0,0,0);
$is_write_append = false;
$is_end = false;

class NOP {
    function __destruct() {
    }
}

class InputAll {
    function __destruct() {
        global $arr;
        $data = file_get_contents("gen/testcase.txt");
        $arr = explode(" ", trim($data));
    }
}

// this is for target 2 (sort)
class InputAll2 {
    function __destruct() {
        global $arr;
        $data = file_get_contents("gen/testcase2.txt");
        $arr = explode(" ", trim($data));
    }
}

class InputOneNumber {
    public $i;
    function __destruct() {
        global $arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $data = file_get_contents("gen/testcase.txt");
        $shit = explode(" ", trim($data));
        array_push($arr, $shit[$_i]);
    }
}

// this is for target 2 (sort)
class InputOneNumber2 {
    public $i;
    function __destruct() {
        global $arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $data = file_get_contents("gen/testcase2.txt");
        $shit = explode(" ", trim($data));
        array_push($arr, $shit[$_i]);
    }
}

class WriteFile {

    public $i;
    public $len;

    function __destruct() {
        global $arr, $is_write_append;
        $token = $_SERVER['TEAM_TOKEN'];
        $_len = (gettype($this->len) === "object") ? intval($this->len->__toString()) : $this->len;
        $_init = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $tmp = "";
        for($_i = $_init; $_i < $_len; $_i++) {
            $tmp .= ($arr[$_i] . " ");
        }
        if($is_write_append)
            file_put_contents("output/{$token}.txt", $tmp . "\n", FILE_APPEND);
        else
            file_put_contents("output/{$token}.txt", $tmp . "\n");
    }
}

class WriteTmpVal {
    public $option;
    public $is_end;
    function __destruct() {
        global $tmp_val;
        if(!($this->option == 'before_end' && $is_end)) {
            $token = $_SERVER['TEAM_TOKEN'];
            if($is_write_append)
                file_put_contents("output/{$token}.txt", $tmp_val . " ", FILE_APPEND);
            else
                file_put_contents("output/{$token}.txt", $tmp_val . " ");
        }
    }
}

class WriteTmpFile {
    public $i;
    public $len;
    public $option;
    function __destruct() {
        global $tmp_arr;
        if(!($this->option == 'before_end' && $is_end)) {
            $token = $_SERVER['TEAM_TOKEN'];
            $_len = (gettype($this->len) === "object") ? intval($this->len->__toString()) : $this->len;
            $_init = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
            $tmp = "";
            for($_i = $_init; $_i < $_len; $_i++) {
                $tmp .= ($tmp_arr[$_i] . " ");
            }
            file_put_contents("output/{$token}.txt", $tmp . " ");
        }
    }
}

class setWriteAppendFlag {
    function __destruct() {
        global $is_write_append;
        $is_write_append = true;
    }
}

class resetWriteAppendFlag {
    function __destruct() {
        global $is_write_append;
        $is_write_append = false;
    }
}

class GetSize {
    function __toString() {
        global $arr;
        return strval(count($arr));
    }
}

class GetValue {
    public $i;
    function __toString() {
        global $arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        return strval($arr[$_i]);
    }
}

class GetValueByTmpArr {
    public $i;
    function __toString() {
        global $arr, $tmp_arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        return strval($arr[$tmp_arr[$_i]]);
    }
}

class SetValue {
    public $i;
    public $val;
    function __destruct() {
        global $arr, $tmp_val;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $arr[$_i] = $this->val;
    }
}

class SetValueByTmpArr {
    public $i;
    public $val;
    function __destruct() {
        global $arr, $tmp_arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $arr[$tmp_arr[$_i]] = $this->val;
    }
}

class ResetZero {
    public $i;
    function __destruct() {
        global $arr, $tmp_val;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $arr[$_i] = 0;
    }

}

class ResetTmpZero {
    public $i;
    function __destruct() {
        global $tmp_arr, $tmp_val;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $tmp_arr[$_i] = 0;
    }

}

class ResetNULL {
    public $i;
    function __destruct() {
        global $arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $arr[$_i] = NULL;
    }
}

class ResetTmpNULL {
    public $i;
    function __destruct() {
        global $tmp_arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $tmp_arr[$_i] = NULL;
    }
}

class AddValue {
    public $i;
    public $val;
    function __destruct() {
        global $arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $arr[$_i] += $this->val;
    }
}

class AddTmpValue {
    public $i;
    public $val;
    function __destruct() {
        global $tmp_arr;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $tmp_arr[$_i] += $this->val;
    }
}

class PushValue {
    public $val;
    function __destruct() {
        global $arr, $tmp_val;
        array_push($arr, $this->val);
    }
}

class PushTmpValue {
    public $val;
    function __destruct() {
        global $tmp_arr, $tmp_val;
        array_push($tmp_arr, $this->val);
    }
}

class PopValue {
    function __destruct() {
        global $arr, $tmp_val;
        array_pop($arr);
    }
}

class PopTmpValue {
    function __destruct() {
        global $tmp_arr, $tmp_val;
        array_pop($tmp_arr);
    }
}

class ShiftValue {
    function __destruct() {
        global $arr, $res;
        $_ = array_shift($arr);
        $res = $_;
    }
}

class ShiftTmpValue {
    function __destruct() {
        global $tmp_arr, $res;
        $_ = array_shift($tmp_arr);
        $res = $_;
    }
}

class ReverseArr {
    function __destruct() {
        global $arr, $tmp_val;
        for($i = 0; $i < count($arr) / 2; $i++) {
            $tmp = $arr[$i];
            $arr[$i] = $arr[count($arr) - 1 - $i];
            $arr[count($arr) - 1 - $i] = $tmp;
        }
    }
}

class Swap {
    public $i;
    public $j;
    function __destruct() {
        global $arr, $tmp_val;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $_j = (gettype($this->j) === "object") ? intval($this->j->__toString()) : $this->j;
        $tmp = $arr[$_i];
        $arr[$_i] = $arr[$_j];
        $arr[$_j] = $tmp;
    }
}

class Swap2 {
    public $i;
    public $j;
    function __destruct() {
        global $tmp_arr, $tmp_val;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $_j = (gettype($this->j) === "object") ? intval($this->j->__toString()) : $this->j;
        $tmp = $tmp_arr[$_i];
        $tmp_arr[$_i] = $tmp_arr[$_j];
        $tmp_arr[$_j] = $tmp;
    }
}

class SetArg1 {
    public $val;
    function __destruct() {
        global $arg1;
        $_val = (gettype($this->val) === "object") ? intval($this->val->__toString()) : $this->val;
        $arg1 = $_val;
    }
}

class SetArg2 {
    public $val;
    function __destruct() {
        global $arg2;
        $_val = (gettype($this->val) === "object") ? intval($this->val->__toString()) : $this->val;
        $arg2 = $_val;
    }
}

class SetRes {
    public $val;
    function __destruct() {
        global $res;
        $_val = (gettype($this->val) === "object") ? intval($this->val->__toString()) : $this->val;
        $res = $_val;
    }
}

class GetArg1 {
    function __toString() {
        global $arg1;
        return strval($arg1);
    }
}

class GetArg2 {
    function __toString() {
        global $arg2;
        return strval($arg2);
    }
}

class GetRes {
    function __toString() {
        global $res;
        return strval($res);
    }
}

class ResToArg1 {
    function __destruct() {
        global $arg1, $res;
        $arg1 = $res;
    }
}

class ResToArg2 {
    function __destruct() {
        global $arg2, $res;
        $arg2 = $res;
    }
}

class Equal {
    function __destruct() {
        global $arg1, $arg2, $res;
        $res = ($arg1 == $arg2);
    }
}

class Equal2 {
    public $arg1, $arg2;
    function __destruct() {
        global $res;
        $res = ($this->arg1 == $this->arg2);
    }
}

class Bigger {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($arg1) === "object") ? intval($arg1->__toString()) : $arg1;
        $_arg2 = (gettype($arg2) === "object") ? intval($arg2->__toString()) : $arg2;
        $res = ($_arg1 > $_arg2) ? True : False;
    }
}

class Bigger2 {
    public $arg1;
    public $arg2;
    function __destruct() {
        global  $res,$tmp_arr;
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        $res = ($_arg1 > $_arg2) ? True : False;
    }
}

class opNOT {
    function __destruct() {
        global $arg1, $res;
        $res = !$arg1;
    }
}

class opOR {
    function __destruct() {
        global $arg1, $arg2, $res;
        $res = ($arg1 | $arg2);
    }
}

class opAND {
    function __destruct() {
        global $arg1, $arg2, $res;
        $res = ($arg1 & $arg2);
    }
}

class opMOD {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($arg1) === "object") ? intval($arg1->__toString()) : $arg1;
        $_arg2 = (gettype($arg2) === "object") ? intval($arg2->__toString()) : $arg2;
        $res = ($_arg1 % $_arg2);
    }
}

class opMOD2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 % $_arg2);
    }
}

class opPLUS {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($arg1) === "object") ? intval($arg1->__toString()) : $arg1;
        $_arg2 = (gettype($arg2) === "object") ? intval($arg2->__toString()) : $arg2;
        $res = ($_arg1 + $_arg2);
    }
}

class opPLUS2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 + $_arg2);
    }
}

class opMINUS {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        $res = ($_arg1 - $_arg2);
    }
}

class opMINUS2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 - $_arg2);
    }
}

class opMUL {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        $res = ($_arg1 * $_arg2);
    }
}

class opMUL2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 * $_arg2);
    }
}

class opDIV {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        $res = ($_arg1 / $_arg2);
    }
}

class opDIV2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 / $_arg2);
    }
}

class opXOR {
    function __destruct() {
        global $arg1, $arg2, $res;
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        $res = ($_arg1 ^ $_arg2);
    }
}

class opXOR2 {
    public $arg1, $arg2;
    function __toString() {
        $_arg1 = (gettype($this->arg1) === "object") ? intval($this->arg1->__toString()) : $this->arg1;
        $_arg2 = (gettype($this->arg2) === "object") ? intval($this->arg2->__toString()) : $this->arg2;
        return strval($_arg1 ^ $_arg2);
    }
}

class IF_ELSE {
    public $op1;
    public $op2;
    public $clear_option; 
    public $last_iter;
    function __destruct() {
        global $res;
        if($res) {
            $this->op1->__destruct();
            if($this->last_iter && gettype($this->op2) === "object")
                $this->op2->i = $this->op2->idx = $this->claer_option;
        } else {
            $this->op2->__destruct();
            if($this->last_iter && gettype($this->op1) === "object")
                $this->op1->i = $this->op1->idx = $this->clear_option;
        }
    }
}

class IF_ELSE2 {
    public $op1;
    public $op2;
    function __destruct() {
        global $res;
        if($res) {
            unserialize($this->op1);
        } else {
            unserialize($this->op2);
        }
    }
}

class clearForOpt {
    public $i;
    function __destruct() {
        global $forloop_option;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $forloop_option[$_i] = 0;
    }
}

class setForBreak {
    public $i;
    function __destruct() {
        global $forloop_option;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $forloop_option[$_i] = 1;
    }
}

class setForContinue {
    public $i;
    function __destruct() {
        global $forloop_option;
        $_i = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $forloop_option[$_i] = 2;
    }
}

class ForLoop {
    public $i;
    public $len;
    public $tmp_idx;
    public $op;
    public $opt_idx;
    function __destruct() {
        global $arr, $tmp_val, $tmp_arr, $forloop_option;
        $_len = (gettype($this->len) === "object") ? intval($this->len->__toString()) : $this->len;
        $_init = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $_tmp_idx = (gettype($this->tmp_idx) === "object") ? intval($this->tmp_idx->__toString()) : $this->tmp_idx;
        for($_i = $_init; $_i < $_len; $_i++) {
            if($this->opt_idx) {
                $_opt_idx = (gettype($this->opt_idx) === "object") ? intval($this->opt_idx->__toString()) : $this->opt_idx;
                if($forloop_option[$_opt_idx] == 1) break;
                else if($forloop_option[$_opt_idx] == 2) continue;
            }
            $tmp_arr[$_tmp_idx] = $_i;
            if(gettype($this->op) === "array") {
                foreach($this->op as $key => &$value) {
                    if(gettype($value) === "object") {
                        if($_i == $_len - 1) $value->last_iter = true;
                        $value->__destruct();
                    }
                }
            } else { 
                if($_i == $_len - 1) $this->last_iter = true;
                if(gettype($this->op) === "object")
                    $this->op->__destruct();
            }
        }
    }
}

class ForLoop2 {
    public $i;
    public $len;
    public $tmp_idx;
    public $op;
    public $opt_idx;
    function __destruct() {
        global $arr, $tmp_val, $tmp_arr, $forloop_option;
        $_len = (gettype($this->len) === "object") ? intval($this->len->__toString()) : $this->len;
        $_init = (gettype($this->i) === "object") ? intval($this->i->__toString()) : $this->i;
        $_tmp_idx = (gettype($this->tmp_idx) === "object") ? intval($this->tmp_idx->__toString()) : $this->tmp_idx;
        for($_i = $_init; $_i < $_len; $_i++) {
            if($this->opt_idx) {
                $_opt_idx = (gettype($this->opt_idx) === "object") ? intval($this->opt_idx->__toString()) : $this->opt_idx;
                if($forloop_option[$_opt_idx] == 1) break;
                else if($forloop_option[$_opt_idx] == 2) continue;
            }
            $tmp_arr[$_tmp_idx] = $_i;
            if($_i == $_len - 1) $value->last_iter = true;
            unserialize($this->op);
        }
    }
}

class createTmpArr {
    public $len;
    public $init_val;
    function __destruct() {
        global $tmp_arr;
        $_len = (gettype($this->len) === "object") ? intval($this->len->__toString()) : $this->len;
        $tmp_arr = array_fill(0, $_len, $this->init_val);
    }
}

class cloneArr {
    function __destruct() {
        global $arr, $tmp_arr;
        $tmp_arr = $arr;
    }
}

class GetTmpVal {
    function __toString() {
        global $tmp_val;
        return strval($tmp_val);
    }
}

class GetTmpArrVal {
    public $idx;
    function __toString() {
        global $tmp_arr;
        $_idx = (gettype($this->idx) === "object") ? intval($this->idx->__toString()) : $this->idx;
        return strval($tmp_arr[$_idx]);
    }
}

class SetTmpVal {
    public $val;
    function __destruct() {
        global $tmp_val;
        $tmp_val = $this->val;
    }
}

class SetTmpArrVal {
    public $idx;
    public $val;
    public $option;
    function __destruct() {
        global $tmp_arr, $is_end;
        if(!($this->option == 'before_end' && $is_end)) {
            $_idx = (gettype($this->idx) === "object") ? intval($this->idx->__toString()) : $this->idx;
            $_val = (gettype($this->val) === "object") ? intval($this->val->__toString()) : $this->val;
            $tmp_arr[$_idx] = $_val;
        }
    }
}

class StringLength {
    public $str;
    function __toString() {
        return strval(strlen($this->str));
    }
}

class getRandom {
    function __toString() {
        return strval(rand());
    }
}

class Meow {
    function __destruct() {
        echo "meow";
    }
}

class ReverseShell {
    function __destruct() {
        eval(base64_decode("ZWNobyAiPGltZyBzcmM9J3JldmVyc2UuanBnJz4iOwo="));
    }
}

// only for 天選之人
class Lucky7 {
    function __destruct() {
        global $arr;
        shuffle($arr);
    }
}

class WakeupDestruct {
    public $op;
    function __wakeup() {
        if(gettype($this->op) === "array") {
            foreach($this->op as &$value) 
                if(gettype($value) === "object")
                    $value->__destruct();
        } else { 
            $this->op->__destruct();
        }
    }
}

class _deserial {
    public $s;
    function __destruct() {
        unserialize($this->s);
    }
}

class _serial {
    public $val;
    function __toString() {
        return serialize($this->val);
    }
}

class _exit {
    function __destruct() {
        exit();
    }
}


try {
    // $argv[1] is the path of your input content
    $obj = @unserialize(file_get_contents($argv[1]));
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

$is_end = true;
