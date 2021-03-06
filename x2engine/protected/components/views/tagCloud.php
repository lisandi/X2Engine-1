<?php
/*********************************************************************************
 * The X2CRM by X2Engine Inc. is free software. It is released under the terms of 
 * the following BSD License.
 * http://www.opensource.org/licenses/BSD-3-Clause
 * 
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95067 USA
 * 
 * Company website: http://www.x2engine.com 
 * Community and support website: http://www.x2community.com 
 * 
 * Copyright (C) 2011-2012 by X2Engine Inc. www.X2Engine.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this 
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this 
 *   list of conditions and the following disclaimer in the documentation and/or 
 *   other materials provided with the distribution.
 * - Neither the name of X2Engine or X2CRM nor the names of its contributors may be 
 *   used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************/

$justMeUrl = $this->controller->createUrl('/site/toggleShowTags', array('tags'=>'justMe'));
$allUsersUrl = $this->controller->createUrl('/site/toggleShowTags', array('tags'=>'allUsers'));?><span style="float:left"><?php
echo CHtml::ajaxLink(Yii::t('app','Just Me'), $justMeUrl,array('success'=>'function(response) { $("#myTags").show(); $("#allTags").hide(); } '))." | ".CHtml::ajaxLink(Yii::t('app','All Users'), $allUsersUrl,array('success'=>'function() { $("#allTags").show(); $("#myTags").hide(); }'))."<br />";
?></span><span style="float:right"><a id="tag-hint" href="#" style="text-decoration:none;">[?]</a></span> <br><br>
<div id="myTags" <?php echo ($showAllUsers? 'style="display:none;"' : ''); ?>>
<?php
foreach($myTags as &$tag) {
	echo '<span style="position:relative;" class="tag hide" tag-name="'.substr($tag['tag'],1).'">'.CHtml::link($tag['tag'],array('/search/search?term=%23'.substr($tag['tag'],1)), array('class'=>'x2-link x2-tag')).'</span>';
}
?>
</div>

<div id="allTags"  <?php echo ($showAllUsers? '' : 'style="display:none;"'); ?>>
<?php
foreach($allTags as &$tag) {
	echo '<span style="position:relative;" class="tag hide" tag-name="'.substr($tag['tag'],1).'">'.CHtml::link($tag['tag'],array('/search/search?term=%23'.substr($tag['tag'],1)), array('class'=>'x2-link x2-tag')).' </span>';
}
?>
</div>
<script>
    $('.hide').mouseenter(function(e){
        e.preventDefault();
        var tag=$(this).attr('tag-name');
        var elem=$(this);
        var content='<span style="position:absolute;right:1px;top:1px;;background-color:#F0F0F0;" class="hide-link-span"><a href="#" class="hide-link" style="color:#06C;">[x]</a></span>';
        $(content).hide().delay(1500).appendTo($(this)).fadeIn(500);
        $('.hide-link').click(function(e){
           e.preventDefault();
           $.ajax({
              url:'<?php echo CHtml::normalizeUrl(array('/profile/hideTag')); ?>'+'?tag='+tag,
              success:function(){
                  $(elem).closest('.tag').fadeOut(500);
              }
           });
        });
    }).mouseleave(function(){
        $('.hide-link-span').remove();
    });
    $('#tag-hint').qtip({
       position:{'my':'top right','at':'bottom left'}, 
       content:'Pressing the X button on a tag will hide it from this widget. Hidden tags can be restored from your Preferences page.'
    });
</script>