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

/**
 * This is the model class for table "x2_imports".
 * @package X2CRM.models
 */
class Events extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Imports the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'x2_events';
	}
    
    public function relations(){
        $relationships=array();
        $relationships=array_merge($relationships,array(
            'children'=>array(self::HAS_MANY,'Events','associationId','condition'=>'children.associationType="Events"'), 
        ));
        return $relationships;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			
        );
	}
    
    protected function parseModelName($model){
        $model=ucfirst($model);
        switch($model){
            case 'Contacts':
                $model='contact';
                break;
            case 'Actions':
                $model='action';
                break;
            case 'Accounts':
                $model='account';
                break;
            case 'Opportunities':
                $model='opportunity';
                break;
            case 'Campaign':
                $model='marketing campaign';
                break;
            case 'Services':
                $model='service case';
                break;
            case 'Docs':
                $model='document';
                break;
            default:
                $model=strtolower($model);
        }
        return Yii::t('app',$model);
    }
    
    public function getText($truncated=false){
        $text="";
        $authorText="";
        $authorRecord=X2Model::model('User')->findByAttributes(array('username'=>$this->user));
        if(isset($authorRecord)){
            if(Yii::app()->user->getName()==$this->user){
                $author=Yii::t('app','You');
            }else{
                $author=$authorRecord->name;
            }
            $authorText=CHtml::link($author,array('profile/view','id'=>$authorRecord->id))." ";
        }
        switch($this->type){
            case 'notif':
                $parent=X2Model::model('Notification')->findByPk($this->associationId);
                if(isset($parent)){
                    $text=$parent->getMessage();
                }else{
                    $text=Yii::t('app',"Notification not found");
                }
                break;
            case 'record_create':
                $actionFlag=false;
                if(class_exists($this->associationType)){
                    if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                        if($this->associationType=='Actions'){
                            $action=X2Model::model('Actions')->findByPk($this->associationId);
                            if(isset($action) && strcasecmp($action->associationType,'Contacts')===0){
                                $actionFlag=true;
                            }
                        }
                        if($actionFlag){
                            $text=$authorText.Yii::t('app',"created a new {actionLink} associated with the contact {contactLink}",
                                    array('{actionLink}'=>CHtml::link($this->parseModelName($this->associationType),Yii::app()->controller->createUrl('/actions/view?id='.$this->associationId)),
                                        '{contactLink}'=>X2Model::getModelLink($action->associationId,ucfirst($action->associationType))));
                        }else{
                            if(!empty($authorText)){
                                $text=$authorText.Yii::t('app',"created a new {modelName}, {modelLink}",
                                    array('{modelName}'=>$this->parseModelName($this->associationType),
                                          '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)));
                            }else{
                                $text=Yii::t('app',"A new {modelName}, {modelLink}, has been created.",
                                    array('{modelName}'=>$this->parseModelName($this->associationType),
                                          '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)));
                            }
                        }
                   }else{
                        $deletionEvent=X2Model::model('Events')->findByAttributes(array('type'=>'record_deleted','associationType'=>$this->associationType,'associationId'=>$this->associationId));
                        if(isset($deletionEvent)){
                            $text=$authorText.Yii::t('app',"created a new {modelName}, {deletionText}. It has been deleted.",array(
                                '{modelName}'=>$this->parseModelName($this->associationType),
                                '{deletionText}'=>$deletionEvent->text,
                            ));
                        }else{
                            $text=$authorText.Yii::t('app',"created a new {modelName}, but it could not be found.",array(
                                '{modelName}'=>$this->parseModelName($this->associationType)
                            ));
                        }
                    }
                }
                break;
            case 'weblead_create':
                if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                    $text=Yii::t('app',"A new web lead has come in: {modelLink}",array(
                        '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                    ));
                }else{
                        $deletionEvent=X2Model::model('Events')->findByAttributes(array('type'=>'record_deleted','associationType'=>$this->associationType,'associationId'=>$this->associationId));
                        if(isset($deletionEvent)){
                            $text=Yii::t('app',"A new web lead has come in: {deletionText}. It has been deleted.",array(
                                '{deletionText}'=>$deletionEvent->text
                            ));
                        }else{
                            $text=Yii::t('app',"A new web lead has come in, but it could not be found.");
                        }
                    }
                break;
            case 'record_deleted':
                if(class_exists($this->associationType) && (!$truncated && (Yii::app()->params->profile->language!='en' && !empty(Yii::app()->params->profile->language)) || (strpos($this->associationType,'A')!==0 && strpos($this->associationType,'E')!==0 && strpos($this->associationType,'I')!==0 && strpos($this->associationType,'O')!==0 && strpos($this->associationType,'U')!==0))){
                    $text=$authorText.Yii::t('app',"deleted a {modelType}, {text}",array(
                        '{modelType}'=>$this->parseModelName($this->associationType),
                        '{text}'=>$this->text
                    ));
                }else{
                    $text=$authorText.Yii::t('app',"deleted an {modelType}, {text}",array(
                        '{modelType}'=>$this->parseModelName($this->associationType),
                        '{text}'=>$this->text
                    ));
                }
                break;
            case 'workflow_start':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $record=X2Model::model(ucfirst($action->associationType))->findByPk($action->associationId);
                    if(isset($record)){
                        $stages=Workflow::getStages($action->workflowId);
                        if(isset($stages[$action->stageNumber-1])){
                            $text=$authorText.Yii::t('app','started the workflow stage "{stage}" for the {modelName} {modelLink}',array(
                                '{stage}'=>$stages[$action->stageNumber-1],
                                '{modelName}'=>$this->parseModelName($action->associationType),
                                '{modelLink}'=>X2Model::getModelLink($action->associationId,$action->associationType)
                            ));
                        }else{
                            $text=$authorText.Yii::t('app',"started a workflow stage for the {modelName} {modelLink}, but the workflow stage could not be found.",array(
                                '{modelName}'=>$this->parseModelName($action->associationType),
                                '{modelLink}'=>X2Model::getModelLink($action->associationId,$action->associationType)
                            ));
                        }
                    }else{
                        $text=$authorText.Yii::t('app',"started a workflow stage, but the associated {modelName} was not found.",array(
                            '{modelName}'=>$this->parseModelName($action->associationType)
                        ));
                    }
                }else{
                    $text=$authorText.Yii::t('app',"started a workflow stage, but the workflow record could not be found.");
                }
                break;
            case 'workflow_complete':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $record=X2Model::model(ucfirst($action->associationType))->findByPk($action->associationId);
                    if(isset($record)){
                        $stages=Workflow::getStages($action->workflowId);
                        if(isset($stages[$action->stageNumber-1])){
                            $text=$authorText.Yii::t('app','completed the workflow stage "{stageName}" for the {modelName} {modelLink}',array(
                                '{stageName}'=>$stages[$action->stageNumber-1],
                                '{modelName}'=>$this->parseModelName($action->associationType),
                                '{modelLink}'=>X2Model::getModelLink($action->associationId,$action->associationType)
                            ));
                        }else{
                            $text=$authorText.Yii::t('app',"completed a workflow stage for the {modelName} {modelLink}, but the workflow stage could not be found.",array(
                                '{modelName}'=>$this->parseModelName($action->associationType),
                                '{modelLink}'=>X2Model::getModelLink($action->associationId,$action->associationType)
                            ));
                        }
                    }else{
                        $text=$authorText.Yii::t('app',"completed a workflow stage, but the associated {modelName} was not found.",array(
                            '{modelName}'=>$this->parseModelName($action->associationType)
                        ));
                    }
                }else{
                    $text=$authorText.Yii::t('app',"completed a workflow stage, but the workflow record could not be found.");
                }
                break;
            case 'workflow_revert':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $record=X2Model::model(ucfirst($action->associationType))->findByPk($action->associationId);
                    if(isset($record)){
                        $stages=Workflow::getStages($action->workflowId);
                        $text=$authorText.Yii::t('app','reverted the workflow stage "{stageName}" for the {modelName} {modelLink}',array(
                            '{stageName}'=>$stages[$action->stageNumber-1],
                            '{modelName}'=>$this->parseModelName($action->associationType),
                            '{modelLink}'=>X2Model::getModelLink($action->associationId,$action->associationType)
                        ));
                    }else{
                        $text=$authorText.Yii::t('app',"reverted a workflow stage, but the associated {modelName} was not found.",array(
                            '{modelName}'=>$this->parseModelName($action->associationType)
                        ));
                    }
                }else{
                    $text=$authorText.Yii::t('app',"reverted a workflow stage, but the workflow record could not be found.");
                }
                break;
            case 'feed':
                $authorRecord = X2Model::model('User')->findByAttributes(array('username'=>$this->user));
                if(Yii::app()->user->getName()==$this->user){
                    $author=Yii::t('app','You');
                }else{
                    $author = $authorRecord->name;
                }
                if($authorRecord->id != $this->associationId && $this->associationId != 0) {
                    $temp=Profile::model()->findByPk($this->associationId);
                    if(Yii::app()->user->getId()==$temp->id){
                        $recipient=Yii::t('app','You');
                    }else{
                        $recipient=$temp->fullName;
                    }
                    $modifier=' &raquo; ';
                } else {
                    $recipient='';
                    $modifier='';
                }
                $text=CHtml::link($author,array('profile/view','id'=>$authorRecord->id)).$modifier.CHtml::link($recipient,array('profile/view','id'=>$this->associationId)).": ".$this->text;
                break;
            case 'email_sent':
                if(class_exists($this->associationType)){
                    if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                        $text=$authorText.Yii::t('app', "sent an email to the {transModelName} {modelLink}",array(
                            '{transModelName}'=>$this->parseModelName($this->associationType),
                            '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                        ));
                    }else{
                        $deletionEvent=X2Model::model('Events')->findByAttributes(array('type'=>'record_deleted','associationType'=>$this->associationType,'associationId'=>$this->associationId));
                        if(isset($deletionEvent)){
                            $text=$authorText.Yii::t('app',"sent an email to a {transModelName}, but that record has been deleted.",array(
                                '{transModelName}'=>$this->parseModelName($this->associationType)
                            ));
                        }else{
                            $text=$authorText.Yii::t('app',"sent an email to a {transModelName}, but that record could not be found.",array(
                                '{transModelName}'=>$this->parseModelName($this->associationType)
                            ));
                        }
                     }
                }
                break;
            case 'email_opened':
                if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                    $text=X2Model::getModelLink($this->associationId,$this->associationType).Yii::t('app'," has opened an email!");
                }else{
                    $text=Yii::t('app',"A contact has opened an email, but that contact cannot be found.");
                }
                break;
            case 'web_activity':
                if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                    $text=X2Model::getModelLink($this->associationId,$this->associationType)." ".Yii::t('app',"is currently on your website!");
                }else{
                    $text=Yii::t('app',"A contact was on your website, but that contact cannot be found.");
                }
                break;
            case 'case_escalated':
                if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                    $case=X2Model::model($this->associationType)->findByPk($this->associationId);
                    $text=$authorText.Yii::t('app',"escalated service case {modelLink} to {userLink}",array(
                        '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType),
                        '{userLink}'=>User::getUserLinks($case->escalatedTo)
                    ));
                }else{
                    $text=$authorText.Yii::t('app',"escalated a service case but that case could not be found.");
                }
                break;
            case 'calendar_event':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $text=Yii::t('app',"{calendarText} event: {actionDescription}",array(
                        '{calendarText}'=>CHtml::link(Yii::t('calendar','Calendar'),array('calendar/index')),
                        '{actionDescription}'=>$action->actionDescription
                    ));
                }else{
                    $text=Yii::t('app',"{calendarText} event: event not found.",array(
                        '{calendarText}'=>CHtml::link(Yii::t('calendar','Calendar'),array('calendar/index')),
                    ));
                }
                break;
            case 'action_reminder':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $text=Yii::t('app',"Reminder! The following action is due now: {transModelLink}",array(
                        '{transModelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                    ));
                }else{
                    $text=Yii::t('app',"An action is due now, but the record could not be found.");
                }
                break;
            case 'action_complete':
                $action=X2Model::model('Actions')->findByPk($this->associationId);
                if(isset($action)){
                    $text=$authorText.Yii::t('app',"completed the following action: {actionDescription}",array(
                        '{actionDescription}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                    ));
                }else{
                    $text=$authorText.Yii::t('app',"completed an action, but the record could not be found.");
                }
                break;
            case 'doc_update':
                $text=$authorText.Yii::t('app','updated a document, {docLink}',array(
                    '{docLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                ));
                break;
			case 'email_from':
				if(class_exists($this->associationType)){
                    if(count(X2Model::model($this->associationType)->findAllByPk($this->associationId))>0){
                        $text=$authorText.Yii::t('app', "received an email from a {transModelName}, {modelLink}",array(
							'{transModelName}'=>$this->parseModelName($this->associationType),
                            '{modelLink}'=>X2Model::getModelLink($this->associationId,$this->associationType)
                        ));
                    }else{
                        $deletionEvent=X2Model::model('Events')->findByAttributes(array('type'=>'record_deleted','associationType'=>$this->associationType,'associationId'=>$this->associationId));
                        if(isset($deletionEvent)){
                            $text=$authorText.Yii::t('app',"received an email from a {transModelName}, but that record has been deleted.",array(
                                '{transModelName}'=>$this->parseModelName($this->associationType)
                            ));
                        }else{
                            $text=$authorText.Yii::t('app',"received an email from a {transModelName}, but that record could not be found.",array(
                                '{transModelName}'=>$this->parseModelName($this->associationType)
                            ));
                        }
                     }
                }
				
				break;
            default:
                $text=$authorText.$this->text;
                break;
        }
        if($truncated && strlen($text) > 250){
            $text=substr($text,0,250)."...";
        }
        return $text;
    }
    
    public static function parseType($type){
        switch($type){
            case 'feed':
                $type="Social Posts";
                break;
            case 'record_create':
                $type='Records Created';
                break;
            case 'record_deleted':
                $type='Records Deleted';
                break;
            case 'action_reminder':
                $type='Action Reminders';
                break;
            case 'action_complete':
                $type='Actions Completed';
                break;
            case 'calendar_event':
                $type='Calendar Events';
                break;
            case 'case_escalated':
                $type='Cases Escalated';
                break;
            case 'email_opened':
                $type='Emails Opened';
                break;
            case 'email_sent':
                $type='Emails Sent';
                break;
            case 'notif':
                $type='Notifications';
                break;
            case 'weblead_create':
                $type='Webleads Created';
                break;
            case 'web_activity':
                $type='Web Activity';
                break;
            case 'workflow_complete':
                $type='Workflow Complete';
                break;
            case 'workflow_revert':
                $type='Workflow Reverted';
                break;
            case 'workflow_start':
                $type='Workflow Started';
                break;
            case 'doc_update':
                $type="Doc Updates";
                break;
			case 'email_from':
				$type = 'Email Received';
				break;
            default:
                break;
        }
        return Yii::t('app',$type);
    }
    
    public static function getEvents($id, $timestamp, $user=null, $maxTimestamp=null){
        if(is_null($maxTimestamp)){
            $maxTimestamp=time();
        }
        if(is_null($user)){
            $user=Yii::app()->user->getName();
        }
        $criteria=new CDbCriteria();
        $parameters=array('order'=>'timestamp DESC, id DESC');
        $condition=isset($_SESSION['feed-condition'])?$_SESSION['feed-condition']." AND timestamp < $maxTimestamp AND (type!='action_reminder' OR user='$user')  AND (type!='notif' OR user='$user') AND (id > $id OR timestamp > $timestamp)":'(id > '.$id.' OR timestamp > '.$timestamp.') AND timestamp <= '.$maxTimestamp.'  AND type!="comment"'." AND (type!='action_reminder' OR user='$user') AND (type!='notif' OR user='".Yii::app()->user->getName()."') AND (visibility=1 OR associationId='".Yii::app()->user->getId()."' OR user='".Yii::app()->user->getName()."')";
        $parameters['condition']=$condition;
        $criteria->scopes=array('findAll'=>array($parameters));
        $events=X2Model::model('Events')->findAll($criteria);
        return array(
            'events'=>$events,
        );
    }
    
    protected function beforeSave(){
        if(empty($this->timestamp))
            $this->timestamp=time();
        $this->lastUpdated=time();
        if(!empty($this->user) && $this->isNewRecord){
            $eventsData=X2Model::model('EventsData')->findByAttributes(array('type'=>$this->type,'user'=>$this->user));
            if(isset($eventsData)){
                $eventsData->count++;
            }else{
                $eventsData=new EventsData;
                $eventsData->user=$this->user;
                $eventsData->type=$this->type;
                $eventsData->count=1;
            }
            $eventsData->save();
        }
        if($this->type=='record_deleted'){
            $this->text=preg_replace('/(<script(.*?)>|<\/script>)/','',$this->text);
        }
        return parent::beforeSave();
    }
    
    protected function beforeDelete(){
        if(!empty($this->children)){
            foreach($this->children as $child){
                $child->delete();
            }
        }
        return parent::beforeDelete();
    }
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => Yii::t('admin','ID'),
			'type' => Yii::t('admin','Type'),
			'text' => Yii::t('admin','Text'),
			'associationType' => Yii::t('admin','Association Type'),
            'associationId' => Yii::t('admin','Association ID'),
		);
	}

}
