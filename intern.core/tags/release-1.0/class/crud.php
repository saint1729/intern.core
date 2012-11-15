<?php
/*
***************************************************************************
*   Copyright (C) 2007-2008 by Sixdegrees                                 *
*   cesar@sixdegrees.com.br                                               *
*   "Working with freedom"                                                *
*   http://www.sixdegrees.com.br                                          *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/

/* actual directory */
$pwd = dirname(__FILE__);

/* constants */
define("GET_TABLES_SQL", "desc %s");
define("UPDATE_SQL","update %s set %s where %s");
define("INSERT_SQL","insert into %s(%s) values(%s)");
define("REQUIRE_DBA","This class require <a href='http://cesars.users.phpclasses.org/dba'>DBA</a> class. Please download it and copy the folder 'dbal' in ${pwd}");
define("REQUIRE_FORMS","This class require <a href='http://cesars.users.phpclasses.org/formsgeneration'>Forms Generation Class</a> class. Please download it and copy the file 'forms.php' in ${pwd}");
define("CAPTION","caption");
define("TABLE", "table" );
define("ID","id");
define("TEXT", "text");
define("SELECT","select");
define("SHOWCOLUMN","showc");
define("EDIT_TEXT",0xFF454);
define("DELETE_TEXT",0xFF5454);
define("EDIT_LINK", 0xFF4544);
define("DELETE_LINK", 0xFF4545);
define("UPDATE_READ_ONLY","ronlyupdate");
define("INSERT_HIDE","inserthide");
define("ROW_ID","number_0x45dsa4654das654da64dsa654da");
define("INPUT_DOIT","doitdas4dsa454a6s54da65s4a6s5d4a6s5");
define("INPUT_SUBMIT","submitas2d1as32d1as2d1a3s21d3a2s13");
define("PASSWORD", "pasword2d1as32d1as2d1a3s21d3a2s13");

(include ("${pwd}/dbal/dbal.php")) or die(REQUIRE_DBA);
(include ("${pwd}/forms.php")) or die(REQUIRE_FORMS);


class crud {
    /**
     *  Table to read
     *
     *  @var string
     *  @access private
     */
    var $table;
    /**
     *  DataBase Abstraction object.
     *
     *  @var object
     *  @access private
     */     
    var $dba=false;
    
    /**
     *  Properties for the form.
     *
     *  @var array
     *  @access private
     */
    var $formParams;
    /**
     *  Table definition
     *
     *  @var array
     *  @access private
     */
    var $tableDefinition;
    /**
     *  Table results
     *  @var array
     *  @access private
     */
     var $result;
    
    function crud($str,$table,$info=array()) {
        $pwd = dirname(__FILE__);
        $this->table = $table;
        $this->dba = new dbal($str);
        $this->dba->setCacheDir( "${pwd}/cache/" );
        $this->tableDefinition = $info;
        $this->getTableInformation();
    }

    function doQuery($filter) {
        $res  =  &$this->result;
        $dba  =  &$this->dba;
        $info =  &$this->formParams;

        $definitions = & $this->tableDefinition;

        $f = $filter == '' ? '' : ' where '.$filter;

        $result = $dba->query("select * from ".$this->table." $f");
        if ($result) {
            while( $r = $result->getNext() ) {
                $res[] = $r;
            }
        } else {
            die("ERROR: ".$dba->getLastError());
        }
    }

