<?php
session_start();
// Quiz Data
$quizQuestions = array(
    array(
        'question' => 'What does HTML stand for?',
        'choices' => array('Hyper Text Markup Language', 'Highly Typed Machine Learning', 'Hyper Transfer Markup Language', 'High Text Machine Learning'),
        'correctAnswer' => 'Hyper Text Markup Language'
    ),
    array(
        'question' => 'In PHP, how do you start a session?',
        'choices' => array('start_session()', 'session_start()', 'new_session()', 'init_session()'),
        'correctAnswer' => 'session_start()'
    ),
    array(
        'question' => 'What is the purpose of the PHP function "echo"?',
        'choices' => array('To display text on the screen', 'To declare a variable', 'To create a loop', 'To include a file'),
        'correctAnswer' => 'To display text on the screen'
    ),
    array(
        'question' => 'In PHP, what does the acronym "PDO" stand for?',
        'choices' => array('PHP Data Objects', 'PHP Database Operations', 'Programming Data Output', 'Public Data Organization'),
        'correctAnswer' => 'PHP Data Objects'
    ),
    array(
        'question' => 'Which of the following is a superglobal variable in PHP?',
        'choices' => array('$GLOBALS', '$super', '$_SESSION', '$_POST'),
        'correctAnswer' => '$GLOBALS'
    )
);

$feedback = array();
$timeLimit = 300;

// Timer functionality (JavaScript)
echo '<script>
    var timeLimit = 300; // 5 minutes
    var timer = setInterval(function() {
        timeLimit--;
        document.getElementById("timer").innerHTML = "Time left: " + timeLimit + " seconds";
        if (timeLimit <= 0) {
            clearInterval(timer);
            document.forms[0].submit();
        }
    }, 1000);
</script>';

// Quiz Interface
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Application</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>';
echo '<h2 class="text-center">Start Quiz</h2>';
echo '<p id="timer" class="text-center">Time left: ' . $timeLimit . ' seconds</p>';

// Display of the quiz form

if (!isset($_SESSION['questions_order'])) {
    $questionsOrder = range(0, count($quizQuestions) - 1);
    shuffle($questionsOrder);

    $_SESSION['questions_order'] = $questionsOrder;
} else {
    $questionsOrder = $_SESSION['questions_order'];
}   

echo '<form method="post">';
foreach ($questionsOrder as $key) {
    $question = $quizQuestions[$key];
    
    echo '<p>' . $question['question'] . '</p>';

    shuffle($question['choices']);

    foreach ($question['choices'] as $choice) {
        echo '<label><input type="radio" name="answers[' . $key . ']" value="' . $choice . '"> ' . $choice . '</label>';
    }
    echo '<br>';
}
echo '<input type="submit" value="Submit Quiz"></form>';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;

    // Validate and calculate score
    for ($i = 0; $i < count($quizQuestions); $i++) {

        if(!isset($_POST['answers'][$i])){
            $feedback[$i] = 'you did not choose the answer. The correct answer is: ' . $quizQuestions[$i]['correctAnswer'];

        }elseif (isset($_POST['answers'][$i]) && $_POST['answers'][$i] === $quizQuestions[$i]['correctAnswer']) {
            $score++;
            $feedback[$i] = 'Correct! Your answer was: ' . $quizQuestions[$i]['correctAnswer'];

        } else {
            $feedback[$i] = 'Incorrect. Your answer was: ' . $_POST['answers'][$i] . ';'  . ' The correct answer is: ' . $quizQuestions[$i]['correctAnswer'];
        }
    }
}

// Display results
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<h2>Your Quiz Results:</h2>';
    for ($i = 0; $i < count($quizQuestions); $i++) {
        echo '<p><strong>Question ' . ($i + 1) . ':</strong> ' . $feedback[$i] . '</p>';
    }
    echo '<h3>Your Score: ' . $score . '/' . count($quizQuestions) . '</h3>';
}
echo '</body>
</html>';
unset($_SESSION['questions_order']);
?>
