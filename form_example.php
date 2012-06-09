<html>
<head>
    <title>Form Test</title>
    <style type="text/css">
        
    </style>
</head>
<body>
<?php
require_once 'FORM.php';
$form1=new FORM('form1_csrt_key');
$form1->addText('Text','Text');
$form1->addTextArea('long_text','Long text');
$form1->addCheckBox('checkbox1','Checkbox',1);
$form1->addCheckBox('checkbox2','Selected checkbox',1,true);
$form1->addLabel('Label','Label','Test');
$form1->addPassword('password','Password');
if($form1->submit())
    {
        echo '<p>Form 1 submited</p>';
        echo '<pre>';
        print_r($form1->getClean());
        echo '</pre>';
    }
echo '<h1>Form1</h1>';
echo $form1->render('Save','Cancel');

$form2=new FORM('form2_csrt_key');
$form2->addText('digit1','Single digit from 0 to 9','','~^\d$~');
$form2->addText('digit2','Single digit from 0 to 9. But NOT 5','','~^\d$~');
if($form2->submit())
    {
        if($form2->getClean('digit2')==5)
            $form2->setError('digit2','NOT 5 PLEASE!');
        echo '<p>Form 2 submited</p>';
        echo '<pre>';
        print_r($form2->getClean());
        echo '</pre>';
    }
echo '<h1>Form2</h1>';
echo $form2->render('Save','Cancel');
?>
</body>
</html>