    /**
     *  Creates a new row.
     *
     *  Show the form for create a new row.
     */
    function create() {
        $this->getTableInformation(true);
        return $this->generic_form_crud();
    }
    /**
     * Generic Form
     *
     *  @access private
     */
    function generic_form_crud($default=array(),$update=false,$update_condition="") {
        $form = new form_class;
        $form->NAME="crud_form";
        $form->METHOD="POST";
        $form->ACTION="";
        $form->ResubmitConfirmMessage="Are you sure you want to submit this form again?";
        $form->OptionsSeparator="<br />\n";

        foreach($this->formParams as $k => $input)   {
            if ( is_array($default) && count($default) > 0) {
                $input["VALUE"] = $default[$k];
            }
            echo $form->AddInput( $input );
        }

         $form->LoadInputValues($form->WasSubmitted(INPUT_DOIT));


        $verify=array();
        $doit=false;
        $error_message="";
        if($form->WasSubmitted(INPUT_DOIT))  {
             if(($error_message=$form->Validate($verify))!="")
             {
                $doit=false;
             }  else {
                $doit=true;
             }
        }

        if($doit)
        {
            $dba  = & $this->dba;
            $sql = "";
            $columns=array();
            foreach($this->formParams as $k=>$v) {
                if ( $k == ROW_ID || $k == INPUT_DOIT || $k == INPUT_SUBMIT) continue;
                $columns[] = $k;
            } 
            foreach ($_POST as $k => $v) {
              if ($this->tableDefinition[$k][PASSWORD] === true) {
                if ($v == "") {
                  echo "on ne change pas le mot de passe";
                  unset($_POST[$k]);
                }
                else {
                  echo "on change le mot de passe";
                  $_POST[$k] = md5($v);
                }
              }
            }

            if ( $update ) {
                $updatx  = array();
                foreach($columns as $v) {
                  if (isset($_POST[$v]))
                    $updatx[] = " $v = :$v";
                }
                $sql = sprintf(UPDATE_SQL,  $this->table,implode(" , ",$updatx),$update_condition);

            } else {
                $sql = sprintf(INSERT_SQL,  $this->table,implode(", ",$columns),":".implode(", :",$columns));
            }

              printr($_POST);
            $dba->compile($sql);
            $f = $dba->execute($_POST);
            if ( $f ) return true;
            else {
               $str = $dba->getLastError();

               if ( substr(strtolower($str),0,9) == "duplicate") {
                    $error_message="Duplicated data";
                    $s = strpos($str,"'")+1;
                    $e = strpos($str,"'",$s);
                    $err = trim( substr($str,$s,$e-$s) );
                    foreach($columns as $k => $v) {
                        if ( $err == $_POST[$v])  {
                            $verify[$v] = $v;
                        }
                    }
               }
            }
        }

        $this->autoTemplate($form,$error_message,$verify,$update);
        return false;
    }

    function update($arr) {

        if ( !is_array($arr) ) return false;
        $filter=Array();
        foreach($arr as $k=>$v) {
            $filter[] ="$k = \"".addslashes($v)."\"";
        }

        $this->doQuery(implode(" && ",$filter));

        return $this->generic_form_crud( $this->result[0], true, implode(" && ",$filter) );

    }

    function delete($arr) {
        if ( !is_array($arr) ) return false;
        $filter=Array();
        foreach($arr as $k=>$v) {
            $filter[] ="$k = \"".addslashes($v)."\"";
        }
        $filter = implode(" && ",$filter);


        $dba  =  &$this->dba;

        $definitions = & $this->tableDefinition;

        $f = $filter == '' ? '' : ' where '.$filter;

        $r = $dba->execute("delete from ".$this->table." $f limit 0,20");
        return $r != false;
    }

