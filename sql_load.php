<?php
require_once("../../_includefiles/DBConnection_Adm_Mysql.php");
require_once("../functions.php"); 
$IPAddress = $_SERVER['REMOTE_ADDR'];

$AdmSession =get_config("AdmSession");
$AdmYear = get_config("AdmYear");
$ClassID	= 1;
if ($AdmSession == 1) {$AdmExamCode = '1,2';} elseif($AdmSession == 2) {$AdmExamCode = '3,5';}

if(isset($_GET['mode']) AND $_GET['mode'] == 'SearchData'){
if(isset($_GET['module']) AND $_GET['module'] == 'Admission'){
$Year 			= $_POST['Year'];
$Session 		= $_POST['Session'];
$RollNo 		= $_POST['RollNo'];
$DateOfBirth 	= $_POST['DateOfBirth'];

//echo $DOB_Oracle 	= date("d-M-Y",strtotime($DateOfBirth));
//exit();
$ApplyMobileNo = $_POST['ApplyMobileNo'];
if ($Session == 1) {$SearchExamCode = '1,2';} elseif ($Session == 2) {$SearchExamCode = '3,5';}
        
        ?>
        <link rel="stylesheet" href="../../Ionicons/css/ionicons.min.css">
        <?php

        if (isset($_POST['Year']) && $_POST['Year'] > 0 && isset($_POST['Session']) && $_POST['Session'] > 0 && isset($_POST['RollNo']) && $_POST['RollNo'] > 0) {
if (isset($_POST['ApplyMobileNo']) && $_POST['ApplyMobileNo'] > 0){
if(preg_match('/^\d{4}-\d{7}$/',$ApplyMobileNo)){

            $sql_cell_check_rollno = $DbConn_Adm_Mysql->prepare("Select Count(*) As Found 
            from student_guzzet g , student_exam me , student_info si 
            Where g.ClassID = me.ClassID
            AND g.Year = me.Year
            and g.Session=me.Session
            AND g.RollNo = me.RollNo
            AND g.ExamCode = me.ExamCode
			AND me.EnrollNo = si.EnrollNo
            AND me.ClassID = si.ClassID
			AND g.ClassID = :cid
            AND g.Year = :year
            AND g.ExamCode IN ($SearchExamCode)
            AND g.RollNo = :rollno
			AND DATE_FORMAT(si.DateOfBirth, '%d-%m-%Y') = :dob");
			
			
			$sql_cell_check_rollno->bindParam('cid',$ClassID);
			$sql_cell_check_rollno->bindParam('year',$Year);
			$sql_cell_check_rollno->bindParam('rollno',$RollNo);
			$sql_cell_check_rollno->bindParam('dob',$DateOfBirth);
			$sql_cell_check_rollno->execute();
		
            $CellDataCheckRollNo = $sql_cell_check_rollno->fetch(PDO::FETCH_OBJ);
            
            if($CellDataCheckRollNo->Found == 1) {
                
                
            $sql_cell_check_enrollno = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.DateOfBirth,g.Year,
            g.ExamCode,g.RollNo
            From student_guzzet g , student_exam me , student_info si 
            Where g.ClassID = me.ClassID
            AND g.Year = me.Year
            and g.Session=me.Session
            AND g.RollNo = me.RollNo
            AND g.ExamCode = me.ExamCode
			AND me.EnrollNo = si.EnrollNo
            AND me.ClassID = si.ClassID
			AND g.ClassID = :cid
            AND g.Year = :year
            AND g.ExamCode IN ($SearchExamCode)
            AND g.RollNo = :rollno");
			$sql_cell_check_enrollno->bindParam('cid',$ClassID);
			$sql_cell_check_enrollno->bindParam('year',$Year);
			$sql_cell_check_enrollno->bindParam('rollno',$RollNo);
			$sql_cell_check_enrollno->execute();
                $CellDataCheckEnrollNo = $sql_cell_check_enrollno->fetch(PDO::FETCH_OBJ);
                $FetchedEnrollNo = $CellDataCheckEnrollNo->EnrollNo;
//Check Current Exam Status
$CheckMigration			= get_migration_status($FetchedEnrollNo);
if($CheckMigration == 0){


$CheckAdmission 		= check_adm_status($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($CheckAdmission == 1){
$CheckAdmissionPrivate  = check_adm_status_private($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($CheckAdmissionPrivate == 1){

$sql_batch = $DbConn_Adm_Mysql->prepare("SELECT si.Gender,si.Name,si.FatherName,me.GroupCode,me.EnrollNo,me.ExamCode,r.ReceiptNo,r.TotalFee,me.Received,me.SaveTimestamp,me.Received_Timestamp,oar.VerificationCode
FROM matric_student_info si , matric_student_exam me , bisep_student_receipts r , online_admission_requests oar
WHERE si.LINK_ID_ADM = me.LINK_ID_ADM
AND si.EnrollNo = me.EnrollNo
AND me.EnrollNo = oar.EnrollNo
AND me.Session = oar.Session
AND me.Year = oar.Year
AND me.ReceiptNo = r.ReceiptNo
AND oar.ClassID = :cid
AND me.Session = :sess
AND me.Year = :year
AND me.EnrollNo = :enr");
$sql_batch->bindParam('cid',$ClassID);	
$sql_batch->bindParam('sess',$AdmSession);	
$sql_batch->bindParam('year',$AdmYear);	
$sql_batch->bindParam('enr',$FetchedEnrollNo);	
$sql_batch->execute();

$FetchData = $sql_batch->fetch(PDO::FETCH_OBJ);
if(isset($_SESSION['VerificationCode']) AND  $_SESSION['VerificationCode'] == $FetchData->VerificationCode){
	
?>
<div class="box-body table-responsive no-padding" >

<table id="DataGrid" class="table table-bordered table-striped gridtable">
                <thead>
                <tr>
                  <td><strong>Receipt No</strong></td>
                  <td><strong>Class</strong></td>
                  <td><strong>Group</strong></td>
                  <td><strong>Name</strong></td>
                  <td><strong>Father Name</strong></td>
                  <td align="left"><strong>Total Fee</strong></td>
                  <td><div align="center"><strong>Form Status</strong></div></td>
             <!--     <td ><div align="center"><strong>Challan</strong></div></td> -->
                  <td ><div align="center"><strong>Print Form</strong></div></td>
                  </tr>
                </thead>


<tr>
                  <td height="73"><div align="center"><strong><?php echo $FetchData->ReceiptNo; ?></strong></div></td>
                  <td><div align="center">
                    <strong>
                    <?php if($FetchData->ExamCode == 1 OR $FetchData->ExamCode == 5){echo '9TH';}elseif($FetchData->ExamCode == 2 OR $FetchData->ExamCode == 3){echo '10TH';} ?>
                  </strong></div></td>
                  <td><div align="center">
                    <strong>
                    <?php if($FetchData->GroupCode == 1){echo 'Science';}elseif($FetchData->GroupCode == 2){echo 'HUMANITIES';} ?>
                  </strong>                  </div></td>
                  <td><div align="left"><strong><?php echo $FetchData->Name; ?></strong>
                  </div></td>
                  <td><div align="left"><strong><?php echo $FetchData->FatherName; ?></strong>
    </div></td>
                  <td align="left"><div align="center"><strong><?php echo $FetchData->TotalFee; ?></strong>
                  </div></td>
                  <td><div align="center"><strong><?php if($FetchData->Received == 1){ ?>
                  <span class="label label-primary">Received</span>
                  <?php }else{ ?>
                  <span class="label label-danger">Pending</span>
                  <?php } ?></strong></div></td>
                  <?php /* <td><form style="margin-bottom:0;" target="_blank" action="print_receipt.php?mode=PrintChallan&module=Admission&ID=<?php echo $FetchData->ReceiptNo;?>&SecKey=<?php echo md5($FetchData->ReceiptNo+1026); ?>" method="post">
                      <div align="center">
                        <input type="image"  style="cursor: pointer;" id="PrintReceipt" src="../../images/download2.gif" width="150" height="24" title="View">
                        <input type="hidden" name="ReceiptNo" value="<?php echo $FetchData->ReceiptNo;?>" >
                      </div>
                    </form></td> */ ?>
                  <td><form style="margin-bottom:0;" target="_blank" action="FinalPrint.php?mode=PrintReport&module=Enrollment&ID=<?php echo $FetchData->ReceiptNo;?>&SecKey=<?php echo md5($FetchData->ReceiptNo+1026); ?>" method="post">
                    <div align="center">
                      <input type="image" id="PrintForm"  style="cursor: pointer;" src="../../images/download.gif" width="150" height="24" title="View">
                      <input type="hidden" name="ReceiptNo" value="<?php echo $FetchData->ReceiptNo;?>" >
                      </div>
                  </form></td>
  </tr>
 <tr>
  <td height="21" colspan="8">&nbsp;</td>
  </tr>
<tr>
  <td height="73" colspan="8" align="center"><img src="../../images/PrivateFormSubmission.jpg" /></td>
  </tr>
</table>
</div>
<?php 
}else{
	?>
     <div class="row">
                                            <div class="col-sm-3">
                    <label>Verify Verification Code:</label>
                    <input type="text" class="form-control" id="VerificationCode" name="VerificationCode" >
                </div>
                                            <div class="col-sm-3">
                    <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control" onClick="VerifyCode('<?php echo $FetchedEnrollNo; ?>');">Verify Verification Code ?</button>
                        </div>
                        
                        
               <div class="col-sm-6">
                    <label>&nbsp;</label>
                    <div id="VerificationMsg">
                    </div>
                    </div>     
                </div>
                <!--added by aziz-->
                        <br><br>
                        <div >
                        <strong><a href="http://portal.biseb.edu.pk/biseb_online_admission/verification_code_search.php">If you are not getting sms, click here</a><strong>
                        </div>
    <?php
	}
}else{
	echo 'Regular Admission Found!';	
}
}else{
// Get Max Exam (Last Exam)
$sql_cell_check_maxexam = $DbConn_Adm_Mysql->prepare("Select g.EnrollNo,g.Year,g.ExamCode,g.RollNo,g.Result_Status,g.Remarks
from student_guzzet g
where g.ClassID = :cid
AND g.EnrollNo = :enr
AND g.Year = (Select Max(gg.Year) From student_guzzet gg 
Where gg.ClassID = g.ClassID AND gg.EnrollNo = g.EnrollNo)
AND g.ExamCode = (Select Max(gg.ExamCode)
From student_guzzet gg 
Where gg.ClassID = g.ClassID AND gg.EnrollNo = g.EnrollNo AND g.Year = gg.Year)");
$sql_cell_check_maxexam->bindParam('cid',$ClassID);
$sql_cell_check_maxexam->bindParam('enr',$FetchedEnrollNo);
$sql_cell_check_maxexam->execute();
//print_r($sql_cell_check_maxexam->errorInfo());
                $CellDataMaxExam = $sql_cell_check_maxexam->fetch(PDO::FETCH_OBJ);
                $MaxYear = $CellDataMaxExam->Year;
                $MaxExamCode= $CellDataMaxExam->ExamCode;
                $MaxRollNo  = $CellDataMaxExam->RollNo;
                $MaxRemarks = @$CellDataMaxExam->Remarks;
				$RandThree 	= rand(100,999);
				$TrackingID = $AdmYear.$ClassID.$AdmSession.$Year.$MaxExamCode.$MaxRollNo;
				$Guzzet_Result_Status = $CellDataMaxExam->Result_Status;
$CheckResultStatus 		  = get_result_status($MaxYear,$MaxExamCode,$MaxRollNo);
//if 0 then ok
if($CheckResultStatus == 0){				
                if ($MaxExamCode == 1 OR $MaxExamCode == 5) {

$sql_cell_check_ninth_remarks = $DbConn_Adm_Mysql->prepare("Select Count(*) As EligibleNinth
From student_guzzet g
Where g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno
-- AND g.Result_Status IN (5,6,9,13)
");
$sql_cell_check_ninth_remarks->bindParam('cid',$ClassID);
$sql_cell_check_ninth_remarks->bindParam('year',$MaxYear);
$sql_cell_check_ninth_remarks->bindParam('exam',$MaxExamCode);
$sql_cell_check_ninth_remarks->bindParam('rollno',$MaxRollNo);
$sql_cell_check_ninth_remarks->execute();
$CellDataCheckNinthRemarks = $sql_cell_check_ninth_remarks->fetch(PDO::FETCH_OBJ);

// Check Group Code

$sql_cell_check_ninth_group_code = $DbConn_Adm_Mysql->prepare("Select e.GroupCode
From student_exam e
Where e.ClassID = :cid
AND e.Year = :year
AND e.ExamCode = :exam
AND e.RollNo = :rollno");
$sql_cell_check_ninth_group_code->bindParam('cid',$ClassID);
$sql_cell_check_ninth_group_code->bindParam('year',$MaxYear);
$sql_cell_check_ninth_group_code->bindParam('exam',$MaxExamCode);
$sql_cell_check_ninth_group_code->bindParam('rollno',$MaxRollNo);
$sql_cell_check_ninth_group_code->execute();
$PreviousGroupCodeForOLDData = $sql_cell_check_ninth_group_code->fetch(PDO::FETCH_OBJ);

if($PreviousGroupCodeForOLDData->GroupCode == 2){$AdmYearLastUpto = 8;}else{$AdmYearLastUpto = 4;}

if ($CellDataCheckNinthRemarks->EligibleNinth == 1 OR $MaxYear < ($AdmYear-$AdmYearLastUpto)) { // 8 chances (4 year)
// Check Regular Status // again here

                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();
$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 1; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
<?php
$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");

	}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two.php");

}else{
include("../../_includefiles/get_form_body_three.php");

 ?>                            
                                
                            
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Group ( &#1711;&#1585;&#1608;&#1662; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="17" name="GroupCode" id="GroupCode" onChange="FetchSubjects();" class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>
                                                    <?php if($PrevGroupCode == 1){?>
                                                    <option value="1">SCIENCE</option>
                                                    <?php } ?>
                                                    <option value="2">HUMANITIES</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <div class="row">
                                    <div class="col-md-4">

                                        <div id="Subjects" align="center">         
                                            <div align="center"><span style="color:red;">--Please Select Group Name --</span>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              
<table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}

                        } else {
//AppearFlag = 2;
// Fresh 10th Eligible
$FetchedEnrollNoRegularCheck = ltrim($FetchedEnrollNo, '0');;
$sql_cell_check_tenth_regular = $DbConn_Adm_Mysql->prepare("SELECT SUM(t.RegularTenth) as RegularTenth FROM(
Select Count(*) As RegularTenth
From bisep_student_info si
Where si.EnrollNo = :enr
AND si.GroupCode = 1
UNION ALL
SELECT COUNT(*) AS RegularTenth
FROM enrollment si
WHERE si.Enrol_No = :enr
AND si.Group_Code = 1) 
t");
$sql_cell_check_tenth_regular->bindParam('enr',$FetchedEnrollNoRegularCheck);
$sql_cell_check_tenth_regular->execute();
$CellDataCheckNinthRegular = $sql_cell_check_tenth_regular->fetch(PDO::FETCH_OBJ);

if ($CellDataCheckNinthRegular->RegularTenth == 0) {
// Copied 


// Check Regular Status

                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();
$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 2; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 1; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");

	}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two.php");

}else{

include("../../_includefiles/get_form_body_three.php");

 ?>                            
                                
          
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine,LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(SELECT LPAD(ss.SubjectCode,2,0) 
FROM student_subjects ss
WHERE ss.SubjectCode = s.SubjectCode
AND ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = 1
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in(01,05,09,13)
 AND (ss.THEORY_PAPER_STATUS < 1 OR ss.THEORY_PAPER_STATUS = 2)
) AS Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
-- and sn.SubjectCode not in(01,05,09,13)
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :ecode
AND s.RollNo = :rno
ORDER BY s.SubjectCode");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('ecode',$MaxExamCode);
$SubjQuery->bindParam('rno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        

//Copied Ends



}else{
 echo '<span style="color:red;font-weight:bold">Student is Not Eligible To Apply Private. Please Apply as a Regular (ERR:10TH)</span>';	
	}


                        }

                
				}else{
					
$sql_cell_check_appear_code = $DbConn_Adm_Mysql->prepare("Select e.AppearCode
From student_exam e
Where e.ClassID = :cid
and EnrollNo=:enrol

");
$sql_cell_check_appear_code->bindParam('cid',$ClassID);
$sql_cell_check_appear_code->bindParam('enrol',$FetchedEnrollNo);
$sql_cell_check_appear_code->execute();
//$appear_code_count=0;
$PreviousAppearCode = $sql_cell_check_appear_code->fetch(PDO::FETCH_OBJ);
					if($PreviousAppearCode->AppearCode == 2 AND ($Guzzet_Result_Status == 4 OR $Guzzet_Result_Status == 9 OR $Guzzet_Result_Status == 11 OR $Guzzet_Result_Status == 20)){
						//echo 'OK WILL APPEAR';

/*$sql_check_improvment = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) AS Found FROM student_info si , student_exam se , student_guzzet g 
WHERE si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno
AND ((se.AppearCode = 2 AND g.Result_Status NOT IN (10,11,20)) OR se.AppearCode = 3)");
$sql_check_improvment->bindParam('cid',$ClassID);  
$sql_check_improvment->bindParam('year',$MaxYear);  
$sql_check_improvment->bindParam('exam',$MaxExamCode);  
$sql_check_improvment->bindParam('rollno',$MaxRollNo);  
$sql_check_improvment->execute();*/

$sql_check_improvment = $DbConn_Adm_Mysql->prepare("
SELECT COUNT(AppearCode) as Found FROM student_exam WHERE ClassID=:cid
and EnrollNo=:enrol
and AppearCode=2

HAVING COUNT(AppearCode)<4
");
$sql_check_improvment->bindParam('cid',$ClassID); 
$sql_check_improvment->bindParam('enrol',$FetchedEnrollNo); 
$sql_check_improvment->execute();


$FetchCheckImrovment = $sql_check_improvment->fetch(PDO::FETCH_OBJ);
if($FetchCheckImrovment->Found <4){

$CheckNextAdmissionStatus = get_next_admission_status($MaxYear,$MaxExamCode,$MaxRollNo);
$CheckResultStatus 		  = get_result_status($MaxYear,$MaxExamCode,$MaxRollNo);

if($CheckNextAdmissionStatus == 0){	
if($CheckResultStatus == 0){	

// Eligible For Marks Improvement

		
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 8; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 2; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");

	}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two.php");

}else{

include("../../_includefiles/get_form_body_three.php");

 ?>                            


<div class="row">
                                    <div class="col-md-4">
                                      <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("
SELECT sn.SubjectName, LPAD((s.SubjectCode),2,0) as SubjectCode 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in (01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
ORDER BY s.SubjectCode");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                        <label>SUBJECTS ( <span style="color:red;font-weight:bold;">IMPROVEMENT</span>)</label>
                                        <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  <td bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Papers Selection</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td height="22" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>9th/</strong><strong>10th</strong></div></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->SubjectCode; ?>"><?php echo $FetchSubjects->SubjectName; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                                      <div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->SubjectCode), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"> <div align="center">
                      <input type="checkbox" name="SubjectCode" id="SubjectCode"  value="<?php echo $FetchSubjects->SubjectCode; ?>" >
                  </div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);

echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div>                                
                                 

                                <?php
}}

}else{
// Observation Found Result Status 14,15,16
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase OBSERVATION (RL-DOC/UFM/Fine etc) Found Please Contact To Board Office!</span>';	
	}    							

}else{
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase Next Admission Record Found i.e (F.A/FSC)</span>';		
	}




}else{
echo '<span style="font-weight:bold;color:red;">Already Applied Last Time For Marks Improvment OR Additional Subjects 1</span>';		
	}


						
						}else{
// Attempted 10th
// AppearFlag = 3
// Check if Failed Paper's Found 
$sql_check_tenth_pass = $DbConn_Adm_Mysql->prepare("SELECT COUNT(s.SubjectCode) AS Found
FROM student_subjects s
WHERE s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)"); 
$sql_check_tenth_pass->bindParam('cid',$ClassID);  
$sql_check_tenth_pass->bindParam('year',$MaxYear);  
$sql_check_tenth_pass->bindParam('exam',$MaxExamCode);  
$sql_check_tenth_pass->bindParam('rollno',$MaxRollNo);  
$sql_check_tenth_pass->execute();
$CheckTenthPass = $sql_check_tenth_pass->fetch(PDO::FETCH_OBJ); 
 if($CheckTenthPass->Found > 0){
// Failed Papers Found

// Next Check Compartment

$sql_check_failed_ninth_subjects = $DbConn_Adm_Mysql->prepare("SELECT COUNT(s.SubjectCode) AS Ninth
FROM student_subjects s
WHERE s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)
AND MOD(s.SubjectCode,2) <> 0"); 
$sql_check_failed_ninth_subjects->bindParam('cid',$ClassID);  
$sql_check_failed_ninth_subjects->bindParam('year',$MaxYear);  
$sql_check_failed_ninth_subjects->bindParam('exam',$MaxExamCode);  
$sql_check_failed_ninth_subjects->bindParam('rollno',$MaxRollNo);  
$sql_check_failed_ninth_subjects->execute();
  
$CheckNinthFailedSubject 	= 	$sql_check_failed_ninth_subjects->fetch(PDO::FETCH_OBJ); 
$CheckFailedNinthSubjects 	=	$CheckNinthFailedSubject->Ninth;


$sql_check_failed_tenth_subjects = $DbConn_Adm_Mysql->prepare("SELECT COUNT(s.SubjectCode) AS Tenth
FROM student_subjects s
WHERE s.ClassID = :cid
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2)
AND MOD(s.SubjectCode,2) = 0"); 
$sql_check_failed_tenth_subjects->bindParam('cid',$ClassID);  
$sql_check_failed_tenth_subjects->bindParam('year',$MaxYear);  
$sql_check_failed_tenth_subjects->bindParam('exam',$MaxExamCode);  
$sql_check_failed_tenth_subjects->bindParam('rollno',$MaxRollNo);  
$sql_check_failed_tenth_subjects->execute();
  
$CheckTenthFailedSubject 	= $sql_check_failed_tenth_subjects->fetch(PDO::FETCH_OBJ); 
$CheckFailedTenthSubjects	= $CheckTenthFailedSubject->Tenth;

if($CheckFailedNinthSubjects <=3 AND $CheckFailedTenthSubjects <=3){
// Compartment Checked	
if($MaxYear < ($AdmYear-4)){
// 8 Chances 4 Years Availed
                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 7; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine,LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
 AND mod(s.SubjectCode,2) = 0
");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        	 
	 
 
	 }else{


$sql_check_failed = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) AS Found FROM student_guzzet g 
WHERE g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno
AND g.Result_Status IN (5,6,7,9,13)");
$sql_check_failed->bindParam('cid',$ClassID);  
$sql_check_failed->bindParam('year',$MaxYear);  
$sql_check_failed->bindParam('exam',$MaxExamCode);  
$sql_check_failed->bindParam('rollno',$MaxRollNo);  

$FetchCheckFailed = $sql_check_failed->fetch(PDO::FETCH_OBJ);
if($FetchCheckFailed->Found == 0){

$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->Group_Code;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 3; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 5; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");

	}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two.php");

}else{

include("../../_includefiles/get_form_body_three.php");

 ?>                            

                                <div class="row">
                                    <div class="col-md-4">
                                      <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectName, LPAD((s.SubjectCode),2,0) AS SubjectCode 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
-- and s.SubjectCode not in (01,05,09,13)
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

 ?>

                                        <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th/10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->TENTH; ?>"><?php echo $FetchSubjects->SubjectName; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->SubjectCode), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        

//Copied Ends
}else{
// 9th or 10th Compart But Remarks Failed
echo 'Please Contact To Board Office (ERROR:CF)';	
	}
}}elseif($CheckFailedNinthSubjects > 3 AND $CheckFailedTenthSubjects > 3){

if($MaxYear < ($AdmYear-4)){
// 8 Chances 4 Years Availed
                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 7; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth  ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        	 
	 
 
	 }else{

//Case- I if Part-I and Part-II Both are Failed

// Failed Subjects
// AppearCode = 4
// AppearFlag  = 4
	

                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->Group_Code;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 4; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        

//Copied Ends



	
	
	
	

	
}}elseif($CheckFailedNinthSubjects < 3 AND $CheckFailedTenthSubjects > 3){
if($MaxYear < ($AdmYear-4)){
// 8 Chances 4 Years Availed
                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 7; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        	 
	 
 
	 }else{

//Case- II if Part-I is Not Full Failed and Part-II is Failed

// Failed Subjects
// AppearCode = 4
// AppearFlag  = 5
	
                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->Group_Code;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 5; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND (ss.THEORY_PAPER_STATUS < 1 OR ss.THEORY_PAPER_STATUS = 2)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);

echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        

//Copied Ends



	
	
	
	

	
}}elseif($CheckFailedNinthSubjects > 3 AND $CheckFailedTenthSubjects <= 3){
//Case- III if Part-I is Full Failed and Part-II is Not Full Failed

// Failed Subjects
// AppearCode = 4
// AppearFlag  = 6
	
if($MaxYear < ($AdmYear-4)){
// 8 Chances 4 Years Availed
                            
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 7; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("SELECT sn.SubjectNameCombine, LPAD((s.SubjectCode + 1),2,0) AS Tenth ,
(Select ss.SubjectCode from student_subjects ss
where ss.ClassID = s.ClassID
AND ss.Year = s.Year
AND ss.ExamCode = s.ExamCode
AND ss.RollNo = s.RollNo
-- and ss.SubjectCode not in (01,05,09,13)
AND ss.SubjectCode = (s.SubjectCode-1)
AND mod(ss.SubjectCode,2) <> 0) As Ninth
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
AND s.Year = :year
-- and s.SubjectCode not in(01,05,09,13)
AND s.ExamCode = :exam
AND s.RollNo = :rollno
AND mod(s.SubjectCode,2) = 0");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                            <label>SUBJECTS</label>
                                          <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td width="11%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>9th</strong></td>
                  <td width="13%" height="22" bgcolor="#FFFFFF" class="BorderClass"><strong>10th</strong></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->Tenth; ?>"><?php echo $FetchSubjects->SubjectNameCombine; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                    <div align="center"><strong>
                      <?php 

if(isset($FetchSubjects->Ninth)){
echo $FetchSubjects->Ninth;
echo '<input type="hidden" name="SubjectsNinth[]" value="'.@$FetchSubjects->Ninth.'">';
}
?>
                      </strong>
                    </div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->Tenth), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);
echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        	 
	 
 
	 }else{                     
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 6; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 4; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");


}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);
	
include("../../_includefiles/get_message_send_confirm_two.php");	
	
}else{
	
include("../../_includefiles/get_form_body_three.php");
	
 ?>                            
                                
                               

                                <div class="row">
                                    <div class="col-md-4">
                                      <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("
SELECT s.SubjectCode , sn.SubjectName 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
-- and s.SubjectCode not in (01,05,09,13)
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
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

//AND (s.THEORY_PAPER_STATUS < 1 OR s.THEORY_PAPER_STATUS = 2) in one part

 ?>

                                        <label>SUBJECTS</label>
                                        <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td height="22" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>9th/</strong><strong>10th</strong></div></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->SubjectCode; ?>"><?php echo $FetchSubjects->SubjectName; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                                      <div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->SubjectCode), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);

echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 

                                <?php
}}
							
                        

//Copied Ends



	
	
	
	

	
}}



				}else{
//10 Passed Students 
//echo 'Eligibility For 10th Improvment';		
					

// Improvment Portion

// Next Check Failed?

// Compartment Checking	

$sql_check_failed = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) AS Found FROM student_guzzet g 
WHERE g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno
AND g.Result_Status IN (5,6,7,9,13)");
$sql_check_failed->bindParam('cid',$ClassID);  
$sql_check_failed->bindParam('year',$MaxYear);  
$sql_check_failed->bindParam('exam',$MaxExamCode);  
$sql_check_failed->bindParam('rollno',$MaxRollNo);  
$sql_check_failed->execute();
$FetchCheckFailed = $sql_check_failed->fetch(PDO::FETCH_OBJ);
if($FetchCheckFailed->Found == 0){

$sql_check_improvment = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) AS Found FROM student_info si , student_exam se , student_guzzet g 
WHERE si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno
AND ((se.AppearCode = 2 AND g.Result_Status NOT IN (10,11)) OR se.AppearCode = 3)");
$sql_check_improvment->bindParam('cid',$ClassID);  
$sql_check_improvment->bindParam('year',$MaxYear);  
$sql_check_improvment->bindParam('exam',$MaxExamCode);  
$sql_check_improvment->bindParam('rollno',$MaxRollNo);  
$sql_check_improvment->execute();

$FetchCheckImrovment = $sql_check_improvment->fetch(PDO::FETCH_OBJ);
if($FetchCheckImrovment->Found >=0){
	
$sql_check_improvment_old = $DbConn_Adm_Mysql->prepare("SELECT COUNT(*) AS Found FROM student_info si , student_exam se , student_guzzet g 
WHERE si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.ClassID = :cid
AND se.EnrollNo = :enr
AND ((se.AppearCode = 2 AND g.Result_Status NOT IN (10,11)) OR se.AppearCode = 3)");
$sql_check_improvment_old->bindParam('cid',$ClassID);  
$sql_check_improvment_old->bindParam('enr',$FetchedEnrollNo);  
$sql_check_improvment_old->execute();

$FetchCheckImrovmentOld = $sql_check_improvment_old->fetch(PDO::FETCH_OBJ);
if($FetchCheckImrovmentOld->Found >= 0){

$CheckNextAdmissionStatus = get_next_admission_status($MaxYear,$MaxExamCode,$MaxRollNo);
$CheckResultStatus 		  = get_result_status($MaxYear,$MaxExamCode,$MaxRollNo);

if($CheckNextAdmissionStatus == 0){	
if($CheckResultStatus == 0){	

// Eligible For Marks Improvement

		
$sql_cell_data = $DbConn_Adm_Mysql->prepare("Select si.EnrollNo,si.Name,si.FatherName,si.Gender,si.DateOfBirth,si.RegYear,si.FatherCNIC,
si.MobileNo,si.FormB,si.Identification_Mark,si.PostalAddress,si.PermanentAddress,si.Religion,
se.GroupCode,se.ExamStatus,se.SchoolCode,se.CountryCode,se.ProvinceCode,se.DistrictCode,g.Year,g.ExamCode,g.RollNo,g.Remarks
from student_info si , student_exam se, student_guzzet g 
where si.ClassID = se.ClassID
AND si.EnrollNo = se.EnrollNo
AND se.ClassID = g.ClassID
AND se.Year = g.Year
AND se.ExamCode = g.ExamCode
AND se.RollNo = g.RollNo
AND g.EnrollNo = si.EnrollNo
AND g.ClassID = :cid
AND g.Year = :year
AND g.ExamCode = :exam
AND g.RollNo = :rollno");
$sql_cell_data->bindParam('cid',$ClassID);
$sql_cell_data->bindParam('year',$MaxYear);
$sql_cell_data->bindParam('exam',$MaxExamCode);
$sql_cell_data->bindParam('rollno',$MaxRollNo);
$sql_cell_data->execute();

$FetchCellData = $sql_cell_data->fetch(PDO::FETCH_OBJ);
$PrevGroupCode = $FetchCellData->GroupCode;
                                if ($FetchCellData->GroupCode == 1) {
                                    $GroupName = 'SCIENCE';
                                } else {
                                    $GroupName = 'HUMANITIES';
                                }
                                if ($FetchCellData->Gender == 1) {
                                    $GenderName = 'MALE';
                                } elseif ($FetchCellData->Gender == 2) {
                                    $GenderName = 'FEMALE';
                                } elseif ($FetchCellData->Gender == 3) {
                                    $GenderName = 'Trans-Gender';
                                } else {
                                    $GenderName = 'Gender Not Set';
                                }

                                $DOB = $FetchCellData->DateOfBirth;

                                $dobtmp = explode("-", $DOB);
                                $dobfinal = $dobtmp[2] . '-' . $dobtmp[1] . '-' . $dobtmp[0];
                                $dobfinalfig = date("Y-m-d", strtotime($FetchCellData->DateOfBirth));
                                ?>
                                <input type="hidden" id="TrackingID" value="<?php echo $TrackingID; ?>">
                                <input type="hidden" id="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>">
                                <input type="hidden" id="MaxYear" value="<?php echo $MaxYear; ?>">
                                <input type="hidden" id="MaxExamCode" value="<?php echo $MaxExamCode; ?>">
                                <input type="hidden" id="MaxRollNo" value="<?php echo $MaxRollNo; ?>">
                                <input type="hidden" id="EligibleFor" value="<?php echo 8; ?>">
                                <input type="hidden" id="AppearCode" value="<?php echo 2; ?>">
                                <input type="hidden" id="GroupCode" value="<?php echo $FetchCellData->GroupCode; ?>">
                                
<?php

$CheckAdmRequest 		=	check_request_status($AdmSession,$AdmYear,$FetchedEnrollNo);

if($CheckAdmRequest == 0){

include("../../_includefiles/get_message_send_one.php");

	}else{   
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two.php");

}else{

include("../../_includefiles/get_form_body_three.php");

 ?>                            


<div class="row">
                                    <div class="col-md-4">
                                      <div class="form-group">
<?php $SubjQuery = $DbConn_Adm_Mysql->prepare("
SELECT sn.SubjectName, LPAD((s.SubjectCode),2,0) as SubjectCode 
FROM student_subjects s , bisep_subjects_name sn 
WHERE s.SubjectCode = sn.SubjectCode
AND s.ClassID = sn.Exam
AND s.ClassID = :cid
-- and s.SubjectCode not in (01,05,09,13)
AND s.Year = :year
AND s.ExamCode = :exam
AND s.RollNo = :rollno
ORDER BY s.SubjectCode");
$SubjQuery->bindParam('cid',$ClassID);
$SubjQuery->bindParam('year',$MaxYear);
$SubjQuery->bindParam('exam',$MaxExamCode);
$SubjQuery->bindParam('rollno',$MaxRollNo);
$SubjQuery->execute();


 ?>

                                        <label>SUBJECTS ( <span style="color:red;font-weight:bold;">IMPROVEMENT</span>)</label>
                                        <table width="347" border="1" class="rounded-border gridtable">
                <tr >
                  <td width="8%" rowspan="2" bgcolor="#FFFFFF"><strong>S#</strong></td>
                  <td width="68%" rowspan="2" bgcolor="#FFFFFF" ><div align="left"><strong>Subjects Name</strong></div></td>
                  <td height="11" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Codes</strong></div></td>
                  <td bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>Papers Selection</strong></div></td>
                  </tr>
                <tr class="rounded-border">
                  <td height="22" colspan="2" bgcolor="#FFFFFF" class="BorderClass"><div align="center"><strong>9th/</strong><strong>10th</strong></div></td>
                  </tr>
<?php 
$i=0;
while($FetchSubjects = $SubjQuery->fetch(PDO::FETCH_OBJ)) { 
$i++;
?>                
                <tr class="rounded-border">
                  <td height="30" bgcolor="#FFFFFF"><div align="center"><strong><?php echo $i; ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF"><select name="Subjects[]" class="form-control select"  style="width:100%;" >
                    <option value="<?php echo $FetchSubjects->SubjectCode; ?>"><?php echo $FetchSubjects->SubjectName; ?></option>
                  </select></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass">
                                      <div align="center"><strong><?php echo $SC = str_pad(($FetchSubjects->SubjectCode), 2, 0, STR_PAD_LEFT); ?></strong></div></td>
                  <td height="30" bgcolor="#FFFFFF" class="BorderClass"> <div align="center">
                      <input type="checkbox" name="SubjectCode" id="SubjectCode"  value="<?php echo $FetchSubjects->SubjectCode; ?>" >
                  </div></td>
                  </tr>
                
<?php } ?>                
              </table>  
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                
                                <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              

 
                                <table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <div id="preview" align="center" style="border:1px solid black;padding-top:10px;padding-bottom:10px;">
                            	  
                <?php
								$Rand = rand(1,1000);

echo '<img src="../getImage.php?ImageName='.$FetchedEnrollNo.'&Rand='.$Rand.'&SecKey='.md5($FetchedEnrollNo+51).'" class="preview" width="100px;" height="110px;" />';
		 ?></div>
                </td>
            </tr></table>
 </div></div></div>
                                
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinth();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div>                                
                                 

                                <?php
}}

}else{
// Observation Found Result Status 14,15,16
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase OBSERVATION (RL-DOC/UFM/Fine etc) Found Please Contact To Board Office!</span>';	
	}    							

}else{
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase Next Admission Record Found i.e (F.A/FSC)</span>';		
	}


}else{
echo 'Already Applied once for Marks Improvement OR Additional Subjects';		
	}

}else{
echo '<span style="font-weight:bold;color:red;">Already Applied Last Time For Marks Improvment OR Additional Subjects 2</span>';		
	}

}else{
// Failed Found
echo '<span style="font-weight:bold;color:red;">Please Contact To Board Office (ERROR:CF10)</span>';	
	}
 


									
					}

				}} }else{
// Observation Found Result Status 14,15,16
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase OBSERVATION (RL-DOC/UFM/Fine etc) Found Please Contact To Board Office!</span>';	
	}    							

			}
        
}else{
// Migration Found Board To Others
echo '<span style="font-weight:bold;color:red;">EnrollNo: <span style="color:blue">'.$FetchedEnrollNo.' </span>is not Eligible for Admission Becuase Board To Board / Board To University Migration Found!</span>';	
	}			
			} else {
// More Then One Record Found		
echo '<span style="font-weight:bold;color:red;">Provided Information is Invalid , Please Type required information as per DMC / Roll No Slip. </span>';	
            }
}else{echo '<span style="font-weight:bold;color:red;">Please Type Mobile No In Correct Format i.e (0333-1234567)</span>';}
       } else {
            echo '<span style="font-weight:bold;color:red;">Please Type Mobile No!</span>';
        } 
		} else {
            echo '<span style="font-weight:bold;color:red;">Please Select Year , Session And Type Roll No!</span>';
        }
    }else{    echo '<span style="font-weight:bold;color:red;">You Are Not Allowed to Do This Action Your ' . $IPAddress . ' has been Logged!</span>';
	}
} else {
}
// end of searchdata module

