<?php 
require_once("../../_includefiles/DBConnection_Adm_Mysql.php");
require_once("../functions.php");
require_once("../fee_status.php");
$Random			= rand(1,99);
$IPAddress = $_SERVER['REMOTE_ADDR'];

$AdmSession 	 = get_config("AdmSession");
$AdmYear 	 	 = get_config("AdmYear");
$ExamStatus		 = 2;
$BoardID		 = 7;
$AckFlag		 = 0;
$ReceiptType	 = 15;
$Saved			 = 0;
$TotalStudents 	 = 1;
$Practicle		 = 0;
$FailedPaper 	 = 0;
$TotalFailedPapers = 0;
$TotalAdmissionFee= 0;


$SchoolCode		 =	99999;
$ClassID		 = 1;
if (isset($_GET['mode']) AND $_GET['mode']=='SendVerificationCode') /// Employees Entry Form 
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$TrackingID		=	$_POST['TrackingID'];
$Year			=	$_POST['Year'];
$ExamCode		=	$_POST['ExamCode'];
$RollNo			=	$_POST['RollNo'];
$EnrollNo		=	trim($_POST['EnrollNo']);
$ApplyMobileNo	=	$_POST['ApplyMobileNo'];
$EligibleFor	=	$_POST['EligibleFor'];
$Flag		 	= 	0;
$VerificationCode = rand(100001,999999);
$TrimZero = ltrim($ApplyMobileNo, '0');
$TrimDash = explode('-',$TrimZero);

$CombineMobile = '92'.$TrimDash[0].$TrimDash[1];	
$q1 = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) As Found 
FROM online_admission_requests 
WHERE ClassID = 1
AND EnrollNo = :enr
AND Session = :sess
AND Year = :year");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('sess',$AdmSession);
	$q1->bindParam('year',$AdmYear);

	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($FetchRequest->Found == 0){
	
	
		
	$q2 = $DbConn_Adm_Mysql->prepare("Insert Into online_admission_requests (TrackingID,EnrollNo,Year,ClassID,Session,MobileNo,VerificationCode,Flag,EligibleFor,IPAddress)
						  Values(:tid,:enr,:year,:class,:sess,:mobile,:vercode,:flag,:elg,:ip)");
	$q2->bindParam('tid',$TrackingID);
	$q2->bindParam('enr',$EnrollNo);
	$q2->bindParam('year',$AdmYear);
	$q2->bindParam('class',$ClassID);
	$q2->bindParam('sess',$AdmSession);
	$q2->bindParam('mobile',$CombineMobile);
	$q2->bindParam('vercode',$VerificationCode);
	$q2->bindParam('flag',$Flag);
	$q2->bindParam('elg',$EligibleFor);
	$q2->bindParam('ip',$IPAddress);
	$q2->execute();
	
	echo $AdmSession;
	exit;
//echo 'ok';
//print_r($q2->errorInfo());
$InsCheck = $q2->rowCount();
if($InsCheck > 0){
//Send SMS Code
	$MSG = 'Verification Code '.$VerificationCode.' for Enrollment No: '.$EnrollNo.' please submit this verification code for further process';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS		
		
}
		
	
	}
	}
}

if (isset($_GET['mode']) AND $_GET['mode']=='ConfirmVerificationCode') /// Employees Entry Form Copied
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$EnrollNo 				= $_POST['EnrollNo'];
$VerificationCode 		= $_POST['VerificationCode'];
//sleep(1);
if(preg_match('/^[0-9]{6}$/',$VerificationCode)){
$AdmSession = get_config("AdmSession");
$AdmYear 	= get_config("AdmYear");
	
	$q1 = $DbConn_Adm_Mysql->prepare("Select * , Count(*) As Found from online_admission_requests
					  Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND Flag = 0");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('sess',$AdmSession);

	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($FetchRequest->Found > 0){
		
	$SavedVerificationCode = $FetchRequest->VerificationCode;
	if($SavedVerificationCode == $VerificationCode){
		$update = $DbConn_Adm_Mysql->prepare("Update online_admission_requests 
									SET Flag = 1
									Where EnrollNo = ?
									AND Session = ?
									AND Year = ?
									AND Flag = 0
									AND ClassID = 1");
		$update->bindparam(1,$EnrollNo);
		$update->bindparam(2,$AdmSession);
		$update->bindparam(3,$AdmYear);
		$update->execute();
		if($update->rowCount() > 0){
			echo 'Verified Seccessfully';
	echo '<input type="hidden" name="Response" id="Response" value="1" />';
			}else{
		echo 'Please try again... ';		
				}
	}else{
		echo 'Verification Code is Invalid!';
		}
	
	

		}else{echo 'Required Data Not Found Please Refresh page and Try again.';}			
	
	
}else{
   echo '<span style="font-family:bold;color:red;">Verification Code Format is Invalid.</span>';			
	}


		}
}

if (isset($_GET['mode']) AND $_GET['mode']=='ResendVerificationCode') /// Resend Verification Code
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$EnrollNo 				= $_POST['EnrollNo'];

$q1 = $DbConn_Adm_Mysql->prepare("Select * from online_admission_requests
					  Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND Flag = 0");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('sess',$AdmSession);
	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($q1->rowCount() > 0){
$CombineMobile 		 = $FetchRequest->MobileNo;
$VerificationCode 	 = $FetchRequest->VerificationCode;

$SmsResendTimestamp = $FetchRequest->SmsResendTimestamp;
$CurrentTimestamp = date("Y-m-d H:i:s",time());

$Unix_SmsResendTimestamp = strtotime($SmsResendTimestamp) + 300;
$Unix_CurrentTimestamp	 = strtotime($CurrentTimestamp);

if($Unix_CurrentTimestamp > $Unix_SmsResendTimestamp){		
$DbConn_Adm_Mysql->beginTransaction();
$q2 = $DbConn_Adm_Mysql->prepare("Update online_admission_requests 
								  SET SmsResendTimestamp = ?
								  Where EnrollNo = ?
								  AND Year = ?
								  AND Session = ?
								  AND ClassID = 1
								  AND Flag = 0");	
$q2->bindParam(1,$CurrentTimestamp);
$q2->bindParam(2,$EnrollNo);
$q2->bindParam(3,$AdmYear);
$q2->bindParam(4,$AdmSession);
$q2->execute();
//echo 'ok';

$InsCheck = $q2->rowCount();
if($InsCheck > 0){
echo '<input type="hidden" name="Response" id="Response" value="1" />';
$DbConn_Adm_Mysql->commit();
echo '<span style="font-weight:bold;color:blue;">Short Code Message has been resent to your provided Mobile # '.$CombineMobile.'</span>';
//Send SMS Code
$MSG = 'Verification Code '.$VerificationCode.' for Enrollment No: '.$EnrollNo.' please submit this verification code for further process';
// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS	

}else{
$DbConn_Adm_Mysql->rollBack();	
	}
}else{
echo '<span style="font-weight:bold;color:red;">Please wait for '.date("i:s",$Unix_SmsResendTimestamp-$Unix_CurrentTimestamp).' (Minutes/seconds)</span>';	
	}

}else{echo 'No Data Found, Please refresh page and try again.';}			
	
	
}
}

if (isset($_GET['mode']) AND $_GET['mode']=='ConfirmChangeMobileNo') /// Confirm Change Mobile No.
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$EnrollNo 		= $_POST['EnrollNo'];
$MobileNo		= $_POST['ChangeMobileNo'];
$TrimZero = ltrim($MobileNo, '0');
$TrimDash = explode('-',$TrimZero);

$CombineMobile = '92'.@$TrimDash[0].@$TrimDash[1];

