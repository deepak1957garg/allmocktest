<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
//print_r($_REQUEST);
$uid = 0;
if(isset($_REQUEST['customer_id'])){
    $uid = $_REQUEST['customer_id'];
}

$istest = 0;
$sections = array();
$questions = array();
$userTest = array();
$timing_arr = array();
$total_time = 0;
$order = array();

$final_total_time = 0;
$test_name = "";
$test_id = "0";
$template_name = "Mock Test";
$exam_id = 19;

    $manager = new MockTestManager();
    //$obj = $manager->getIncompleteNockTests($uid);
    
    $template = $manager->getTemplateInfo($exam_id);
    if(isset($template['template_name']))   $template_name = $template['template_name'];

        list($sections,$questions) = $manager->getExamQuestions($exam_id);
 
        //print_r(array_keys($questions));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAT 2025 Mock Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://static.thingsapp.co/catmocktest-static/appv202507261655.css">
 <style>
    .questionpaper-modal { display: block; position: unset; }
    .questionpaper-container { position: unset; width: 100%; }
 </style>
</head>
<body>

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
                                           <tr><td class="firstCol"><?php echo $question['question_type']; ?></td><td><?php echo $question['question_id']; ?></td></tr>
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

   



<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</body>
</html>