if (isset($_GET['mode']) AND $_GET['mode']=='LoadChangeMobileNo') /// Load Change Mobile No
{
if (isset($_GET['module']) AND $_GET['module']=='Admission') {
	 $EnrollNo 	= $_POST['EnrollNo'];
?>
<div class="row">
                                            <div class="col-sm-3">
                    <label>Type New Mobile No:</label>
                    <input type="text" class="form-control" id="ChangeMobileNo" name="ChangeMobileNo" >
                </div>
                                            <div class="col-sm-3">
                    <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control" onClick="ConfirmChangeMobileNo('<?php echo $EnrollNo; ?>');">Confirm Change Mobile No ?</button>
                        </div>
               <div class="col-sm-6">
                    <label>&nbsp;</label>
                    <div id="VerificationMsg">
                    </div>
                    </div>     
                </div>
<?php	
		}
}

// Load Private Fresh Data



if (isset($_GET['mode']) AND $_GET['mode'] == 'SaveData') { /// Employees Entry Form
    if (isset($_GET['module']) AND $_GET['module'] == 'Admission') {
$ApplicationType= $_POST['ApplicationType'];
$Name 			= strtoupper($_POST['Name']);
$FatherName 	= strtoupper($_POST['FatherName']);
$DateOfBirth 	= $_POST['DateOfBirth'];
$ApplyMobileNo 	= $_POST['ApplyMobileNo'];

$TrimZero = ltrim($ApplyMobileNo, '0');
$TrimDash = explode('-',$TrimZero);

$dobtmp 		= 	explode("-",$DateOfBirth);
$dobfinal		=	@$dobtmp[2].'-'.@$dobtmp[1].'-'.@$dobtmp[0];

$CombineMobile = '92'.@$TrimDash[0].@$TrimDash[1];       
        ?>
        <link rel="stylesheet" href="../../Ionicons/css/ionicons.min.css">
        <?php

if (isset($_POST['Name']) && !empty($_POST['Name']) && isset($_POST['FatherName']) && !empty($_POST['FatherName'])
&& isset($_POST['DateOfBirth']) && !empty($_POST['DateOfBirth'])) {
if (isset($_POST['ApplyMobileNo']) && $_POST['ApplyMobileNo'] > 0){
if(preg_match('/^\d{4}-\d{7}$/',$ApplyMobileNo)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$Name)){
if(preg_match('/^[a-zA-Z ]{4,50}$/',$FatherName)){ 

if(validateDate($DateOfBirth) == $DateOfBirth){
if($dobtmp[2] != 0 AND $dobtmp[1] != 0 AND $dobtmp[1] != 0 AND ($dobtmp[2] > 1970 AND $dobtmp[2] < 2010)){
$sql_mobile_check = $DbConn_Adm_Mysql->prepare("
Select Count(*) As Found 
From online_admission_requests 
Where ClassID = 1
AND Session = :sess
AND Year = :year
AND MobileNo = :mno
AND EligibleFor IN (20,30)");
$sql_mobile_check->bindParam('sess',$AdmSession);
$sql_mobile_check->bindParam('year',$AdmYear);
$sql_mobile_check->bindParam('mno',$CombineMobile);
$sql_mobile_check->execute();
$FetchMobileCheck = $sql_mobile_check->fetch(PDO::FETCH_OBJ);

if ($FetchMobileCheck->Found > 0) {
$sql_check_apply = $DbConn_Adm_Mysql->prepare("
Select *,Count(*) As Found 
From online_admission_requests 
Where ClassID = 1
AND Session = :sess
AND Year = :year
AND MobileNo = :mno
AND Name = :name
AND FatherName = :fname
AND DateOfBirth = :dob
AND EligibleFor IN (20,30)");
$sql_check_apply->bindParam('sess',$AdmSession);
$sql_check_apply->bindParam('year',$AdmYear);
$sql_check_apply->bindParam('mno',$CombineMobile);
$sql_check_apply->bindParam('name',$Name);
$sql_check_apply->bindParam('fname',$FatherName);
$sql_check_apply->bindParam('dob',$dobfinal);
$sql_check_apply->execute();
$FetchApplyCheck = $sql_check_apply->fetch(PDO::FETCH_OBJ);

if($FetchApplyCheck->Found > 0){
$FetchedEnrollNo = $FetchApplyCheck->EnrollNo;
$CheckAdmRequestVCode	=	check_request_verification_status($AdmSession,$AdmYear,$FetchedEnrollNo);
if($CheckAdmRequestVCode == 0){
$MobileNoCustom	=	get_request_mobile_no($AdmSession,$AdmYear,$FetchedEnrollNo);

include("../../_includefiles/get_message_send_confirm_two_two.php");

}else{

$CheckAdmissionPrivate  = check_adm_status_private($AdmSession,$AdmYear,$AdmExamCode,$FetchedEnrollNo);
if($CheckAdmissionPrivate == 1){

$sql_batch = $DbConn_Adm_Mysql->prepare("SELECT si.Gender,si.Name,si.FatherName,me.GroupCode,me.EnrollNo,me.ExamCode,r.ReceiptNo,r.TotalFee,me.AppearCode,me.Received,me.SaveTimestamp,me.Received_Timestamp,oar.VerificationCode
FROM matric_student_info si , matric_student_exam me , bisep_student_receipts r , online_admission_requests oar
WHERE si.LINK_ID_ADM = me.LINK_ID_ADM
AND si.EnrollNo = me.EnrollNo
AND me.EnrollNo = oar.EnrollNo
AND me.Session = oar.Session
AND me.Year = oar.Year
AND me.ReceiptNo = r.ReceiptNo
AND oar.ClassID = 1
AND me.Session = :sess
AND me.Year = :year
AND me.EnrollNo = :enr");
$sql_batch->bindParam('sess',$AdmSession);	
$sql_batch->bindParam('year',$AdmYear);	
$sql_batch->bindParam('enr',$FetchedEnrollNo);	
$sql_batch->execute();

$FetchData = $sql_batch->fetch(PDO::FETCH_OBJ);
if(isset($_SESSION['VerificationCode']) AND  $_SESSION['VerificationCode'] == $FetchData->VerificationCode){
	
?>
<div class="box-body table-responsive no-padding" >

<table id="DataGrid" class="table table-bordered table-striped gridtable">
                <thead>
                <tr>
                  <td><strong>Receipt No</strong></td>
                  <td><strong>Class</strong></td>
                  <td><strong>Group</strong></td>
                  <td><strong>Name</strong></td>
                  <td><strong>Father Name</strong></td>
                  <td align="left"><strong>Total Fee</strong></td>
                  <td><div align="center"><strong>Form Status</strong></div></td>
             <!--     <td ><div align="center"><strong>Challan</strong></div></td> -->
                  <td ><div align="center"><strong>Print Form</strong></div></td>
                  </tr>
                </thead>


<tr>
                  <td height="73"><div align="center"><strong><?php echo $FetchData->ReceiptNo; ?></strong></div></td>
                  <td><div align="center">
                    <strong>
                    <?php if($FetchData->ExamCode == 1 OR $FetchData->ExamCode == 5){echo '9TH';}elseif($FetchData->ExamCode == 2 OR $FetchData->ExamCode == 3){echo '10TH';} ?>
                  </strong></div></td>
                  <td><div align="center">
                    <strong>
                    <?php if($FetchData->GroupCode == 1){echo 'SCIENCE';}elseif($FetchData->GroupCode == 2){
						if($FetchData->AppearCode == 3){echo 'ADDITIONAL SUBJECTS';}else{echo 'HUMANITIES';}} ?>
                  </strong>                  </div></td>
                  <td><div align="left"><strong><?php echo $FetchData->Name; ?></strong>
                  </div></td>
                  <td><div align="left"><strong><?php echo $FetchData->FatherName; ?></strong>
    </div></td>
                  <td align="left"><div align="center"><strong><?php echo $FetchData->TotalFee; ?></strong>
                  </div></td>
                  <td><div align="center"><strong><?php if($FetchData->Received == 1){ ?>
                  <span class="label label-primary">Received</span>
                  <?php }else{ ?>
                  <span class="label label-danger">Pending</span>
                  <?php } ?></strong></div></td>
              <?php /*    <td><form style="margin-bottom:0;" target="_blank" action="print_receipt_fresh.php?mode=PrintChallan&module=Admission&ID=<?php echo $FetchData->ReceiptNo;?>&SecKey=<?php echo md5($FetchData->ReceiptNo+1026); ?>" method="post">
                      <div align="center">
                        <input type="image"  style="cursor: pointer;" id="PrintReceipt" src="../../images/download2.gif" width="150" height="24" title="View">
                        <input type="hidden" name="ReceiptNo" value="<?php echo $FetchData->ReceiptNo;?>" >
                      </div>
                    </form></td> */ ?>
                  <td><form style="margin-bottom:0;" target="_blank" action="FinalPrint_Fresh.php?mode=PrintReport&module=Enrollment&ID=<?php echo $FetchData->ReceiptNo;?>&SecKey=<?php echo md5($FetchData->ReceiptNo+1026); ?>" method="post">
                    <div align="center">
                      <input type="image" id="PrintForm"  style="cursor: pointer;" src="../../images/download.gif" width="150" height="24" title="View">
                      <input type="hidden" name="ReceiptNo" value="<?php echo $FetchData->ReceiptNo;?>" >
                      </div>
                  </form></td>
  </tr>
<tr>
  <td height="21" colspan="8">&nbsp;</td>
  </tr>
<tr>
  <td height="73" colspan="8" align="center"><img src="../../images/PrivateFormSubmission.jpg"></td>
  </tr>
 
</table>
</div>
<?php 
}else{


	?>
     <div class="row">
                                            <div class="col-sm-3">
                    <label>Verify Verification Code:</label>
                    <input type="text" class="form-control" id="VerificationCode" name="VerificationCode" >
                </div>
                                            <div class="col-sm-3">
                    <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control" onClick="VerifyCodeTwo('<?php echo $FetchedEnrollNo; ?>');">Verify Verification Code ?</button>
                        </div>
               <div class="col-sm-6">
                    <label>&nbsp;</label>
                    <div id="VerificationMsg">
                    </div>
                    </div>     
                </div>

<?php	}


}else{
$sql_fetch_data = $DbConn_Adm_Mysql->prepare("
Select *
From online_admission_requests 
Where ClassID = 1
AND Session = :sess
AND Year = :year
AND EnrollNo = :enr
AND EligibleFor IN(20,30)");
$sql_fetch_data->bindParam('sess',$AdmSession);
$sql_fetch_data->bindParam('year',$AdmYear);
$sql_fetch_data->bindParam('enr',$FetchedEnrollNo);
$sql_fetch_data->execute();
$FetchData = $sql_fetch_data->fetch(PDO::FETCH_OBJ);

$GroupName = 'HUMANITIES';
if($FetchData->EligibleFor == 20){
?>
<input type="hidden" id="EnrollNo" value="<?php echo $FetchData->EnrollNo; ?>">
<input type="hidden" id="AppearCode" value="1">
<input type="hidden" id="EligibleFor" value="<?php echo $FetchData->EligibleFor; ?>">
    
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nationality ( &#1602;&#1608;&#1605;&#1740;&#1578;)</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-flag"></i>
                                                </div>
                                                <select tabindex="1" name="CountryCode" id = "CountryCode" class="form-control select" style="width: 100%;" onChange="FetchProvince();" required>
                                                    <option value="">- Select -</option>
                                                    <?php
                                                    $sql3 = $DbConn_Adm_Mysql->prepare("Select * from bisep_countries");
                                                    $sql3->execute();
                                                    while ($FetchCountries = $sql3->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchCountries->CountryCode; ?>"
                                                                ><?php echo $FetchCountries->CountryName; ?></option>
                                                            <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student Province ( &#1589;&#1608;&#1576;&#1729; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-flag-o"></i>
                                                </div>
                                                <select tabindex="2" name="ProvinceCode" id="ProvinceCode"  class="form-control select" style="width: 100%;" onChange="FetchDistrict();" required>
                                                    <option value="">- Select -</option>

                                                    <?php
                                                    $ProvinceCode = $FetchData->ProvinceCode;
                                                    $CountryCode = $FetchData->CountryCode;
                                                    $sql4 = $DbConn_Adm_Mysql->prepare("Select * from bisep_province
						  Where CountryCode = :ccode");
                                                    $sql4->bindParam('ccode', $CountryCode);
                                                    $sql4->execute();
                                                    while ($FetchPovince = $sql4->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchPovince->ProvinceCode; ?>"
                                                                ><?php echo $FetchPovince->ProvinceName; ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student District ( &#1590;&#1604;&#1593; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <select tabindex="3" name="DistrictCode" id="DistrictCode"  class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>

                                                    <?php
                                                    $ProvinceCode = $FetchData->ProvinceCode;
                                                    $sql5 = $DbConn_Adm_Mysql->prepare("Select * from bisep_districts 
 						   Where ProvinceCode = :pcode");
                                                    $sql5->bindParam('pcode', $ProvinceCode);
                                                    $sql5->execute();
                                                    while ($FetchDistrict = $sql5->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchDistrict->DistrictCode; ?>"
                                                                ><?php echo $FetchDistrict->DistrictName; ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
<div class="col-md-4">
              <div class="form-group">
                <label>Student Name ( طالب علم کا نام )</label>
<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </div>
                  <input tabindex="4" type="text" name="StudentName" id="StudentName" value="<?php echo $FetchData->Name; ?>"  class="form-control pull-right ToUpper" maxlength="50" placeholder="As Per School Record" >
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>
              
<div class="col-md-4">
              <div class="form-group">
                <label>Father Name ( والد کا نام ) </label>           
<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-user-secret"></i>
                  </div>
                  <input tabindex="5" type="text"  name="StudentFatherName" id="StudentFatherName" value="<?php echo $FetchData->FatherName; ?>" class="form-control pull-right ToUpper" maxlength="50" placeholder="As Per School Record">
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>              
<div class="col-md-4">
              <div class="form-group">
                <label>Date of Birth ( تاریخ پیدایش )</label>
            <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-birthday-cake"></i>
                  </div>
                  <input tabindex="6" type="text" name="StudentDateOfBirth" id="StudentDateOfBirth" value="<?php echo $DateOfBirth; ?>" maxlength="12" class="form-control pull-right" placeholder="DD-MM-YYYY" data-inputmask='"mask": "99-99-9999"' data-mask>
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>

</div>

                                           
                                <div class="row">
<div class="col-md-4">
              <div class="form-group">
                <label>Gender ( جنس )</label>
             <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-intersex"></i>
                  </div>
                     <select tabindex="7" name="Gender" id="Gender"  class="form-control select" style="width: 100%;" required>
                      <option value="">- Select -</option>
                      <option value="1">Male</option>
                      <option value="2">Female</option>
                      <option value="3">Transgender</option>
                    </select>
              </div>
  </div>
            </div>                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Religion ( &#1605;&#1584;&#1729;&#1576; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="8" name="Religion" id="Religion" class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>
                                                    <option value="1">Muslim</option>
                                                    <option value="2">Non-Muslim</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Father/Guardian CNIC ( &#1608;&#1575;&#1604;&#1583; / &#1587;&#1585;&#1662;&#1585;&#1587;&#1578; &#1705;&#1575; &#1588;&#1606;&#1575;&#1582;&#1578;&#1740; &#1705;&#1575;&#1585;&#1672; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user-secret"></i>
                                                </div>
                                                <input tabindex="9" type="text" name="FatherCNIC" id="FatherCNIC" maxlength="16" class="form-control pull-right" placeholder="Nadra ID Card #" data-inputmask='"mask": "99999-9999999-9"' data-mask >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>              
                                    
                                </div>
                                <div class="row">
 <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student Form (B) ( &#1601;&#1575;&#1585;&#1605; (&#1576;) &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <input tabindex="10" type="text" name="FormB" id="FormB" maxlength="16" class="form-control pull-right" placeholder="Student Form(B) #" data-inputmask='"mask": "99999-9999999-9"' data-mask >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>                               
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student/Father/Guardian Mobile # (  &#1605;&#1608;&#1576;&#1575;&#1740;&#1604; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-mobile"></i>
                                                </div>
                                                <input tabindex="11" type="text" name="MobileNo" id="MobileNo" value="<?php echo $ApplyMobileNo; ?>" maxlength="13" class="form-control pull-right" placeholder="Student/Father Mobile #" data-inputmask='"mask": "9999-9999999"' data-mask disabled>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone # ( &#1657;&#1740;&#1604;&#1740; &#1601;&#1608;&#1606; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-phone"></i>
                                                </div>
                                                <input tabindex="12" type="text"  name="PhoneNo" id="PhoneNo" class="form-control pull-right" maxlength="15" >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Present Address ( &#1605;&#1608;&#1580;&#1608;&#1583;&#1729; &#1662;&#1578;&#1729; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-envelope-o"></i>
                                                </div>
                                                <textarea tabindex="13" name="PostalAddress" id="PostalAddress" class="form-control pull-right" maxlength="120" ></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Permanent Address ( &#1605;&#1587;&#1578;&#1602;&#1604; &#1662;&#1578; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <textarea tabindex="14"  name="PermanentAddress" id="PermanentAddress" class="form-control pull-right" maxlength="120"></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>                          
                                </div>
<div class="row">
 <div class="col-md-12">
                                        <div class="form-group">
                                            <label><span style="color:red;">Proposed Center (Name of Nearest Exam Center)</span></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <textarea tabindex="15"  name="ProposedCenter" id="ProposedCenter" class="form-control pull-right" maxlength="250" required></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>
 </div>                                
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Group ( &#1711;&#1585;&#1608;&#1662; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="16" name="GroupCode" id="GroupCode" onChange="FetchSubjectsFresh();" class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>
                                                    <option value="2">HUMANITIES (9TH FRESH)</option>
                                                    <option value="3">ADDITIONAL GROUP</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                </div>
<div class="row">
                                    <div class="col-md-4">

                                        <div id="Subjects" align="center">         
                                            <div align="center"><span style="color:red;">--Please Select Group Name --</span>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              
<table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <form id="imageform" method="post" enctype="multipart/form-data" action='ajaximage.php'>
                  <table width="auto" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td height="132" valign="middle" >
                            <!-- Table for photo display -->
                            <table align="center" width="70" height="90" cellpadding="0" cellspacing="0" class="phototab" >
                              <tr>
                            	<td>
                            	<div id="preview" align="center">
                            	  <p class="Normal2" style="color:#F92B30">Photo<br>
                            	    (only .jpg format)</p>
                                  <p class="Normal2" style="color:#F92B30">Maximum Size<br>
                                    (300 x 300)</p>
                            	</div>
                                </td>
                              </tr>
                            </table>
                    </td></tr>                          
                          <tr>
                            <td valign="middle" align="center"><div align="center">
                              <input tabindex="16" name="photoimg" type="file" id="photoimg"  style="width:73%" />
                              <input type="hidden" id="MobileNo" name="MobileNo" value="<?php echo $CombineMobile; ?>" />
                              <input type="hidden" id="EnrollNo" name="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>" />
                              <input type="hidden" id="AppearCode" name="AppearCode" value="1" />
                            </div></td>                            
                          </tr>
                          <tr>
                            <td height="19" valign="middle"><div class="Normal2" align="center"></div></td>
                          </tr>
                    </table>
                  </form>
                </td>
            </tr></table>
 </div></div></div>
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button tabindex="17" type="submit" class="btn btn-primary" onClick="SaveAdmissionFormNinthFresh();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 
 
<?php 
}else{
	
?>
<input type="hidden" id="EnrollNo" value="<?php echo $FetchData->EnrollNo; ?>">
<input type="hidden" id="AppearCode" value="1">
<input type="hidden" id="EligibleFor" value="<?php echo $FetchData->EligibleFor; ?>">
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Admission Class ( Will Appear In Upcoming Exam)</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-flag"></i>
                                                </div>
                                                <select tabindex="1" name="AdmissionClass" id = "AdmissionClass" class="form-control select" style="width: 100%;" onChange="LoadPreviousForm(this.value);" required>
                                                    <option value="">- Select -</option>
                                                    <option value="9">Class 9th</option>
                                                    <option value="10">Class 10th</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
 <div class="col-md-4">
              <div class="form-group">
                <label>Board Name (Migrated From)</label>
                  <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-flag"></i>
                  </div>
                <select tabindex="1" name="BoardIDOB" id = "BoardIDOB" class="form-control select" style="width: 100%;" required>
                      <option value="">- Select -</option>
               <?php 
			   $sql3 = $DbConn_Adm_Mysql->prepare("Select * from boards Where Board_ID <> 7");
			   $sql3->execute();
			   while($FetchBoard = $sql3->fetch(PDO::FETCH_OBJ)){
			   ?>
                      <option value="<?php echo $FetchBoard->Board_ID; ?>"><?php echo $FetchBoard->Board_Name; ?></option>
                <?php } ?>
                    </select>
              </div>
  </div>
            </div>                                   
                                    </div>    
<div id="LoadPreviousForm"></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nationality ( &#1602;&#1608;&#1605;&#1740;&#1578;)</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-flag"></i>
                                                </div>
                                                <select tabindex="1" name="CountryCode" id = "CountryCode" class="form-control select" style="width: 100%;" onChange="FetchProvince();" required>
                                                    <option value="">- Select -</option>
                                                    <?php
                                                    $sql3 = $DbConn_Adm_Mysql->prepare("Select * from bisep_countries");
                                                    $sql3->execute();
                                                    while ($FetchCountries = $sql3->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchCountries->CountryCode; ?>"
                                                                ><?php echo $FetchCountries->CountryName; ?></option>
                                                            <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student Province ( &#1589;&#1608;&#1576;&#1729; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-flag-o"></i>
                                                </div>
                                                <select tabindex="2" name="ProvinceCode" id="ProvinceCode"  class="form-control select" style="width: 100%;" onChange="FetchDistrict();" required>
                                                    <option value="">- Select -</option>

                                                    <?php
                                                    $ProvinceCode = $FetchData->ProvinceCode;
                                                    $CountryCode = $FetchData->CountryCode;
                                                    $sql4 = $DbConn_Adm_Mysql->prepare("Select * from bisep_province
						  Where CountryCode = :ccode");
                                                    $sql4->bindParam('ccode', $CountryCode);
                                                    $sql4->execute();
                                                    while ($FetchPovince = $sql4->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchPovince->ProvinceCode; ?>"
                                                                ><?php echo $FetchPovince->ProvinceName; ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student District ( &#1590;&#1604;&#1593; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <select tabindex="3" name="DistrictCode" id="DistrictCode"  class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>

                                                    <?php
                                                    $ProvinceCode = $FetchData->ProvinceCode;
                                                    $sql5 = $DbConn_Adm_Mysql->prepare("Select * from bisep_districts 
 						   Where ProvinceCode = :pcode");
                                                    $sql5->bindParam('pcode', $ProvinceCode);
                                                    $sql5->execute();
                                                    while ($FetchDistrict = $sql5->fetch(PDO::FETCH_OBJ)) {
                                                        ?>
                                                        <option value="<?php echo $FetchDistrict->DistrictCode; ?>"
                                                                ><?php echo $FetchDistrict->DistrictName; ?></option>
                                                            <?php }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
<div class="col-md-4">
              <div class="form-group">
                <label>Student Name ( طالب علم کا نام )</label>
<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </div>
                  <input tabindex="4" type="text" name="StudentName" id="StudentName" value="<?php echo $FetchData->Name; ?>"  class="form-control pull-right ToUpper" maxlength="50" placeholder="As Per School Record" >
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>
              
<div class="col-md-4">
              <div class="form-group">
                <label>Father Name ( والد کا نام ) </label>           
<div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-user-secret"></i>
                  </div>
                  <input tabindex="5" type="text"  name="StudentFatherName" id="StudentFatherName" value="<?php echo $FetchData->FatherName; ?>" class="form-control pull-right ToUpper" maxlength="50" placeholder="As Per School Record">
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>              
<div class="col-md-4">
              <div class="form-group">
                <label>Date of Birth ( تاریخ پیدایش )</label>
            <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-birthday-cake"></i>
                  </div>
                  <input tabindex="6" type="text" name="StudentDateOfBirth" id="StudentDateOfBirth" value="<?php echo $DateOfBirth; ?>" maxlength="12" class="form-control pull-right" placeholder="DD-MM-YYYY" data-inputmask='"mask": "99-99-9999"' data-mask>
                </div>
                <!-- /.input group -->
              </div>
           <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
             -->
              </div>

</div>

                                           
                                <div class="row">
<div class="col-md-4">
              <div class="form-group">
                <label>Gender ( جنس )</label>
             <div class="input-group date">
                  <div class="input-group-addon">
                    <i class="fa fa-intersex"></i>
                  </div>
                     <select tabindex="7" name="Gender" id="Gender"  class="form-control select" style="width: 100%;" required>
                      <option value="">- Select -</option>
                      <option value="1">Male</option>
                      <option value="2">Female</option>
                      <option value="3">Transgender</option>
                    </select>
              </div>
  </div>
            </div>                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Religion ( &#1605;&#1584;&#1729;&#1576; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="8" name="Religion" id="Religion" class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>
                                                    <option value="1">Muslim</option>
                                                    <option value="2">Non-Muslim</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Father/Guardian CNIC ( &#1608;&#1575;&#1604;&#1583; / &#1587;&#1585;&#1662;&#1585;&#1587;&#1578; &#1705;&#1575; &#1588;&#1606;&#1575;&#1582;&#1578;&#1740; &#1705;&#1575;&#1585;&#1672; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user-secret"></i>
                                                </div>
                                                <input tabindex="9" type="text" name="FatherCNIC" id="FatherCNIC" maxlength="16" class="form-control pull-right" placeholder="Nadra ID Card #" data-inputmask='"mask": "99999-9999999-9"' data-mask >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>              
                                    
                                </div>
                                <div class="row">
 <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student Form (B) ( &#1601;&#1575;&#1585;&#1605; (&#1576;) &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <input tabindex="10" type="text" name="FormB" id="FormB" maxlength="16" class="form-control pull-right" placeholder="Student Form(B) #" data-inputmask='"mask": "99999-9999999-9"' data-mask >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>                               
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student/Father/Guardian Mobile # (  &#1605;&#1608;&#1576;&#1575;&#1740;&#1604; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-mobile"></i>
                                                </div>
                                                <input tabindex="11" type="text" name="MobileNo" id="MobileNo" value="<?php echo $ApplyMobileNo; ?>" maxlength="13" class="form-control pull-right" placeholder="Student/Father Mobile #" data-inputmask='"mask": "9999-9999999"' data-mask disabled>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Phone # ( &#1657;&#1740;&#1604;&#1740; &#1601;&#1608;&#1606; &#1606;&#1605;&#1576;&#1585; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-phone"></i>
                                                </div>
                                                <input tabindex="12" type="text"  name="PhoneNo" id="PhoneNo" class="form-control pull-right" maxlength="15" >
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Present Address ( &#1605;&#1608;&#1580;&#1608;&#1583;&#1729; &#1662;&#1578;&#1729; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-envelope-o"></i>
                                                </div>
                                                <textarea tabindex="13" name="PostalAddress" id="PostalAddress" class="form-control pull-right" maxlength="120" ></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Permanent Address ( &#1605;&#1587;&#1578;&#1602;&#1604; &#1662;&#1578; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <textarea tabindex="14"  name="PermanentAddress" id="PermanentAddress" class="form-control pull-right" maxlength="120"></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>                          
                                </div>
<div class="row">
 <div class="col-md-12">
                                        <div class="form-group">
                                            <label><span style="color:red;">Proposed Center (Name of Nearest Exam Center)</span></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                                <textarea tabindex="15"  name="ProposedCenter" id="ProposedCenter" class="form-control pull-right" maxlength="250" required></textarea>
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>
 </div>                                
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Group ( &#1711;&#1585;&#1608;&#1662; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-group"></i>
                                                </div>
                                                <select tabindex="16" name="GroupCode" id="GroupCode" onChange="LoadSubjectsOtherboard();" class="form-control select" style="width: 100%;" required>
                                                    <option value="">- Select -</option>
                                                    <option value="2">HUMANITIES (9TH FRESH / 10TH FRESH)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <!-- /.col -->
                                </div>
<div class="row">
                                    <div class="col-md-4">

                                        <div id="Subjects" align="center">         
                                            <div align="center"><span style="color:red;">--Please Select Group Name --</span>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                <label>&nbsp;</label>              
<table width="126"  border="0" align="center" cellpadding="2" cellspacing="1" class="rounded-border" bgcolor="#99CCFF">
              <tr>
                <td width="14%" height="31" colspan="-1" bgcolor="#EAEAEB" align="center" ><strong>Student Picture</strong></td>
              </tr>
              <tr>
                <td height="218" bgcolor="#FFFFFF">
               <form id="imageform" method="post" enctype="multipart/form-data" action='ajaximage.php'>
                  <table width="auto" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td height="132" valign="middle" >
                            <!-- Table for photo display -->
                            <table align="center" width="70" height="90" cellpadding="0" cellspacing="0" class="phototab" >
                              <tr>
                            	<td>
                            	<div id="preview" align="center">
                            	  <p class="Normal2" style="color:#F92B30">Photo<br>
                            	    (only .jpg format)</p>
                                  <p class="Normal2" style="color:#F92B30">Maximum Size<br>
                                    (300 x 300)</p>
                            	</div>
                                </td>
                              </tr>
                            </table>
                    </td></tr>                          
                          <tr>
                            <td valign="middle" align="center"><div align="center">
                              <input tabindex="16" name="photoimg" type="file" id="photoimg"  style="width:73%" />
                              <input type="hidden" id="MobileNo" name="MobileNo" value="<?php echo $CombineMobile; ?>" />
                              <input type="hidden" id="EnrollNo" name="EnrollNo" value="<?php echo $FetchedEnrollNo; ?>" />
                              <input type="hidden" id="AppearCode" name="AppearCode" value="1" />
                            </div></td>                            
                          </tr>
                          <tr>
                            <td height="19" valign="middle"><div class="Normal2" align="center"></div></td>
                          </tr>
                    </table>
                  </form>
                </td>
            </tr></table>
 </div></div></div>
<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-save"></i>
                                                </div>
                                                <button tabindex="17" type="submit" class="btn btn-primary" onClick="SaveAdmissionFormOtherBoard();">Save Admission Form (   محفوظ کیجیے )</button> 
                                                
                                            </div>
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4" align="center">
                                        <br />
                                        <div class="form-group">
                                            <label id="DisplayMsg"></label>
                                            <br />
                                        </div>
                                        <!-- /.form-group -->

                                        <!-- /.form-group -->
                                    </div>

                                </div> 
 
<?php 
	
	}
}
}

}else{

$sql_check_Student = $DbConn_Adm_Mysql->prepare("
Select * 
From online_admission_requests 
Where ClassID = 1
AND Session = :sess
AND Year = :year
AND MobileNo = :mno
AND EligibleFor IN (20,30)
LIMIT 1");
$sql_check_Student->bindParam('sess',$AdmSession);
$sql_check_Student->bindParam('year',$AdmYear);
$sql_check_Student->bindParam('mno',$CombineMobile);
$sql_check_Student->execute();
$FetchApplyStudents = $sql_check_Student->fetch(PDO::FETCH_OBJ);

if($sql_check_Student->rowCount() > 0){

$FetchedDob = date("d-m-Y", strtotime($FetchApplyStudents->DateOfBirth));

echo '<input type="hidden" name="Response" id="Response"value="1" />';


echo '<input type="hidden" name="DBEligibleFor" id="DBEligibleFor" value="'.$FetchApplyStudents->EligibleFor.'" />';
echo '<input type="hidden" name="Name" id="DBName" value="'.$FetchApplyStudents->Name.'" />';
echo '<input type="hidden" name="FatherName" id="DBFatherName" value="'.$FetchApplyStudents->FatherName.'" />';
echo '<input type="hidden" name="DateOfBirth" id="DBDateOfBirth" value="'.$FetchedDob.'" />';

	
}


	
	}
	} else {

//Data Not Found Save it
?>

<input type="hidden" id="Name" value="<?php echo $Name; ?>">
<input type="hidden" id="FatherName" value="<?php echo $FatherName; ?>">
<input type="hidden" id="DateOfBirth" value="<?php echo $DateOfBirth; ?>">
<input type="hidden" id="ApplyMobileNo" value="<?php echo $ApplyMobileNo; ?>">

<div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student Name ( &#1591;&#1575;&#1604;&#1576; &#1593;&#1604;&#1605; &#1705;&#1575; &#1606;&#1575;&#1605; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <input type="text" disabled value="<?php echo $Name; ?>" class="form-control pull-right">

                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Father Name ( &#1608;&#1575;&#1604;&#1583; &#1705;&#1575; &#1606;&#1575;&#1605; ) </label>           
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user-secret"></i>
                                                </div>
                                                <input type="text" disabled value="<?php echo $FatherName; ?>" class="form-control pull-right">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                     <!--     <input type="text" name="AdmDate" id="datepicker"  class="form-control select2" style="width: 100%;" required>
                                        -->
                                    </div>              
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Date of Birth ( &#1578;&#1575;&#1585;&#1740;&#1582; &#1662;&#1740;&#1583;&#1575;&#1740;&#1588; )</label>
                                            <div class="input-group date">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-intersex"></i>
                                                </div>
                                                <input type="text" disabled value="<?php echo $DateOfBirth; ?>" class="form-control pull-right">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NOTE:</label>
                                            If the above information is correct and you want to apply for online admission, please click on the below button, A Verification Code will be sent to provided Mobile #  
                                 
                                 </div></div>
                                 
                                 <div class="col-md-6">
                                        <div class="form-group">
                                            <label></label>
                                            <img src="../../images/sms_code.gif" align="right">
                                 
                                 </div></div>
                                 </div>
                                  <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            </div>
                                            </div>                                            
                                    <div class="col-md-4" align="center">
                                        <div class="form-group">
                                            <strong>
                                            <label>MOBILE #:</label>
                                            <?php echo $ApplyMobileNo;?>
                                            </strong></div>
                                            </div>
                                            
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label></label>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-sm-4">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary" onClick="SendVerificationCodeTwo();">Confirm Record & Send Verification Code ?</button><div id="SearchArea2"></div>
                    </div>
                </div>
         </div>

  
  <?php  }
}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check (ERR-II).</span>';	
	}


}else{
   echo '<span style="font-family:bold;color:red;">Student Date of Birth is Invalid Please check.</span>';	
	}


}else{
   echo '<span style="font-family:bold;color:red;">Father Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}

}else{
   echo '<span style="font-family:bold;color:red;">Name must be Alphabatical with minumum length of 4 and maximum 50 Characters</span>';	
	}

}else{echo '<span style="font-weight:bold;color:red;">Please Type Mobile No In Correct Format i.e (0333-1234567)</span>';}
       } else {
            echo '<span style="font-weight:bold;color:red;">Please Type Mobile No!</span>';
        } 
		} else {
            echo '<span style="font-weight:bold;color:red;">Please Type Student Name , Father Name And Type Date of Birth & Mobile #</span>';
        }
    }else{    echo '<span style="font-weight:bold;color:red;">You Are Not Allowed to Do This Action Your ' . $IPAddress . ' has been Logged!</span>';
	}
} else {
}