if(preg_match('/^\d{4}-\d{7}$/',$MobileNo)){		


$EnrollNo 				= $_POST['EnrollNo'];
$AppType 				= $_POST['AppType'];

$q1 = $DbConn_Adm_Mysql->prepare("Select * from online_admission_requests
					  Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND Flag = 0");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('sess',$AdmSession);
	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($q1->rowCount() > 0){
//$CombineMobile 		 = $FetchRequest->MobileNo;

if($AppType == 2){
$q_dup_mobile = $DbConn_Adm_Mysql->prepare("Select Count(*) As Found from online_admission_requests
					  Where Year = :year
					  AND ClassID = 1
					  AND EligibleFor = 20
					  AND Session = :sess
					  AND MobileNo = :mno");
	$q_dup_mobile->bindParam('year',$AdmYear);
	$q_dup_mobile->bindParam('sess',$AdmSession);
	$q_dup_mobile->bindParam('mno',$CombineMobile);
	$q_dup_mobile->execute();
	$FetchDupMobile = $q_dup_mobile->fetch(PDO::FETCH_OBJ);
	if($FetchDupMobile->Found > 0){
		echo 'This Mobile No is Already In Use.';
		exit();
	}else{
		
		}

}

$VerificationCode 	 = $FetchRequest->VerificationCode;

$SmsResendTimestamp = $FetchRequest->SmsResendTimestamp;
$CurrentTimestamp = date("Y-m-d H:i:s",time());

$Unix_SmsResendTimestamp = strtotime($SmsResendTimestamp) + 300;
$Unix_CurrentTimestamp	 = strtotime($CurrentTimestamp);

if($Unix_CurrentTimestamp > $Unix_SmsResendTimestamp){	

	
$DbConn_Adm_Mysql->beginTransaction();

$log = $DbConn_Adm_Mysql->prepare("INSERT INTO online_admission_requests_logs
(AutoID,TrackingID,EnrollNo,ClassID,Session,Year,MobileNo,VerificationCode,Flag,EligibleFor,IPAddress,Remarks,SmsResendTimestamp,TimeStamp,Ins_IPAddress) SELECT AutoID,TrackingID,EnrollNo,ClassID,Session,Year,MobileNo,VerificationCode,Flag,EligibleFor,IPAddress,Remarks,SmsResendTimestamp,TimeStamp,:ip From online_admission_requests
Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND Flag = 0");
	$log->bindParam('ip',$IPAddress);
	$log->bindParam('enr',$EnrollNo);
	$log->bindParam('year',$AdmYear);
	$log->bindParam('sess',$AdmSession);
	$log->execute();
if($log->rowCount() > 0){
$q2 = $DbConn_Adm_Mysql->prepare("Update online_admission_requests 
								  SET MobileNo = ?,
								  SmsResendTimestamp = ?
								  Where EnrollNo = ?
								  AND Year = ?
								  AND Session = ?
								  AND ClassID = 1
								  AND Flag = 0");	
$q2->bindParam(1,$CombineMobile);
$q2->bindParam(2,$CurrentTimestamp);
$q2->bindParam(3,$EnrollNo);
$q2->bindParam(4,$AdmYear);
$q2->bindParam(5,$AdmSession);
$q2->execute();
//echo 'ok';

$InsCheck = $q2->rowCount();
if($InsCheck > 0){
$DbConn_Adm_Mysql->commit();
echo '<span style="font-weight:bold;color:blue;">Mobile # has been changed & Short Code Message is resent to your provided Mobile # '.$CombineMobile.'</span>';
//Send SMS Code
echo '<input type="hidden" name="Response" id="Response" value="1" />';
echo '<input type="hidden" name="GetMobileNo" id="GetMobileNo" value="'.$MobileNo.'" />';

$MSG = 'Verification Code '.$VerificationCode.' for Enrollment No: '.$EnrollNo.' please submit this verification code for further process';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS	

}else{
$DbConn_Adm_Mysql->rollBack();	
	}
}
}else{
echo '<span style="font-weight:bold;color:red;">Please wait for '.date("i:s",$Unix_SmsResendTimestamp-$Unix_CurrentTimestamp).' (Minutes/seconds)</span>';	
	}

}else{echo 'No Data Found, Please refresh page and try again.';}			
	
	

	
	}else{
   echo '<div class="form-bottom" style="color:white;" ><span style="font-family:bold;color:red;">Mobile No Format is Invalid.</span></div>';		
	}

		}
}

if (isset($_GET['mode']) AND $_GET['mode']=='SaveAdmissionForm') /// Employees Entry Form
{
		if (isset($_GET['module']) AND $_GET['module']=='SSCAdmissionPrivateNinth') {
if(isset($_POST['CountryCode']) && $_POST['CountryCode'] > 0 && isset($_POST['ProvinceCode']) && $_POST['ProvinceCode'] > 0
&& isset($_POST['DistrictCode']) && $_POST['DistrictCode'] > 0 && isset($_POST['Religion']) && $_POST['Religion'] > 0
&& isset($_POST['FatherCNIC']) && $_POST['FatherCNIC'] > 0
&& isset($_POST['MobileNo']) && $_POST['MobileNo'] > 0 && isset($_POST['PostalAddress']) && !empty($_POST['PostalAddress'])
&& isset($_POST['PermanentAddress']) && !empty($_POST['PermanentAddress']))
{


$EnrollNo 		= trim($_POST['EnrollNo']);
$MaxYear 		=  $_POST['MaxYear'];
$MaxExamCode 	=  $_POST['MaxExamCode'];
$MaxRollNo 		=  $_POST['MaxRollNo'];
$UserID			= 0;
$AppearCode		= $_POST['AppearCode'];
$AppearFlag		= $_POST['AppearFlag'];

$TotalFailedPapers  = 0;
$FailedPaper		= 0;

if(isset($_POST['Papers'])){$Papers	= $_POST['Papers'];}

$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.Religion,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,
se.GroupCode,se.ExamStatus,se.SchoolCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se , student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();
//print_r($sql_cell_data->errorInfo());
$FetchData = $sql_cell_data->fetch(PDO::FETCH_OBJ);

$Gender		 	 = $FetchData->Gender;
$StudentName	 = strtoupper($FetchData->Name);
$FatherName		 = strtoupper($FetchData->FatherName);
$DateOfBirth	 = date("Y-m-d", strtotime($FetchData->DateOfBirth));

$CountryCode 	 = $_POST['CountryCode'];
$ProvinceCode 	 = $_POST['ProvinceCode'];
$DistrictCode 	 = $_POST['DistrictCode'];
$Religion	 	 = $_POST['Religion'];
$FatherCNIC		 = $_POST['FatherCNIC'];
$FormB			 = $_POST['FormB'];
$MobileNo		 = $_POST['MobileNo'];
$PhoneNo		 = $_POST['PhoneNo'];
$PostalAddress	 = strtoupper($_POST['PostalAddress']);
$PermanentAddress= strtoupper($_POST['PermanentAddress']);
$ProposedCenter	 = strtoupper($_POST['ProposedCenter']);
$GroupCode		 = $_POST['GroupCode'];

$GroupCodeFee 	 = 1;

$Flag			 = 0;
if(preg_match('/^[1-9][0-9]{0,2}$/',$CountryCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$ProvinceCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$DistrictCode)){
if(preg_match('/^[1-2]{1,1}$/',$Religion)){
if(preg_match('/^[1-3]{1,1}$/',$Gender)){

// Dob Validation Starts
if(validateDate2($DateOfBirth) == $DateOfBirth){
//DOB OK
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FatherCNIC)){
if(isset($_POST['FormB'])){
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FormB)){
}
if(preg_match('/^\d{4}-\d{7}$/',$MobileNo)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PostalAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PermanentAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PermanentAddress)){
if(preg_match('/^[1-2]{1,1}$/',$GroupCode)){

if($AppearFlag == 2 OR $AppearFlag == 3 OR $AppearFlag == 4 OR $AppearFlag == 5 OR $AppearFlag == 6){$Subject7 = 101;$Subject8=102;}
	
$sqlimg = $DbConn_Adm_Mysql->prepare("SELECT Count(*) As ImageFound 
			FROM student_images
 			WHERE ClassID = 1
			AND EnrollNo = '$EnrollNo'");
$sqlimg->execute();
$FetchImageCheck = $sqlimg->fetch(PDO::FETCH_OBJ);
if($FetchImageCheck->ImageFound > 0){ // == 1


$DbConn_Adm_Mysql->beginTransaction();
$sql_SICheck = $DbConn_Adm_Mysql->prepare("Select Count(*) As Found 
			 	from matric_student_info
			 	Where EnrollNo = :enr");
$sql_SICheck->bindParam('enr',$EnrollNo);	
$sql_SICheck->execute();
$FetchLinkID = $sql_SICheck->fetch(PDO::FETCH_OBJ);
if($FetchLinkID->Found == 0){
$sql_si = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_info (EnrollNo,Name,FatherName,Gender,DateOfBirth,FatherCNIC,FormB,MobileNo,PhoneNo,PostalAddress,PermanentAddress,Religion,DateOfAdmission,UserID,RegYear)
							Values (:enr,:name,:fname,:gender,:dob,:fnic,:formb,:mno,:pno,:padd,:pradd,:rel,:doa,:userid,:regyear)");
$sql_si->bindParam('enr',$EnrollNo);
$sql_si->bindParam('name',$StudentName);
$sql_si->bindParam('fname',$FatherName);
$sql_si->bindParam('gender',$Gender);
$sql_si->bindParam('dob',$DateOfBirth);
$sql_si->bindParam('fnic',$FatherCNIC);
$sql_si->bindParam('formb',$FormB);
$sql_si->bindParam('mno',$MobileNo);
$sql_si->bindParam('pno',$PhoneNo);
$sql_si->bindParam('padd',$PostalAddress);
$sql_si->bindParam('pradd',$PermanentAddress);
$sql_si->bindParam('rel',$Religion);
$sql_si->bindParam('doa',$DateOfAdmission);
$sql_si->bindParam('userid',$UserID);
$sql_si->bindParam('regyear',$AdmYear);
$sql_si->execute();
//print_r($sql_si->errorInfo());
if($sql_si->rowCount() > 0){

}else{
echo 'SI Insertion ERROR';	
//print_r($sql_si->errorInfo());
	}
}else{
$sql_si_updated = $DbConn_Adm_Mysql->prepare("Update matric_student_info 
										SET Name = ?,
										FatherName = ?,
										DateOfBirth = ?,
										Gender = ?,
										FatherCNIC = ?,
										FormB = ?,
										MobileNo = ?,
										PhoneNo = ?,
										PostalAddress = ?,
										PermanentAddress = ?,
										Religion = ?
										Where EnrollNo = ?
										");
$sql_si_updated->bindParam(1,$StudentName);
$sql_si_updated->bindParam(2,$FatherName);
$sql_si_updated->bindParam(3,$DateOfBirth);
$sql_si_updated->bindParam(4,$Gender);
$sql_si_updated->bindParam(5,$FatherCNIC);
$sql_si_updated->bindParam(6,$FormB);
$sql_si_updated->bindParam(7,$MobileNo);
$sql_si_updated->bindParam(8,$PhoneNo);
$sql_si_updated->bindParam(9,$PostalAddress);
$sql_si_updated->bindParam(10,$PermanentAddress);
$sql_si_updated->bindParam(11,$Religion);
$sql_si_updated->bindParam(12,$EnrollNo);

$sql_si_updated->execute();
}
if($FetchLinkID->Found > 0 OR $sql_si->rowCount() > 0 ){
		
$sql_link = $DbConn_Adm_Mysql->prepare("Select LINK_ID_ADM from matric_student_info
			 Where EnrollNo = :enr");
$sql_link->bindParam('enr',$EnrollNo);	
$sql_link->execute();
$FetchLinkID = $sql_link->fetch(PDO::FETCH_OBJ);

if($sql_link->rowCount() > 0){
	
$LinkIDADM = $FetchLinkID->LINK_ID_ADM;

if($AppearFlag == 1){
if ($AdmSession == 1) {$AdmExamCode = 1;} elseif($AdmSession == 2) {$AdmExamCode = 5;} 
$Subject1 		 = $_POST['Subject1'];
$Subject2 		 = $_POST['Subject2'];
$Subject3 		 = $_POST['Subject3'];
$Subject4 		 = $_POST['Subject4'];
$Subject5 		 = $_POST['Subject5'];
$Subject6 		 = $_POST['Subject6'];
$Subject7 		 = $_POST['Subject7'];
$Subject8 		 = $_POST['Subject8'];

// Practicle Subjects Fee Calculation;
for($k=1; $k<=8; $k++){ 
$Subject = 'Subject'.$k;

$SubjectCodePrac = $$Subject;
//$Practicle = $Practicle + get_total_practicle($SubjectCodePrac);
}

}elseif($AppearFlag == 2){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();



$SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(SELECT LPAD(ss.SubjectCode,2,0) 
FROM student_subjects ss
WHERE ss.SubjectCode = s.SubjectCode
AND ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
AND (ss.THEORY_PAPER_STATUS < 1 OR ss.THEORY_PAPER_STATUS = 2)) AS Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :ecode
AND s.RollNo = :rno
ORDER BY s.SubjectCode");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('ecode',$MaxExamCode);
$SubjQuery->bindParam('rno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$SubjectCodeTenthPrac = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT);
//$Practicle = $Practicle + get_total_practicle($SubjectCodeTenthPrac);

$TenthSubjectsArr[] =  $FetchSubjects->Tenth;
if(isset($FetchSubjects->Ninth)){$NinthSubjectsArr[] =  $FetchSubjects->Ninth;
$SubjectCodeNinthPrac = str_pad(($FetchSubjects->Ninth), 2, 0, STR_PAD_LEFT);

//$Practicle = $Practicle + get_total_practicle($SubjectCodeNinthPrac);

//$FailedPaper++;

}



}

}elseif($AppearFlag == 3){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();
$SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectName, LPAD((s.SubjectCode),2,0) AS SubjectCode 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)
ORDER BY s.SubjectCode");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$SubjectCodeTenthPrac = str_pad(($FetchSubjects->SubjectCode), 2, 0, STR_PAD_LEFT);
//$Practicle = $Practicle + get_total_practicle($SubjectCodeTenthPrac);

$TenthSubjectsArr[] =  $FetchSubjects->SubjectCode;
//$FailedPaper = $FailedPaper + get_total_practicle_ninth($FetchSubjects->SUBJECT_CODE);
}

}elseif($AppearFlag == 4 OR $AppearFlag == 7){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();
$SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, s.SubjectCode as Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$SubjectCodeTenthPrac = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT);
//$Practicle = $Practicle + get_total_practicle($SubjectCodeTenthPrac);

$TenthSubjectsArr[] =  $FetchSubjects->Tenth;
if(isset($FetchSubjects->Ninth)){$NinthSubjectsArr[] =  $FetchSubjects->Ninth;
//$SubjectCodeNinthPrac = str_pad(($FetchSubjects->Ninth), 2, 0, STR_PAD_LEFT);

//$Practicle = $Practicle + get_total_practicle($SubjectCodeNinthPrac);

//$FailedPaper++;


}
}

}elseif($AppearFlag == 5){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();
$SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, s.SubjectCode as Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
AND ss.SubjectCode = (s.SubjectCode-1)
AND (ss.THEORY_PAPER_STATUS < 1 OR ss.THEORY_PAPER_STATUS = 2)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$SubjectCodeTenthPrac = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT);
//$Practicle = $Practicle + get_total_practicle($SubjectCodeTenthPrac);

$TenthSubjectsArr[] =  $FetchSubjects->Tenth;
if(isset($FetchSubjects->Ninth)){$NinthSubjectsArr[] =  $FetchSubjects->Ninth;
//$SubjectCodeNinthPrac = str_pad(($FetchSubjects->Ninth), 2, 0, STR_PAD_LEFT);

//$Practicle = $Practicle + get_total_practicle($SubjectCodeNinthPrac);

//$FailedPaper++;


}
}
}elseif($AppearFlag == 6){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 	
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();
$SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT s.SubjectCode , sn.SubjectName 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)
AND MOD(s.SubjectCode,2) <> 0

UNION ALL

SELECT s.SubjectCode , sn.SubjectName 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)
AND MOD(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$TenthSubjectsArr[] =  $FetchSubjects->SubjectCode;


}

	
	
}elseif($AppearFlag == 8){	
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
if(isset($_POST['Papers']) AND !empty($_POST['Papers'])){	
			
// Get Subjects Code 10 + 9th
$TenthSubjectsArr = array();
$NinthSubjectsArr = array();
$SubjQuery = $DbConn_Adm_Mysql->prepare("
SELECT sn.SubjectName, LPAD((s.SubjectCode),2,0) as SubjectCode 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND s.SubjectCode IN ($Papers)");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();

while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 

$TenthSubjectsArr[] =  $FetchSubjects->SubjectCode;
}
		}else{echo '<span style="color:red;font-weight:bold">Please Select Subject(s) To be Improved</span>';exit();	}



}

if($AppearCode == 2 OR $AppearFlag == 7){$Subject7 = 101;$Subject8=102;}
if(isset($Subject7) AND $Subject7 > 0 AND isset($Subject8) AND $Subject8 > 0){
if($Subject7 != $Subject8){

$TotalPracticleFee 	= $Practicle 	* 50;
$TotalFailedPapers	= $FailedPaper  * 100;
$GroupCodeFee		= 1;
if($AppearCode == 2 OR $AppearCode == 3){ $GetAppearCodeFee = 2; }else{$GetAppearCodeFee = 1;}
$TotalAdmissionFee  = get_admission_fee($AdmYear,$AdmSession,$AdmExamCode,$GroupCodeFee,$GetAppearCodeFee,$FeeStatus);
$CombineTotalFee 	= $TotalAdmissionFee;



//echo $AdmYear.'-'.$AdmSession.'-'.$AdmExamCode.'-'.$GroupCodeFee.'-'.$GetAppearCode.'-'.$FeeStatus;
//echo $CombineTotalFee;exit();
 
if($CombineTotalFee > 700){
$check_adm_status	=	check_adm_status($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($check_adm_status == 0){
$GetVerCode		 =	get_request_verification_code($AdmSession,$AdmYear,$EnrollNo);
$CombineMobile	 =	get_request_mobile_no($AdmSession,$AdmYear,$EnrollNo);
//$Rand			 = rand(100,999);
//$TrackingID	 = $Rand.$MaxYear.$MaxExamCode.$MaxRollNo;


$TrackingID		 =  get_request_tracking_id($AdmSession,$AdmYear,$EnrollNo);
//echo $ReceiptType.'-'.$Saved.'-'.$AdmYear.'-'.$SchoolCode.'-'.$TotalStudents.'-'.$CombineTotalFee.'-'.$TrackingID;exit;

$sql2 = $DbConn_Online_Mysql->prepare("INSERT INTO bisep_student_receipts (ReceiptType,Saved,RegYear,InstituteCode,TotalStudents,TotalFee,TrackingID)
						 Values (:rectype,:saved,:regyear,:icode,:tstd,:tfee,:tid)");
$sql2->bindParam('rectype',$ReceiptType);
$sql2->bindParam('saved',$Saved);
$sql2->bindParam('regyear',$AdmYear);
$sql2->bindParam('icode',$SchoolCode);
$sql2->bindParam('tstd',$TotalStudents);
$sql2->bindParam('tfee',$CombineTotalFee);
$sql2->bindParam('tid',$TrackingID);
$sql2->execute();
//print_r($sql2->errorInfo());
if($sql2->rowCount() > 0){
   
$sql_max = $DbConn_Online_Mysql->prepare("SELECT Max(ReceiptNo) As LastReceiptNo From bisep_student_receipts
						 Where RegYear = :regyear
						 AND InstituteCode = :icode
						 AND TrackingID = :tid
						 AND Saved = :saved
						 AND ReceiptType = :rectype");
$sql_max->bindParam('regyear',$AdmYear);
$sql_max->bindParam('icode',$SchoolCode);
$sql_max->bindParam('tid',$TrackingID);
$sql_max->bindParam('saved',$Saved);
$sql_max->bindParam('rectype',$ReceiptType);

$sql_max->execute();						
$FetchReceipt = $sql_max->fetch(PDO::FETCH_OBJ);	
if($FetchReceipt->LastReceiptNo > 0){	
$ReceiptNo	=	$FetchReceipt->LastReceiptNo;

$sql_exam = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_exam (LINK_ID_ADM,EnrollNo,Session,Year,ExamCode,ReceiptNo,SchoolCode,GroupCode,CountryCode,ProvinceCode,DistrictCode,ExamStatus,AppearCode,AppearFlag,
BoardID,Prev_Year,Prev_ExamCode,Prev_RollNo,Random,RegYear,SavedIPAddress,TrackingID,ProposedCenter,UserID)
Values (:lida,:enr,:Session,:year,:excode,:recno,:scode,:gcode,:ccode,:pcode,:dcode,:estatus,:apcode,:aflag,:bid,:pyear,:pexam,:prno,
:rand,:regyear,:sip,:trid,:ProposedCenter,:usid)");
$sql_exam->bindParam('lida',$LinkIDADM);
$sql_exam->bindParam('enr',$EnrollNo);
$sql_exam->bindParam('Session',$AdmSession);
$sql_exam->bindParam('year',$AdmYear);
$sql_exam->bindParam('excode',$AdmExamCode);
$sql_exam->bindParam('recno',$ReceiptNo);
$sql_exam->bindParam('scode',$SchoolCode);
$sql_exam->bindParam('gcode',$GroupCode);
$sql_exam->bindParam('ccode',$CountryCode);
$sql_exam->bindParam('pcode',$ProvinceCode);
$sql_exam->bindParam('dcode',$DistrictCode);
$sql_exam->bindParam('estatus',$ExamStatus);
$sql_exam->bindParam('apcode',$AppearCode);
$sql_exam->bindParam('aflag',$AppearFlag);
$sql_exam->bindParam('bid',$BoardID);
$sql_exam->bindParam('pyear',$MaxYear);
$sql_exam->bindParam('pexam',$MaxExamCode);
$sql_exam->bindParam('prno',$MaxRollNo);
$sql_exam->bindParam('rand',$Random);
$sql_exam->bindParam('regyear',$AdmYear);
$sql_exam->bindParam('sip',$IPAddress);
$sql_exam->bindParam('trid',$TrackingID);
$sql_exam->bindParam('ProposedCenter',$ProposedCenter);
$sql_exam->bindParam('usid',$UserID);
$sql_exam->execute();


if($sql_exam->rowCount() > 0){

$sql_s = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_subjects (LINK_ID_ADM,EnrollNo,Year,ExamCode,SubjectCode,AckFlag)
							Values (:lida,:enr,:year,:excode,:scode,:ack)");
if($AppearFlag == 1){
for($j=1; $j<=8; $j++){ 
$Subject = 'Subject'.$j;

$SubjectCode = $$Subject;

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCode);
$sql_s->bindParam('ack',$AckFlag);

$sql_s->execute();
	
	}
}elseif($AppearFlag == 2 OR $AppearFlag == 3 OR $AppearFlag == 4 OR $AppearFlag == 5 OR $AppearFlag == 6 
 OR $AppearFlag == 7 OR $AppearFlag == 8){

foreach($TenthSubjectsArr as $TenthSubCode){ 

$SubjectCodeTenth = str_pad(($TenthSubCode), 2, 0, STR_PAD_LEFT);

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCodeTenth);
$sql_s->bindParam('ack',$AckFlag);

$sql_s->execute();

} 
if(count($NinthSubjectsArr > 0)){
foreach($NinthSubjectsArr as $NinthSubCode){ 

$SubjectCodeNinth = str_pad(($NinthSubCode), 2, 0, STR_PAD_LEFT);

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCodeNinth);
$sql_s->bindParam('ack',$AckFlag);

$sql_s->execute();

} 
}


}
//print_r($sql_s->errorInfo());
if($sql_s->rowCount() > 0){
$SavedFinal = 1;
$sql_update_rec = $DbConn_Online_Mysql->prepare("Update bisep_student_receipts
				SET Saved = ?,
				ChildReceiptNo = ?
				Where RegYear = ?
				AND InstituteCode = ?
				AND TrackingID = ?
				AND ReceiptNo = ?
				AND Saved = 0
				AND ReceiptType = ?");
$sql_update_rec->bindParam(1,$SavedFinal);
$sql_update_rec->bindParam(2,$ReceiptNo);
$sql_update_rec->bindParam(3,$AdmYear);
$sql_update_rec->bindParam(4,$SchoolCode);
$sql_update_rec->bindParam(5,$TrackingID);
$sql_update_rec->bindParam(6,$ReceiptNo);
$sql_update_rec->bindParam(7,$ReceiptType);
$sql_update_rec->execute();
if($sql_update_rec->rowCount() > 0){

$DbConn_Adm_Mysql->commit();

$_SESSION['VerificationCode']	= $GetVerCode;

echo 'Successfully Saved!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	

$MSG = 'Please download and deposit the required Fee on Receipt No: '.$ReceiptNo.'. Submit Admission Form and Receipt in Board Office.
Note: Manual Receipt is not acceptable';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS	

}

}else{
$DbConn_Adm_Mysql->rollBack();	
echo 'Subject Insertion Problem Please Notify Administrative!';	

	}
}else{
echo 'Matric Exam Insertion Problem Please Try Again...';	
	}

}else{$DbConn_Adm_Mysql->rollBack();}
}else{
     
    $DbConn_Adm_Mysql->rollBack();}

}else{
echo 'Duplicate Admission Found Or You have Clicked 2 Time!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	
	}

}else{echo '<span style="font-weight:bold;color:red;">Please Contact To Board Office .(ERROR:FP)!1</span>';}

}else{
   echo '<span style="font-family:bold;color:red;">Optional Subject shall not be the same</span>';		
		}
	}else{
   echo '<span style="font-family:bold;color:red;">Please Select Optional Subject!</span>';		
		}

}else{
echo 'Matric Exam Admission Link Selection Problem Please Try Again...';	
	}
//$DbConn->rollBack();
	}else{
echo 'Student Info Problem!';		
		}
}else{
   echo '<span style="font-family:bold;color:red;">Student Picture Problem Please Contact To Computer Cell Section!</span>';		
		}
	
}else{
   echo '<span style="font-family:bold;color:red;">Group Code is invalid, Please Contact To Computer Cell Section!</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Proposed Center Is Invalid, Please do not use special charecters</span>';		
	}


}else{
   echo '<span style="font-family:bold;color:red;">Permanent Address is invalid</span>';		
	}
}else{
   echo '<span style="font-family:bold;color:red;">Present Address is invalid</span>';		
	}
}else{
   echo '<span style="font-family:bold;color:red;">Mobile Format is Invalid.</span>';	
	}
if(isset($_POST['FormB'])){
}else{
   echo '<span style="font-family:bold;color:red;">Form(B) Format is Invalid.</span>';	
	}
}
}else{
   echo '<span style="font-family:bold;color:red;">Nic Format is Invalid.</span>';	
	}
}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}
//DOB Validation ENDS

}else{
		echo 'Gender Code is Invalid!';
		exit();
		}		}else{
		echo 'Religion Code is Invalid!';
		exit();
		}	
	}else{
		echo 'District Code is Invalid!';
		exit();
		}
	
	}else{
		echo 'Province Code is Invalid!';
		exit();
		}
	}else{
		echo 'Country Code is Invalid!';
		exit();
		}


}
		}
}