    /**
     *  READ
     *  @param string $filter SQL filter.
     */
    function read($filter='') {
        $this->doQuery($filter);

        $res  = & $this->result;
        $info = & $this->formParams;
        $definitions = & $this->tableDefinition;
        echo '<table summary="read table">';
        echo '<tr>';

        foreach($definitions as $key => $value) {
            if ( !is_array($value) || !isset($value[SHOWCOLUMN]) || !$value[SHOWCOLUMN]) continue;
            echo '<td>'.$value[CAPTION].'</td>';
        }
        echo '<td></td>';
        echo '</tr>';

        if ( is_array($res) ) {
           foreach($res as $k => $r) {
                echo "<tr>";
                foreach($definitions as $k => $v) {
                    if ( ! isset($v[SHOWCOLUMN]) || !$v[SHOWCOLUMN]) continue;
                    $text = isset($info[$k]["OPTIONS"][$r[$k]]) ? $info[$k]["OPTIONS"][$r[$k]] : $r[$k];
                    echo '<td>'.$text.'</td>';
                }
                $edit_url = $definitions[EDIT_LINK];
                $del_url  = $definitions[DELETE_LINK];
                foreach($r as $k => $v) {
                    $edit_url = str_replace('%'.$k, $v, $edit_url);
                    $del_url  = str_replace('%'.$k, $v, $del_url);
                }
                echo '<td><a href="'.$edit_url.'">'.$definitions[EDIT_TEXT].'</a> - <a href="'.$del_url.'">'.$definitions[DELETE_TEXT].'</a></td>';
                echo "</tr>";
            }
        }
        echo '</table>';
    }

    /**
     *  Generate a basic template for the form.
     *
     *  @param object $form Form object
     *  @access private
     */
    function autoTemplate($form,$error_message,$verify,$update) {
        $def = & $this->tableDefinition;
        $form->StartLayoutCapture();
        echo $error_message;
        echo '<table summary="Input fields table">';
        foreach($this->formParams as $inpName => $i) { 
            if ( $inpName == INPUT_DOIT) {
                $form->AddInputPart($inpName);
                continue;
            }
            echo '<tr>';
            echo "\n";
            echo '<th align="right">';
            echo $form->AddLabelPart(array("FOR"=>$inpName));
            echo ':</th>';
            echo "\n";
            echo '<td>';
            if ( $update && isset($def[$inpName][UPDATE_READ_ONLY]) && $def[$inpName][UPDATE_READ_ONLY] )
                $form->AddInputReadOnlyPart( $inpName );
            else
                $form->AddInputPart($inpName);
            echo '</td>';
            echo "\n";
            echo '<td>'.(IsSet($verify[$inpName]) ? "[Verify]" : "").'</td>';
            echo "\n";
            echo '</tr>';
            echo "\n";
        }
        echo '</table>';
        $form->EndLayoutCapture();
        $form->DisplayOutput();

    }
    
