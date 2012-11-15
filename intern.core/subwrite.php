<?php
require('fpdf.php');

class PDF extends FPDF
{




var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P',$unit='mm',$format='A4')
{
    //Call parent constructor
    $this->FPDF($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
}

function WriteHTML($html)
{
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/', $v, $a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag,$attr)
{
    //Opening tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,true);
    elseif($tag=='A')
        $this->HREF=$attr['HREF'];
    elseif($tag=='BR')
        $this->Ln(5);
	elseif($tag=='SUB1')
	$this->subWrite(5,'1','',7,-3);
	elseif($tag=='SUB2')
	$this->subWrite(5,'2','',7,-3);
	elseif($tag=='SUBC')
	$this->subWrite(5,'c','',7,-3);
	elseif($tag=='SUBS')
	$this->subWrite(5,'s','',7,-3);
	elseif($tag=='SUBU')
	$this->subWrite(5,'u','',7,-3);
	elseif($tag=='SUBU1')
	$this->subWrite(5,'u1','',7,-3);
	elseif($tag=='SUBU2')
	$this->subWrite(5,'u2','',7,-3);
	elseif($tag=='SUBV')
	$this->subWrite(5,'v','',7,-3);
	elseif($tag=='SUBY')
	$this->subWrite(5,'y','',7,-3);
	elseif($tag=='SUBCK')
	$this->subWrite(5,'ck','',7,-3);
	elseif($tag=='SUBST')
	$this->subWrite(5,'st','',7,-3);
	elseif($tag=='SUBST2')
	$this->subWrite(5,'st2','',7,-3);
	elseif($tag=='SUBSC')
	$this->subWrite(5,'sc','',7,-3);
	elseif($tag=='SUBSU')
	$this->subWrite(5,'su','',7,-3);
	elseif($tag=='SUBSV')
	$this->subWrite(5,'sv','',7,-3);
	elseif($tag=='SUBUC')
	$this->subWrite(5,'uc','',7,-3);
	elseif($tag=='SUBUS')
	$this->subWrite(5,'us','',7,-3);
	elseif($tag=='SUBUT')
	$this->subWrite(5,'ut','',7,-3);
	elseif($tag=='SUBBAL')
	$this->subWrite(5,'bal.','',7,-3);
	elseif($tag=='SUBSTBAL')
	$this->subWrite(5,'st(bal.)','',7,-3);
	elseif($tag=='SUBDASH')
	$this->subWrite(5,"'",'',7,-3);
	elseif($tag=='SUBLEG')
	$this->subWrite(5,'leg','',7,-3);
	elseif($tag=='SUBMAX')
	$this->subWrite(5,'max.','',7,-3);
	elseif($tag=='SUBMIN')
	$this->subWrite(5,'min.','',7,-3);
	elseif($tag=='SUP1')
	$this->subWrite(5,'1','',7,7);
	elseif($tag=='SUP2')
	$this->subWrite(5,'2','',7,7);
	elseif($tag=='SUP-3')
	$this->subWrite(5,'-3','',7,7);
	elseif($tag=='SUP-6')
	$this->subWrite(5,'-6','',7,7);
	elseif($tag=='SUP.5')
	$this->subWrite(5,'1/2','',7,7);
	elseif($tag=='ls')
	$this->Write('<');
	elseif($tag=='gr')
	$this->Write('>');

}

function CloseTag($tag)
{
    //Closing tag
    if($tag=='B' or $tag=='I' or $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
}

function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}





function subWrite($h, $txt, $link='', $subFontSize=12, $subOffset=0)
{
	// resize font
	$subFontSizeold = $this->FontSizePt;
	$this->SetFontSize($subFontSize);
	
	// reposition y
	$subOffset = ((($subFontSize - $subFontSizeold) / $this->k) * 0.3) + ($subOffset / $this->k);
	$subX        = $this->x;
	$subY        = $this->y;
	$this->SetXY($subX, $subY - $subOffset);

	//Output text
	$this->Write($h, $txt, $link);

	// restore y position
	$subX        = $this->x;
	$subY        = $this->y;
	$this->SetXY($subX,  $subY + $subOffset);

	// restore font size
	$this->SetFontSize($subFontSizeold);
}
}
?>
