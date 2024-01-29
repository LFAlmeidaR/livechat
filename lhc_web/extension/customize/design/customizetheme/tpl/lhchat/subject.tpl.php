<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Set a subject')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

    <div role="alert" class="alert alert-info alert-dismissible fade show">
        <div id="subject-message-<?php echo $chat->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Choose a subject')?></div>
    </div>

    <?php
        $subjects = erLhAbstractModelSubjectDepartment::getList(array('customfilter' => array('(dep_id = ' . (int)$chat->dep_id . ' OR dep_id = 0)')));
        $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        $selectedSubjects = array();
        foreach ($subjectsChat as $subject) {
            $selectedSubjects[] = $subject->subject_id;
        }

        $sortedSubjects = array();
        foreach ($subjects as $subject) {
            $sortedSubjects[] = erLhAbstractModelSubject::fetch($subject->subject_id);
        }

        uasort($sortedSubjects, function($a, $b) {
            return strcasecmp($a->name, $b->name);
        });

        $subjects = $sortedSubjects;
        $aux = 1;
?>  
    <?php foreach($subjects as $id => $subject) : ?>
        <?php switch ((int)$subject->name) {
            case 1:
                $st = 'color:#1b5e26';
                $row = $aux == 1 ? '<div class="row" style="border: 1px solid #1b5e26">' : '';
                $aux=2;
            break;
            case 2:
                $st = 'color:#dd5f36';
                $row = $aux == 2 ? '</div><br><div class="row" style="border: 1px solid #dd5f36">' : '';
                $aux = 3;
            break;
            case 3:
                $st = 'color:#1e3de6';
                $row = $aux == 3 ? '</div><br><div class="row" style="border: 1px solid #1e3de6">' : '';
                $aux = 4;
            break;
            case 4:
                $st = 'color:#72249e';
                $row = $aux == 4 ? '</div><br><div class="row" style="border: 1px solid #72249e">' : '';
                $aux = 5;
            break;
            case 5:
                $st = 'color:#4c4f56';
                $row = $aux == 5 ? '</div><br><div class="row" style="border: 1px solid #4c4f56">' : '';
                $aux = 6;
            break;
            default:
                $st = 'color:black';
                $row = $aux == 6 ? '</div><br><div class="row" style="border: 1px solid black">' : '';
		$aux = 7;           
 break;
            
        } echo $row;?>        <div class="col-3"><label style="<?php echo $st ?>"><input type="checkbox" onchange="lhinst.setSubject($(this),<?php echo $chat->id?>)" name="subject" value="<?php echo $subject->id?>" <?php if (in_array($subject->id,$selectedSubjects)) : ?>checked="checked"<?php endif?> ><?php echo htmlspecialchars($subject)?></label></div>
    <?php endforeach; ?>    
    </div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>