if (isset($_GET['mode']) AND $_GET['mode']=='VerifyVerificationCode') /// Employees Entry Form
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$EnrollNo 				= $_POST['EnrollNo'];
$VerificationCode 		= $_POST['VerificationCode'];
//sleep(1);
if(preg_match('/^[0-9]{6}$/',$VerificationCode)){
$AdmSession = get_config("AdmSession");
$AdmYear 	= get_config("AdmYear");
	
	$q1 = $DbConn_Adm_Mysql->prepare("Select * , Count(*) As Found from online_admission_requests
					  Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND VerificationCode = :vercode");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('sess',$AdmSession);
	$q1->bindParam('vercode',$VerificationCode);

	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($FetchRequest->Found > 0){
		$_SESSION['VerificationCode']	= $VerificationCode;
			echo 'Verified Seccessfully';
	echo '<input type="hidden" name="Response" id="Response" value="1" />';
		}else{echo '<span style="font-family:bold;color:red;">Verification Code is Invalid</span>';}			
	
	
}else{
   echo '<span style="font-family:bold;color:red;">Verification Code Format is Invalid.</span>';			
	}


		}
}




// Private Fresh Area


if (isset($_GET['mode']) AND $_GET['mode']=='SendVerificationCodeTwo') /// OK Utalized
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$ApplicationType=	$_POST['ApplicationType'];
$Name			=	strtoupper($_POST['Name']);
$FatherName		=	strtoupper($_POST['FatherName']);
$DateOfBirth	=	$_POST['DateOfBirth'];
$ApplyMobileNo	=	$_POST['ApplyMobileNo'];

