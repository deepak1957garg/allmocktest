<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
include_once dirname(__FILE__) . '/../modules/mocktest/models/UserTest.php';
$uid = 0;
if(isset($_REQUEST['customer_id'])){
    $uid = $_REQUEST['customer_id'];
}

$test_id = isset($_REQUEST['tst']) ? $_REQUEST['tst'] : 0;

$istest = 0;
$sections = array();
$questions = array();
$userTest = array();
$timing_arr = array();
$total_time = 0;
$order = array();

$final_total_time = 0;
$test_name = "";
//$test_id = "0";
$template_name = "Mock Test";
$return_uri = 'https://catmocktest.com/pages/my-swot';

if($uid!=0){
    $manager = new MockTestManager();
    $obj = new UserTest();
    if($test_id!=0){
        $obj = $manager->getIncompleteMockTestById($test_id);
    }
    if($obj->getValue('id')==0){
        $obj = $manager->getIncompleteNockTests($uid);
    }

    $template = $manager->getTemplateInfo($obj->getValue('exam_id'));
    if(isset($template['template_name']))   $template_name = $template['template_name'];

    if($obj->getValue('id')!=0){
        list($sections,$questions) = $manager->getExamQuestions($obj->getValue('exam_id'));

        $userTest = $obj->getObject();
        $num_ques = 1;
        $total_time = $sections[0]['total_time'];
        for($i=0; $i<count($sections); $i++){
            $obj1 = array();
            $obj1['end_time'] = $total_time - $sections[$i]['num_time'];
            $obj1['start_ques'] = $num_ques;
            $obj1['end_ques'] = $num_ques + $sections[$i]['num_questions'] - 1;
            $obj1['num_time'] = $sections[$i]['num_time'];
            $total_time = $obj1['end_time'];
            $num_ques = $obj1['end_ques'] + 1;
            array_push($timing_arr,$obj1);
        }
        $istest = 1;
        $final_total_time = $sections[0]['total_time'];
        $test_name = $sections[0]['test_name'];
        $test_id = $userTest['id'];
        $order = $manager->getMockTestOrder($userTest['order_id']);
        $return_uri = 'https://catmocktest.com/pages/test-details?tst=' . $test_id  . '&ref=mocktest';
        //https://catmocktest.com/pages/test-details?tst=5&qno=1&ref=mydashboard


        //print_r($questions['3308']);

    }
    else{
        $timing_arr[0]['num_time'] = 0;
    }
}
else{
    $timing_arr[0]['num_time'] = 0;
}
//print_r($sections);
$timing_json = json_encode($timing_arr);
$sections_json = json_encode($sections);
//print_r($sections);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAT 2025 Mock Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://static.thingsapp.co/catmocktest-static/app_v202508031147.css">
    <!-- <link rel="stylesheet" href="../assets/app.css"> -->
    <style type="text/css">
    </style>
    <style type="text/css">

        /*.question-panel { background-color: #BCE8F5; overflow-y:scroll; }
        .legend { background-color: #BCE8F5; }*/
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-strip">
            <div class="pageClose">&nbsp;</div>
            <div class="logo-container">
                <div class="logo"><img src="https://catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=600" alt="CatMockTest" srcset="//catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=300 300w, //catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=450 450w, //catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=600 600w" width="" height="50" loading="eager" class="header__heading-logo" sizes="(min-width: 750px) 300px, 50vw"></div>
            </div>
            <a  href="https://catmocktest.com/pages/my-swot" target="_parent" style="display:inline-block; color:#000; text-decoration:none;"><div class="pageClose">X</div></a>
        </div>
    </header>
    <div class="header-strip2" id="testNameHeader" style="display:none;">
        <div>
            <div>Common Admission Test 2025 - <?php echo $test_name; ?></div>
        </div>
        <div class="header-controls">
                <button class="instruction-btn">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>View Instructions</span>
                </button>
                <button class="qpaper-btn">
                    <i class="fa-solid fa-book"></i>
                    <span>Question Paper</span>
                </button>
        </div>
    </div>

    <div id="startCont" style="background:#fff;">
        <?php if($istest==1){ ?>
        <div style="width:100%; height:calc(100vh - 89px); display: flex;">
            <div style="width:100%; ">
                <div class="instructionListCont" style="width:100%; height:100%; overflow-y:auto;">
                        <div class="instruction" style="width:100%; text-align:left;"><h1>Welcome to the CAT 2025 Mock Test</h1></div>
                        <div class="instruction" style="width:100%; text-align:left;"><h3>Thank you for choosing this mock test to evaluate and enhance your preparation for the CAT 2025 exam.</h3></div>
                        <div class="instruction"></div>
                        <div class="instruction" style="width:100%; text-align:left;">This test is designed to simulate the actual CAT experience — including section-wise timing, navigation controls, on-screen calculator, and a similar user interface — so you can build confidence and develop your test-taking strategy.</div>
                        <div>
                            <button class="btn btn-tertiary" id="initBtn" style="padding: 5px 8px; background-color: #47f20c !important; border-radius: 25px; border: 2px solid black; box-shadow: 4px 4px #000; font-weight: bold; margin: 16px 0px;"><div style="display:flex; flex-wrap:wrap;  font-size:16px; padding: 8px 32px; border-radius: 8px;"><div>Start <?php echo $template_name; ?></div> </div></button>
                        </div>
                </div>
               <!--  <div class="actions" style="width:100%;">
                    <div style="margin:0 auto;">
                        <button class="btn btn-tertiary" id="initBtn"><div style="display:flex; flex-wrap:wrap;"><div>Start Test</div> </div></button>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- <div style="width:100%; height:calc(100vh - 89px); padding:32px;">
            <div style="width:100%;height:calc(100vh - 89px);padding:32px;position: relative;">
                <div style="position: absolute; margin: 0 auto; top: 50%; transform: translate(-50%); left: 50%; z-index: 3; font-size: 24px; text-align: center;">
                    <div>This is a proctored Mock Test For CAT 2025.</div>
                    <div><a href="javascript:void(0);" id="initBtn"><u>Start Test</u></a></div>
                </div>
            </div>
        </div> -->
        <?php } else{ ?>
         <div style="width:100%; height:calc(100vh - 89px); padding:32px;">
            <div style="width:100%;height:calc(100vh - 89px);padding:32px;position: relative;">
                <div style="position: absolute; margin: 0 auto; top: 50%; transform: translate(-50%); left: 50%; z-index: 3; font-size: 20px; text-align: center;">
                    <div style="padding: 16px;">There is no more active mock test you had bought.</div>
                    <div><a href="https://catmocktest.com/pages/my-swot" target="_parent"><u>Back to dashboard</u></a></div>
                </div>
            </div>    
        </div>
        <?php } ?>
    </div>

    <?php
        
        for($l=0; $l<count($sections); $l++){  
        $section = $sections[$l];
        
    ?>
        <div class="questionpaper-modal" id="qpaper<?php echo $l; ?>">
            <div class="questionpaper-container">
                <div style="display:flex; flex-wrap:wrap; width:100%; background-color: #4285f4; color:#fff; justify-content: space-between;">
                    <div class="instruction" style="padding:8px 8px;">Question Paper</div>
                    <div class="instruction" style="padding:8px 8px;"><a href="javascript:void(0);" class="closeqpaper" style="color:#ffffff; text-decoration: none;">Close x</a></div>
                </div>
                <div style="overflow-y: scroll; height: calc(100% - 34px); padding-bottom:32px;">
                    <div>
                        <div class="instruction" style="color: #ff0000; padding:16px 32px; font-size:24px; width: 100%; ">Note that timer is ticking while you read the instructions. Close this page to return to answering the questions </div>
                    </div>
                    <!-- <div>
                        <div style="color:#2f73b7; padding: 16px 16px; font-size:32px;"><?php echo $section['section_name']; ?></div>
                    </div> -->
                    <div>
                        <div style="color:#2f73b7; padding: 16px 32px; font-size:32px;"><?php echo $section['section_name']; ?></div>
                    </div>
                    <div>
                        <div>
                            <?php
                            $i=0;
                            $qids = explode(",",$section['questions']);
                            for($j=0;$j<count($qids);$j++){ 
                                $i++;
                                $question = $questions[$qids[$j]];
                            ?>
                            <div style="padding: 16px;">
                               <div style="padding: 16px; border-bottom:1px solid #ccc;">    
                                    <?php if($question['group_type']!='unknown'){ ?>
                                    <div>
                                        <?php if($question['pic']!=''){ ?>
                                            <img src="https://static.thingsapp.co/catmocktest/<?php echo $question['pic']; ?>" style="width:40%;">
                                        <?php  
                                            }
                                            if($question['paragraph']!=''){
                                        ?>
                                            <p><?php echo $question['paragraph']; ?></p>
                                        <?php
                                            }
                                        ?>
                                    </div>
                                    <?php } ?>

                                    <table>
                                           <tr><td class="firstCol">Q.<?php echo $i; ?>)</td><td><?php echo $question['question_text']; ?></td></tr>
                                           <tr><td class="firstCol">&nbsp;</td><td><?php if($question['question_type']=="MCQ"){ ?>
                                            <div class="marks">Marks for correct answer: 3 | Negative Marks: -1</div>
                                            <?php } else { ?>
                                            <div class="marks">Marks for correct answer: 3</div>
                                            <?php } ?></td></tr>
                                            <?php if($question['question_type']=="MCQ"){ ?>
                                            <?php 
                                            for($k=0;$k<count($question['options']);$k++){
                                                $option = $question['options'][$k];
                                            ?>
                                             <tr><td><?php echo ($k + 1); ?>.</td><td><?php echo $option['option_text']; ?></td></tr>
                                            <?php } ?>
                                       <?php 
                                        } ?>
                                    </table>
                                </div>
                            </div>
                            <?php 
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

        <div class="questionpaper-modal" id="instructionCont">
            <div class="questionpaper-container">
                <div style="display:flex; flex-wrap:wrap; width:100%; background-color: #4285f4; color:#fff; justify-content: space-between;">
                    <div class="instruction" style="padding:8px 8px;">Instructions</div>
                    <div class="instruction" style="padding:8px 8px;"><a href="javascript:void(0);" class="closeqpaper" style="color:#ffffff; text-decoration: none;">Close x</a></div>
                </div>
                <div style="overflow-y: scroll; height: calc(100% - 34px); padding-bottom:32px;">
                    <div style="padding:32px;">
                        <div class="instruction"><strong>General Instructions:</strong></div>
                        <div class="instruction"><strong>1. The number, type and pattern of questions, as well as sequence and timing of sections in the Mock Exam are only indicative and these are subject to variations from year to year as decided by the CAT authorities.</strong></div>
                        <div class="instruction">2. The test has 3 (three) sections. The total duration of the test is 120 minutes. PwD candidates will have 13 minutes and 20 seconds extra time for each section.</div>
                        <div class="instruction">3. The time allotted to each section is 40 minutes (53 minutes and 20 seconds for PwD candidates). As soon as you start answering a section, the clock (displayed on the top right corner of the screen) will start. On completion of 40 minutes, the clock will stop, the particular section will be locked, and the responses to questions in that section will be auto-submitted. You will then need to move to the next section and start answering the next set of questions. The same process will be repeated for the other two sections. On submission of all three sections, a summary of your answers will be displayed on your screen.</div>
                        <div class="instruction">For PwD candidates the process will be the same as above. The only exception is that, for each section, the PwD candidate will have 53 minutes and 20 seconds, with an option for submitting the answers of a section at any point of time after the completion of 40 minutes. Thus, PwD candidates will have the option to complete the entire test with all three sections within a maximum of 160 minutes.</div>
                        <div class="instruction">4. You will be allowed to leave the test hall only after a minimum of 120 minutes.</div>
                        <div class="instruction">5. Your time will be set and synchronized to the server clock. The <strong>Countdown Timer</strong> at the top right corner of your screen will display the remaining time available to you to complete the currently active section. <strong>When the timer reaches zero, the test for that section will automatically end.</strong></div>
                        <div class="instruction">PwD candidates will be allowed to complete a section at any point of time between duration of 40 minutes and 53 minutes and 20 seconds by clicking on the ‘Submit’ button. After 53 minutes and 20 seconds, however, the test for the section will automatically end.</div>
                        <div class="instruction">6. The question paper will have a mix of <strong>Multiple Choice Question (MCQ) type</strong> with options and <strong>Non-MCQ type</strong>.</div>
                        <div class="instruction">7. A writing pad will be provided to the candidates for rough work, which will have to be returned after the test. <strong>Please note that only one writing pad will be provided to you.</strong> Candidates should clearly write their name and registration number on the writing pad.</div>
                        <div class="instruction">8. An on-screen calculator will be provided, which can be used for computing. You will <strong>not be allowed</strong> to use any other calculator, computing machine, or device.</div>
                        <div class="instruction">9. The question palette displayed on the right side of the screen will show the status of each question with the help of one of the following symbols:</div>

                        <div class="instruction"><img src="https://static.thingsapp.co/catmocktest-static/legends.png"></div>

                        <div class="instruction">*Answers to all questions flagged as <strong>‘Marked for Review’</strong> (Serial No. E) will be automatically considered as submitted for evaluation at the end of the time allotted for that section.</div>

                        <div class="instruction">10. You can click on the " < " arrow which appears to the left of the question palette to collapse the question palette and maximize the window. To view the question palette again, you can click on the " > " which appears on the right side of the computer console. Please note that you may have to scroll down to view the full question and options in some cases.</div>

                        <div class="instruction">11. <strong>To answer a question, you will need to do the following:</strong></div>

                        <div class="instruction">a. Click on the question number in the question palette to go to that question directly.</div>

                        <div class="instruction">b. Select an answer for an <strong>MCQ</strong> by clicking on the radio button ( ) placed just before the choice.</div>

                        <div class="instruction"><strong>For a Non-MCQ, enter only a whole number</strong> as the answer in the space provided on the screen using the on-screen keyboard. For example, if the correct answer to a Non-MCQ is 50, then enter <strong>ONLY</strong> 50 and <strong>NOT</strong> 50.0 or 050 etc.</div>

                        <div class="instruction">c. Click on <strong>‘Save & Next’</strong> to save your answer for the current question and then go to the next question.</div>

                        <div class="instruction">Alternatively, you may click on <strong>‘Mark for Review & Next’</strong> to save your answer for the current question and mark it for your review at any time before the completion of the section, and then move to the next question.</div>

                        <div class="instruction"><strong>Caution: Your answer for the current question will not be saved, if you navigate directly to another question by clicking on a question number and not click ‘Save & Next’ or ‘Mark for Review & Next’ button.</strong></div>

                        <div class="instruction">d. You will be able to view all the questions of a section by clicking on the <strong>‘Question Paper’</strong> button. <strong>This feature is provided for you to see the entire question paper of a particular section.</strong></div>

                        <div class="instruction">12. <strong>Procedure for changing your response to a question:</strong></div>

                        <div class="instruction">a. To deselect your chosen answer, click on the <strong>question number</strong> on the question palette and click on the <strong>‘Clear Response’</strong> button.</div>

                        <div class="instruction">b. To change your chosen answer, click on the radio button corresponding to another option.</div>

                        <div class="instruction">c. To save your changed answer, you must click on the <strong>‘Save & Next’ or ‘Mark for Review & Next’</strong> button.</div>

                        <div class="instruction">13. <strong>Navigating through Sections:</strong></div>

                        <div class="instruction">a.The test has three sections administered in the following order: I. Verbal Ability and Reading Comprehension (VARC), II. Data Interpretation and Logical Reasoning (DILR), and III. Quantitative Ability (QA). The names of the three sections are displayed on the top bar of the screen. The section you are currently viewing is highlighted.</div>

                        <div class="instruction">
                            b. From any section, you will be able to move to the next section only after completing a minimum of 40 minutes, i.e., after the time allocated to that section for non-PwD candidates.<br />
                            c. PwD candidates with blindness and low vision (or VI candidates) will have the screen magnification option enabled by default and will find two magnifying glass icons at the top of the screen. You can click on <img src="https://static.thingsapp.co/catmocktest-static/zoomplus.png" /> icon to zoom-in and click on <img src="https://static.thingsapp.co/catmocktest-static/zoomminus.png" /> to zoom out the question.<br />
                            Zoom is enabled at 2 levels from the default view, which are-
                        </div>
                         <div class="instruction" style="padding: 8px 0px 0px 32px;">
                            <ul>
                                <li>16x Pixel - Default View</li>
                                <li>21x Pixel - Level 1</li>
                                <li>24x Pixel - Level 2</li>
                            </ul>
                        </div>
                    </div>

                    <div style="padding:32px;">
                        <div class="instruction"><strong><u>Subject Specific Instructions:</u></strong></div>

                        <!-- <div class="instruction">1. To login, enter your registration number and password following instructions provided to you by the invigilator.</div> -->

                        <div class="instruction">1. Go through the various symbols used in the test and understand their meaning before you start the test.</div>

                        <div class="instruction">2. The question paper consists of 3 (three) sections:</div>

                        <div class="instruction">
                            <table>
                                    <tr>
                                        <th>Section</th>
                                        <th>Test</th>
                                    </tr>
                                    <tr>
                                        <td>I</td>
                                        <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                    </tr>
                                    <tr>
                                        <td>II</td>
                                        <td>Data Interpretation and Logical Reasoning (DILR)</td>
                                    </tr>
                                     <tr>
                                        <td>III</td>
                                        <td>Quantitative Ability (QA)</td>
                                    </tr>
                                
                            </table>
                        </div>


                        <div class="instruction">3. The Data Interpretation and Logical Reasoning (DILR) section each problem is based on situations or scenarios and can have any number of sub questions. Similarly, for Reading Comprehension, each passage consists of a group of four questions.</div>

                        <div class="instruction">4. For an <strong>MCQ</strong>, a candidate will be given <strong>3 (three) marks for a correct answer, -1 (minus one) mark for a wrong answer and a 0 (zero) mark for an un-attempted question.</strong></div>

                        <div class="instruction">5. For a <strong>Non-MCQ</strong>, a candidate will be given <strong>3 (three) marks for a correct answer, and a 0 (zero) mark for a wrong answer as well as for an un-attempted question. There will be no negative mark for a wrong answer in a Non-MCQ.</strong></div>

                        <div class="instruction">6. An MCQ will have choices out of which only one will be the correct answer. The computer allotted to you at the test centre runs on a specialized software that permits you to select only one answer for an MCQ. You will have to choose the correct answer by clicking on the radio button () placed just before the option. For a Non-MCQ, you will have to enter only a whole number as the answer in the space provided on the screen using the on-screen keyboard.</div>

                        <div class="instruction">7. Your answers will be updated and saved on a server periodically. The test will end automatically at the end of <strong>120 minutes</strong> (or at the end of <strong>160 minutes</strong> for PwD candidates). The time allotted for each section will be 40 minutes (or 53 minutes and 20 seconds for PwD candidates), after which you will not be allowed to go back to the earlier section(s).</div>

                        <div class="instruction"><strong>Declaration by a Candidate:</strong></div>

                        <div class="instruction" style="padding: 8px 0px 0px 32px;">
                            <ul>
                                <li>I have read and understood all the above instructions. I have also read and understood clearly the instructions given on the admit card and CAT website and shall follow the same. I declare that I am not wearing/carrying/in possession of any electronic communication gadgets or any prohibited material with me into the examination hall. I also understand that in case I violate any of these instructions, my candidature is liable to be cancelled and/or disciplinary action taken which may include debarring me from future tests and examinations.</li>

                                <li>
                                I confirm that at the start of the test, all computer hardware and software allotted to me are in working condition.</li>

                                <li>
                                I will not disclose, publish, reproduce, transmit, store, or facilitate transmission and storage of the contents of the CAT or any information therein in whole or part thereof in any form or by any means, verbal or written, electronically or mechanically for any purpose. I am aware that this shall be in violation of the Indian Contract Act, 1872 and/or the Copyright Act, 1957 and/or the Information Technology Act, 2000. I am aware that such actions and/or abetment thereof as aforementioned may constitute a cognizable offence punishable with imprisonment for a term up to three years and fine up to Rs. Two Lakhs.</li>
                            </ul>
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>

    <div id="instruct1Cont" style="background:#fff; display:none;">
        <div style="width:100%; height:calc(100vh - 89px); display: flex;">
            <div style="width:80%; border-right:1px solid #000;">
                <div class="instruction" style="background-color:#BCE8F5; padding:8px 8px; font-size:20px; width: 100%; border-right: 1px solid #000;">Instructions</div>
                <div class="instructionListCont">
                    <div class="instruction"><strong>General Instructions:</strong></div>
                    <div class="instruction"><strong>1. The number, type and pattern of questions, as well as sequence and timing of sections in the Mock Exam are only indicative and these are subject to variations from year to year as decided by the CAT authorities.</strong></div>
                    <div class="instruction">2. The test has 3 (three) sections. The total duration of the test is 120 minutes. PwD candidates will have 13 minutes and 20 seconds extra time for each section.</div>
                    <div class="instruction">3. The time allotted to each section is 40 minutes (53 minutes and 20 seconds for PwD candidates). As soon as you start answering a section, the clock (displayed on the top right corner of the screen) will start. On completion of 40 minutes, the clock will stop, the particular section will be locked, and the responses to questions in that section will be auto-submitted. You will then need to move to the next section and start answering the next set of questions. The same process will be repeated for the other two sections. On submission of all three sections, a summary of your answers will be displayed on your screen.</div>
                    <div class="instruction">For PwD candidates the process will be the same as above. The only exception is that, for each section, the PwD candidate will have 53 minutes and 20 seconds, with an option for submitting the answers of a section at any point of time after the completion of 40 minutes. Thus, PwD candidates will have the option to complete the entire test with all three sections within a maximum of 160 minutes.</div>
                    <div class="instruction">4. You will be allowed to leave the test hall only after a minimum of 120 minutes.</div>
                    <div class="instruction">5. Your time will be set and synchronized to the server clock. The <strong>Countdown Timer</strong> at the top right corner of your screen will display the remaining time available to you to complete the currently active section. <strong>When the timer reaches zero, the test for that section will automatically end.</strong></div>
                    <div class="instruction">PwD candidates will be allowed to complete a section at any point of time between duration of 40 minutes and 53 minutes and 20 seconds by clicking on the ‘Submit’ button. After 53 minutes and 20 seconds, however, the test for the section will automatically end.</div>
                    <div class="instruction">6. The question paper will have a mix of <strong>Multiple Choice Question (MCQ) type</strong> with options and <strong>Non-MCQ type</strong>.</div>
                    <div class="instruction">7. A writing pad will be provided to the candidates for rough work, which will have to be returned after the test. <strong>Please note that only one writing pad will be provided to you.</strong> Candidates should clearly write their name and registration number on the writing pad.</div>
                    <div class="instruction">8. An on-screen calculator will be provided, which can be used for computing. You will <strong>not be allowed</strong> to use any other calculator, computing machine, or device.</div>
                    <div class="instruction">9. The question palette displayed on the right side of the screen will show the status of each question with the help of one of the following symbols:</div>

                    <div class="instruction"><img src="https://static.thingsapp.co/catmocktest-static/legends.png"></div>

                    <div class="instruction">*Answers to all questions flagged as <strong>‘Marked for Review’</strong> (Serial No. E) will be automatically considered as submitted for evaluation at the end of the time allotted for that section.</div>

                    <div class="instruction">10. You can click on the " < " arrow which appears to the left of the question palette to collapse the question palette and maximize the window. To view the question palette again, you can click on the " > " which appears on the right side of the computer console. Please note that you may have to scroll down to view the full question and options in some cases.</div>

                    <div class="instruction">11. <strong>To answer a question, you will need to do the following:</strong></div>

                    <div class="instruction">a. Click on the question number in the question palette to go to that question directly.</div>

                    <div class="instruction">b. Select an answer for an <strong>MCQ</strong> by clicking on the radio button ( ) placed just before the choice.</div>

                    <div class="instruction"><strong>For a Non-MCQ, enter only a whole number</strong> as the answer in the space provided on the screen using the on-screen keyboard. For example, if the correct answer to a Non-MCQ is 50, then enter <strong>ONLY</strong> 50 and <strong>NOT</strong> 50.0 or 050 etc.</div>

                    <div class="instruction">c. Click on <strong>‘Save & Next’</strong> to save your answer for the current question and then go to the next question.</div>

                    <div class="instruction">Alternatively, you may click on <strong>‘Mark for Review & Next’</strong> to save your answer for the current question and mark it for your review at any time before the completion of the section, and then move to the next question.</div>

                    <div class="instruction"><strong>Caution: Your answer for the current question will not be saved, if you navigate directly to another question by clicking on a question number and not click ‘Save & Next’ or ‘Mark for Review & Next’ button.</strong></div>

                    <div class="instruction">d. You will be able to view all the questions of a section by clicking on the <strong>‘Question Paper’</strong> button. <strong>This feature is provided for you to see the entire question paper of a particular section.</strong></div>

                    <div class="instruction">12. <strong>Procedure for changing your response to a question:</strong></div>

                    <div class="instruction">a. To deselect your chosen answer, click on the <strong>question number</strong> on the question palette and click on the <strong>‘Clear Response’</strong> button.</div>

                    <div class="instruction">b. To change your chosen answer, click on the radio button corresponding to another option.</div>

                    <div class="instruction">c. To save your changed answer, you must click on the <strong>‘Save & Next’ or ‘Mark for Review & Next’</strong> button.</div>

                    <div class="instruction">13. <strong>Navigating through Sections:</strong></div>

                    <div class="instruction">a.The test has three sections administered in the following order: I. Verbal Ability and Reading Comprehension (VARC), II. Data Interpretation and Logical Reasoning (DILR), and III. Quantitative Ability (QA). The names of the three sections are displayed on the top bar of the screen. The section you are currently viewing is highlighted.</div>

                    <div class="instruction">
                        b. From any section, you will be able to move to the next section only after completing a minimum of 40 minutes, i.e., after the time allocated to that section for non-PwD candidates.<br />
                        c. PwD candidates with blindness and low vision (or VI candidates) will have the screen magnification option enabled by default and will find two magnifying glass icons at the top of the screen. You can click on <img src="https://static.thingsapp.co/catmocktest-static/zoomplus.png" /> icon to zoom-in and click on <img src="https://static.thingsapp.co/catmocktest-static/zoomminus.png" /> to zoom out the question.<br />
                        Zoom is enabled at 2 levels from the default view, which are-
                    </div>
                     <div class="instruction" style="padding: 8px 0px 0px 32px;">
                        <ul>
                            <li>16x Pixel - Default View</li>
                            <li>21x Pixel - Level 1</li>
                            <li>24x Pixel - Level 2</li>
                        </ul>
                    </div>
                </div>
                <div class="actions">
                    <div></div>
                    <div>
                        <button class="btn btn-secondary" id="init2Btn"><div style="display:flex; flex-wrap:wrap;"><div>Next</div> <!-- <div><img src="https://static.thingsapp.co/catmocktest-static/forward.png"></div> --></div></button>
                    </div>
                </div>
            </div>
            <div style="width: 20%; text-align: center; padding: 32px;">
                <img src="https://static.thingsapp.co/catmocktest-static/profile.jpg">
                <?php if(isset($order['customer_first_name'])){
                    echo '<p>' . trim($order['customer_first_name'] . ' ' . $order['customer_last_name']) . '</p>';
                }
                ?>
                <!-- <p>John Smith</p> -->
            </div>
        </div>
    </div>

    <div id="instruct2Cont" style="background:#fff;  display:none;">
        <div style="width:100%; height:calc(100vh - 89px); display: flex;">
            <div style="width:80%; border-right:1px solid #000;">
                <div class="instruction" style="background-color:#BCE8F5; padding:8px 8px; font-size:20px; width: 100%;">Other Important Instructions</div>
                <div class="instructionListCont">
                    <div class="instruction"><strong><u>Subject Specific Instructions:</u></strong></div>

                    <!-- <div class="instruction">1. To login, enter your registration number and password following instructions provided to you by the invigilator.</div> -->

                    <div class="instruction">1. Go through the various symbols used in the test and understand their meaning before you start the test.</div>

                    <?php if(count($sections)==1){ ?>
                    <div class="instruction">2. The question paper consists of 1 section:</div>

                    <div class="instruction">
                        <table>
                                <tr>
                                    <th>Section</th>
                                    <th>Test</th>
                                </tr>
                                <?php if($sections[0]['section_name']=='VARC'){    ?>
                                <tr>
                                    <td>I</td>
                                    <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                </tr>
                                <?php }  else if($sections[0]['section_name']=='DILR'){    ?>
                                <tr>
                                    <td>I</td>
                                    <td>Data Interpretation and Logical Reasoning (DILR)</td>
                                </tr>
                                <?php }  else if($sections[0]['section_name']=='QA'){    ?>
                                 <tr>
                                    <td>I</td>
                                    <td>Quantitative Ability (QA)</td>
                                </tr>
                                <?php } ?>
                        </table>
                    </div>

                    
                    <?php    
                    }
                    else{
                    ?>    
                    <div class="instruction">2. The question paper consists of 3 (three) sections:</div>

                    <div class="instruction">
                        <table>
                                <tr>
                                    <th>Section</th>
                                    <th>Test</th>
                                </tr>
                                <tr>
                                    <td>I</td>
                                    <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                </tr>
                                <tr>
                                    <td>II</td>
                                    <td>Data Interpretation and Logical Reasoning (DILR)</td>
                                </tr>
                                 <tr>
                                    <td>III</td>
                                    <td>Quantitative Ability (QA)</td>
                                </tr>
                            
                        </table>
                    </div>
                    <?php } ?>

                    <?php if(count($sections)==1){ ?>
                        <?php if($sections[0]['section_name']=='VARC'){    ?>
                            <div class="instruction">3. For Reading Comprehension,section each problem is based on situations or scenarios and each passage consists of a group of four questions.</div>
                        <?php }  else if($sections[0]['section_name']=='DILR'){    ?>
                            <div class="instruction">3. The Data Interpretation and Logical Reasoning (DILR) section each problem is based on situations or scenarios and can have any number of sub questions.</div>
                        <?php }  else if($sections[0]['section_name']=='QA'){    ?>
                            <div class="instruction">3. Each Question is independent of each other.</div>
                        <?php } ?>
                    <?php    
                    }
                    else{
                    ?>
                    <div class="instruction">3. The Data Interpretation and Logical Reasoning (DILR) section each problem is based on situations or scenarios and can have any number of sub questions. Similarly, for Reading Comprehension, each passage consists of a group of four questions.</div>
                    <?php } ?>

                    <div class="instruction">4. For an <strong>MCQ</strong>, a candidate will be given <strong>3 (three) marks for a correct answer, -1 (minus one) mark for a wrong answer and a 0 (zero) mark for an un-attempted question.</strong></div>

                    <div class="instruction">5. For a <strong>Non-MCQ</strong>, a candidate will be given <strong>3 (three) marks for a correct answer, and a 0 (zero) mark for a wrong answer as well as for an un-attempted question. There will be no negative mark for a wrong answer in a Non-MCQ.</strong></div>

                    <div class="instruction">6. An MCQ will have choices out of which only one will be the correct answer. The computer allotted to you at the test centre runs on a specialized software that permits you to select only one answer for an MCQ. You will have to choose the correct answer by clicking on the radio button () placed just before the option. For a Non-MCQ, you will have to enter only a whole number as the answer in the space provided on the screen using the on-screen keyboard.</div>

                    <div class="instruction">7. Your answers will be updated and saved on a server periodically. The test will end automatically at the end of <strong>120 minutes</strong> (or at the end of <strong>160 minutes</strong> for PwD candidates). The time allotted for each section will be 40 minutes (or 53 minutes and 20 seconds for PwD candidates), after which you will not be allowed to go back to the earlier section(s).</div>

                    <div class="instruction"><strong>Declaration by a Candidate:</strong></div>

                    <div class="instruction" style="padding: 8px 0px 0px 32px;">
                        <ul>
                            <li>I have read and understood all the above instructions. I have also read and understood clearly the instructions given on the admit card and CAT website and shall follow the same. I declare that I am not wearing/carrying/in possession of any electronic communication gadgets or any prohibited material with me into the examination hall. I also understand that in case I violate any of these instructions, my candidature is liable to be cancelled and/or disciplinary action taken which may include debarring me from future tests and examinations.</li>

                            <li>
                            I confirm that at the start of the test, all computer hardware and software allotted to me are in working condition.</li>

                            <li>
                            I will not disclose, publish, reproduce, transmit, store, or facilitate transmission and storage of the contents of the CAT or any information therein in whole or part thereof in any form or by any means, verbal or written, electronically or mechanically for any purpose. I am aware that this shall be in violation of the Indian Contract Act, 1872 and/or the Copyright Act, 1957 and/or the Information Technology Act, 2000. I am aware that such actions and/or abetment thereof as aforementioned may constitute a cognizable offence punishable with imprisonment for a term up to three years and fine up to Rs. Two Lakhs.</li>
                        </ul>
                    </div>
                </div>
                <div style="padding:8px 16px; width:100%;">
                   <input type="checkbox" name="agree_terms" id="agree_terms" /> <span id="agree_terms_text">I agree to the above terms and conditions for examination</span>
                </div>
                <div class="actions" style="width:100%;">
                    <div>
                        <button class="btn btn-secondary" id="init3Btn">Previous</button>
                    </div>
                    <div>
                        <button class="btn btn-primary" disabled="true" id="startBtn" style="background-color:#38aae9;">I am ready to begin</button>
                    </div>
                </div>
            </div>
            <div style="width: 20%; text-align: center; padding: 32px;">
                <img src="https://static.thingsapp.co/catmocktest-static/profile.jpg">
                <?php if(isset($order['customer_first_name'])){
                    echo '<p>' . trim($order['customer_first_name'] . ' ' . $order['customer_last_name']) . '</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <div id="completeCont"  style="background:#fff; display:none;">
        <div style="width:100%;height:calc(100vh - 115px);padding:32px;position: relative;">
            <div style="position: absolute; margin: 0 auto; top: 50%; transform: translate(-50%); left: 50%; z-index: 3; font-size: 20px; text-align:center;">
                <div style="padding: 16px;">Test Completed</div>
                <div><a href="<?php echo $return_uri; ?>" target="_parent"><u>View Swot Analysis Of This Test</u></a></div>
            </div>
        </div>
    </div>

    <div id="switchTab"  style="background:#fff; display:none;">
        <div style="width:100%;height:calc(100vh - 115px);padding:32px;position: relative;">
            <div style="position: absolute; margin: 0 auto; top: 50%; transform: translate(-50%); left: 50%; z-index: 3; font-size: 20px; text-align:center;">
                <div style="padding: 16px;">Test is closed due to either switch Tab/or move away.</div>
                <div>
                    <button class="btn btn-tertiary" id="resume1Btn" style="padding: 5px 8px; background-color: #47f20c !important; border-radius: 25px; border: 2px solid black; box-shadow: 4px 4px #000; font-weight: bold; margin: 16px 0px;"><div style="display:flex; flex-wrap:wrap;  font-size:16px; padding: 8px 32px; border-radius: 8px;"><div>Resume Mock Test</div> </div></button>

                </div>
            </div>
        </div>
    </div>    

    <div class="container" id="examCont" style="display:none;">        
        <div class="content-area">
            <div>
                <div class="header-strip3">
                    <div style="display:flex; flex-wrap: wrap;">
                        <?php
                         for($l=0; $l<count($sections); $l++){
                        ?>
                        <div class="section-name <?php if($l==0) echo 'section-select'; ?>" id="secname<?php echo $l; ?>" ><?php echo $sections[$l]['section_name']; ?> <i class="fa-solid fa-circle-info"></i></div>
                        <?php } ?>
                    </div>
                    <div class="header-controls">
                        <!-- <button class="yellow-btn">
                            <i class="fa-solid fa-book"></i>
                        </button> -->
                        <button class="yellow-btn" id="openCalculator">
                            <i class="fas fa-calculator"></i>
                        </button>
                    </div>
                </div>
                <div class="header-strip4">
                    <div>
                        <div>Section</div>
                    </div>
                    <div class="header-controls">
                        <div class="timer">
                            Time left: 
                            <span id="time">
                                <?php
                                    $total_time = 0;
                                	if(isset($sections[0]['total_time']))  $total_time = $sections[0]['total_time'];//$final_total_time;
                                    echo ($total_time/60) . ":" . ($total_time%60);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
             </div>

            <?php 
            $i=0;
            for($l=0; $l<count($sections); $l++){  
                $section = $sections[$l];
            ?>
            <div id="qcont<?php echo $l; ?>" class="question-section" <?php if($l!=0) echo 'style="display:none;"'; ?>>
                <div class="header-strip4">
                    <div>
                        <div style="background: #1565c0; color: #fff; margin: -8px 0px; padding: 8px 16px;"><?php echo $section['section_name']; ?> <i class="fa-solid fa-circle-info"></i></div>
                    </div>
                </div>
                


                <?php
                $qids = explode(",",$section['questions']);
                for($j=0;$j<count($qids);$j++){ 
                    $question = $questions[$qids[$j]];
                    $i++;

                ?>
                <div class="question-area" id="qbox<?php echo $i; ?>" <?php if($i!=1) echo 'style="display:none;"'; ?>>

                    <div class="header-strip4">
                        <div class="logo-container"></div>
                        <div class="header-controls">
                            <?php if($question['question_type']=="MCQ"){ ?>
                                <div class="marks">Marks for correct answer: 3 | Negative Marks: -1</div>
                            <?php } else { ?>
                                <div class="marks">Marks for correct answer: 3</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="myanswer" style="display:none;" data-option="0" data-answer="" data-question="<?php echo $question['question_id']; ?>" data-status="not_visited" data-time="0"></div>


                    <div class="questionCombine">    

                        <?php if($question['group_type']!='unknown'){ ?>
                        <div class="comprehension-box">
                            <!-- <h3 class="comprehension-title">Reading Comprehension</h3> -->
                            <div class="comprehension-content">
                                <?php if($question['pic']!=''){
                                ?>
                                    <img src="https://static.thingsapp.co/catmocktest/<?php echo $question['pic']; ?>" style="width:80%;">
                                <?php
                                }
                                 if($question['paragraph']!=''){
                                ?>
                                     <p><?php echo nl2br($question['paragraph']); ?></p>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="question-box <?php if($question['group_type']!='unknown') echo 'with-passage'; ?>">
                            <!-- <div class="question-title">
                                <div class="marks">Marks for correct answer: 3 | Negative Marks: -1</div>
                            </div> -->
                            
                            <div class="question-title">
                                <h3>Question <?php echo $i; ?></h3>
                            </div>

                            <div class="question-text">
                                <?php echo nl2br($question['question_text']); ?>
                            </div>


                            
                            <?php if($question['question_type']=="MCQ"){ ?>
                            <div class="options">
                                <?php 
                                for($k=0;$k<count($question['options']);$k++){
                                    $option = $question['options'][$k];
                                    $ascii = 65 + $j;
                                ?>
                                <div class="option">
                                    <input type="radio" data-id="option<?php echo ($k + 1); ?>" name="answerOption<?php echo ($i + 1); ?>">
                                   <!--  <div class="option-label">&#<?php echo $ascii; ?>;</div> -->
                                    <div class="option-text"><?php echo $option['option_text']; ?></div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php 
                            }
                            else if($question['question_type']=="TITA"){
                            ?>

                            <div class="titaBox" id="tita<?php echo $i; ?>">
                                <div class="titaInputBox">
                                    <input type="hidden" class="caret" value="0" />
                                    <input type="text" class="titaInput" readonly />
                                    <div id="fakeCaret" class="caret" style="display: none;"></div>
                                </div>
                                <div class="buttonCont">
                                    <div class="buttonBox numKeyPad">
                                        <button class="tita-btn3" onclick="titaAppend(this,'backspace')">BackSpace</button>
                                    </div>
                                    <div class="buttonBox numKeyPad">
                                        <button class="tita-btn" onclick="titaAppend(this,'7')">7</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'8')">8</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'9')">9</button>
                                    </div>
                                    <div class="buttonBox numKeyPad">    
                                        <button class="tita-btn" onclick="titaAppend(this,'4')">4</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'5')">5</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'6')">6</button>
                                    </div>
                                    <div class="buttonBox numKeyPad">    
                                        <button class="tita-btn" onclick="titaAppend(this,'1')">1</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'2')">2</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'3')">3</button>
                                    </div>
                                    <div class="buttonBox numKeyPad">    
                                        <button class="tita-btn" onclick="titaAppend(this,'0')">0</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'.')">.</button>
                                        <button class="tita-btn" onclick="titaAppend(this,'-')">-</button>
                                    </div>
                                    <div class="buttonBox numKeyPad" style="display:none;">
                                        <button class="tita-btn2" onclick="titaAppend(this,'left')"><--</button>
                                        <button class="tita-btn2" onclick="titaAppend(this,'right')">--></button>
                                    </div>
                                    <div class="buttonBox numKeyPad">
                                        <button class="tita-btn3" onclick="titaAppend(this,'clear')">Clear All</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            } 
                            ?>
                        </div>
                    </div>

                    <div class="actions" id="action<?php echo $i; ?>">
                        <div>
                            <button class="btn btn-secondary btn-mark">
                                Mark for Review
                            </button>
                            <button class="btn btn-secondary btn-clear">
                                Clear Response
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-secondary btn-prev">
                                Previous
                            </button> 
     
                            <button class="btn btn-primary btn-next">
                                Save & Next
                            </button>
                        </div>
                    </div>
                </div>
                <?php 
                } ?>
            </div>
            <?php } ?>

        </div>

        <div class="panel-area">
            <div style="width:100%;height:100px;border-left:1px solid #000;display: flex;">
                <img src="https://static.thingsapp.co/catmocktest-static/profile.jpg"> 
                <div style="padding: 16px;">
                    <?php if(isset($order['customer_first_name'])){
                        echo '<p>' . trim($order['customer_first_name'] . ' ' . $order['customer_last_name']) . '</p>';
                    }
                    ?>
                </div>
            </div>

        <!-- Question Navigation Panel -->
        <?php 
        $i = 0;
        for($l=0; $l<count($sections); $l++){  
                    $section = $sections[$l];
                ?>
            <div class="question-panel" id="qpanel<?php echo $l; ?>" <?php if($l!=0) echo 'style="display:none;"'; ?>>

                <div class="legend" style="display: flex; flex-wrap: wrap;">
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(#A9DA2F,#64B77C);"></div>
                        <div class="legend-text">Answered</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(#F44D08,#B82D07);"></div>
                        <div class="legend-text">Not Answered</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #cccccc;"></div>
                        <div class="legend-text">Not Visited</div>
                    </div>                    
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(#855FF7,#6A4A84); border-radius:20px;"></div>
                        <div class="legend-text">Marked for Review</div>
                    </div>
                    <div class="legend-item" style="width:100%;">
                        <div class="legend-color" style="background: linear-gradient(#855FF7,#6A4A84);"></div>
                        <div class="legend-text">Answered & Marked for Review( will also be evaluated)</div>
                    </div>
                </div>
                <div style="clear:both;"></div>

                <!-- <div class="panel-title">Question Navigation</div> -->
                <div class="section-title"><?php echo $section['section_name']; ?></div>
                <div class="question-grid">
                    <?php
                        $qids = explode(",",$section['questions']);
                        for($j=0;$j<count($qids);$j++){
                            $i++;
                            if($i==0){
                                echo '<div class="question-number current" id="ano' . ($i) . '" data-answer="">' . ($i) . '</div>';
                            }
                            else{
                                echo '<div class="question-number" id="ano' . ($i) . '" data-answer="">' . ($i) . '</div>';
                            }
                            
                        }
                    ?>
                </div>
            </div>

            <?php if($l==(count($sections)-1)){ ?>
                <div style="padding:8px; width: 100%; background: #BCE8F5; position: relative; height: 60px;">
                    <button class="btn btn-secondary btn-submit submitBtnCont" style="margin: 0 auto; position: absolute; left: 50%; transform: translateX(-50%);">Submit</button>
                </div>
            <?php } ?>
 
        <?php } ?>
        </div>
    </div>

    <!-- Calculator Modal -->
    <div class="calculator-modal" id="calculatorModal">
        <div class="calculator-container">
            <div class="calculator-header">
                <div class="calculator-title">Calculator</div>
                <button class="close-calculator" id="closeCalculator">&times;</button>
            </div>
            <div class="calculator-display">
                <div class="history" id="calcHistory"></div>
                <div class="current" id="calcDisplay">0</div>
            </div>
            <div class="calculator-keypad">
                <button class="calc-btn function" onclick="calcClear()">C</button>
                <button class="calc-btn function" onclick="calcBackspace()">⌫</button>
                <button class="calc-btn function" onclick="calcPercent()">%</button>
                <button class="calc-btn operator" onclick="calcOperation('/')">÷</button>
                
                <button class="calc-btn" onclick="calcAppend('7')">7</button>
                <button class="calc-btn" onclick="calcAppend('8')">8</button>
                <button class="calc-btn" onclick="calcAppend('9')">9</button>
                <button class="calc-btn operator" onclick="calcOperation('*')">×</button>
                
                <button class="calc-btn" onclick="calcAppend('4')">4</button>
                <button class="calc-btn" onclick="calcAppend('5')">5</button>
                <button class="calc-btn" onclick="calcAppend('6')">6</button>
                <button class="calc-btn operator" onclick="calcOperation('-')">−</button>
                
                <button class="calc-btn" onclick="calcAppend('1')">1</button>
                <button class="calc-btn" onclick="calcAppend('2')">2</button>
                <button class="calc-btn" onclick="calcAppend('3')">3</button>
                <button class="calc-btn operator" onclick="calcOperation('+')">+</button>
                
                <button class="calc-btn" onclick="calcAppend('0')">0</button>
                <button class="calc-btn" onclick="calcAppend('.')">.</button>
                <button class="calc-btn" onclick="calcSqrt()">√</button>
                <button class="calc-btn equals" onclick="calcCalculate()">=</button>
            </div>
        </div>
    </div>


    <div id="sectionComplete"  style="background:#fff; display:none; font-size:12px;">
        <div style="width:100%; height:calc(100vh - 116px); display: flex;">
            <div style="width:100%;">
                <div style="width:100%;height:100px;border-bottom:1px solid #000; display: flex; justify-content: flex-end;">
                        <img src="https://static.thingsapp.co/catmocktest-static/profile.jpg"> 
                        <div style="padding: 16px;">
                            <?php if(isset($order['customer_first_name'])){
                                echo '<p>' . trim($order['customer_first_name'] . ' ' . $order['customer_last_name']) . '</p>';
                            }
                            ?>
                        </div>
                </div>
                <div class="instructionListCont" style="height:calc(100vh - 330px); overflow-y:scroll;">
                    <div class="instruction" style="width:100%; text-align:center;"><strong>Exam Summary</strong></div>
                    <?php for($l=0; $l<count($sections); $l++){ ?>
                    <div style="margin-bottom: 32px;">
                        <div class="instruction"><strong><?php echo $sections[$l]['section_name']; ?> : </strong><span id="section-status-<?php echo $l; ?>">(Yet to attempt)</span></div>

                        <div class="results" id="section-result-<?php echo $l; ?>" style="display: none;">
                            <table style="">
                                    <tr>
                                        <th>Section Name</th>
                                        <th>No. of Questions</th>
                                        <th>Answered</th>
                                        <th>Not Answered</th>
                                        <th>Marked for Review</th>
                                        <th>Answered & Marked for Review (will also be evaluated)</th>
                                        <th>Not Visited</th>
                                    </tr>
                                    <tr class="sectionData">
                                        <td><?php echo $sections[$l]['section_name']; ?></td>
                                        <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                        <td>I</td>
                                        <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                        <td>I</td>
                                        <td>Verbal Ability and Reading Comprehension (VARC)</td>
                                        <td>I</td>
                                    </tr>                            
                            </table>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div style="padding:8px 16px; width:100%; text-align: center;">
                   <span>Are you sure you want to exit this Group? Click 'Yes' to proceed; Click 'No' to go back.<br> Dear Candidate, Once the group is submitted, you cannot revisit and edit your responses.  </span>
                </div>
                <div class="actions" style="width:100%; display: flex; justify-content: center; gap: 32px;">
                    <div>
                        <button class="btn btn-secondary btn-exit1">Yes</button>
                    </div>
                    <div>
                        <button class="btn btn-secondary btn-back2">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sectionCompleteConfirm"  style="background:#fff; display:none; font-size:12px;">
        <div style="width:100%; height:calc(100vh - 116px); display: flex;">
            <div style="width:100%;">
                <div style="width:100%;height:100px;border-bottom:1px solid #000; display: flex; justify-content: flex-end;">
                    <img src="https://static.thingsapp.co/catmocktest-static/profile.jpg"> 
                    <div style="padding: 16px;">
                        <?php 
                            if(isset($order['customer_first_name'])){
                                echo '<p>' . trim($order['customer_first_name'] . ' ' . $order['customer_last_name']) . '</p>';
                            }
                        ?>
                    </div>
                </div>
                <div style="height: calc(100% - 100px); padding: 48px;">
                    <div style="padding:8px 16px; width:100%; text-align: center;">
                       <span>Are you sure to submit the group "<span class="secName1"></span>"?.  </span>
                    </div>
                    <div class="actions" style="width:100%; display: flex; justify-content: center; gap: 32px;">
                        <div>
                            <button class="btn btn-secondary btn-exit2">Yes</button>
                        </div>
                        <div>
                            <button class="btn btn-secondary btn-back3">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    

<style type="text/css">
.calculator {
  width: 260px;
  background: #f9f9f9;
  border: 2px solid #444;
  position: absolute;
  top: 100px;
  right: 10px;
  z-index: 1000;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
  user-select: none;
  border-radius: 8px;
  font-family: Arial;
  display: none;
}

.calc-header {
  background-color: #333;
  color: white;
  padding: 8px;
  cursor: move;
  font-weight: bold;
  border-radius: 8px 8px 0 0;
  display: flex;
    justify-content: space-between;
    align-items: center;
}

.close-calc {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
}

.calc-body {
  padding: 10px;
}

#display {
  width: 100%;
  height: 30px;
  margin-bottom: 10px;
  text-align: right;
  font-size: 18px;
}

.buttons button {
  width: 50px;
  height: 40px;
  margin: 2px;
  font-size: 16px;
  cursor: pointer;
}
.question-area{
    user-select : none;
}

</style>
<div id="calculator" class="calculator">
  <div class="calc-header" id="calcHeader">
    <div class="calc-title">Calculator</div>
    <button class="close-calc" id="closeCalc">×</button>
  </div>
  <div class="calc-body">
    <input type="text" id="display" disabled />
    <div class="buttons">
      <!-- Memory Buttons -->
      <button onclick="memoryStore()">MS</button>
      <button onclick="memoryRecall()">MR</button>
      <button onclick="memoryClear()">MC</button>
      <button onclick="memoryAdd()">M+</button>
      <button onclick="memorySubtract()">M-</button>

      <!-- Utility -->
      <button onclick="clearDisplay()">C</button>
      <button onclick="backspace()" style="width:108px;">←</button>

      <!-- Numbers and Operators -->
      <button onclick="append('7')">7</button>
      <button onclick="append('8')">8</button>
      <button onclick="append('9')">9</button>
      <button onclick="append('/')">/</button>

      <button onclick="append('4')">4</button>
      <button onclick="append('5')">5</button>
      <button onclick="append('6')">6</button>
      <button onclick="append('*')">*</button>

      <button onclick="append('1')">1</button>
      <button onclick="append('2')">2</button>
      <button onclick="append('3')">3</button>
      <button onclick="append('-')">-</button>

      <button onclick="append('0')">0</button>
      <button onclick="append('.')">.</button>
      <button onclick="calculate()">=</button>
      <button onclick="append('+')">+</button>
    </div>
  </div>
</div>

    <!-- Footer -->
    <footer>
        <p>© 2025 CAT Mock Test. This is a proctored mock test for practicing.</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script>
        var timing_json = '<?php echo $timing_json; ?>';
        var sections_json = '<?php echo $sections_json; ?>';
        var timing_arr = eval(timing_json);
        var section_arr = eval(sections_json);
       
        let total_time = <?php echo $timing_arr[0]['num_time']; ?>;
        let num_section = <?php echo count($sections); ?>;
        let current_section = 0;
        let qcount = 33;
        var current = 1;
        var startTime = <?php echo $timing_arr[0]['num_time']; ?>;
        var test = "<?php echo $test_name; ?>";
        var uid = "<?php echo $uid; ?>";
        var test_id = <?php echo $test_id; ?>;
        var sections = new Array();
        var status = "";


        function initSections(){
            for(var i=0;i<section_arr.length;i++){
                section_arr[i]['answered'] = 0;
                section_arr[i]['skipped'] = 0;
                section_arr[i]['review'] = 0;
                section_arr[i]['review_answer'] = 0;
                section_arr[i]['not_visited'] = section_arr[i]['num_questions'];
            }
        }
        initSections();
       
        function updateTimer() {
            const timerElement = document.getElementById('time');
            var minutes = Math.floor(total_time/60);
            var seconds = total_time%60;
            // Format minutes and seconds
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');
            
            timerElement.textContent = `${formattedMinutes}:${formattedSeconds}`;

            if(total_time <= 0){
                if(current_section<(num_section - 1)){
                    clearInterval(timerInterval);
                    saveCompleteSection();
                    current_section++;
                    changeSection(current_section);
                }
                else{
                    clearInterval(timerInterval);
                    var timetaken = startTime - total_time
                    //alert('Time is up! Your test will be submitted automatically.');
                    setTimeTaken(current);
                    saveResponse(current);
                    saveCompleteTest(timetaken);
                    $("#examCont").hide();
                    $('#completeCont').show();
                    $("#testNameHeader").hide();
                    $("#sectionComplete").hide();
                    $("#sectionCompleteConfirm").hide();
                    return;
                }
            }
            total_time--;
        }

        function changeSection(new_current_section){
            //if(new_current_section < timing_arr.length){
                $status = $("#qbox" + current).children('.myanswer').attr('data-status')
                setTimeTaken(current)
                saveResponse(current);
                $(".section-name").removeClass('section-select');
                $('.question-section').hide();
                $('#qcont' + new_current_section).show();
                $('.question-panel').hide();
                $('#qpanel' + new_current_section).show();
                current = timing_arr[new_current_section]['start_ques'];
                $("#qbox" + current).show();
                $("#ano" + current).addClass('skipped');
                $("#secname" + current_section).addClass('section-select');
                total_time = timing_arr[current_section]['num_time'];
                startTime = timing_arr[current_section]['num_time'];
                //console.log(current_section + "  " + total_time + "  " + startTime);
                timerInterval = setInterval(updateTimer, 1000);
                setCurrent()
                $("#examCont").show();
                $("#sectionComplete").hide();
                $("#sectionCompleteConfirm").hide();
                // if(new_current_section == (timing_arr.length -1)){
                //     $("#submitBtnCont").show();
                // }
            //}
        }

        var timerInterval = null;

        function startTest(){
            saveStartTest()
            status = "start";
            timerInterval = setInterval(updateTimer, 1000);
            setCurrent()
        }
        
        $('#initBtn').click(function() {
            $("#startCont").hide();
            $("#instruct1Cont").show();
            $("#instruct2Cont").hide();
        });

        $('#init2Btn').click(function() {
            $("#startCont").hide();
            $("#instruct1Cont").hide();
            $("#instruct2Cont").show();
        });

        $('#init3Btn').click(function() {
            $("#startCont").hide();
            $("#instruct1Cont").show();
            $("#instruct2Cont").hide();
        });

        $('#startBtn').click(function() {
            //console.log("Hiiii");
            $("#startCont").hide();
            $("#instruct1Cont").hide();
            $("#instruct2Cont").hide();
            $("#examCont").show();
            $("#testNameHeader").show();
            $('.pageClose').hide();
            startTest();
        });

        $('#resume1Btn').click(function() {
            $("#startCont").hide();
            $("#instruct1Cont").hide();
            $("#instruct2Cont").hide();
            $("#examCont").show();
            $("#testNameHeader").css('visibility','visible');
            $('.pageClose').hide();
            $("#switchTab").hide();
            resumeTest();
        });

        function resumeTest(){
            timerInterval = setInterval(updateTimer, 1000);
        }

        $('#agree_terms_text').click(function() {
            var $checkbox = $('#agree_terms');
            $checkbox.prop('checked', !$checkbox.prop('checked'));
            if ($('#agree_terms').is(':checked')) {
                $("#startBtn").prop('disabled', false).css('background-color', '#0d47a1');
              } else {
                $("#startBtn").prop('disabled', true).css('background-color', '#38aae9');
              }
        });

        $('#agree_terms').change(function() {
          if ($(this).is(':checked')) {
            $("#startBtn").prop('disabled', false).css('background-color', '#0d47a1');
          } else {
            $("#startBtn").prop('disabled', true).css('background-color', '#38aae9');
          }
        });

        $('.btn-submit').click(function() {
            $("#sectionComplete").show();
            $("#examCont").hide();
            $("#section-status-" + current_section).html("(Current Group)");
            var secData = section_arr[current_section];
            var ihtml='<td>' + secData['section_name'] + '</td><td>' + secData['num_questions'] + '</td>';
            ihtml+='<td>' + secData['answered'] + '</td><td>' + secData['skipped'] + '</td>';
            ihtml+='<td>' + secData['review'] + '</td><td>' + secData['review_answer'] + '</td>';
            ihtml+='<td>' + secData['not_visited'] + '</td>';

            $("#section-result-" + current_section).children().children().children('.sectionData').html(ihtml);
            $("#section-result-" + current_section).show();
        });

        $('.btn-exit1').click(function() {
            $("#sectionComplete").hide();
            $("#sectionCompleteConfirm").show();
            $(".secName1").html(section_arr[current_section]['section_name']);
        });

        $('.btn-back2').click(function() {
            $("#sectionComplete").hide();
            $("#examCont").show();
        });

        $('.btn-exit2').click(function() {
            $("#sectionComplete").hide();
            $("#sectionCompleteConfirm").hide();
            if(current_section!=(num_section - 1)){
                $("#examCont").show();
                clearInterval(timerInterval);
                saveCompleteSection();
                current_section++;
                changeSection(current_section)
            }
            else{
                clearInterval(timerInterval);
                var timetaken = startTime - total_time
                setTimeTaken(current);
                saveResponse(current);
                saveCompleteTest(timetaken);
                $("#examCont").hide();
                $('#completeCont').show();
                $("#testNameHeader").hide();
            }
        });

        $('.btn-back3').click(function() {
            $("#sectionCompleteConfirm").hide();
            $("#examCont").show();
        });

        $('.qpaper-btn').click(function() {
            $('#qpaper' + current_section).show();
        });

        $('.instruction-btn').click(function() {
            $('#instructionCont').show();
        });

        $('.closeqpaper').click(function() {
            $('.questionpaper-modal').hide();
        });



        function saveStartTest(){
            var data = new FormData();
            data.append("test_id",test_id);
            data.append("uid",uid);
             $.ajax({
                type: "POST",
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/apps/mocktest?route=start_test",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {
                    // console.log(data)
                },
                error: function (e) {
                    // $("#result").text(e.responseText);
                    // console.log("ERROR : ", e);
                },
            });
        }

        function saveCompleteTest(timetaken){
        	//var timetaken = startTime - total_time
            var data = new FormData();
            data.append("test_id",test_id);
            data.append("uid",uid);
            data.append("section_no",current_section);
            data.append("time",timetaken);
            data.append("section_id",section_arr[current_section]['section_id']);
            $.ajax({
                type: "POST",
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/apps/mocktest?route=complete_test",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {
                    // console.log(data)
                },
                error: function (e) {
                    // $("#result").text(e.responseText);
                    // console.log("ERROR : ", e);
                },
            });
		}

        function saveCompleteSection(){
        	var timetaken = startTime - total_time
            var data = new FormData();
            data.append("test_id",test_id);
            data.append("uid",uid);
            data.append("section_no",current_section);
            data.append("time",timetaken);
            data.append("section_id",section_arr[current_section]['section_id']);
            $.ajax({
                type: "POST",
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/apps/mocktest?route=complete_section",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {
                    // console.log(data)
                },
                error: function (e) {
                    // $("#result").text(e.responseText);
                    // console.log("ERROR : ", e);
                },
            });
        }        

        // Calculator functionality
        let calcDisplayValue = '0';
        let calcHistoryValue = '';
        let firstOperand = null;
        let waitingForSecondOperand = false;
        let operator = null;
        
        const calcDisplay = document.getElementById('calcDisplay');
        const calcHistory = document.getElementById('calcHistory');
        
        function calcUpdateDisplay() {
            calcDisplay.textContent = calcDisplayValue;
            calcHistory.textContent = calcHistoryValue;
        }
        
        function calcAppend(number) {
            if (waitingForSecondOperand) {
                calcDisplayValue = number;
                waitingForSecondOperand = false;
            } else {
                calcDisplayValue = calcDisplayValue === '0' ? number : calcDisplayValue + number;
            }
            calcUpdateDisplay();
        }
        
        function calcClear() {
            calcDisplayValue = '0';
            calcHistoryValue = '';
            firstOperand = null;
            waitingForSecondOperand = false;
            operator = null;
            calcUpdateDisplay();
        }
        
        function calcBackspace() {
            calcDisplayValue = calcDisplayValue.length === 1 ? '0' : calcDisplayValue.slice(0, -1);
            calcUpdateDisplay();
        }
        
        function calcPercent() {
            const value = parseFloat(calcDisplayValue);
            calcDisplayValue = String(value / 100);
            calcUpdateDisplay();
        }
        
        function calcSqrt() {
            const value = parseFloat(calcDisplayValue);
            if (value < 0) {
                calcDisplayValue = 'Error';
            } else {
                calcDisplayValue = String(Math.sqrt(value));
            }
            calcUpdateDisplay();
        }
        
        function calcOperation(nextOperator) {
            const inputValue = parseFloat(calcDisplayValue);
            
            if (operator && waitingForSecondOperand) {
                operator = nextOperator;
                calcHistoryValue = `${firstOperand} ${operator}`;
                calcUpdateDisplay();
                return;
            }
            
            if (firstOperand === null) {
                firstOperand = inputValue;
            } else if (operator) {
                const result = calcCalculate(firstOperand, inputValue, operator);
                calcDisplayValue = String(result);
                firstOperand = result;
            }
            
            waitingForSecondOperand = true;
            operator = nextOperator;
            calcHistoryValue = `${calcDisplayValue} ${operator}`;
            calcUpdateDisplay();
        }
        
        function calcCalculate(operand1, operand2, op) {
            switch(op) {
                case '+':
                    return operand1 + operand2;
                case '-':
                    return operand1 - operand2;
                case '*':
                    return operand1 * operand2;
                case '/':
                    return operand1 / operand2;
                default:
                    return operand2;
            }
        }
        
        function calcCalculate() {
            if (operator === null || waitingForSecondOperand) {
                return;
            }
            
            const inputValue = parseFloat(calcDisplayValue);
            const result = calcCalculate(firstOperand, inputValue, operator);
            
            calcDisplayValue = String(result);
            calcHistoryValue = `${firstOperand} ${operator} ${inputValue} =`;
            firstOperand = null;
            operator = null;
            waitingForSecondOperand = false;
            calcUpdateDisplay();
        }
        
        // // Modal functionality
        //const calculatorModal = document.getElementById('calculatorModal');
        // const openCalculatorBtn = document.getElementById('openCalculator');
        // const closeCalculatorBtn = document.getElementById('closeCalculator');
        
        // openCalculatorBtn.addEventListener('click', () => {
        //     // calculatorModal.style.display = 'flex';
        // });
        
        // closeCalculatorBtn.addEventListener('click', () => {
        //     calculatorModal.style.display = 'none';
        // });
        
        // window.addEventListener('click', (event) => {
        //     if (event.target === calculatorModal) {
        //         calculatorModal.style.display = 'none';
        //     }
        // });

        const qpaperModal0 = document.getElementById('qpaper0');
        const qpaperModal1 = document.getElementById('qpaper1');
        const qpaperModal2 = document.getElementById('qpaper2');
        const instructionModal =  document.getElementById('instructionCont');

        window.addEventListener('click', (event) => {
            if (event.target === qpaperModal0) {
                qpaperModal0.style.display = 'none';
            }
            if (event.target === qpaperModal1) {
                qpaperModal1.style.display = 'none';
            }
            if (event.target === qpaperModal2) {
                qpaperModal2.style.display = 'none';
            }
            if (event.target === instructionModal) {
                instructionModal.style.display = 'none';
            }
        });

        $('.option').click(function() {
            $(this).parent().children('.option').removeClass('selected');
            //$(this).parent().children('.option').children('input[type=radio]').removeAttr('checked');

            $(this).addClass('selected');
            //console.log($(this).children('input[type=radio]'));
            $(this).children('input[type=radio]').prop("checked", true);
            var id = $(this).parent().parent().parent().parent().attr('id');
            id = parseInt(id.replace("qbox", ""));

 
            var optionid = $(this).children('input[type=radio]').attr('data-id');
            optionid = parseInt(optionid.replace("option", ""));

            //console.log(optionid);

            var optiontext = $(this).children('.option-text').html();
            console.log(optiontext);
            $("#qbox" + id).children('.myanswer').attr('data-option',optionid);
            $("#qbox" + id).children('.myanswer').attr('data-answer',optiontext);
        });
        
        $('.question-number').click(function() {
            setTimeTaken(current)
            saveResponse(current);
            $(".question-area").hide();
            $("#ano" + current).removeClass('current');
            var id = $(this).attr('id');
            id = parseInt(id.replace("ano", ""));
            current = id
            $("#qbox" + id).show();
            $("#ano" + id).addClass('current');
            setCurrent()
        });

        function clearAnswerStatusClass(id){
            $("#ano" + id).removeClass('marked');
            $("#ano" + id).removeClass('marked2');
            $("#ano" + id).removeClass('skipped');
            $("#ano" + id).removeClass('answered');
            $("#ano" + id).removeClass('current');
        }

        function setResponse(contId){
            id = contId.replace("action", "");
            id = parseInt(id.replace("qbox", "")); 
 
            var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
            var old_status = $("#qbox" + id).children('.myanswer').attr('data-status');
            console.log('old_status2 ' + old_status);
            section_arr[current_section][old_status]--;
            
            clearAnswerStatusClass(id);
            if(val1!=""){
                $("#ano" + id).addClass('answered');
                $("#qbox" + id).children('.myanswer').attr('data-status','answered');
                section_arr[current_section]['answered']++;
            }
            else{
                $("#ano" + id).addClass('skipped');
                $("#qbox" + id).children('.myanswer').attr('data-status','skipped');
                section_arr[current_section]['skipped']++;
            }
            //console.log(section_arr[current_section]);
            setTimeTaken(id);
            saveResponse(id);
            var new_status = $("#qbox" + id).children('.myanswer').attr('data-status');
            console.log('new_status2 ' + new_status);
            return id
        }

        $('.btn-prev').click(function() {
            var contId = $(this).parent().parent().attr('id');
            var id = setResponse(contId);
            if(id>timing_arr[current_section]['start_ques']){
                $("#qbox" + id).hide();
                current = id - 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        $('.btn-next').click(function() {
            var contId = $(this).parent().parent().attr('id');
            var id = setResponse(contId);
            if(id<timing_arr[current_section]['end_ques']){
                $("#qbox" + id).hide();
                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            else{
                $("#qbox" + id).hide();
                current = timing_arr[current_section]['start_ques'];
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        $('.btn-clear').click(function() {
            var id = $(this).parent().parent().attr('id');
            id = parseInt(id.replace("action", ""));
            var old_status = $("#qbox" + id).children('.myanswer').attr('data-status');
            $("#qbox" + id).children('.myanswer').attr('data-status','skipped');
            $("#qbox" + id).children('.myanswer').attr('data-answer','');
            $("#qbox" + id).children('.myanswer').attr('data-option','0');
            $("#ano" + id).removeClass('answered');
            $("#ano" + id).removeClass('review_answer');
            $("#ano" + id).removeClass('review');
            $("#ano" + id).addClass('skipped');
            $("#qbox" + id).children('.questionCombine').children('.question-box').children('.options').children('.option').removeClass('selected');
            $("#qbox" + id).children('.questionCombine').children('.question-box').children('.options').children('.option').children('input[type=radio]').prop('checked', false);
            setTimeTaken(id)
            saveResponse(id);
            section_arr[current_section][old_status]--;
            section_arr[current_section]['skipped']++;
            if(id<timing_arr[current_section]['end_ques']){
                $("#qbox" + id).hide();
                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            else{
                $("#qbox" + id).hide();
                current = timing_arr[current_section]['start_ques'];
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        function setTimeTaken(id){
            //console.log("qwery " + current_section + "  " + total_time + "  " + startTime);
            timeRemaining =  total_time//minutes*60 + seconds
            timetaken = startTime - timeRemaining
            earlierTimeTaken = parseInt($("#qbox" + id).children('.myanswer').attr('data-time'))
            timetaken = timetaken + earlierTimeTaken
            $("#qbox" + id).children('.myanswer').attr('data-time',timetaken)
            startTime = timeRemaining
        }

        $('.btn-mark').click(function() {
            var id = $(this).parent().parent().parent().attr('id');
            id = parseInt(id.replace("qbox", ""));
            if(id<timing_arr[current_section]['end_ques']){
                $("#qbox" + id).hide();
                clearAnswerStatusClass(id);

                var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
                var old_status = $("#qbox" + id).children('.myanswer').attr('data-status');
                console.log("old status " + old_status);
                section_arr[current_section][old_status]--;
                if(val1!=""){
                    $("#ano" + id).addClass('marked2');
                    $("#qbox" + id).children('.myanswer').attr('data-status','review_answer');
                    section_arr[current_section]['review_answer']++;
                }
                else{
                    $("#qbox" + id).children('.myanswer').attr('data-status','review');
                    $("#ano" + id).addClass('marked');
                    section_arr[current_section]['review']++;
                }
                setTimeTaken(id);
                saveResponse(id);

                var new_status = $("#qbox" + id).children('.myanswer').attr('data-status');
                console.log("new status " + new_status);

                // console.log(current + "  " + old_status);

                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
                // console.log(current);
                // console.log(section_arr);
            }
            else{
                $("#qbox" + id).hide();
                $("#ano" + id).removeClass('skipped');
                clearAnswerStatusClass(id);
                //$("#ano" + id).addClass('marked');

                var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
                var old_status = $("#qbox" + id).children('.myanswer').attr('data-status');
                console.log("old status1 " + old_status);
                section_arr[current_section][old_status]--;
                if(val1!=""){
                    //$("#ano" + id).addClass('answered');
                    $("#ano" + id).addClass('marked2');
                    $("#qbox" + id).children('.myanswer').attr('data-status','review_answer');
                    section_arr[current_section]['review_answer']++;
                }
                else{
                    $("#ano" + id).addClass('marked');
                    $("#qbox" + id).children('.myanswer').attr('data-status','review');
                    section_arr[current_section]['review']++;
                }
                setTimeTaken(id);
                saveResponse(id);
                console.log(current + "  " + old_status);
                var new_status = $("#qbox" + id).children('.myanswer').attr('data-status');
                console.log("new status1 " + new_status);
                current = 1
                $("#qbox" + current).show();
                setCurrent()
                console.log(current);
                console.log(section_arr);
            }
            setTimeTaken(id)
        });

        function setCurrent(){
            var old_status = $("#qbox" + current).children('.myanswer').attr('data-status');
            if(old_status=="not_visited"){
                section_arr[current_section][old_status]--;
                $("#ano" + current).addClass('skipped');
                $("#qbox" + current).children('.myanswer').attr('data-status','skipped');
                section_arr[current_section]['skipped']++;           
            }
            //console.log(section_arr[current_section]);
            setTimeTaken(current);
        }

        function saveResponse(id){
            var data = new FormData();
            data.append("qno", id);
            data.append("answer", $("#qbox" + id).children('.myanswer').attr('data-answer'));
            data.append("status", $("#qbox" + id).children('.myanswer').attr('data-status'));
            data.append("time", $("#qbox" + id).children('.myanswer').attr('data-time'));
            data.append("question",$("#qbox" + id).children('.myanswer').attr('data-question'));
            data.append("option",$("#qbox" + id).children('.myanswer').attr('data-option'));
            data.append("test",test);
            data.append("test_id",test_id);
            data.append("uid",uid);
            $.ajax({
                type: "POST",
                url: "/apps/mocktest?route=save_response",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {
                    // console.log(data)
                },
                error: function (e) {
                    retrySaveResponse(data);
                },
            });
            hideCalculator();
        }

        function retrySaveResponse(data){
            $.ajax({
                type: "POST",
                url: "/apps/mocktest?route=save_response",
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (data) {
                    // console.log(data)
                },
                error: function (e) {
                    // $("#result").text(e.responseText);
                    // console.log("ERROR : ", e);
                },
            });
        }

        // Initialize calculator display
       //calcUpdateDisplay();
    </script>
    <script type="text/javascript">
        $('.titaInput').on('keydown', function(e) {
            e.preventDefault();
        });

        // function updateCaretPosition(id1) {
        //     const input = $("#" + id1).children('.titaInputBox').children('.titaInput');
        //     const caret = $("#" + id1).children('.titaInputBox').children('.caret');
        //     const val = $(input).val();
        //     const font = $(input).css('font');
        //     const letterSpacing = parseFloat($(input).css('letter-spacing')) || 0;
        //     const padding = parseFloat($(input).css('padding-left')) || 0;

        //     const canvas = document.createElement("canvas");
        //     const ctx = canvas.getContext("2d");
        //     ctx.font = font;

        //     const textBeforeCaret = val.substring(0, caretPos);
        //     let width = ctx.measureText(textBeforeCaret).width + caretPos * letterSpacing;

        //     $caret.css('left', (padding + width) + 'px');
        // }

        function titaAppend(elm,string){
            console.log($(elm));
            var id1 = $(elm).parent().parent().parent().attr("id");
            var value1 = $("#" + id1).children('.titaInputBox').children('.titaInput').val();
            $('input').on('keydown', function(e) {
              e.preventDefault();
            });
            var value2 = value1;
            //input.addEventListener("keydown", (e) => e.preventDefault());
            var caretPos = $("#" + id1).children('.titaInputBox').children('.caret').val();
            if (string === "left") {
                caretPos = Math.max(0, caretPos - 1);
            } 
            else if (string === "right") {
                caretPos = Math.min(value1.length, caretPos + 1);
            } 
            else if (string === "-") {
                if(parseInt(value2)>0){
                   value2 = "-" + value2; 
                }
                caretPos = Math.min(value1.length, caretPos + 1);
            } 
            else if (string === "backspace") {
                if (caretPos > 0) {
                  value2 = value1.slice(0, caretPos - 1) + value1.slice(caretPos);
                  caretPos--;
                }
            } else if (string === "clear") {
                value2 = "";
                caretPos = 0;
            } else {
                // Insert character at caret position
                value2 = value1.slice(0, caretPos) + string + value1.slice(caretPos);
                caretPos++;
            }

            //var value2 = value1 + string;
            $("#" + id1).children('.titaInputBox').children('.titaInput').val(value2);
            $("#" + id1).children('.titaInputBox').children('.caret').val(caretPos);
            var id = parseInt(id1.replace("tita", ""));
            $("#qbox" + id).children('.myanswer').attr('data-option',0);
            $("#qbox" + id).children('.myanswer').attr('data-answer',value2);
            console.log(id1);
        }


    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            console.log("User switched away from the tab/window.");
            if(status=="start"){                
                clearInterval(timerInterval);
                $("#testNameHeader").css('visibility','hidden');
                $("#sectionComplete").hide();
                $("#sectionCompleteConfirm").hide();
                $("#examCont").hide();
                $("#switchTab").show();
            }
         } else {
            console.log("User returned to the tab/window.");
            // Perform actions like resuming activities, fetching updates, etc.
        }
        // Alternatively, using document.visibilityState
        console.log("Visibility state:", document.visibilityState);
    });

    // window.addEventListener('onunload', (event) => { 
    //     e.preventDefault();
    //     e.returnValue = 'Are you sure you want to leave?';
    // });

    // window.addEventListener('beforeunload', function (e) {
    //     e.preventDefault();
    //     e.returnValue = 'Are you sure you want to leave?';
    // });

    </script>
    <script type="text/javascript">
        let memoryValue = 0;

        $('#openCalculator').click(function() {
            showCalculator();
        });

        $('#closeCalc').click(function() {
            hideCalculator();
        });

        function hideCalculator(){
            $('#calculator').hide();
            $('#calculator').css({'top':'100px','right':'10px','left':'auto'})
        }

        function showCalculator(){
            $('#calculator').show();
            $('#calculator').css({'top':'100px','right':'10px','left':'auto'})
        }

function append(val) {
  document.getElementById("display").value += val;
}

function calculate() {
  try {
    const result = eval(document.getElementById("display").value);
    document.getElementById("display").value = result;
  } catch (e) {
    document.getElementById("display").value = "Error";
  }
}

function clearDisplay() {
  document.getElementById("display").value = "";
}

function backspace() {
  const display = document.getElementById("display");
  display.value = display.value.slice(0, -1);
}

// Memory Functions
function memoryStore() {
  const val = parseFloat(document.getElementById("display").value);
  if (!isNaN(val)) memoryValue = val;
}

function memoryRecall() {
  document.getElementById("display").value += memoryValue;
}

function memoryClear() {
  memoryValue = 0;
}

function memoryAdd() {
  const val = parseFloat(document.getElementById("display").value);
  if (!isNaN(val)) memoryValue += val;
}

function memorySubtract() {
  const val = parseFloat(document.getElementById("display").value);
  if (!isNaN(val)) memoryValue -= val;
}

// Drag functionality
dragElement(document.getElementById("calculator"));

function dragElement(elmnt) {
  const header = document.getElementById("calcHeader");
  let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

  header.onmousedown = dragMouseDown;

  function dragMouseDown(e) {
    e.preventDefault();
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e.preventDefault();
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
    </script>
        <script>
      //(A) PREVENT CONTEXT MENU FROM OPENING
      document.addEventListener("contextmenu", (evt) => {
        evt.preventDefault();
      }, false);

      // (B) PREVENT CLIPBOARD COPYING
      document.addEventListener("copy", (evt) => {
        // (B1) CHANGE THE COPIED TEXT IF YOU WANT
        //evt.clipboardData.setData("text/plain", "Copying is not allowed on this webpage");

        // (B2) PREVENT THE DEFAULT COPY ACTION
        evt.preventDefault();
      }, false);


      $(document).bind('keydown', 'ctrl+u', function(e) {
      e.preventDefault();

      return false;
    },false);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</body>
</html>