    /**
     *  Get information about the table
     *
     *  @access private.
     */
    function getTableInformation($insert=false) {
        $dba  = & $this->dba;

        $info = & $this->tableDefinition;
        unset($this->formParams);
        $formParams = & $this->formParams;

        $sql  = sprintf(GET_TABLES_SQL, $this->table);

        $record = $dba->query($sql);
        
        if ( !$record ) 
            return false;
        
        $Field     = & $record->bindColumn('Field');
        $Type      = & $record->bindColumn('Type');
        $Null      = & $record->bindColumn('Null');
        $Key       = & $record->bindColumn('Key');
        $Extra     = & $record->bindColumn('Extra');
        while ( $foo=$record->getNext() ) {
            $actInfo = & $info[$Field];
            /**
             *  If the field is autoincrement, we
             *  do not need to show it on the form.
             */
            if ( $insert && isset($actInfo[INSERT_HIDE]) && $actInfo[INSERT_HIDE]  || !isset($actInfo))
                continue;
                
            /* reseting form information */
            $form = array();
            
            $type = $this->parseColumnInfo($Type);
            if (isset($actInfo[TABLE]) && isset($actInfo[ID]) && isset($actInfo[TEXT])) {
                $form["TYPE"] = "select";
                $opt = & $form["OPTIONS"];
                $rec1 = $dba->query("select ".$actInfo[ID].",".$actInfo[TEXT]." from ".$actInfo[TABLE]);
                if ( !$rec1 ) continue;
                while ( $f = $rec1->getNext() ) {
                    if ( !isset($form["VALUE"]) ) $form["VALUE"]= $f[$actInfo[ID] ]; 
                    $opt[ $f[$actInfo[ID] ] ] = $f[ $actInfo[TEXT] ];
                }
            } else if ( isset($actInfo[SELECT]) ){
                $form["TYPE"] = "select";
                $form["OPTIONS"] = $actInfo[SELECT];
                $form["VALUE"] = array_shift( array_keys($actInfo[SELECT]) );
            } else if ( isset($actInfo[PASSWORD]) && $actInfo[PASSWORD] ){
                $form["TYPE"] = "password";
            } else 
                $form["TYPE"] = $type["html"];

            $form["NAME"] = trim($Field);
            $form["ID"] = $form["NAME"];
            $form["LABEL"] = isset($actInfo[CAPTION]) ? $actInfo[CAPTION] : $Field;
            $form["ValidationErrorMessage"] = "This information is not correct";

            if ( $Null == "NO" ) $form["ValidateAsNotEmpty"] = 1;
            if ( isSet($type["options"]) ) $form["OPTIONS"] = $type["options"];
            if ( isSet($type["maxlength"]) ) $form["MAXLENGTH"] = $type["maxlength"];
            if ( isSet($type["ValidateAsInteger"]) ) $form["ValidateAsInteger"] = $type["ValidateAsInteger"];
            if ( isSet($type["ValidateAsFloat"]) ) $form["ValidateAsFloat"] = $type["ValidateAsFloat"];
            if ( $type["html"]=="select" ) {
                $form["VALUE"] = strlen($foo["Default"])>0? $foo["Default"] : current($form["OPTIONS"]);

            }

            $formParams[$Field] = $form;
        }
        
        /*special field, for helps to know if the form were submited or not */
        $formParams[INPUT_DOIT]   = array("TYPE"=>"hidden","NAME"=>INPUT_DOIT,"VALUE"=>1);
        $formParams[INPUT_SUBMIT] = array("TYPE"=>"submit","LABEL"=>"Submit this form using","VALUE"=>"this button","ID"=>INPUT_SUBMIT ,"NAME"=>INPUT_SUBMIT);

    }
    
    /**
     *  Analyze the column type, parse it, and return
     *  to the class for prepare the form.
     *
     *  @access private
     *  @param string $type MySQL column description
     *  @return array Parsed information
     */
    function parseColumnInfo($type) {

        $type = trim($type);
        $pos = strpos($type,'(');
        if ( $pos !== false) {
            $extra = substr($type,$pos+1);
            $extra[strlen($extra)-1] = ' ';
            $type = substr($type,0,$pos);
        }

        $return = array();

        switch( strtolower($type) ) {
            case "int":
                $return["html"] = "text";
                $return["maxlength"] = $extra;
                $return["ValidateAsInteger"]=1;
                break;
            case "float":
                $t=explode(",",$extra);
                $return["html"] = "text";
                $return["maxlength"] = $t[0]+$t[1]+1;
                $return["ValidateAsFloat"] = 1;
                break;
            case "varchar":
                $return["html"] = "text";
                $return["maxlength"] = $extra;
                break;
            case "enum":
                $return["html"] = "select";
                $options = & $return["options"];
                $max = strlen($extra);
                $buf = "";
                for($i=0; $i < $max; $i++)
                    switch ( $extra[$i] ) {
                        case "'":
                        case '"':
                            $end = $extra[$i++];
                            
                            for(;$i < $max && $extra[$i] != $end; $i++) {
                                if ( $extra[$i] == "\\") {
                                    $buf .= $extra[$i+1];
                                    $i++;
                                    continue;
                                }
                                $buf .= $extra[$i];
                            }
                            break;
                        case ",":
                            $options[$buf] = $buf;
                            $buf = "";
                            break;
                    }
                    if ( $buf!='') $options[$buf] = $buf;
                break;
            default:
                $return["html"] = "text";
                break;
        }

        return $return;
    }
}
?>