$dobtmp 		= 	explode("-",$DateOfBirth);
$dobfinal		=	$dobtmp[2].'-'.$dobtmp[1].'-'.$dobtmp[0];
if($ApplicationType == 2){
$EligibleFor	=	20; // Private Fresh Student
}elseif($ApplicationType == 3){
$EligibleFor	=	30; // Private Otherboard Student
}
$Flag		 	= 	0;
$VerificationCode = rand(100001,999999);
$TrimZero = ltrim($ApplyMobileNo, '0');
$TrimDash = explode('-',$TrimZero);

$CombineMobile = '92'.$TrimDash[0].$TrimDash[1];	
$q1 = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) As Found 
FROM online_admission_requests 
WHERE ClassID = 1
AND Name = :name
AND FatherName = :fname
AND DateOfBirth = :dob
AND Session = :sess
AND Year = :year
AND MobileNo = :mno");
	$q1->bindParam('name',$Name);
	$q1->bindParam('fname',$FatherName);
	$q1->bindParam('dob',$DateOfBirth);
	$q1->bindParam('sess',$AdmSession);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('mno',$CombineMobile);

	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($FetchRequest->Found == 0){

$Counter		=	sprintf('%05d',get_counter());//insert 5 zeros in front of counter
$SchoolCode		=   9999;
$TrackingID		=	$AdmYear.$ClassID.$AdmSession.$SchoolCode.$Counter;
$EnrollNo		=	$AdmYear.$ClassID.$SchoolCode.$Counter;

		
$q2 = $DbConn_Adm_Mysql->prepare("Insert Into online_admission_requests(TrackingID,EnrollNo,Year,ClassID,Session,MobileNo,VerificationCode,Name,FatherName,DateOfBirth,Counter,Flag,EligibleFor,IPAddress)
						  Values(:tid,:enr,:year,:class,:sess,:mobile,:vercode,:name,:fname,:dob,:count,:flag,:elg,:ip)");
	$q2->bindParam('tid',$TrackingID);
	$q2->bindParam('enr',$EnrollNo);
	$q2->bindParam('year',$AdmYear);
	$q2->bindParam('class',$ClassID);
	$q2->bindParam('sess',$AdmSession);
	$q2->bindParam('mobile',$CombineMobile);
	$q2->bindParam('vercode',$VerificationCode);
	$q2->bindParam('name',$Name);
	$q2->bindParam('fname',$FatherName);
	$q2->bindParam('dob',$dobfinal);
	$q2->bindParam('count',$Counter);
	$q2->bindParam('flag',$Flag);
	$q2->bindParam('elg',$EligibleFor);
	$q2->bindParam('ip',$IPAddress);
	$q2->execute();
//echo 'ok';
print_r($q2->errorInfo());
$InsCheck = $q2->rowCount();
if($InsCheck > 0){
//Send SMS Code
	$MSG = 'Verification Code '.$VerificationCode.' for Student Name: '.$Name.' please submit this verification code for further process';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS	

}
		
	
	}
	}
}

