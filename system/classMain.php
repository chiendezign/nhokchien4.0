<?php
/**
 * Created by PhpStorm.
 * User: Nhokchien
 * Date: 1/10/2016
 * Time: 10:27 PM
 */

class main{
    public $conn = NULL;
    public $result = NULL;
    public $host="localhost";
    public $user="root";
    public $pass="";
    public $database="magic_property";
    function __construct(){
        $this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->database);
        //mysqli_select_db($this->database, $this->conn);
        mysqli_query($this->conn,"set names 'utf8'");
    }
    function dump($array){
        header("Content-type:application/json");
        echo json_encode($array);

    }
    function showErrors(){
        ini_set('display_errors',1);
    }
    function error(){
        header("HTTP/1.0 404 Not Found");
        $_GET['p']='error';
        include($_SERVER['DOCUMENT_ROOT'].'/index.php');
        exit;
    }
    function selectRows($table, $where ='', $field = '', $order = '', $desc = '', $limit = '', $show_code=false){
        if($field == '') $field='*';
        $sql = "SELECT $field FROM $table";
        if($where != '') $sql.=" WHERE $where";
        if($order != '') $sql.=" ORDER BY $order";
        if($desc  != '') $sql.=" $desc";
        if($limit != '') $sql.=" LIMIT $limit";
        if($show_code)echo $sql;
        $kq=mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
        $ketqua = array();
        while($row_kq = mysqli_fetch_assoc($kq)){$ketqua[] = $row_kq ;}
        return $ketqua;
    }


    function runSql($sql){
        $kq=mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
        $ketqua = array();
        while($row_kq = mysqli_fetch_assoc($kq)){$ketqua[] = $row_kq ;}
        return $ketqua;
    }


    function getCell($table, $where ='', $field, $order = '', $desc = '', $show_code = false){
        $cell = $this->selectRow($table,$where,$field,$order,$desc,$show_code);
        $field = str_replace('`','',$field);
        if(!$cell) return false;
        else return $cell[$field];
    }

    function fullSearch($table, $where='', $field, $matchCol, $match , $limit = '', $show_code = false){
        $wh = "MATCH($matchCol) AGAINST('\\"."$match\\')".($where != ''?" AND $where":'');
        $cols = $field.', '."MATCH($matchCol) AGAINST('\\"."$match\\')".' AS relevance';
        return $this->selectRows($table,$wh,$cols,'relevance','',$limit,$show_code);
    }

    function selectRow($table, $where ='', $field = '', $order = '', $desc = '',$limit='', $show_code = false){
        if($field == '') $field='*';
        $sql = "SELECT $field FROM $table";
        if($where != '') $sql.=" WHERE $where";
        if($order != '') $sql.=" ORDER BY $order";
        if($desc != '') $sql.=" $desc";
        if($limit=='')$sql.=' LIMIT 0,1';
        else $sql.=' LIMIT '.$limit.',1';
        if($show_code)echo $sql;
        $kq=mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
        $row_kq  = mysqli_fetch_assoc($kq);
        return $row_kq;
    }

    function countRows($table,$where='',$limit='', $show_code=false){
        $sql = "SELECT count(*) AS tong FROM $table";
        if($where != '') $sql.=" WHERE $where";
        if($limit != '') $sql.=" LIMIT $limit";
        if($show_code)echo $sql;
        $kq=mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
        $row_kq  = mysqli_fetch_assoc($kq);
        return $row_kq['tong'];
    }
    function insert($table, $fields, $data, $show_code=false){
        $sql = "INSERT INTO $table ($fields) VALUES ($data)";
        if($show_code)echo $sql;
        mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
    }

    function update($table, $where='', $fields, $data, $show_code=false){
        $arr_fields = explode(',',$fields);
        $arr_data 	= explode('|',$data);
        $values 	= '';
        for($i=0; $i<count($arr_fields);$i++){
            $values .= $arr_fields[$i];
            $values .= '=';
            $values .= $arr_data[$i];
            if($i!= count($arr_fields)-1) $values .= ',';
        }
        $sql = "UPDATE $table SET $values";
        if($where != '') $sql.=" WHERE $where";
        if($show_code)echo $sql;
        mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
    }

    function delete($table, $where='', $show_code=false){
        $sql = "DELETE FROM $table WHERE $where";
        if($show_code)echo $sql;
        mysqli_query($this->conn,$sql) or die(mysqli_error($this->conn));
    }

    function upper($string, $encoding='UTF-8'){
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_strtolower(mb_substr($string, 1, $strlen - 1, $encoding),$encoding);
        return mb_strtoupper($firstChar, $encoding).$then;
    }

    function catchuoi($str, $len, $charset='UTF-8'){
        $str = html_entity_decode($str, ENT_QUOTES, $charset);
        if(mb_strlen($str, $charset)> $len){
            $arr 	= explode(' ', $str);
            $str 	= mb_substr($str, 0, $len, $charset);
            $arrRes = explode(' ', $str);
            $last 	= $arr[count($arrRes)-1];
            unset($arr);
            if(strcasecmp($arrRes[count($arrRes)-1], $last))unset($arrRes[count($arrRes)-1]);
            return implode(' ', $arrRes)."...";
        }
        return $str;
    }
    function catchuoi2($chuoi,$gioihan){
        if(mb_strlen($chuoi,'UTF-8')<=$gioihan)return $chuoi;
        $new_chuoi = mb_substr($chuoi,0,$gioihan,'UTF-8')."...";
        return $new_chuoi;
    }

    function testArr($arr){
        echo '<pre>';
        print_r($arr);
        echo '<pre>';
    }

    function getImage($content) {
        $first_img = '';
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
        $first_img = $matches [1] [0];
        if(empty($first_img)){ //Defines a default image
            $first_img = "/images/default.jpg";
        }
        return $first_img;
    }



    function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    function encrypt($value,$key){
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

    function decrypt($value,$key){
        if(!$value){return false;}
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    function myTime($time){
        $kq = '';
        $curTime = strtotime('now');
        //$time = strtotime("-75 days");
        $time = $curTime -  $time;
        if($time<30) $kq = 'Vài giây trước';
        else if ($time < 3600){
            $min = $time/60;
            $kq = round($min). ' phút trước';
        }
        else if($time < 3600 * 24){
            $mins = round($time/60);
            $hour = floor($mins/60);
            $min = $mins%60;
            if ($min!=0) $min = $min.' phút ';
            else $min ='';
            $kq = $hour . ' giờ ' .$min . 'trước';
        }
        else if($time < 3600 * 24 * 30){
            $date = round($time/3600/24);
            if($date == 1) $kq = 'Hôm qua';
            else if($date == 2) $kq = 'Hôm kia';
            else $kq = $date . ' ngày trước';
        }
        else if($time < 3600 * 24 * 30 * 12){
            $days = round($time/3600/24);
            $month = floor($days/30);
            $date = $days%30;
            if ($date!=0) $date = $date.' ngày ';
            else $date ='';
            $kq = $month . ' tháng ' .  $date .'trước';
            //$kq = $days;
        }
        else{
            $kq = date('h:m:s \n\gà\y d-m-Y',$time);
        }
        return $kq;
    }


    function convertTime($string){
        $arr_time = explode('/',$string);
        $time = $arr_time[2].'-'.$arr_time[1].'-'.$arr_time[0];
        return $time;
    }

    function isValid( $what, $data ) {
        switch( $what ) {
            case 'phone':
                $pattern = '#^0[0-9]{6,10}$#';
                break;
            case 'email':
                $pattern = "/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i";
                break;
            default:
                return false;
                break;
        }
        return preg_match($pattern, $data) ? true : false;
    }

    function varcharType($string){
        settype($string,'string');
        $string = strip_tags($string);
        $string = mysql_real_escape_string($string);
        return $string;
    }
    function textType($tring){
        settype($string,'string');
        $string = mysql_real_escape_string($string);
        return $string;
    }
    function intType($string){
        $string = strip_tags($string);
        $string = mysql_real_escape_string($string);
        settype($string,'int');
        return $string;
    }
    function boolType($string){
        $string = strip_tags($string);
        $string = mysql_real_escape_string($string);
        settype($string,'bool');
        return $string;
    }

    function oldAvartars($string){
        if($string=='') $string = 'male.jpg';
        if(strpos($string, 'images')!== false) $string=substr($string,strlen('images/avatar/'),strlen($string));
        return $string;
    }

    function linkTitle($id){
        $link = $this->selectRow('view_links',"id=$id",'`table`,item');
        $title = $this->getCell($link['table'],'id='.$link['item'],'title');
        return $title;
    }
    function linkTitle2($link){
        if($link=='') return 'Không rõ';
        else {
            $lk = $this->selectRow('view_links',"link='$link'",'`table`,item');
            $title = $this->getCell($lk['table'],'id='.$lk['item'],'title');
            return $title;
        }
    }

    function listPages($total,$size,$page,$link){
        $num = ceil((int)$total/(int)$size);
        $pages='';
        if($num>1){
            $pages = '<div class="pages_wrapper clearfix">';
            if($page == 1) {
                $pages .= '<span class="pages_num dis"><span class="fa icon-angle-double-left"></span></span>';
                $pages .= '<span class="pages_num dis"><span class="fa icon-angle-left"></span></span>';
            }
            else {
                $pages .= '<a class="pages_num" href="'.$link.'"><span class="fa icon-angle-double-left"></span></a>';
                $pages .= '<a class="pages_num" href="'.$link.'/'.($page-1).'"><span class="fa icon-angle-left"></span></a>';
            }

            for($i=max($page-5,1);$i<=min($page+5,$num);$i++){
                if($i==$page) $pages .= '<span class="pages_num">'.$i.'</span>';
                else if($i==1)$pages .= '<a class="pages_num" href="'.$link.'">'.$i.'</a>';
                else $pages .= '<a class="pages_num" href="'.$link.'/'.$i.'">'.$i.'</a>';
            }
            if($page == $num){
                $pages .= '<span class="pages_num dis"><span class="fa icon-angle-right"></span></span>';
                $pages .= '<span class="pages_num dis"><span class="fa icon-angle-double-right"></span></span>';
            }
            else {
                $pages .= '<a class="pages_num" href="'.$link.'/'.($page+1).'"><span class="fa icon-angle-right"></span></a>';
                $pages .= '<a class="pages_num" href="'.$link.'/'.$num.'"><span class="fa icon-angle-double-right"></span></a>';
            }
            $pages .= '</div>';
        }
        ;
        return $pages;
    }

    function addLog($typeID,$CusID,$linkID=false,$CusID2=false,$num1=false,$num2=false,$title=false){
        $field = "typeID, CusID";
        $data  = "$typeID, $CusID";
        if(!!$linkID) {
            $field .= ", linkID";
            $data  .= ", $linkID";
        }
        if(!!$CusID2) {
            $field .= ", CusID2";
            $data  .= ", $CusID2";
        }
        if(!!$title) {
            $field .= ", title";
            $data  .= ", $title";
        }
        if(!!$num1) {
            $field .= ", num1";
            $data  .= ", $num1";
        }
        if(!!$num2) {
            $field .= ", num2";
            $data  .= ", $num2";
        }

        $this->insert('v3_action',$field,$data);
    }

    function bb($text){
        $bbcode = array(
            "<", ">",
            "[ul][li]",
            "[/li][/ul]",
            "[/li][li]",
            "[li]",
            "[/li]",
            "[list]", "[*]", "[/list]",
            "[img]", "[/img]",
            "[b]", "[/b]",
            "[u]", "[/u]",
            "[i]", "[/i]",
            '[color=', "[/color]",
            "[size=\"", "[/size]",
            '[url=', "[/url]",
            "[code]", "[/code]",
            "[quote]", "[/quote]",
            '[',
            ']',
            '\r\n',
            '\n',
            '\r',
            '</p><br />'
        );
        $htmlcode = array("&lt;", "&gt;",
            "<ul class=\"icon-ul\"><li><i class=\"icon-li fa icon-caret-right\"></i> ",
            "</li></ul>",
            "</li><li><i class=\"icon-li fa icon-caret-right\"></i> ",
            "<ul class=\"icon-ul\"><li><i class=\"icon-li fa icon-caret-right\"></i> ",
            "</li></ul>",
            "<ul>", "<li>", "</ul>",
            "<img src='", "'\>",
            "<b>", "</b>",
            "<u>", "</u>",
            "<i>", "</i>",
            "<span style=\"color:#", "</span>",
            "<span style=\"font-size:", "</span>",
            '<a href="', "</a>",
            "<code>", "</code>",
            "<table width=100% bgcolor=lightgray><tr><td bgcolor=white>", "</td></tr></table>",
            '<',
            '">',
            '<br>',
            '<br>',
            '<br>',
            '</p>');
        $newtext = str_replace($bbcode, $htmlcode, $text);
        $newtext = nl2br($newtext);//second pass
        $newtext = html_entity_decode($newtext,ENT_QUOTES,"UTF-8");
        return $newtext;
    }

    function stopword($stopword, $string){
        $stopwords 	= explode(',', $stopword);
        $words 		= explode(' ', $string);
        $word 		= '';
        foreach ($words as $key => $value) {
            if(!in_array($value, $stopwords)) $word .= $value .' ';
        }
        return $word;
    }

    function loginLink($isMobile){
        echo $isMobile?'dang-nhap.html':'javascript:;';
    }

    function toFriendly($text){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            '-'=>'[!@#$%^&*()_+=\-[\]{}:;\'"\|\\<>,.?\/\~`\s]+',
            '' => '\A-|-\z'
        );
        foreach($unicode as $nonUnicode=>$uni) $text = preg_replace("/($uni)/i",$nonUnicode,$text);
        return $text;
    }

    function array2xml($array, $xml = false, $defaultTitle = 'item'){
        if($xml === false){
            $xml = new SimpleXMLElement('<root/>');
            // $xml->addAttribute('encoding', 'UTF-8');
        }
        foreach($array as $key => $value){
            if (is_numeric($key)) {
                $key = $defaultTitle;
            }
            if(is_array($value)){
                $this->array2xml($value, $xml->addChild($key));
            }else{
                $xml->addChild($key, $value);
            }
        }
        $outputXML = $xml->asXML();
        $outputXML=str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="UTF-8"?>', $outputXML);
        $outputXML = html_entity_decode($outputXML,ENT_QUOTES,"UTF-8");
        return $outputXML;
    }
}
