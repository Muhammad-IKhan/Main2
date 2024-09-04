

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Online Admission (Private Students)</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../../css/bootstrap.min.css">
        <script src="../../scripts/jquery.min.js"></script>
        <script src="../../scripts/bootstrap.min.js"></script>
        

        <style>
            /* Remove the navbar's default margin-bottom and rounded borders */ 
            .navbar {
                margin-bottom: 0;
                border-radius: 0;
            }

            /* Add a gray background color and some padding to the footer */
            footer {
                background-color: #f2f2f2;
                padding: 25px;
            }

            .carousel-inner img {
                width: 100%; /* Set width to 100% */
                margin: auto;
                min-height:200px;
            }

            /* Hide the carousel text when the screen is less than 600 pixels wide */
            @media (max-width: 600px) {
                .carousel-caption {
                    display: none; 
                }
            }
        </style>
 <style>
 .phototab
{
width:90px;
border:solid 1px #2D26CB ;
padding:1px;
}
.preview
{
width:90px;
border:solid 0px #2D26CB ;
padding:1px;
}
#preview
{
color:#cc0000;
font-size:11px
}
.ToUpper{
text-transform:uppercase;
}
</style>
    </head>
    <body>

        <div class="container text-center" style="width:85%; border:1px solid cadetblue; border-radius:10px; margin-top:10px">      
            <header>
                <img class="img-responsive" src="../../images/title.jpg" style="margin:5px 0 ">
            </header>
            <hr style="margin:5px;mar">
            <div class="row" style="background-color: aliceblue; padding-top:15px; padding-bottom:0px; border-bottom:1px solid cadetblue;border-top:1px solid groove;">
            <div class="row">
             <div class="col-md-4" align="right" style="padding:5px;"><label>Please Select Required Option </label></div>
            <div class="col-md-4">
                                        <div class="form-group" align="left">
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="1" name="ApplicationType" id="ApplicationType" class="form-control select" style="width: 100%;" required onChange="LoadApplicationType(this.value);">
                                                    <option value="">-- Select --</option>
                                                    <option value="1">Reappear 9th/10th/Improvement (Old Students)</option>
                                                    <option value="2">Additional Subjects</option>
                                                    <option value="3">Otherboard (9th/10th)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                     <div class="col-md-4"><img src="../../images/select_option.gif" align="left">
</div>
                                    </div>
            </div>
            <br />
            <div class="row" style="background-color: aliceblue; padding-top:20px; padding-bottom:30px; border-bottom:1px solid cadetblue;border-top:1px solid cadetblue;display:none;" id="ApplicationOne">
                <div class="col-sm-2">
                    <label>Year </label>
                    <div>
                        <select id="Year" class="form-control" name="Year">
                            <option value="2024" selected>2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                            <option value="2021">2021</option>
                            <option value="2020">2020</option>
                            <option value="2019">2019</option>
                            <option value="2018">2018</option>
                            <option value="2017">2017</option>
                            <option value="2016">2016</option>
                            <option value="2015">2015</option>
                            <option value="2014">2014</option>
                            <option value="2013">2013</option>
                            <option value="2012">2012</option>
                            <option value="2011">2011</option>
                            <option value="2010">2010</option>
                            <option value="2009">2009</option>
                            <option value="2008">2008</option>
                            <option value="2007">2007</option>
                            <option value="2006">2006</option>
                            <option value="2005">2005</option>
                            <option value="2004">2004</option>
                            <option value="2003">2003</option>
                            <option value="2002">2002</option>
                           
                            
                        </select>
                    </div>
                </div>
                <div class="col-sm-2"> 
                    <label>Session</label>
                    <select id="Session" class="form-control" name="Session">
                      <option value="1">Annual</option>
                        <option value="2">Supply</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Roll No</label>
                    <input type="text" class="form-control" id="RollNo" name="RollNo" >
                </div>
                
                <div class="col-sm-2">
                  <label>Date of Birth </label>
                    <input type="text" class="form-control" id="DateOfBirthReappear" placeholder="DD-MM-YYYY" name="DateOfBirthReappear" >
                </div>
                
                <div class="col-sm-2">
                    <label>Mobile No</label>
                    <input type="text" class="form-control" id="ApplyMobileNo" name="ApplyMobileNo" >
                </div>
                <div class="col-sm-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary" onClick="SearchData();">Process Data</button>
                    </div>
                </div>
            </div>
 
 <!-- Private Student Fresh Entry -->
 <div class="row" style="background-color: aliceblue; padding-top:20px; padding-bottom:30px; border-bottom:1px solid cadetblue;border-top:1px solid cadetblue;display:none;" id="ApplicationTwo">
                <div class="col-sm-3">
                    <label>Name </label>
                    <div>
                    <input type="text" class="form-control" id="Name" name="Name" style="text-transform: uppercase;">

                    </div>
                </div>
                <div class="col-sm-3"> 
                    <label>Father Name</label>
                 <input type="text" class="form-control" id="FatherName" name="FatherName" style="text-transform: uppercase;">

                </div>
                <div class="col-sm-2">
                  <label>Date of Birth </label>
                    <input type="text" class="form-control" id="DateOfBirth" name="DateOfBirth" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-sm-2">
                    <label>Mobile No</label>
                    <input type="text" class="form-control" id="ApplyMobileNoTwo" name="ApplyMobileNoTwo" >
                </div>
                <div class="col-sm-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary" onClick="SaveData();">Process Data</button>
                    </div>
                </div>
            </div>
           
            
            <div class="row" id="SearchArea" style="margin:20px 0" align="left">
           <div class="row">
      <div class="col-md-12">
                                        <div class="form-group" align="right" >
            <img src="../../images/instructions_index.gif" align="right" width="100%">
                                            </div>
                                        </div>