if (isset($_GET['mode']) AND $_GET['mode']=='ConfirmVerificationCodeTwo') /// Confirm Verification Code
{
		if (isset($_GET['module']) AND $_GET['module']=='Admission') {
$EnrollNo 				= $_POST['EnrollNo'];
$VerificationCode 		= $_POST['VerificationCode'];
//sleep(1);
if(preg_match('/^[0-9]{6}$/',$VerificationCode)){
$AdmSession = get_config("AdmSession");
$AdmYear 	= get_config("AdmYear");
	
	$q1 = $DbConn_Adm_Mysql->prepare("Select * , Count(*) As Found from online_admission_requests
					  Where EnrollNo = :enr
					  AND Year = :year
					  AND ClassID = 1
					  AND Session = :sess
					  AND Flag = 0");
	$q1->bindParam('enr',$EnrollNo);
	$q1->bindParam('year',$AdmYear);
	$q1->bindParam('sess',$AdmSession);

	$q1->execute();
	$FetchRequest = $q1->fetch(PDO::FETCH_OBJ);
	if($FetchRequest->Found > 0){
		
	$SavedVerificationCode = $FetchRequest->VerificationCode;
	if($SavedVerificationCode == $VerificationCode){
		$update = $DbConn_Adm_Mysql->prepare("Update online_admission_requests 
									SET Flag = 1
									Where EnrollNo = ?
									AND Session = ?
									AND Year = ?
									AND Flag = 0
									AND ClassID = 1");
		$update->bindparam(1,$EnrollNo);
		$update->bindparam(2,$AdmSession);
		$update->bindparam(3,$AdmYear);
		$update->execute();
		if($update->rowCount() > 0){
			echo 'Verified Seccessfully';
	echo '<input type="hidden" name="Response" id="Response" value="1" />';
			}else{
		echo 'Please try again... ';		
				}
	}else{
		echo 'Verification Code is Invalid!';
		}
	
	

		}else{echo 'Required Data Not Found Please Refresh page and Try again.';}			
	
	
}else{
   echo '<span style="font-family:bold;color:red;">Verification Code Format is Invalid.</span>';			
	}


		}
}

if (isset($_GET['mode']) AND $_GET['mode']=='SaveAdmissionFormTwo') /// Save Admission Form Private Fresh Copied
{
		if (isset($_GET['module']) AND $_GET['module']=='SSCAdmissionPrivateNinthFresh') {
if(isset($_POST['CountryCode']) && $_POST['CountryCode'] > 0 && isset($_POST['ProvinceCode']) && $_POST['ProvinceCode'] > 0
&& isset($_POST['DistrictCode']) && $_POST['DistrictCode'] > 0
&& isset($_POST['Gender']) && $_POST['Gender'] > 0
&& isset($_POST['StudentName']) && !empty($_POST['StudentName']) && isset($_POST['FatherName']) && !empty($_POST['FatherName'])
&& isset($_POST['DateOfBirth']) && !empty($_POST['DateOfBirth'])
&& isset($_POST['Religion']) && $_POST['Religion'] > 0
&& isset($_POST['FatherCNIC']) && $_POST['FatherCNIC'] > 0
&& isset($_POST['MobileNo']) && $_POST['MobileNo'] > 0 && isset($_POST['PostalAddress']) && !empty($_POST['PostalAddress'])
&& isset($_POST['PermanentAddress']) && !empty($_POST['PermanentAddress'])
&& ( isset($_POST['Subject1']) && $_POST['Subject1'] > 0 && isset($_POST['Subject2']) && $_POST['Subject2'] > 0
&& isset($_POST['Subject3']) && $_POST['Subject3'] > 0 && isset($_POST['Subject4']) && $_POST['Subject4'] > 0
&& isset($_POST['Subject5']) && $_POST['Subject5'] > 0 && isset($_POST['Subject6']) && $_POST['Subject6'] > 0
) OR (isset($_POST['arrSubjects']) AND !empty($_POST['arrSubjects'])> 0))
{
$EnrollNo 		= trim($_POST['EnrollNo']);
$StudentName	= strtoupper($_POST['StudentName']);
$FatherName		= strtoupper($_POST['FatherName']);
$DateOfBirth	= $_POST['DateOfBirth'];
$UserID			= 0;
$SchoolCode		= 99999;
$AppearCode		= $_POST['AppearCode'];
$AppearFlag		= $_POST['AppearFlag'];

if(isset($_POST['arrSubjects'])){
$arrSubjectsCount	= explode(',',$_POST['arrSubjects']);
if(count($arrSubjectsCount) > 3){echo 'Maximum Subjects allowed is not more then 3 '; exit();}
	}
//$DateOfBirth	 = date("Y-m-d", strtotime($FetchData->DATE_OF_BIRTH));
$dobtmp 		= 	explode("-",$DateOfBirth);
$dobfinal		=	$dobtmp[2].'-'.$dobtmp[1].'-'.$dobtmp[0];

if(validateDate($DateOfBirth) == $DateOfBirth){
if($dobtmp[2] != 0 AND $dobtmp[1] != 0 AND $dobtmp[1] != 0 AND ($dobtmp[2] > 1970 AND $dobtmp[2] < 2010)){

$CountryCode 	 = $_POST['CountryCode'];
$ProvinceCode 	 = $_POST['ProvinceCode'];
$DistrictCode 	 = $_POST['DistrictCode'];
$Religion	 	 = $_POST['Religion'];
$FatherCNIC		 = $_POST['FatherCNIC'];
$FormB			 = $_POST['FormB'];
$MobileNo		 = $_POST['MobileNo'];
$PhoneNo		 = $_POST['PhoneNo'];
$PostalAddress	 = strtoupper($_POST['PostalAddress']);
$PermanentAddress= strtoupper($_POST['PermanentAddress']);
$ProposedCenter	 = strtoupper($_POST['ProposedCenter']);
$Gender		 	 = $_POST['Gender'];
$GroupCode		 = $_POST['GroupCode'];
$ImageName		 = $EnrollNo;
$Flag			 = 0;

if(preg_match('/^[1-9][0-9]{0,2}$/',$CountryCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$ProvinceCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$DistrictCode)){
if(preg_match('/^[1-2]{1,1}$/',$Religion)){
if(preg_match('/^[1-3]{1,1}$/',$Gender)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$StudentName)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$FatherName)){ 
// Dob Validation Starts
if(validateDate($DateOfBirth) == $DateOfBirth){
//DOB OK
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FatherCNIC)){
if(isset($_POST['FormB'])){
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FormB)){
}
if(preg_match('/^\d{4}-\d{7}$/',$MobileNo)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PostalAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PermanentAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $ProposedCenter)){
if(preg_match('/^[2-2]{1,1}$/',$GroupCode)){


$sqlimg = $DbConn_Online_Mysql->prepare("SELECT Count(*) As ImageFound FROM bisep_student_images 
						 	WHERE image_name = :imgname
						 	AND School_Code = :scode
						 	AND Reg_Year = :regyear
						 	AND Saved = 0
							AND ClassID = 9
							AND ExamStatus = 2");					 
$sqlimg->bindParam('imgname',$ImageName);
$sqlimg->bindParam('scode',$SchoolCode);
$sqlimg->bindParam('regyear',$AdmYear);
$sqlimg->execute();

$FetchImageCheck = $sqlimg->fetch(PDO::FETCH_OBJ);
if($FetchImageCheck->ImageFound == 1){

$DbConn_Adm_Mysql->beginTransaction();
$sql_SICheck = $DbConn_Adm_Mysql->prepare("Select Count(*) As Found 
			 	from matric_student_info
			 	Where EnrollNo = :enr");
$sql_SICheck->bindParam('enr',$EnrollNo);	
$sql_SICheck->execute();
$FetchLinkID = $sql_SICheck->fetch(PDO::FETCH_OBJ);
if($FetchLinkID->Found == 0){
$sql_si = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_info (EnrollNo,Name,FatherName,Gender,DateOfBirth,FatherCNIC,FormB,MobileNo,PhoneNo,PostalAddress,PermanentAddress,Religion,DateOfAdmission,UserID,RegYear)
							Values (:enr,:name,:fname,:gender,:dob,:fnic,:formb,:mno,:pno,:padd,:pradd,:rel,:doa,:userid,:regyear)");
$sql_si->bindParam('enr',$EnrollNo);
$sql_si->bindParam('name',$StudentName);
$sql_si->bindParam('fname',$FatherName);
$sql_si->bindParam('gender',$Gender);
$sql_si->bindParam('dob',$dobfinal);
$sql_si->bindParam('fnic',$FatherCNIC);
$sql_si->bindParam('formb',$FormB);
$sql_si->bindParam('mno',$MobileNo);
$sql_si->bindParam('pno',$PhoneNo);
$sql_si->bindParam('padd',$PostalAddress);
$sql_si->bindParam('pradd',$PermanentAddress);
$sql_si->bindParam('rel',$Religion);
$sql_si->bindParam('doa',$DateOfAdmission);
$sql_si->bindParam('userid',$UserID);
$sql_si->bindParam('regyear',$AdmYear);
$sql_si->execute();
//print_r($sql_si->errorInfo());
if($sql_si->rowCount() > 0){
		
$sql_link = $DbConn_Adm_Mysql->prepare("Select LINK_ID_ADM from matric_student_info
			 Where EnrollNo = :enr");
$sql_link->bindParam('enr',$EnrollNo);	
$sql_link->execute();
$FetchLinkID = $sql_link->fetch(PDO::FETCH_OBJ);

if($sql_link->rowCount() > 0){
	
$LinkIDADM = $FetchLinkID->LINK_ID_ADM;

if($AppearCode == 1){
if ($AdmSession == 1) {$AdmExamCode = 1;} elseif($AdmSession == 2) {$AdmExamCode = 5;} 
$Subject1 		 = $_POST['Subject1'];
$Subject2 		 = $_POST['Subject2'];
$Subject3 		 = $_POST['Subject3'];
$Subject4 		 = $_POST['Subject4'];
$Subject5 		 = $_POST['Subject5'];
$Subject6 		 = $_POST['Subject6'];
$Subject7 		 = $_POST['Subject7'];
$Subject8 		 = $_POST['Subject8'];
}elseif($AppearCode == 3){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
$Subject7 = 101;$Subject8=102;	
	}
// Practicle Subjects Fee Calculation;
/*
for($k=1; $k<=8; $k++){ 
$Subject = 'Subject'.$k;

$SubjectCodePrac = $$Subject;
$Practicle = $Practicle + get_total_practicle($SubjectCodePrac);
}
*/
if(isset($Subject7) AND $Subject7 > 0 AND isset($Subject8) AND $Subject8 > 0){
if($Subject7 != $Subject8){

//$TotalPracticleFee 	= $Practicle 	* 50;
//$TotalFailedPapers	= $FailedPaper  * 100;
if($AppearCode == 1){$GetAppearCodeFee = 1;}else{$GetAppearCodeFee = 2;}
$GroupCodeFee	   = 1;
//$GetAppearCodeFee  = 1;
$TotalAdmissionFee = get_admission_fee($AdmYear,$AdmSession,$AdmExamCode,$GroupCodeFee,$GetAppearCodeFee,$FeeStatus);


$CombineTotalFee = $TotalAdmissionFee;
//echo $CombineTotalFee;exit();

if($CombineTotalFee > 700){
$check_adm_status	=	check_adm_status($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($check_adm_status == 0){
$GetVerCode		 =	get_request_verification_code($AdmSession,$AdmYear,$EnrollNo);
$CombineMobile	 =	get_request_mobile_no($AdmSession,$AdmYear,$EnrollNo);
//$Rand			 = rand(100,999);
//$TrackingID	 = $Rand.$MaxYear.$MaxExamCode.$MaxRollNo;


$TrackingID		 =  get_request_tracking_id($AdmSession,$AdmYear,$EnrollNo);

$sql2 = $DbConn_Online_Mysql->prepare("INSERT INTO bisep_student_receipts (ReceiptType,Saved,RegYear,InstituteCode,TotalStudents,TotalFee,TrackingID)
						 Values (:rectype,:saved,:regyear,:icode,:tstd,:tfee,:tid)");
$sql2->bindParam('rectype',$ReceiptType);
$sql2->bindParam('saved',$Saved);
$sql2->bindParam('regyear',$AdmYear);
$sql2->bindParam('icode',$SchoolCode);
$sql2->bindParam('tstd',$TotalStudents);
$sql2->bindParam('tfee',$CombineTotalFee);
$sql2->bindParam('tid',$TrackingID);
$sql2->execute();
//print_r($sql2->errorInfo());
if($sql2->rowCount() > 0){
$sql_max = $DbConn_Online_Mysql->prepare("SELECT Max(ReceiptNo) As LastReceiptNo From bisep_student_receipts
						 Where RegYear = :regyear
						 AND InstituteCode = :icode
						 AND TrackingID = :tid
						 AND Saved = :saved
						 AND ReceiptType = :rectype");
$sql_max->bindParam('regyear',$AdmYear);
$sql_max->bindParam('icode',$SchoolCode);
$sql_max->bindParam('tid',$TrackingID);
$sql_max->bindParam('saved',$Saved);
$sql_max->bindParam('rectype',$ReceiptType);

$sql_max->execute();						
$FetchReceipt = $sql_max->fetch(PDO::FETCH_OBJ);	
if($FetchReceipt->LastReceiptNo > 0){	
$ReceiptNo	=	$FetchReceipt->LastReceiptNo;

$sql_exam = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_exam (LINK_ID_ADM,EnrollNo,Session,Year,ExamCode,ReceiptNo,SchoolCode,GroupCode,CountryCode,ProvinceCode,DistrictCode,ExamStatus,AppearCode,AppearFlag,
BoardID,Prev_Year,Prev_ExamCode,Prev_RollNo,Random,RegYear,SavedIPAddress,TrackingID,ProposedCenter,UserID)
Values (:lida,:enr,:Session,:year,:excode,:recno,:scode,:gcode,:ccode,:pcode,:dcode,:estatus,:apcode,:aflag,:bid,:pyear,:pexam,:prno,
:rand,:regyear,:sip,:trid,:ProposedCenter,:usid)");
$sql_exam->bindParam('lida',$LinkIDADM);
$sql_exam->bindParam('enr',$EnrollNo);
$sql_exam->bindParam('Session',$AdmSession);
$sql_exam->bindParam('year',$AdmYear);
$sql_exam->bindParam('excode',$AdmExamCode);
$sql_exam->bindParam('recno',$ReceiptNo);
$sql_exam->bindParam('scode',$SchoolCode);
$sql_exam->bindParam('gcode',$GroupCode);
$sql_exam->bindParam('ccode',$CountryCode);
$sql_exam->bindParam('pcode',$ProvinceCode);
$sql_exam->bindParam('dcode',$DistrictCode);
$sql_exam->bindParam('estatus',$ExamStatus);
$sql_exam->bindParam('apcode',$AppearCode);
$sql_exam->bindParam('aflag',$AppearFlag);
$sql_exam->bindParam('bid',$BoardID);
$sql_exam->bindParam('pyear',$MaxYear);
$sql_exam->bindParam('pexam',$MaxExamCode);
$sql_exam->bindParam('prno',$MaxRollNo);
$sql_exam->bindParam('rand',$Random);
$sql_exam->bindParam('regyear',$AdmYear);
$sql_exam->bindParam('sip',$IPAddress);
$sql_exam->bindParam('trid',$TrackingID);
$sql_exam->bindParam('ProposedCenter',$ProposedCenter);
$sql_exam->bindParam('usid',$UserID);
$sql_exam->execute();

if($sql_exam->rowCount() > 0){

$sql_s = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_subjects (LINK_ID_ADM,EnrollNo,Year,ExamCode,SubjectCode,AckFlag)
							Values (:lida,:enr,:year,:excode,:scode,:ack)");

if($AppearCode == 1){
for($j=1; $j<=8; $j++){ 
$Subject = 'Subject'.$j;

$SubjectCode = $$Subject;

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCode);
$sql_s->bindParam('ack',$AckFlag);

$sql_s->execute();
	
	}
}elseif($AppearCode == 3){
$arrSubjects = explode(',',$_POST['arrSubjects']);
foreach($arrSubjects as $Subject){ 

$SubjectCodeNinth = str_pad((($Subject-1)), 2, 0, STR_PAD_LEFT);
$SubjectCodeTenth = str_pad(($Subject), 2, 0, STR_PAD_LEFT);

//9th Subjects..
$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCodeNinth);
$sql_s->bindParam('ack',$AckFlag);
$sql_s->execute();
//10th Subjects..
$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCodeTenth);
$sql_s->bindParam('ack',$AckFlag);
$sql_s->execute();

$Subject = "";
$SubjectCodeNinth = "";
$SubjectCodeTenth = "";
}
	}
//print_r($sql_s->errorInfo());
if($sql_s->rowCount() > 0){
$SavedFinal = 1;
$sql_update_rec = $DbConn_Online_Mysql->prepare("Update bisep_student_receipts
				SET Saved = ?,
				ChildReceiptNo = ?
				Where RegYear = ?
				AND InstituteCode = ?
				AND TrackingID = ?
				AND ReceiptNo = ?
				AND Saved = 0
				AND ReceiptType = ?");
$sql_update_rec->bindParam(1,$SavedFinal);
$sql_update_rec->bindParam(2,$ReceiptNo);
$sql_update_rec->bindParam(3,$AdmYear);
$sql_update_rec->bindParam(4,$SchoolCode);
$sql_update_rec->bindParam(5,$TrackingID);
$sql_update_rec->bindParam(6,$ReceiptNo);
$sql_update_rec->bindParam(7,$ReceiptType);
$sql_update_rec->execute();
if($sql_update_rec->rowCount() > 0){
$Saved = 1;
$sql_pic = $DbConn_Online_Mysql->prepare("Update bisep_student_images SET Saved = ?
							   WHERE image_name = ?
							   AND School_Code = ?
							   AND Reg_Year = ?
							   AND ClassID = 9
							   AND ExamStatus = 2");
$sql_pic->bindParam(1,$Saved);
$sql_pic->bindParam(2,$ImageName);
$sql_pic->bindParam(3,$SchoolCode);
$sql_pic->bindParam(4,$AdmYear);
$sql_pic->execute();
if($sql_pic->rowCount() > 0){
	
$sql_request_update = $DbConn_Adm_Mysql->prepare("Update online_admission_requests SET Name = ?,
							   FatherName = ?,
							   DateOfBirth = ?
							   Where Session = ?
							   AND Year = ?
							   AND EnrollNo = ?
							   AND ClassID = 1
							   AND EligibleFor = 20");
$sql_request_update->bindParam(1,$StudentName);
$sql_request_update->bindParam(2,$FatherName);
$sql_request_update->bindParam(3,$dobfinal);
$sql_request_update->bindParam(4,$AdmSession);
$sql_request_update->bindParam(5,$AdmYear);
$sql_request_update->bindParam(6,$EnrollNo);
$sql_request_update->execute();	
	
	
$DbConn_Adm_Mysql->commit();

$_SESSION['VerificationCode']	= $GetVerCode;

echo 'Successfully Saved!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	

$MSG = 'Please download and deposit the required Fee on Receipt No: '.$ReceiptNo.'. Submit Admission Form and Receipt in Board Office.
Note: Manual Receipt is not acceptable';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS	


}
}
}else{
$DbConn_Adm_Mysql->rollBack();	
echo 'Subject Insertion Problem Please Notify Administrative!';	

	}
}else{
echo 'Matric Exam Insertion Problem Please Try Again...';	
	}

}else{$DbConn_Adm_Mysql->rollBack();}
}else{$DbConn_Adm_Mysql->rollBack();}

}else{
echo 'Duplicate Admission Found Or You have Clicked 2 Time!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	
	}

}else{echo '<span style="font-weight:bold;color:red;">Please Contact To Board Office .(ERROR:FP)!2</span>';}

}else{
   echo '<span style="font-family:bold;color:red;">Optional Subject shall not be the same</span>';		
		}
	}else{
   echo '<span style="font-family:bold;color:red;">Please Select Optional Subject!</span>';		
		}

}else{
echo 'Matric Exam Admission Link Selection Problem Please Try Again...';	
	}
//$DbConn->rollBack();
	}else{
echo 'Student Info Problem!';		
		}
}else{
   echo '<span style="font-family:bold;color:red;">Please Contact To Board Office!</span>';		
		}
	
}else{
   echo '<span style="font-family:bold;color:red;">Please Upload Picture!</span>';		
		}

}else{
   echo '<span style="font-family:bold;color:red;">Group Code is invalid, Please Contact To Computer Cell Section!</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Proposed Center Is Invalid, Please do not use special charecters</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Permanent Address is invalid</span>';		
	}
}else{
   echo '<span style="font-family:bold;color:red;">Permanent Address is invalid</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Mobile Format is Invalid.</span>';	
	}
if(isset($_POST['FormB'])){
}else{
   echo '<span style="font-family:bold;color:red;">Form(B) Format is Invalid.</span>';	
	}
}
}else{
   echo '<span style="font-family:bold;color:red;">Nic Format is Invalid.</span>';	
	}
}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}
//DOB Validation ENDS
}else{
   echo '<span style="font-family:bold;color:red;">Father Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}

}else{
   echo '<span style="font-family:bold;color:red;">Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}


}else{
		echo 'Gender Code is Invalid!';
		exit();
		}		}else{
		echo 'Religion Code is Invalid!';
		exit();
		}	
	}else{
		echo 'District Code is Invalid!';
		exit();
		}
	
	}else{
		echo 'Province Code is Invalid!';
		exit();
		}
	}else{
		echo 'Country Code is Invalid!';
		exit();
		}

}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check (ERR-II).</span>';	
	}


}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}


}else{
	echo '<span style="color:red;">Please Select Subjects!</span>';
	}
		}
}
//Private Fresh Area Ends


