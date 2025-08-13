<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
include_once dirname(__FILE__) . '/../modules/mocktest/classes/MockTestManager.php';
//print_r($_REQUEST);
$uid = $_REQUEST['customer_id'];
$manager = new MockTestManager();
// $obj = $manager->getIncompleteNockTests($uid);

$sections = array();
$questions = array();
$userTest = array();
$timing_arr = array();

// if($obj->getValue('id')!=0){
//     list($sections,$questions) = $manager->getExamQuestions($obj->getValue('exam_id'));
//     $userTest = $obj->getObject();
//     $num_ques = 1;
//     $total_time = $sections[0]['total_time'];
//     for($i=0; $i<count($sections); $i++){
//         $obj1 = array();
//         $obj1['end_time'] = $total_time - $sections[$i]['num_time'];
//         $obj1['start_ques'] = $num_ques;
//         $obj1['end_ques'] = $num_ques + $sections[$i]['num_questions'] - 1;
//         $total_time = $obj1['end_time'];
//         $num_ques = $obj1['end_ques'] + 1;
//         array_push($timing_arr,$obj1);
//     }
// }
$timing_json = json_encode($timing_arr);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAT 2025 Mock Test</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/app.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-strip">
            <div class="logo-container">
                <div class="logo"><img src="https://catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=600" alt="CatMockTest" srcset="//catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=300 300w, //catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=450 450w, //catmocktest.com/cdn/shop/files/Lanka_800_x_800_px_29146e2f-a8d6-4c8b-afbd-c67eb56fc3ed.png?v=1750919570&amp;width=600 600w" width="" height="50" loading="eager" class="header__heading-logo" sizes="(min-width: 750px) 300px, 50vw"></div>
            </div>
        </div>
    </header>
    <div class="header-strip2">
        <div>
            <div>Common Admission Test 2025 - <?php echo $sections[0]['test_name']; ?></div>
        </div>
        <div class="header-controls">
                <button class="instruction-btn">
                    <i class="fas fa-calculator"></i>
                    <span>View Instructions</span>
                </button>
                <button class="calculator-btn">
                    <i class="fas fa-calculator"></i>
                    <span>Question Paper</span>
                </button>
        </div>
    </div>

    <div id="startCont" style="display:none;">
        <div style="width:100%; height:calc(100vh - 115px); padding:32px;">
        Loading Page
        <a href="javascript:void(0);" id="initBtn">Start Test</a>
        </div>
    </div>



    <div id="instruct2Cont" style="display:none;">
        <div style="width:100%; height:calc(100vh - 115px); padding:32px;">
            Instruction Second Page
            <a href="javascript:void(0);" id="startBtn">Start</a>&nbsp;&nbsp;
            <a href="javascript:void(0);" id="init3Btn">Previous</a>
        </div>
    </div>

    <div id="completeCont" style="display:none;">
        <div style="width:100%; height:calc(100vh - 115px); padding:32px;">
        Test Completed
        </div>
    </div>    

    <div class="container" id="examCont" style="display:none;">        
        <div class="content-area">
            <div>
                <div class="header-strip3">
                    <div style="display:flex; flex-wrap: wrap;">
                        <div class="section-name">QA <i class="fas fa-calculator"></i></div>
                        <div class="section-name">DILR <i class="fas fa-calculator"></i></div>
                        <div class="section-name">VARC <i class="fas fa-calculator"></i></div>
                    </div>
                    <div class="header-controls">
                        <button class="yellow-btn">
                            <i class="fas fa-clock"></i>
                        </button>
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
                                    $total_time = $sections[0]['total_time'];
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
                        <div class="section-name"><?php echo $section['section_name']; ?></div>
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

                    <div class="myanswer" style="display:none;" data-option="0" data-answer="" data-question="<?php echo $question['question_id']; ?>" data-status="" data-time="0"></div>


                    <div class="questionCombine">    

                        <?php if($question['group_type']!='unknown'){ ?>
                        <div class="comprehension-box">
                            <!-- <h3 class="comprehension-title">Reading Comprehension</h3> -->
                            <div class="comprehension-content">
                                <?php if($question['pic']!=''){
                                ?>
                                    <img src="https://static.thingsapp.co/catmocktest/<?php echo $question['pic']; ?>" style="height:50%; max-height:300px;">
                                <?php
                                }
                                 if($question['paragraph']!=''){
                                ?>
                                     <p><?php echo $question['paragraph']; ?></p>
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
                                <?php echo $question['question_text']; ?>
                            </div>


                            
                            <?php if($question['question_type']=="MCQ"){ ?>
                            <div class="options">
                                <?php 
                                for($k=0;$k<count($question['options']);$k++){
                                    $option = $question['options'][$k];
                                    $ascii = 65 + $j;
                                ?>
                                <div class="option">
                                    <input type="radio" data-id="option<?php echo ($j + 1); ?>" name="answerOption<?php echo ($i + 1); ?>">
                                   <!--  <div class="option-label">&#<?php echo $ascii; ?>;</div> -->
                                    <div class="option-text"><?php echo $option['option_text']; ?></div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php 
                            }else{

                            } ?>
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
        <div style="width:100%; height:150px; border-left:1px solid #000;">
        </div>

        <!-- Question Navigation Panel -->
        <?php 
        $i = 0;
        for($l=0; $l<count($sections); $l++){  
                    $section = $sections[$l];
                ?>
            <div class="question-panel" id="qpanel<?php echo $l; ?>" <?php if($l!=0) echo 'style="display:none;"'; ?>>

                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #0d47a1;"></div>
                        <div>Current Question</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #4caf50;"></div>
                        <div>Answered</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ff9800;"></div>
                        <div>Marked for Review</div>
                    </div>
                </div>

                <div class="panel-title">Question Navigation</div>
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
                <div style="padding:8px; display:none;" id="submitBtnCont">
                    <button class="btn btn-secondary btn-submit">Submit</button>
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

    <!-- Footer -->
    <footer>
        <p>© 2025 CAT Mock Test. This is a simulation for practice purposes only.</p>
    </footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script>
        var timing_json = '<?php echo $timing_json; ?>';
        var timing_arr = eval(timing_json);
        //console.log(timing_arr);
        // Timer functionality
        let total_time = <?php echo $sections[0]['total_time']; ?>;
        let num_section = <?php echo count($sections); ?>;
        let current_section = 0;
        //let minutes = 59;
        //let seconds = 59;
        let qcount = 33;
        var current = 1;
        var startTime = 3600;
        var test = "<?php echo $sections[0]['test_name']; ?>";
        var uid = "11";
        var test_id = <?php echo $userTest['id']; ?>
        
        function updateTimer() {
            const timerElement = document.getElementById('time');
            var minutes = Math.floor(total_time/60);
            var seconds = total_time%60;
            // Format minutes and seconds
            const formattedMinutes = String(minutes).padStart(2, '0');
            const formattedSeconds = String(seconds).padStart(2, '0');
            
            timerElement.textContent = `${formattedMinutes}:${formattedSeconds}`;

            if(total_time <= 0){
                clearInterval(timerInterval);
                //alert('Time is up! Your test will be submitted automatically.');
                saveCompleteTest();
                $("#examCont").hide();
                $('#completeCont').show();
                return;
            }
            else if(total_time <= timing_arr[current_section]['end_time']){
                current_section++;
                changeSection(current_section);
            }
            
            // Update time
            // if (seconds === 0) {
            //     if (minutes === 0) {
            //         // Time's up
            //         clearInterval(timerInterval);
            //         alert('Time is up! Your test will be submitted automatically.');
            //         return;
            //     }
            //     minutes--;
            //     seconds = 59;
            // } else {
            //     seconds--;
            // }
            total_time--;
        }

        function changeSection(new_current_section){
            if(new_current_section < timing_arr.length){
                $status = $("#qbox" + current).children('.myanswer').attr('data-status')
                setTimeTaken(current)
                saveResponse(current);
 
                $('.question-section').hide();
                $('#qcont' + new_current_section).show();
                $('.question-panel').hide();
                $('#qpanel' + new_current_section).show();
                current = timing_arr[new_current_section]['start_ques'];
                $("#qbox" + current).show();
                $("#ano" + current).addClass('skipped');
                if(new_current_section == (timing_arr.length -1)){
                    $("#submitBtnCont").show();
                }

            }
        }

        var timerInterval = null;

        function startTest(){
            saveStartTest()
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
            $("#startCont").hide();
            $("#instruct1Cont").hide();
            $("#instruct2Cont").hide();
            $("#examCont").show();
            startTest();
        });

        $('.btn-submit').click(function() {
            clearInterval(timerInterval);
                //alert('Time is up! Your test will be submitted automatically.');
                saveCompleteTest();
                $("#examCont").hide();
                $('#completeCont').show();
        });

        function saveStartTest(){
            var data = new FormData();
            data.append("test_id",test_id);
            data.append("uid",uid);
             $.ajax({
                type: "POST",
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/mymocktest/views/view?route=start_test",
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


        function saveCompleteTest(){
            var data = new FormData();
            data.append("test_id",test_id);
            data.append("uid",uid);
             $.ajax({
                type: "POST",
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/mymocktest/views/view?route=complete_test",
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
        
        // Modal functionality
        const calculatorModal = document.getElementById('calculatorModal');
        const openCalculatorBtn = document.getElementById('openCalculator');
        const closeCalculatorBtn = document.getElementById('closeCalculator');
        
        openCalculatorBtn.addEventListener('click', () => {
            calculatorModal.style.display = 'flex';
        });
        
        closeCalculatorBtn.addEventListener('click', () => {
            calculatorModal.style.display = 'none';
        });
        
        window.addEventListener('click', (event) => {
            if (event.target === calculatorModal) {
                calculatorModal.style.display = 'none';
            }
        });





        // // Question navigation and selection
        // document.querySelectorAll('.option').forEach(option => {
        //     option.addEventListener('click', () => {
        //         // Remove selected class from all options
        //         option.parentElement.querySelectorAll('.option').forEach(opt => {
        //             opt.classList.remove('selected');
        //         });
                
        //         // Add selected class to clicked option
        //         option.classList.add('selected');
                
        //         // Check the radio button
        //         const radio = option.querySelector('input[type="radio"]');
        //         radio.checked = true;
        //         //callMe();
        //         });
        // });

        $('.option').click(function() {
            $(this).parent().children('.option').removeClass('selected');
            //$(this).parent().children('.option').children('input[type=radio]').removeAttr('checked');

            $(this).addClass('selected');
            console.log($(this).children('input[type=radio]'));
            $(this).children('input[type=radio]').prop("checked", true);
            var id = $(this).parent().parent().parent().parent().attr('id');
            id = parseInt(id.replace("qbox", ""));

 
            var optionid = $(this).children('input[type=radio]').attr('data-id');
            optionid = parseInt(optionid.replace("option", ""));

            var optiontext = $(this).children('.option-text').html();

            $("#qbox" + id).children('.myanswer').attr('data-option',optionid);
            $("#qbox" + id).children('.myanswer').attr('data-answer',optiontext);
        });
        
        // // Mark question numbers as answered when clicked
        // document.querySelectorAll('.question-number').forEach(qn => {
        //     qn.addEventListener('click', function() {
        //         // Toggle answered state
        //         if (this.classList.contains('answered')) {
        //             this.classList.remove('answered');
        //         } else {
        //             this.classList.add('answered');
        //         }
        //     });
        // });

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
        


        function setResponse(contId){
            id = contId.replace("action", "");
            id = parseInt(id.replace("qbox", "")); 
 
            var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
            if(val1!=""){
                $("#ano" + id).addClass('answered');
                $("#qbox" + id).children('.myanswer').attr('data-status','answered');
            }
            else{
                $("#qbox" + id).children('.myanswer').attr('data-status','skipped');
            }
            setTimeTaken(id);
            saveResponse(id);
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
            console.log("helloooo");
            var contId = $(this).parent().parent().attr('id');
            console.log(contId)
            var id = setResponse(contId);
            if(id<timing_arr[current_section]['end_ques']){
                $("#qbox" + id).hide();
                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        $('.btn-clear').click(function() {
            var id = $(this).parent().parent().attr('id');
            id = parseInt(id.replace("action", ""));
            $("#qbox" + id).children('.myanswer').attr('data-status','clear');
            $("#qbox" + id).children('.myanswer').attr('data-answer','');
            $("#qbox" + id).children('.myanswer').attr('data-option','0');
            $("#ano" + id).removeClass('answered');
            $("#ano" + id).removeClass('review_answer');
            $("#ano" + id).removeClass('review');
            $("#ano" + id).addClass('skipped');
            $("#qbox" + id).children('.question-box').children('.options').children('.option').removeClass('selected');
            $("#qbox" + id).children('.question-box').children('.options').children('.option').children('input[type=radio]').prop('checked', false);
            setTimeTaken(id)
            saveResponse(id);
            if(id>1){
                $("#qbox" + id).hide();
                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        

        function setTimeTaken(id){
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
            if(id<qcount){
                $("#qbox" + id).hide();
                $("#ano" + id).removeClass('skipped');
                $("#ano" + id).addClass('marked');

                var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
                if(val1!=""){
                    $("#ano" + id).addClass('marked2');
                    $("#qbox" + id).children('.myanswer').attr('data-status','review_answer');
                }
                else{
                    $("#qbox" + id).children('.myanswer').attr('data-status','review');
                    $("#ano" + id).addClass('marked');
                }
                setTimeTaken(id);
                saveResponse(id);

                current = id + 1;
                $("#qbox" + current).show();
                setCurrent()
            }
            else{
                $("#qbox" + id).hide();
                $("#ano" + id).removeClass('skipped');
                $("#ano" + id).addClass('marked');

                var val1 = $("#qbox" + id).children('.myanswer').attr('data-answer');
                if(val1!=""){
                    $("#ano" + id).addClass('answered');
                    $("#qbox" + id).children('.myanswer').attr('data-status','review_answer');
                }
                else{
                    $("#qbox" + id).children('.myanswer').attr('data-status','review');
                }
                setTimeTaken(id);
                saveResponse(id);

                current = 1
                $("#qbox" + current).show();
                setCurrent()
            }
            setTimeTaken(id)
        });

        function setCurrent(){
            var val1 = $("#qbox" + current).children('.myanswer').attr('data-status');
            console.log(val1);
            if(val1==""){
                $("#ano" + current).addClass('skipped');
                $("#qbox" + current).children('.myanswer').attr('data-status','skipped');
            }
            setTimeTaken(current);
            //return current
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
                        // url: "https://tech.thingsapp.co/check.php",
                url: "/mymocktest/views/view?route=save_response",
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
</body>
</html>