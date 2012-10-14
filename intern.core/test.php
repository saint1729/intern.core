<HTML>
<HEAD>
<TITLE>JavaScript Form - Input Text Field</TITLE>
<SCRIPT Language="JavaScript">
function showAndClearField(frm){
  if (frm.firstName.value == "")
        alert("Hey! You didn't enter anything!");
  else
	{
        var D = frm.firstName.value;
        alert(D);
	}
}
</SCRIPT>
</HEAD>
<BODY>
<FORM NAME="test">
<P>Enter your First Name: <INPUT TYPE="TEXT" NAME="firstName" VALUE="default"><BR><BR>
<INPUT TYPE="Button" Value="Show and Clear Input" onClick="showAndClearField(test)">
</P>
</FORM>
</BODY>
</HTML>