// OTHER BOARD STUDENTS

if (isset($_GET['mode']) AND $_GET['mode']=='SaveAdmissionFormThree') /// Save Admission Form Private Fresh Copied
{
		if (isset($_GET['module']) AND $_GET['module']=='SaveAdmissionFormOtherBoard') {
if(isset($_POST['CountryCode']) && $_POST['CountryCode'] > 0 && isset($_POST['ProvinceCode']) && $_POST['ProvinceCode'] > 0
&& isset($_POST['DistrictCode']) && $_POST['DistrictCode'] > 0
&& isset($_POST['Gender']) && $_POST['Gender'] > 0
&& isset($_POST['StudentName']) && !empty($_POST['StudentName']) && isset($_POST['FatherName']) && !empty($_POST['FatherName'])
&& isset($_POST['DateOfBirth']) && !empty($_POST['DateOfBirth'])
&& isset($_POST['Religion']) && $_POST['Religion'] > 0
&& isset($_POST['FatherCNIC']) && $_POST['FatherCNIC'] > 0
&& isset($_POST['MobileNo']) && $_POST['MobileNo'] > 0 && isset($_POST['PostalAddress']) && !empty($_POST['PostalAddress'])
&& isset($_POST['PermanentAddress']) && !empty($_POST['PermanentAddress'])
&& ( isset($_POST['Subject1']) && $_POST['Subject1'] > 0 && isset($_POST['Subject2']) && $_POST['Subject2'] > 0
&& isset($_POST['Subject3']) && $_POST['Subject3'] > 0 && isset($_POST['Subject4']) && $_POST['Subject4'] > 0
&& isset($_POST['Subject5']) && $_POST['Subject5'] > 0 && isset($_POST['Subject6']) && $_POST['Subject6'] > 0
&& isset($_POST['Subject7']) && $_POST['Subject7'] > 0 && isset($_POST['Subject8']) && $_POST['Subject8'] > 0)
&& isset($_POST['AdmissionClass']) && $_POST['AdmissionClass'] > 0 && isset($_POST['BoardIDOB']) && $_POST['BoardIDOB'] > 0)
{
$EnrollNo 		= trim($_POST['EnrollNo']);
$StudentName	= strtoupper($_POST['StudentName']);
$FatherName		= strtoupper($_POST['FatherName']);
$DateOfBirth	= $_POST['DateOfBirth'];
$UserID			= 0;
$SchoolCode		= 99999;
$AppearCode		= $_POST['AppearCode'];
$AppearFlag		= $_POST['AppearFlag'];

if(isset($_POST['arrSubjects'])){
$arrSubjectsCount	= explode(',',$_POST['arrSubjects']);
if(count($arrSubjectsCount) > 3){echo 'Maximum Subjects allowed is not more then 3 '; exit();}
	}
//$DateOfBirth	 = date("Y-m-d", strtotime($FetchData->DATE_OF_BIRTH));
$dobtmp 		 = 	explode("-",$DateOfBirth);
$dobfinal		 =	$dobtmp[2].'-'.$dobtmp[1].'-'.$dobtmp[0];

if(validateDate($DateOfBirth) == $DateOfBirth){
if($dobtmp[2] != 0 AND $dobtmp[1] != 0 AND $dobtmp[1] != 0 AND ($dobtmp[2] > 1970 AND $dobtmp[2] < 2010)){


$AdmissionClass	 =  $_POST['AdmissionClass'];
$BoardIDOB		 =  $_POST['BoardIDOB'];
$BoardFlag		 =	1;
if($AdmissionClass == 9){
$PreviousExamCodeOB	 = NULL;
$PreviousYearOB		 = NULL;
$PreviousRollNoOB	 = NULL;
}elseif($AdmissionClass == 10){
$PreviousExamCodeOB	 = $_POST['PreviousExamCodeOB'];
$PreviousYearOB		 = $_POST['PreviousYearOB'];
$PreviousRollNoOB	 = $_POST['PreviousRollNoOB'];	
if($PreviousExamCodeOB > 0 AND $PreviousYearOB > 0 AND $PreviousRollNoOB > 0){}else{echo '<span style="color:red;">Please Select Previous Exam (Session), Previous Year And Previous Roll No'; exit();}
}

$CountryCode 	 = $_POST['CountryCode'];
$ProvinceCode 	 = $_POST['ProvinceCode'];
$DistrictCode 	 = $_POST['DistrictCode'];
$Religion	 	 = $_POST['Religion'];
$FatherCNIC		 = $_POST['FatherCNIC'];
$FormB			 = $_POST['FormB'];
$MobileNo		 = $_POST['MobileNo'];
$PhoneNo		 = $_POST['PhoneNo'];
$PostalAddress	 = strtoupper($_POST['PostalAddress']);
$PermanentAddress= strtoupper($_POST['PermanentAddress']);
$ProposedCenter	 = strtoupper($_POST['ProposedCenter']);
$Gender		 	 = $_POST['Gender'];
$GroupCode		 = $_POST['GroupCode'];
$ImageName		 = $EnrollNo;
$Flag			 = 0;

if(preg_match('/^[1-9][0-9]{0,2}$/',$ClassID)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$BoardIDOB)){

if(preg_match('/^[1-9][0-9]{0,2}$/',$CountryCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$ProvinceCode)){
if(preg_match('/^[1-9][0-9]{0,2}$/',$DistrictCode)){
if(preg_match('/^[1-2]{1,1}$/',$Religion)){
if(preg_match('/^[1-3]{1,1}$/',$Gender)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$StudentName)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$FatherName)){ 
// Dob Validation Starts
if(validateDate($DateOfBirth) == $DateOfBirth){
//DOB OK
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FatherCNIC)){
if(isset($_POST['FormB'])){
if(preg_match('/^\d{5}-\d{7}-\d{1}$/',$FormB)){
}
if(preg_match('/^\d{4}-\d{7}$/',$MobileNo)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PostalAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $PermanentAddress)){
if(!preg_match('/[^a-z_#.,\- \-0-9]/i', $ProposedCenter)){
if(preg_match('/^[2-2]{1,1}$/',$GroupCode)){


$sqlimg = $DbConn_Online_Mysql->prepare("SELECT Count(*) As ImageFound FROM bisep_student_images 
						 	WHERE image_name = :imgname
						 	AND School_Code = :scode
						 	AND Reg_Year = :regyear
						 	AND Saved = 0
							AND ClassID = 9
							AND ExamStatus = 2");					 
$sqlimg->bindParam('imgname',$ImageName);
$sqlimg->bindParam('scode',$SchoolCode);
$sqlimg->bindParam('regyear',$AdmYear);
$sqlimg->execute();

$FetchImageCheck = $sqlimg->fetch(PDO::FETCH_OBJ);
if($FetchImageCheck->ImageFound == 1){

$DbConn_Adm_Mysql->beginTransaction();
$sql_SICheck = $DbConn_Adm_Mysql->prepare("Select Count(*) As Found 
			 	from matric_student_info
			 	Where EnrollNo = :enr");
$sql_SICheck->bindParam('enr',$EnrollNo);	
$sql_SICheck->execute();
$FetchLinkID = $sql_SICheck->fetch(PDO::FETCH_OBJ);
if($FetchLinkID->Found == 0){
$sql_si = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_info (EnrollNo,Name,FatherName,Gender,DateOfBirth,FatherCNIC,FormB,MobileNo,PhoneNo,PostalAddress,PermanentAddress,Religion,DateOfAdmission,UserID,RegYear)
							Values (:enr,:name,:fname,:gender,:dob,:fnic,:formb,:mno,:pno,:padd,:pradd,:rel,:doa,:userid,:regyear)");
$sql_si->bindParam('enr',$EnrollNo);
$sql_si->bindParam('name',$StudentName);
$sql_si->bindParam('fname',$FatherName);
$sql_si->bindParam('gender',$Gender);
$sql_si->bindParam('dob',$dobfinal);
$sql_si->bindParam('fnic',$FatherCNIC);
$sql_si->bindParam('formb',$FormB);
$sql_si->bindParam('mno',$MobileNo);
$sql_si->bindParam('pno',$PhoneNo);
$sql_si->bindParam('padd',$PostalAddress);
$sql_si->bindParam('pradd',$PermanentAddress);
$sql_si->bindParam('rel',$Religion);
$sql_si->bindParam('doa',$DateOfAdmission);
$sql_si->bindParam('userid',$UserID);
$sql_si->bindParam('regyear',$AdmYear);
$sql_si->execute();
//print_r($sql_si->errorInfo());
if($sql_si->rowCount() > 0){
		
$sql_link = $DbConn_Adm_Mysql->prepare("Select LINK_ID_ADM from matric_student_info
			 Where EnrollNo = :enr");
$sql_link->bindParam('enr',$EnrollNo);	
$sql_link->execute();
$FetchLinkID = $sql_link->fetch(PDO::FETCH_OBJ);

if($sql_link->rowCount() > 0){
	
$LinkIDADM = $FetchLinkID->LINK_ID_ADM;

if($AdmissionClass == 9){
if ($AdmSession == 1) {$AdmExamCode = 1;} elseif($AdmSession == 2) {$AdmExamCode = 5;} 
$Subject1 		 = $_POST['Subject1'];
$Subject2 		 = $_POST['Subject2'];
$Subject3 		 = $_POST['Subject3'];
$Subject4 		 = $_POST['Subject4'];
$Subject5 		 = $_POST['Subject5'];
$Subject6 		 = $_POST['Subject6'];
$Subject7 		 = $_POST['Subject7'];
$Subject8 		 = $_POST['Subject8'];
}elseif($AdmissionClass == 10){
if ($AdmSession == 1) {$AdmExamCode = 2;} elseif($AdmSession == 2) {$AdmExamCode = 3;} 
$Subject1 		 = $_POST['Subject1'];
$Subject2 		 = $_POST['Subject2'];
$Subject3 		 = $_POST['Subject3'];
$Subject4 		 = $_POST['Subject4'];
$Subject5 		 = $_POST['Subject5'];
$Subject6 		 = $_POST['Subject6'];
$Subject7 		 = $_POST['Subject7'];
$Subject8 		 = $_POST['Subject8'];
$SubArrNinth 	 = $_POST['SubjectsNinth'];

	}
// Practicle Subjects Fee Calculation;
/*
for($k=1; $k<=8; $k++){ 
$Subject = 'Subject'.$k;

$SubjectCodePrac = $$Subject;
$Practicle = $Practicle + get_total_practicle($SubjectCodePrac);
}
*/
if(isset($Subject7) AND $Subject7 > 0 AND isset($Subject8) AND $Subject8 > 0){
if($Subject7 != $Subject8){

if(isset($_POST['SubjectsNinth']) AND strlen($_POST['SubjectsNinth']) > 0){
	if(isset($_POST['SubjectsNinth']) AND strlen($_POST['SubjectsNinth']) > 0 AND strlen($_POST['SubjectsNinth']) <= 5 ){
		}else{
	echo 'Full Failed Student Can\'t be appear in 10th Class';			
exit();
		} }


//$TotalPracticleFee 	= $Practicle 	* 50;
//$TotalFailedPapers	= $FailedPaper  * 100;
$GroupCodeFee	   = 1;
$GetAppearCodeFee  = 1;
$TotalAdmissionFee = get_admission_fee($AdmYear,$AdmSession,$AdmExamCode,$GroupCodeFee,$GetAppearCodeFee,$FeeStatus);

$CombineTotalFee = $TotalAdmissionFee;
//echo $CombineTotalFee;exit();

if($CombineTotalFee > 700){
$check_adm_status	=	check_adm_status($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($check_adm_status == 0){
$GetVerCode		 =	get_request_verification_code($AdmSession,$AdmYear,$EnrollNo);
$CombineMobile	 =	get_request_mobile_no($AdmSession,$AdmYear,$EnrollNo);
//$Rand			 = rand(100,999);
//$TrackingID	 = $Rand.$MaxYear.$MaxExamCode.$MaxRollNo;


$TrackingID		 =  get_request_tracking_id($AdmSession,$AdmYear,$EnrollNo);

$sql2 = $DbConn_Online_Mysql->prepare("INSERT INTO bisep_student_receipts (ReceiptType,Saved,RegYear,InstituteCode,TotalStudents,TotalFee,TrackingID)
						 Values (:rectype,:saved,:regyear,:icode,:tstd,:tfee,:tid)");
$sql2->bindParam('rectype',$ReceiptType);
$sql2->bindParam('saved',$Saved);
$sql2->bindParam('regyear',$AdmYear);
$sql2->bindParam('icode',$SchoolCode);
$sql2->bindParam('tstd',$TotalStudents);
$sql2->bindParam('tfee',$CombineTotalFee);
$sql2->bindParam('tid',$TrackingID);
$sql2->execute();
//print_r($sql2->errorInfo());
if($sql2->rowCount() > 0){
$sql_max = $DbConn_Online_Mysql->prepare("SELECT Max(ReceiptNo) As LastReceiptNo From bisep_student_receipts
						 Where RegYear = :regyear
						 AND InstituteCode = :icode
						 AND TrackingID = :tid
						 AND Saved = :saved
						 AND ReceiptType = :rectype");
$sql_max->bindParam('regyear',$AdmYear);
$sql_max->bindParam('icode',$SchoolCode);
$sql_max->bindParam('tid',$TrackingID);
$sql_max->bindParam('saved',$Saved);
$sql_max->bindParam('rectype',$ReceiptType);

$sql_max->execute();						
$FetchReceipt = $sql_max->fetch(PDO::FETCH_OBJ);	
if($FetchReceipt->LastReceiptNo > 0){	
$ReceiptNo	=	$FetchReceipt->LastReceiptNo;

$sql_exam = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_exam (LINK_ID_ADM,EnrollNo,Session,Year,ExamCode,ReceiptNo,SchoolCode,GroupCode,CountryCode,ProvinceCode,DistrictCode,ExamStatus,AppearCode,AppearFlag,BoardFlag,BoardID,Prev_Year,Prev_ExamCode,Prev_RollNo,Random,RegYear,SavedIPAddress,TrackingID,ProposedCenter,UserID)
Values (:lida,:enr,:Session,:year,:excode,:recno,:scode,:gcode,:ccode,:pcode,:dcode,:estatus,:apcode,:aflag,:BoardFlag,
:bid,:pyear,:pexam,:prno,:rand,:regyear,:sip,:trid,:ProposedCenter,:usid)");
$sql_exam->bindParam('lida',$LinkIDADM);
$sql_exam->bindParam('enr',$EnrollNo);
$sql_exam->bindParam('Session',$AdmSession);
$sql_exam->bindParam('year',$AdmYear);
$sql_exam->bindParam('excode',$AdmExamCode);
$sql_exam->bindParam('recno',$ReceiptNo);
$sql_exam->bindParam('scode',$SchoolCode);
$sql_exam->bindParam('gcode',$GroupCode);
$sql_exam->bindParam('ccode',$CountryCode);
$sql_exam->bindParam('pcode',$ProvinceCode);
$sql_exam->bindParam('dcode',$DistrictCode);
$sql_exam->bindParam('estatus',$ExamStatus);
$sql_exam->bindParam('apcode',$AppearCode);
$sql_exam->bindParam('aflag',$AppearFlag);
$sql_exam->bindParam('BoardFlag',$BoardFlag);
$sql_exam->bindParam('bid',$BoardIDOB);
$sql_exam->bindParam('pyear',$PreviousYearOB);
$sql_exam->bindParam('pexam',$PreviousExamCodeOB);
$sql_exam->bindParam('prno',$PreviousRollNoOB);
$sql_exam->bindParam('rand',$Random);
$sql_exam->bindParam('regyear',$AdmYear);
$sql_exam->bindParam('sip',$IPAddress);
$sql_exam->bindParam('trid',$TrackingID);
$sql_exam->bindParam('ProposedCenter',$ProposedCenter);
$sql_exam->bindParam('usid',$UserID);
$sql_exam->execute();

if($sql_exam->rowCount() > 0){

$sql_s = $DbConn_Adm_Mysql->prepare("INSERT INTO matric_student_subjects (LINK_ID_ADM,EnrollNo,Year,ExamCode,SubjectCode,AckFlag)
							Values (:lida,:enr,:year,:excode,:scode,:ack)");

if($AdmissionClass == 9){
for($j=1; $j<=8; $j++){ 
$Subject = 'Subject'.$j;

$SubjectCode = $$Subject;

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCode);
$sql_s->bindParam('ack',$AckFlag);

$sql_s->execute();
	
	}
}elseif($AdmissionClass == 10){
for($j=1; $j<=8; $j++){ 
$Subject = 'Subject'.$j;

$SubjectCode = $$Subject;

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$SubjectCode);
$sql_s->bindParam('ack',$AckFlag);
$sql_s->execute();
}
if(isset($_POST['SubjectsNinth']) AND strlen($_POST['SubjectsNinth']) > 0){
$NinthSparated	=	explode(",",$SubArrNinth);	
$NinthSubText	= 'Subject';
foreach($NinthSparated as $ninth){
	$NinthSub = $NinthSubText.$ninth;
	$NinthSubCodes = str_pad(($$NinthSub-1), 2, 0, STR_PAD_LEFT);

$sql_s->bindParam('lida',$LinkIDADM);
$sql_s->bindParam('enr',$EnrollNo);
$sql_s->bindParam('year',$AdmYear);
$sql_s->bindParam('excode',$AdmExamCode);
$sql_s->bindParam('scode',$NinthSubCodes);
$sql_s->bindParam('ack',$AckFlag);
$sql_s->execute();


}
}
	}
//print_r($sql_s->errorInfo());
if($sql_s->rowCount() > 0){
$SavedFinal = 1;
$sql_update_rec = $DbConn_Online_Mysql->prepare("Update bisep_student_receipts
				SET Saved = ?,
				ChildReceiptNo = ?
				Where RegYear = ?
				AND InstituteCode = ?
				AND TrackingID = ?
				AND ReceiptNo = ?
				AND Saved = 0
				AND ReceiptType = ?");
$sql_update_rec->bindParam(1,$SavedFinal);
$sql_update_rec->bindParam(2,$ReceiptNo);
$sql_update_rec->bindParam(3,$AdmYear);
$sql_update_rec->bindParam(4,$SchoolCode);
$sql_update_rec->bindParam(5,$TrackingID);
$sql_update_rec->bindParam(6,$ReceiptNo);
$sql_update_rec->bindParam(7,$ReceiptType);
$sql_update_rec->execute();
if($sql_update_rec->rowCount() > 0){
$Saved = 1;
$sql_pic = $DbConn_Online_Mysql->prepare("Update bisep_student_images SET Saved = ?
							   WHERE image_name = ?
							   AND School_Code = ?
							   AND Reg_Year = ?
							   AND ClassID = 9
							   AND ExamStatus = 2");
$sql_pic->bindParam(1,$Saved);
$sql_pic->bindParam(2,$ImageName);
$sql_pic->bindParam(3,$SchoolCode);
$sql_pic->bindParam(4,$AdmYear);
$sql_pic->execute();
if($sql_pic->rowCount() > 0){
	
$sql_request_update = $DbConn_Adm_Mysql->prepare("Update online_admission_requests SET Name = ?,
							   FatherName = ?,
							   DateOfBirth = ?
							   Where Session = ?
							   AND Year = ?
							   AND EnrollNo = ?
							   AND ClassID = 1
							   AND EligibleFor = 30");
$sql_request_update->bindParam(1,$StudentName);
$sql_request_update->bindParam(2,$FatherName);
$sql_request_update->bindParam(3,$dobfinal);
$sql_request_update->bindParam(4,$AdmSession);
$sql_request_update->bindParam(5,$AdmYear);
$sql_request_update->bindParam(6,$EnrollNo);
$sql_request_update->execute();	
	
	
$DbConn_Adm_Mysql->commit();

$_SESSION['VerificationCode']	= $GetVerCode;

echo 'Successfully Saved!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	

$MSG = 'Please download and deposit the required Fee on Receipt No: '.$ReceiptNo.'. Submit Admission Form and Receipt in Board Office
Note: Manual Receipt is not acceptable';

// SEND SMS STARTS
$CheckSendSMS = SendSMS($CombineMobile,$MSG);
// SEND SMS ENDS

}
}
}else{
$DbConn_Adm_Mysql->rollBack();	
echo 'Subject Insertion Problem Please Notify Administrative!';	

	}
}else{
echo 'Matric Exam Insertion Problem Please Try Again...';	
	}

}else{$DbConn_Adm_Mysql->rollBack();}
}else{$DbConn_Adm_Mysql->rollBack();}

}else{
echo 'Duplicate Admission Found Or You have Clicked 2 Time!';		
echo '<input name="Response" id="Response" type="hidden" value="1" >';	
	}

}else{echo '<span style="font-weight:bold;color:red;">Please Contact To Board Office .(ERROR:FP)!3</span>';}

}else{
   echo '<span style="font-family:bold;color:red;">Optional Subject shall not be the same</span>';		
		}
	}else{
   echo '<span style="font-family:bold;color:red;">Please Select Optional Subject!</span>';		
		}

}else{
echo 'Matric Exam Admission Link Selection Problem Please Try Again...';	
	}
//$DbConn->rollBack();
	}else{
echo 'Student Info Problem!';		
		}
}else{
   echo '<span style="font-family:bold;color:red;">Please Contact To Board Office!</span>';		
		}
	
}else{
   echo '<span style="font-family:bold;color:red;">Please Upload Picture!</span>';		
		}

}else{
   echo '<span style="font-family:bold;color:red;">Group Code is invalid, Please Contact To Computer Cell Section!</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Proposed Center Is Invalid, Please do not use special charecters</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Permanent Address is invalid</span>';		
	}
}else{
   echo '<span style="font-family:bold;color:red;">Permanent Address is invalid</span>';		
	}

}else{
   echo '<span style="font-family:bold;color:red;">Mobile Format is Invalid.</span>';	
	}
if(isset($_POST['FormB'])){
}else{
   echo '<span style="font-family:bold;color:red;">Form(B) Format is Invalid.</span>';	
	}
}
}else{
   echo '<span style="font-family:bold;color:red;">Nic Format is Invalid.</span>';	
	}
}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}
//DOB Validation ENDS
}else{
   echo '<span style="font-family:bold;color:red;">Father Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}

}else{
   echo '<span style="font-family:bold;color:red;">Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}


}else{
		echo 'Gender Code is Invalid!';
		exit();
		}		}else{
		echo 'Religion Code is Invalid!';
		exit();
		}	
	}else{
		echo 'District Code is Invalid!';
		exit();
		}
	
	}else{
		echo 'Province Code is Invalid!';
		exit();
		}
	}else{
		echo 'Country Code is Invalid!';
		exit();
		}
}else{
		echo 'Board ID is Invalid!';
		exit();
		}
}else{
		echo 'Class Code is Invalid!';
		exit();
		}			

}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check (ERR-II).</span>';	
	}


}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}

}else{
	echo '<span style="color:red;">Please Select Subjects! Admission Class , Board Name etc</span>';
	}
		}
}