</div>
      
            </div>
            
        </div> <br /><br />     
    </body>
</html>

<script>
function LoadApplicationType(){
document.getElementById("SearchArea").innerHTML = '';
	var ApplicationType  = document.getElementById("ApplicationType").value;
 	if(ApplicationType == 1){
	$("#ApplicationOne").show();
	$("#ApplicationTwo").hide();
	}else if(ApplicationType == 2){
	$("#ApplicationTwo").show();
	$("#ApplicationOne").hide();

	}else if(ApplicationType == 3){
	$("#ApplicationTwo").show();
	$("#ApplicationOne").hide();

	}
	else {
	$("#ApplicationOne").hide();
$("#ApplicationTwo").hide();
		}
}
</script>



<script>

    function SearchData() {
        var ajax = new XMLHttpRequest();
        var Year 			= document.getElementById("Year").value;
        var Session 		= document.getElementById("Session").value;
        var RollNo 			= document.getElementById("RollNo").value;
        var DateOfBirth		= document.getElementById("DateOfBirthReappear").value;
        var ApplyMobileNo 	= document.getElementById("ApplyMobileNo").value;

        ajax.onreadystatechange = function () {
            if (this.readyState != 4 && this.status != 200) {
                document.getElementById("SearchArea").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
            }
            if (ajax.readyState == 4 && this.status == 200) {
                document.getElementById("SearchArea").innerHTML = this.responseText;
                
                $(document).ready(function ($) {
                    $("#DateOfAdmission").mask("99-99-9999");
                    $("#DateOfBirth").mask("99-99-9999");
                    $("#FatherCNIC").mask("99999-9999999-9");
                    $("#FormB").mask("99999-9999999-9");
                    $("#MobileNo").mask("9999-9999999");
                })
//location.reload(1);
//	window.location.replace("./?Type="+Response+"");
            }
        }
        ajax.open("POST", "sql_load.php?mode=SearchData&module=Admission", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("Year=" + Year + "&Session=" + Session + "&RollNo=" + RollNo + "&DateOfBirth=" + DateOfBirth + "&ApplyMobileNo=" + ApplyMobileNo);
    }

</script>


<script>
    function SendVerificationCode() {
        var ajax = new XMLHttpRequest();
        var Year 			= document.getElementById("MaxYear").value;
        var ExamCode 		= document.getElementById("MaxExamCode").value;
        var RollNo 			= document.getElementById("MaxRollNo").value;
        var EnrollNo		= document.getElementById("EnrollNo").value;
        var ApplyMobileNo 	= document.getElementById("ApplyMobileNo").value;
        var TrackingID 		= document.getElementById("TrackingID").value;
        var EligibleFor		= document.getElementById("EligibleFor").value;

        ajax.onreadystatechange = function () {
            if (this.readyState != 4 && this.status != 200) {
                document.getElementById("SearchArea").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
            }
            if (ajax.readyState == 4 && this.status == 200) {
                document.getElementById("SearchArea").innerHTML = this.responseText;       
SearchData();
            }
        }
        ajax.open("POST", "sql.php?mode=SendVerificationCode&module=Admission", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("Year=" + Year + "&ExamCode=" + ExamCode + "&RollNo=" + RollNo + "&EnrollNo=" + EnrollNo+ "&ApplyMobileNo=" + ApplyMobileNo+ "&TrackingID=" + TrackingID+ "&EligibleFor=" + EligibleFor);
    }

</script>

<script>
function ConfirmVerificationCode(EnrollNo){
	var ajax = new XMLHttpRequest();
	var VerificationCode  = document.getElementById("VerificationCode").value;
    ajax.onreadystatechange = function() {
	 if (this.readyState !=4) {
document.getElementById("VerificationMsg").innerHTML= '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML= this.responseText;
var Response = document.getElementById("Response").value;
	if(Response == 1){
SearchData();
	}
	}
  };  
  ajax.open("POST", "sql.php?mode=ConfirmVerificationCode&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo+"&VerificationCode="+VerificationCode);
}
</script>


<script>
function VerifyCode(EnrollNo){
	var ajax = new XMLHttpRequest();
	var VerificationCode  = document.getElementById("VerificationCode").value;
    ajax.onreadystatechange = function() {
	 if (this.State !=4) {
document.getElementById("VerificationMsg").innerHTML= '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML= this.responseText;
var Response = document.getElementById("Response").value;
	if(Response == 1){
SearchData();
	}
	}
  };  
  ajax.open("POST", "sql.php?mode=VerifyVerificationCode&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo+"&VerificationCode="+VerificationCode);
}
</script>

<script>
function ResendVerCode(EnrollNo){
	var ajax = new XMLHttpRequest();
  	ajax.onreadystatechange = function() {
	 if (this.readyState !=4) {
      document.getElementById("VerificationMsg").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML = this.responseText; 
	}
  };
  
  ajax.open("POST", "sql.php?mode=ResendVerificationCode&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo);
}
</script>

<script>
function LoadChangeMobileNo(EnrollNo){
	var ajax = new XMLHttpRequest();
  	ajax.onreadystatechange = function() {
	 if (this.readyState !=4) {
document.getElementById("SearchArea").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("SearchArea").innerHTML = this.responseText; 
$(document).ready(function ($) {
		$("#ChangeMobileNo").mask("9999-9999999");
                })
	}
  };
  ajax.open("POST", "sql_load.php?mode=LoadChangeMobileNo&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo);
}
</script> 
<script>
function ConfirmChangeMobileNo(EnrollNo){
	var ajax = new XMLHttpRequest();
	var ChangeMobileNo = document.getElementById("ChangeMobileNo").value;
	var AppType = document.getElementById("ApplicationType").value;	
  	ajax.onreadystatechange = function() {
	 if (this.readyState !=4) {
		 document.getElementById("VerificationMsg").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML = this.responseText; 
var Response = document.getElementById("Response").value;
if(Response == 1){

if(AppType ==1){	
SearchData();
}else if(AppType ==2){
var GetMobileNo = document.getElementById("GetMobileNo").value;
document.getElementById("ApplyMobileNoTwo").value = GetMobileNo;	
	
SaveData();	
	}
	}

	}
  };
  
  ajax.open("POST", "sql.php?mode=ConfirmChangeMobileNo&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo+"&ChangeMobileNo="+ChangeMobileNo+"&AppType="+AppType);
}
</script>


<script>
function getparent(strvalue) {
        if (strvalue.charAt(0) == '0') {
            value = parseInt(strvalue) - 1
            value.toString()
            strvalue = '0' + value
        } else {
            value = parseInt(strvalue) - 1
            strvalue = value
            strvalue = strvalue.toString()
        }
        return strvalue;
    }

    function getchild(strvalue) {
        if (strvalue.charAt(0) == '0') {
            value = parseInt(strvalue) + 1
            value.toString()
            strvalue = '0' + value
        } else {
            value = parseInt(strvalue) + 1
            strvalue = value
            strvalue = strvalue.toString()
        }
        return strvalue;
    }
function SaveAdmissionFormNinth(){
	var ajax = new XMLHttpRequest();
	var arrSubjects = []; 
	var EnrollNo		= document.getElementById("EnrollNo").value;	
	var MaxYear			= document.getElementById("MaxYear").value;	
	var MaxExamCode		= document.getElementById("MaxExamCode").value;	
	var MaxRollNo		= document.getElementById("MaxRollNo").value;	
	var AppearCode		= document.getElementById("AppearCode").value;	
	var AppearFlag		= document.getElementById("EligibleFor").value;	

	var CountryCode		= document.getElementById("CountryCode").value;	
	var ProvinceCode	= document.getElementById("ProvinceCode").value;	
	var DistrictCode	= document.getElementById("DistrictCode").value;	

	var Religion		= document.getElementById("Religion").value;	
	var FatherCNIC		= document.getElementById("FatherCNIC").value;	
	var FormB			= document.getElementById("FormB").value;	

	var MobileNo		= document.getElementById("MobileNo").value;	
	var PhoneNo			= document.getElementById("PhoneNo").value;	
	
	var PostalAddress	= document.getElementById("PostalAddress").value;	
	var PermanentAddress= document.getElementById("PermanentAddress").value;	
	
	var ProposedCenter	= document.getElementById("ProposedCenter").value;	
	var GroupCode		= document.getElementById("GroupCode").value;	
	
if(AppearFlag == 1){
if(GroupCode > 0){
	var Subject1		= document.getElementById("Subject1").value;	
	var Subject2		= document.getElementById("Subject2").value;	
	var Subject3		= document.getElementById("Subject3").value;	
	var Subject4		= document.getElementById("Subject4").value;	
	var Subject5		= document.getElementById("Subject5").value;	
	var Subject6		= document.getElementById("Subject6").value;	
	var Subject7		= document.getElementById("Subject7").value;	
	var Subject8		= document.getElementById("Subject8").value;	
}
}
   if(document.getElementById("CountryCode").value.length != 0 ){
   if(document.getElementById("ProvinceCode").value.length != 0 ){
   if(document.getElementById("DistrictCode").value.length != 0 ){
   if(document.getElementById("Religion").value.length != 0 ){
   if(document.getElementById("FatherCNIC").value.length != 0 ){
   if(document.getElementById("MobileNo").value.length != 0 ){
   if(document.getElementById("PostalAddress").value.length != 0 ){
   if(document.getElementById("PermanentAddress").value.length != 0 ){
   if(document.getElementById("ProposedCenter").value.length != 0 ){
   if(document.getElementById("GroupCode").value.length != 0 ){
	
	ajax.onreadystatechange = function() {
    if (this.readyState != 4 && this.status != 200) {
document.getElementById("DisplayMsg").innerHTML = '<img src="../../images/loading_small.gif" width="32px" height="32px">';
    }
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("DisplayMsg").innerHTML = this.responseText;
var Response = document.getElementById("Response").value;
	if(Response == 1){
SearchData();
//		window.location.replace("./?Type=1");
				}
    
	}	
  };
  if(AppearFlag == 1){
  ajax.open("POST", "sql.php?mode=SaveAdmissionForm&module=SSCAdmissionPrivateNinth", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&Subject1="+Subject1+"&Subject2="+Subject2+"&Subject3="+Subject3+"&Subject4="+Subject4
  +"&Subject5="+Subject5+"&Subject6="+Subject6+"&Subject7="+Subject7+"&Subject8="+Subject8+"&EnrollNo="+EnrollNo
  +"&MaxYear="+MaxYear+"&MaxExamCode="+MaxExamCode+"&MaxRollNo="+MaxRollNo+"&GroupCode="+GroupCode+"&AppearCode="+AppearCode
  +"&AppearFlag="+AppearFlag);
  }else{
  if(AppearFlag == 2 || AppearFlag == 3 || AppearFlag == 4 || AppearFlag == 5 || AppearFlag == 6 || AppearFlag == 7){
  ajax.open("POST", "sql.php?mode=SaveAdmissionForm&module=SSCAdmissionPrivateNinth", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&EnrollNo="+EnrollNo+"&MaxYear="+MaxYear+"&MaxExamCode="+MaxExamCode
  +"&MaxRollNo="+MaxRollNo+"&GroupCode="+GroupCode+"&AppearCode="+AppearCode+"&AppearFlag="+AppearFlag);
	  }
else if(AppearFlag == 8){
var Papers = [];
$.each($('input[name="SubjectCode"]:checked'), function() {
  var value = $(this).val()

  Papers.push(value)

})	
  ajax.open("POST", "sql.php?mode=SaveAdmissionForm&module=SSCAdmissionPrivateNinth", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&EnrollNo="+EnrollNo+"&MaxYear="+MaxYear+"&MaxExamCode="+MaxExamCode
  +"&MaxRollNo="+MaxRollNo+"&GroupCode="+GroupCode+"&AppearCode="+AppearCode+"&AppearFlag="+AppearFlag+"&Papers="+Papers);
	  }	  
  }
}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Group Name </span>';
document.getElementById("GroupCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Proposed Center </span>';
document.getElementById("ProposedCenter").style="border-color:red";
}, 500);}



}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Permanent Address </span>';
document.getElementById("PermanentAddress").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Postal Address </span>';
document.getElementById("PostalAddress").style="border-color:red";
}, 500);}


}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Student/Father/Guardian Mobile # </span>';
document.getElementById("MobileNo").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Father/Guardian CNIC</span>';
document.getElementById("FatherCNIC").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Religion </span>';
document.getElementById("Religion").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select District </span>';
document.getElementById("DistrictCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Province</span>';
document.getElementById("ProvinceCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Nationality</span>';
document.getElementById("CountryCode").style="border-color:red";

}, 500); }
}

</script>




<!-- Private Fresh Students -->

<script>
    function SendVerificationCodeTwo() {
        var ajax = new XMLHttpRequest();
        var ApplicationType	= document.getElementById("ApplicationType").value;
        var Name 			= document.getElementById("Name").value;
        var FatherName 		= document.getElementById("FatherName").value;
        var DateOfBirth		= document.getElementById("DateOfBirth").value;
        var ApplyMobileNo	= document.getElementById("ApplyMobileNoTwo").value;
       
        ajax.onreadystatechange = function () {
            if (this.readyState != 4 && this.status != 200) {
                document.getElementById("SearchArea").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
            }
            if (ajax.readyState == 4 && this.status == 200) {
                document.getElementById("SearchArea").innerHTML = this.responseText;       
SaveData();
            }
        }
        ajax.open("POST", "sql.php?mode=SendVerificationCodeTwo&module=Admission", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("ApplicationType="+ApplicationType+"&Name="+Name+"&FatherName="+FatherName+"&DateOfBirth="+DateOfBirth
		+"&ApplyMobileNo="+ApplyMobileNo);
    }

</script>

<script>
function ConfirmVerificationCodeTwo(EnrollNo){
	var ajax = new XMLHttpRequest();
	var VerificationCode  = document.getElementById("VerificationCode").value;
    ajax.onreadystatechange = function() {
	 if (this.readyState !=4) {
document.getElementById("VerificationMsg").innerHTML= '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML= this.responseText;
var Response = document.getElementById("Response").value;
	if(Response == 1){
SaveData();
	}
	}
  };  
  ajax.open("POST", "sql.php?mode=ConfirmVerificationCodeTwo&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo+"&VerificationCode="+VerificationCode);
}
</script>


<script>
function VerifyCodeTwo(EnrollNo){
	var ajax = new XMLHttpRequest();
	var VerificationCode  = document.getElementById("VerificationCode").value;
    ajax.onreadystatechange = function() {
	 if (this.State !=4) {
document.getElementById("VerificationMsg").innerHTML= '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
    }  
      if (this.readyState == 4 && this.status == 200) {
document.getElementById("VerificationMsg").innerHTML= this.responseText;
var Response = document.getElementById("Response").value;
	if(Response == 1){
SaveData();
	}
	}
  };  
  ajax.open("POST", "sql.php?mode=VerifyVerificationCode&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("EnrollNo="+EnrollNo+"&VerificationCode="+VerificationCode);
}
</script>


<script>

    function SaveData() {
        var ajax = new XMLHttpRequest();
        var ApplicationType	= document.getElementById("ApplicationType").value;
        var Name 			= document.getElementById("Name").value;
        var FatherName 		= document.getElementById("FatherName").value;
        var DateOfBirth		= document.getElementById("DateOfBirth").value;
        var ApplyMobileNo 	= document.getElementById("ApplyMobileNoTwo").value;

        ajax.onreadystatechange = function () {
            if (this.readyState != 4 && this.status != 200) {
                document.getElementById("SearchArea").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
            }
            if (ajax.readyState == 4 && this.status == 200) {
                document.getElementById("SearchArea").innerHTML = this.responseText;
                
                $(document).ready(function ($) {
                    $("#StudentDateOfBirth").mask("99-99-9999");
                    $("#FatherCNIC").mask("99999-9999999-9");
                    $("#FormB").mask("99999-9999999-9");
                    $("#MobileNo").mask("9999-9999999");
                })
//location.reload(1);
//	window.location.replace("./?Type="+Response+"");
var Response = document.getElementById("Response").value;
if(Response == 1){
 var DBEligibleFor = document.getElementById("DBEligibleFor").value;
 if(DBEligibleFor == '20'){document.getElementById('ApplicationType').value = '2';}
 else if(DBEligibleFor == '30'){document.getElementById('ApplicationType').value = '3';}
 document.getElementById("Name").value = document.getElementById("DBName").value;
 document.getElementById("FatherName").value = document.getElementById("DBFatherName").value;
 document.getElementById("DateOfBirth").value = document.getElementById("DBDateOfBirth").value;
SaveData();
	}
	}
        }
        ajax.open("POST", "sql_load.php?mode=SaveData&module=Admission", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("ApplicationType="+ApplicationType+"&Name=" + Name + "&FatherName=" + FatherName + "&DateOfBirth=" + DateOfBirth + "&ApplyMobileNo=" + ApplyMobileNo);
    }

</script>

<script>
    function LoadPreviousForm(AdmissionClass) {
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState != 4 && this.status != 200) {
                document.getElementById("LoadPreviousForm").innerHTML = '<div align="center"><img src="../../images/loading_small.gif" width="42px" height="42px"></div>';
            }
            if (ajax.readyState == 4 && this.status == 200) {
                document.getElementById("LoadPreviousForm").innerHTML = this.responseText;
document.getElementById('GroupCode').value = ''; 
document.getElementById('Subjects').innerHTML = '';               
	}
        }
        ajax.open("POST", "../general_functions.php?mode=LoadPreviousForm&module=Admission", true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send("AdmissionClass="+AdmissionClass);
    }

</script>
<script>
function LoadSubjectsOtherboard(){
	var ajax = new XMLHttpRequest();
	var AdmissionClass	= document.getElementById("AdmissionClass").value;	
	var GroupCode		= document.getElementById("GroupCode").value;	
   if(document.getElementById("GroupCode").value.length != 0){
    ajax.onreadystatechange = function() {
    if (this.readyState != 4 && this.status != 200) {
	 document.getElementById("Subjects").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
    }
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("Subjects").innerHTML = this.responseText;
    }	
  };
  ajax.open("POST", "../general_functions.php?mode=LoadSubjectsOtherboard&module=Admission", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("AdmissionClass="+AdmissionClass+"&GroupCode="+GroupCode);
   }else{
	   //If Required Fields Empty
	    document.getElementById("Subjects").innerHTML = '<span style="color:red;">Please Select Group Name</span>';
 	   }
}

</script>
<script>

function SaveAdmissionFormNinthFresh(){
	var ajax = new XMLHttpRequest();
	var arrSubjects = []; 
	var EnrollNo		= document.getElementById("EnrollNo").value;	
	var AppearCode		= document.getElementById("AppearCode").value;	
	var AppearFlag		= document.getElementById("EligibleFor").value;	

	var Gender			= document.getElementById("Gender").value;	
	var StudentName		= document.getElementById("StudentName").value;	
	var FatherName		= document.getElementById("StudentFatherName").value;	
	var DateOfBirth		= document.getElementById("StudentDateOfBirth").value;	
	
	var CountryCode		= document.getElementById("CountryCode").value;	
	var ProvinceCode	= document.getElementById("ProvinceCode").value;	
	var DistrictCode	= document.getElementById("DistrictCode").value;	

	var Religion		= document.getElementById("Religion").value;	
	var FatherCNIC		= document.getElementById("FatherCNIC").value;	
	var FormB			= document.getElementById("FormB").value;	

	var MobileNo		= document.getElementById("MobileNo").value;	
	var PhoneNo			= document.getElementById("PhoneNo").value;	
	
	var PostalAddress	= document.getElementById("PostalAddress").value;	
	var PermanentAddress= document.getElementById("PermanentAddress").value;	

	var ProposedCenter	= document.getElementById("ProposedCenter").value;	

	var GroupCode		= document.getElementById("GroupCode").value;	
if(GroupCode > 0 && GroupCode != 3){
	var Subject1		= document.getElementById("Subject1").value;	
	var Subject2		= document.getElementById("Subject2").value;	
	var Subject3		= document.getElementById("Subject3").value;	
	var Subject4		= document.getElementById("Subject4").value;	
	var Subject5		= document.getElementById("Subject5").value;	
	var Subject6		= document.getElementById("Subject6").value;	
	var Subject7		= document.getElementById("Subject7").value;	
	var Subject8		= document.getElementById("Subject8").value;	
}
   if(document.getElementById("CountryCode").value.length != 0 ){
   if(document.getElementById("ProvinceCode").value.length != 0 ){
   if(document.getElementById("DistrictCode").value.length != 0 ){
   if(document.getElementById("StudentName").value.length != 0 ){
   if(document.getElementById("StudentFatherName").value.length != 0 ){
   if(document.getElementById("StudentDateOfBirth").value.length != 0 ){
   if(document.getElementById("Gender").value.length != 0 ){
   if(document.getElementById("Religion").value.length != 0 ){
   if(document.getElementById("FatherCNIC").value.length != 0 ){
   if(document.getElementById("MobileNo").value.length != 0 ){
   if(document.getElementById("PostalAddress").value.length != 0 ){
   if(document.getElementById("PermanentAddress").value.length != 0 ){
   if(document.getElementById("ProposedCenter").value.length != 0 ){
   if(document.getElementById("GroupCode").value.length != 0 ){
	
	ajax.onreadystatechange = function() {
    if (this.readyState != 4 && this.status != 200) {
document.getElementById("DisplayMsg").innerHTML = '<img src="../../images/loading_small.gif" width="32px" height="32px">';
    }
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("DisplayMsg").innerHTML = this.responseText;

var Response = document.getElementById("Response").value;
	if(Response == 1){
		
document.getElementById("Name").value = StudentName;		
document.getElementById("FatherName").value = FatherName;		
document.getElementById("DateOfBirth").value = DateOfBirth;		

SaveData();
//		window.location.replace("./?Type=1");
				}
    
	}	
  };
  ajax.open("POST", "sql.php?mode=SaveAdmissionFormTwo&module=SSCAdmissionPrivateNinthFresh", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
if(GroupCode == 2){
  ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB+"&StudentName="+StudentName+"&FatherName="+FatherName+"&DateOfBirth="+DateOfBirth
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&Subject1="+Subject1+"&Subject2="+Subject2+"&Subject3="+Subject3+"&Subject4="+Subject4
  +"&Subject5="+Subject5+"&Subject6="+Subject6+"&Subject7="+Subject7+"&Subject8="+Subject8+"&EnrollNo="+EnrollNo
  +"&GroupCode="+GroupCode+"&AppearCode="+AppearCode+"&Gender="+Gender
  +"&AppearFlag="+AppearFlag);
}else if (GroupCode == 3){
var	AppearCode = 3;
var	GroupCode = 2;
	$(document).ready(function () {
		$(".clsSubjectCodes").each(function(){
			arrSubjects.push($(this).text())
		})
		
		
		$(".clsSubjectCodesCkb:checked").each(function(){
			arrSubjects.push($(this).val())
		})
		
		console.log(arrSubjects)
		
ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB+"&StudentName="+StudentName+"&FatherName="+FatherName+"&DateOfBirth="+DateOfBirth
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&arrSubjects="+arrSubjects+"&EnrollNo="+EnrollNo+"&GroupCode="+GroupCode
  +"&AppearCode="+AppearCode+"&Gender="+Gender+"&AppearFlag="+AppearFlag);		
	})
	
	
	}
}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Group Name </span>';
document.getElementById("GroupCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Proposed Center </span>';
document.getElementById("ProposedCenter").style="border-color:red";
}, 500);}



}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Permanent Address </span>';
document.getElementById("PermanentAddress").style="border-color:red";
}, 500);}


}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Postal Address </span>';
document.getElementById("PostalAddress").style="border-color:red";
}, 500);}


}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Student/Father/Guardian Mobile # </span>';
document.getElementById("MobileNo").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Father/Guardian CNIC</span>';
document.getElementById("FatherCNIC").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Religion </span>';
document.getElementById("Religion").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Gender </span>';
document.getElementById("Gender").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Date of Birth </span>';
document.getElementById("StudentDateOfBirth").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Father Name </span>';
document.getElementById("StudentFatherName").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Student Name </span>';
document.getElementById("StudentName").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select District </span>';
document.getElementById("DistrictCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Province</span>';
document.getElementById("ProvinceCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Nationality</span>';
document.getElementById("CountryCode").style="border-color:red";

}, 500); }
}

</script>




<script>

function SaveAdmissionFormOtherBoard(){
	var ajax = new XMLHttpRequest();
	var AdmissionClass	= document.getElementById("AdmissionClass").value;	
	var BoardIDOB		= document.getElementById("BoardIDOB").value;	
if(AdmissionClass == 10){
	var PreviousExamCodeOB = document.getElementById("PreviousExamCodeOB").value;
	var PreviousYearOB  = document.getElementById("PreviousYearOB").value;
	var PreviousRollNoOB= document.getElementById("PreviousRollNoOB").value;
	var SubjectsNinth	= document.getElementById("SubjectsNinth").value;
	}else{
	var PreviousExamCodeOB = null;
	var PreviousYearOB  = null;
	var PreviousRollNoOB= null;	
	var SubjectsNinth	= '';	
	}
	var EnrollNo		= document.getElementById("EnrollNo").value;	
	var AppearCode		= document.getElementById("AppearCode").value;	
	var AppearFlag		= document.getElementById("EligibleFor").value;	

	var Gender			= document.getElementById("Gender").value;	
	var StudentName		= document.getElementById("StudentName").value;	
	var FatherName		= document.getElementById("StudentFatherName").value;	
	var DateOfBirth		= document.getElementById("StudentDateOfBirth").value;	
	
	var CountryCode		= document.getElementById("CountryCode").value;	
	var ProvinceCode	= document.getElementById("ProvinceCode").value;	
	var DistrictCode	= document.getElementById("DistrictCode").value;	

	var Religion		= document.getElementById("Religion").value;	
	var FatherCNIC		= document.getElementById("FatherCNIC").value;	
	var FormB			= document.getElementById("FormB").value;	

	var MobileNo		= document.getElementById("MobileNo").value;	
	var PhoneNo			= document.getElementById("PhoneNo").value;	
	
	var PostalAddress	= document.getElementById("PostalAddress").value;	
	var PermanentAddress= document.getElementById("PermanentAddress").value;	

	var ProposedCenter	= document.getElementById("ProposedCenter").value;	

	var GroupCode		= document.getElementById("GroupCode").value;	
if(GroupCode > 0){
	var Subject1		= document.getElementById("Subject1").value;	
	var Subject2		= document.getElementById("Subject2").value;	
	var Subject3		= document.getElementById("Subject3").value;	
	var Subject4		= document.getElementById("Subject4").value;	
	var Subject5		= document.getElementById("Subject5").value;	
	var Subject6		= document.getElementById("Subject6").value;	
	var Subject7		= document.getElementById("Subject7").value;	
	var Subject8		= document.getElementById("Subject8").value;	
}
   if(document.getElementById("AdmissionClass").value.length != 0 ){
   if(document.getElementById("BoardIDOB").value.length != 0 ){
   if(document.getElementById("CountryCode").value.length != 0 ){
   if(document.getElementById("ProvinceCode").value.length != 0 ){
   if(document.getElementById("DistrictCode").value.length != 0 ){
   if(document.getElementById("StudentName").value.length != 0 ){
   if(document.getElementById("StudentFatherName").value.length != 0 ){
   if(document.getElementById("StudentDateOfBirth").value.length != 0 ){
   if(document.getElementById("Gender").value.length != 0 ){
   if(document.getElementById("Religion").value.length != 0 ){
   if(document.getElementById("FatherCNIC").value.length != 0 ){
   if(document.getElementById("MobileNo").value.length != 0 ){
   if(document.getElementById("PostalAddress").value.length != 0 ){
   if(document.getElementById("PermanentAddress").value.length != 0 ){
   if(document.getElementById("ProposedCenter").value.length != 0 ){
   if(document.getElementById("GroupCode").value.length != 0 ){
	
	ajax.onreadystatechange = function() {
    if (this.readyState != 4 && this.status != 200) {
document.getElementById("DisplayMsg").innerHTML = '<img src="../../images/loading_small.gif" width="32px" height="32px">';
    }
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("DisplayMsg").innerHTML = this.responseText;

var Response = document.getElementById("Response").value;
	if(Response == 1){
		
document.getElementById("Name").value = StudentName;		
document.getElementById("FatherName").value = FatherName;		
document.getElementById("DateOfBirth").value = DateOfBirth;		

SaveData();
//		window.location.replace("./?Type=1");
				}
	}	
  };
  ajax.open("POST", "sql.php?mode=SaveAdmissionFormThree&module=SaveAdmissionFormOtherBoard", true);
  ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajax.send("CountryCode="+CountryCode+"&ProvinceCode="+ProvinceCode+"&DistrictCode="+DistrictCode+"&Religion="+Religion
  +"&FatherCNIC="+FatherCNIC+"&FormB="+FormB+"&StudentName="+StudentName+"&FatherName="+FatherName+"&DateOfBirth="+DateOfBirth
  +"&MobileNo="+MobileNo+"&PhoneNo="+PhoneNo+"&PostalAddress="+PostalAddress+"&PermanentAddress="+PermanentAddress
  +"&ProposedCenter="+ProposedCenter+"&Subject1="+Subject1+"&Subject2="+Subject2+"&Subject3="+Subject3+"&Subject4="+Subject4
  +"&Subject5="+Subject5+"&Subject6="+Subject6+"&Subject7="+Subject7+"&Subject8="+Subject8+"&EnrollNo="+EnrollNo
  +"&GroupCode="+GroupCode+"&AppearCode="+AppearCode+"&Gender="+Gender+"&AppearFlag="+AppearFlag
  +"&AdmissionClass="+AdmissionClass+"&BoardIDOB="+BoardIDOB+"&PreviousExamCodeOB="+PreviousExamCodeOB
  +"&PreviousYearOB="+PreviousYearOB+"&PreviousRollNoOB="+PreviousRollNoOB+"&SubjectsNinth="+SubjectsNinth);

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Group Name </span>';
document.getElementById("GroupCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Proposed Center </span>';
document.getElementById("ProposedCenter").style="border-color:red";
}, 500);}



}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Permanent Address </span>';
document.getElementById("PermanentAddress").style="border-color:red";
}, 500);}


}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Postal Address </span>';
document.getElementById("PostalAddress").style="border-color:red";
}, 500);}


}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Student/Father/Guardian Mobile # </span>';
document.getElementById("MobileNo").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Father/Guardian CNIC</span>';
document.getElementById("FatherCNIC").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Religion </span>';
document.getElementById("Religion").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Gender </span>';
document.getElementById("Gender").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Date of Birth </span>';
document.getElementById("StudentDateOfBirth").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Father Name </span>';
document.getElementById("StudentFatherName").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Type Student Name </span>';
document.getElementById("StudentName").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select District </span>';
document.getElementById("DistrictCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Province</span>';
document.getElementById("ProvinceCode").style="border-color:red";
}, 500);}

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Nationality</span>';
document.getElementById("CountryCode").style="border-color:red";

}, 500); }

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Board Name</span>';
document.getElementById("BoardIDOB").style="border-color:red";

}, 500); }

}else{document.getElementById("DisplayMsg").innerHTML = '<span align="Center"><img src="../../images/loading_small.gif" width="32px" height="32px"></span>';
setTimeout(function(){document.getElementById("DisplayMsg").innerHTML = '<span style="color:red;">Please Select Admission Class</span>';
document.getElementById("AdmissionClass").style="border-color:red";

}, 500); }


}

</script>



<!-- Private Fresh Students Ends -->





<script src="../../scripts/general_scripts.js"></script>


<script src="../../scripts/jQuery-1.7-custom.js"></script>
<script language="javascript" src="../../scripts/jquery.maskedinput.js"></script>
<script src="../../scripts/jquery-form.js"></script>
<script>
jQuery(function($){
 	$("#ApplyMobileNo").mask("9999-9999999");
 	$("#ApplyMobileNoTwo").mask("9999-9999999");
	$("#DateOfBirth").mask("99-99-9999");
	$("#DateOfBirthReappear").mask("99-99-9999");
});

$(document).ready(function() { 
		
            $('#photoimg').live('change', function()			{ 
			           $("#preview").html('');
			    $("#preview").html('<img src="../../images/loading_small.gif" width="30px" alt="Uploading...."/>');
			$("#imageform").ajaxForm({
						target: '#preview'
		}).submit();
		
			});
        });
</script>
<script>
function hamburger2(sender) { // sender here has all details of dropdown
        if (sender.value === "54") {			
			document.getElementById('UrCode').innerHTML = '<strong>54</strong>';
			document.getElementById('LabelOne').innerHTML = '<strong>02</strong>';
LoadSubject();
        }else{
LoadSubject();
			document.getElementById('LabelOne').innerHTML = '<strong>02</strong>';

/*
			document.getElementById("Ur2").disabled = false;
			document.getElementById("Ur1").disabled = true;
            document.getElementById("Ur2").checked = true;
*/
			document.getElementById('UrCode').innerHTML = '<strong>06</strong>';
			}

			
    }
 function IslCode2(sender) {			
	 if (sender.value === "09") {
			document.getElementById('IslCode').innerHTML = '<strong>09</strong>';
        }else{
			document.getElementById('IslCode').innerHTML = '<strong>10</strong>';
			}		
 }
 function BioCode2(sender) {			
	 if (sender.value === "76") {
			document.getElementById('BioCode').innerHTML = '<strong>76</strong>';
        }else{
			document.getElementById('BioCode').innerHTML = '<strong>78</strong>';
			}		
 }
function SelectLabelOneFun2(sender) {			
			document.getElementById('SelectLabelOneCode').innerHTML = sender.value;  

}
function SelectLabelTwoFun2(sender) {			
		document.getElementById('SelectLabelTwoCode').innerHTML = sender.value;     
}
</script>

<script>
/// Add Failed Subjects

function AddFailedSubjects(SubCode) {
						var FailedSubject =  document.getElementById('SubjectsNinth').value;	
						var SubjectText = 'SubjectsNinth';	
var checkBox = document.getElementById(SubjectText+SubCode);

 var strArray = FailedSubject.split(',');
            for (var i = 0; i < strArray.length; i++) {
                if (strArray[i] === SubCode) {
                    strArray.splice(i, 1);
                }
            }
            document.getElementById("SubjectsNinth").value = strArray;
	if (checkBox.checked == true){
		if(FailedSubject.length == 0){
			  document.getElementById('SubjectsNinth').value = SubCode;
	}else{
			  document.getElementById('SubjectsNinth').value = FailedSubject+','+SubCode;
		}
	}		
 }


</script>