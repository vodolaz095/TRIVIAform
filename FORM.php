<?php
class FORM
{
private $fields=array();
private $clean=array();
private $submited;
private $csrf='csrf';


public function __construct($csrf=null)
    {
        if($csrf) $this->csrf=md5($csrf);
    }

public function addLabel($name,$caption=null,$value=null)
    {
        $this->fields[$name]=array(
            'type'=>'label',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>$value
        );
        return true;
    }

public function addText($name,$caption=null,$value=null,$regex='~^.*$~')
    {
        $this->fields[$name]=array(
            'type'=>'text',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>$value,
            'regex'=>$regex
        );
        return true;
    }

public function addPassword($name,$caption=null,$value=null,$regex='~^.*$~')
    {
        $this->fields[$name]=array(
            'type'=>'password',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>$value,
            'regex'=>$regex
        );
        return true;
    }

public function addTextArea($name,$caption=null,$value=null,$escape=true)
    {
        $this->fields[$name]=array(
            'type'=>'textarea',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>$value,
            'escape'=>$escape ? true : false
        );
        return true;
    }

public function addCheckBox($name,$caption=null,$value=null,$check=false)
    {
        $this->fields[$name]=array(
            'type'=>'checkbox',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>$value ? $value : '1',
            'check'=>$check ? true : false
        );
        return true;
    }

public function addDropdown($name,$caption=null,$values=null,$selected=null)
    {
        $this->fields[$name]=array(
            'type'=>'dropdown',
            'caption'=>isset($caption) ? $caption : $name,
            'value'=>is_array($values) ? $values : array($values=>$values),
            'selected'=>$selected
        );
        return true;
    }

private function filter($text)
    {
        /*
         * add some filtering functions here!
         */
        return $text;
    }

private function generateCSRF()
    {
        return md5('klojiliha'.$_SERVER['REQUEST_URI'].$_SERVER['HTTP_HOST'].$_SERVER['HTTP_USER_AGENT']);
    }

private function validateCSRF($a=null)
    {
        if($a)
            return ($a==md5('klojiliha'.$_SERVER['REQUEST_URI'].$_SERVER['HTTP_HOST'].$_SERVER['HTTP_USER_AGENT']));
        else
            return false;
    }

public function submit()
    {
        if(isset($_POST[$this->csrf]) and $this->validateCSRF($_POST[$this->csrf]))
            {
            foreach(array_keys($this->fields) as $field)
                {
                    if(isset($_POST[$field]))
                        {
                            if($this->fields[$field]['type']=='text' or $this->fields[$field]['type']=='password')
                                {
                                    if(preg_match($this->fields[$field]['regex'],$_POST[$field]))
                                        {
                                            $this->fields[$field]['value']=$_POST[$field];
                                            $this->clean[$field]=$_POST[$field];
                                        }
                                    else
                                        {
                                            $this->fields[$field]['value']=$_POST[$field];
                                            //$this->fields[$field]['error']=true;
                                        }
                                }
                            elseif($this->fields[$field]['type']=='textarea')
                                {
                                    $this->fields[$field]['value']=$_POST[$field];
                                    $this->clean[$field]=$this->filter($_POST[$field]);
                                }
                            elseif($this->fields[$field]['type']=='checkbox')
                                {
                                    if(isset($_POST[$field]))
                                        {
                                         //   $this->fields[$field]['value']=$_POST[$field];
                                            $this->fields[$field]['check']=true;
                                            $this->clean[$field]=$this->filter($_POST[$field]);
                                        }
                                }
                            elseif($this->fields[$field]['type']=='dropdown')
                                {
                                    if(isset($_POST[$field]) and in_array($_POST[$field],array_keys($this->fields[$field]['value'])))
                                        {
                                            $this->fields[$field]['selected']=$_POST[$field];
                                            $this->clean[$field]=$this->filter($_POST[$field]);
                                        }
                                }
                            else
                                {
                                    throw new Exception('Strange value in form...');
                                }
                        }
                }
            $this->submited=true;
            return true;
            }
        else
            return false;
    }

public function render($submit_text='Сохранить',$reset_text='Отмена')
    {
        ob_start();
        ?>
    <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
        <input name="<?php echo $this->csrf;?>" type="hidden" value="<?php echo $this->generateCSRF();?>">
        <table border="0" cellpadding="0" cellspacing="0">
            <?php
            foreach(array_keys($this->fields) as $field)
                {
                    if($this->fields[$field]['type']=='text' or $this->fields[$field]['type']=='password')
                        {
                            ?>
                            <?php if(isset($this->fields[$field]['error'])):?>
                            <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
                                <td colspan="2" align="center"><?php echo $this->fields[$field]['error'];?></td>
                            </tr>
    <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
<?php else: ?>
    <tr>
<?php endif;?>
                            <td width="50%" align="right"><?php echo $this->fields[$field]['caption'];?></td>
                            <td width="50%" align="left">
                                <input name="<?php echo $field;?>"
                                       type="<?php echo $this->fields[$field]['type'];?>"
                                       value="<?php echo $this->fields[$field]['value'];?>">
                            </td>
    </tr>
<?php
                        }
                    elseif($this->fields[$field]['type']=='textarea')
                        {
                            ?>
                            <?php if(isset($this->fields[$field]['error'])):?>
                            <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
                                <td colspan="2" align="center"><?php echo $this->fields[$field]['error'];?></td>
                            </tr>
    <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
<?php else: ?>
    <tr>
<?php endif;?>
                            <td colspan="2">
                                    <?php echo $this->fields[$field]['caption'];?></br>
                                <textarea rows="5" cols="50" style="width: 100%;" name="<?php echo $field;?>"><?php echo $this->fields[$field]['value'];?></textarea>
                            </td>
    </tr>
<?php
                        }
                    elseif($this->fields[$field]['type']=='checkbox')
                        {
                            ?>
                            <?php if(isset($this->fields[$field]['error'])):?>
                            <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
                                <td colspan="2" align="center"><?php echo $this->fields[$field]['error'];?></td>
                            </tr>
    <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
<?php else: ?>
    <tr>
<?php endif;?>
                            <td colspan="2" align="center">
                                <input type="checkbox" name="<?php echo $field;?>" value="<?php echo $this->fields[$field]['value'];?>" <?php if($this->fields[$field]['check']) echo ' checked="checked" ';?>><?php echo $this->fields[$field]['caption'];?>
                            </td>
    </tr>
<?php
                        }
                    elseif($this->fields[$field]['type']=='dropdown')
                        {
                            ?>
                            <?php if(isset($this->fields[$field]['error'])):?>
                            <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
                                <td colspan="2" align="center"><?php echo $this->fields[$field]['error'];?></td>
                            </tr>
    <tr class="form_error" title="<?php echo $this->fields[$field]['error'];?>">
<?php else: ?>
    <tr>
<?php endif;?>
                            <td align="right"><?php echo $this->fields[$field]['caption'];?></td>
                            <td align="left">
                                <select name="<?php echo $field;?>">
                                    <?php foreach(array_keys($this->fields[$field]['value']) as $value):?>
                                    <option value="<?php echo $value;?>"
                                        <?php if($value==$this->fields[$field]['selected']) echo ' selected="selected" ';?>
                                        >
                                        <?php echo $this->fields[$field]['value'][$value];//. ' '.$value.'='.$this->fields[$field]['selected'] ;?>
                                    </option>
                                    <?php endforeach;?>
                                </select>
                            </td>
</tr>
<?php
                        }
                    elseif($this->fields[$field]['type']=='label')
                        {
?>
<tr>
                            <td align="right"><?php echo $this->fields[$field]['caption'];?>:&nbsp;&nbsp;</td>
                            <td align="left"><?php echo $this->fields[$field]['value'];?></td>
</tr>
<?php
                        }


                }
            ?>
            <tr>
                <td align="right"><?php if($submit_text) echo '<input type="submit" value="'.$submit_text.'">';?></td>
                <td align="left"><?php if($reset_text) echo '<input type="reset" value="'.$reset_text.'">';?></td>
            </tr>
        </table>
    </form>
    <?php
        return ob_get_clean();
    }


public function setError($name,$error_text)
    {
        if(isset($this->fields[$name]))
            {
                $this->fields[$name]['error']=$error_text;
            }
        else
            {
                return false;
            }
    }

public function __toString()
    {
        return $this->render();
    }

public function getClean($name=null)
    {
        if($this->submited)
            if($name)
                {
                return isset($this->clean[$name]) ? $this->clean[$name] : false;
                }
            else
                {
                return $this->clean;
                }

        else
            return false;
    }
}

