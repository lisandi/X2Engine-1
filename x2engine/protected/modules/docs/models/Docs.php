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

// Yii::import('application.models.X2Model');

/**
 * This is the model class for table "x2_docs".
 * 
 * @package X2CRM.modules.docs.models
 */
class Docs extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @return Docs the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'x2_docs';
	}
	
	public function behaviors() {
		return array(
			'X2LinkableBehavior'=>array(
				'class'=>'X2LinkableBehavior',
				'baseRoute'=>'/docs',
			),
            'ERememberFiltersBehavior' => array(
				'class'=>'application.components.ERememberFiltersBehavior',
				'defaults'=>array(),
				'defaultStickOnClear'=>false
			)
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, text, createdBy', 'required'),
			array('createDate, lastUpdated', 'numerical', 'integerOnly'=>true),
			array('name, editPermissions, subject', 'length', 'max'=>100),
			array('createdBy', 'length', 'max'=>60),
			array('updatedBy', 'length', 'max'=>40),
			array('type', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, text, createdBy, createDate, updatedBy, lastUpdated, editPermissions, type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => Yii::t('docs','ID'),
			'type' => Yii::t('docs','Doc Type'),
			'name' => Yii::t('docs','Title'),
			'text' => Yii::t('docs','Text'),
			'createdBy' => Yii::t('docs','Created By'),
			'createDate' => Yii::t('docs','Create Date'),
			'updatedBy' => Yii::t('docs','Updated By'),
			'lastUpdated' => Yii::t('docs','Last Updated'),
			'editPermissions' => Yii::t('docs','Edit Permissions'),
            'subject'=>Yii::t('docs','Subject'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type);
		$criteria->compare('subject',$this->subject,true);
        $criteria->compare('name',$this->name,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('createdBy',$this->createdBy,true);
		$criteria->compare('createDate',$this->createDate);
		$criteria->compare('updatedBy',$this->updatedBy,true);
		$criteria->compare('lastUpdated',$this->lastUpdated);
		$criteria->compare('editPermissions',$this->editPermissions,true);

		return new SmartDataProvider(get_class($this),array(
			'sort' => array(
				'defaultOrder' => 'lastUpdated DESC, id DESC',
			),
			'pagination' => array(
				'pageSize' => ProfileChild::getResultsPerPage(),
			),
			'criteria' => $criteria,
		));
	}

	public static function replaceVariables($str,&$model,$vars=array()) {
		$matches = array();
		
		if(preg_match_all('/{\w+}/',$str,$matches)) {		// loop through the things (email body)
			foreach($matches[0] as &$match) {
				$match = substr($match,1,-1);	// remove { and }
				
				if(isset($vars[$match])) {
					$str = preg_replace('/{'.$match.'}/',$vars[$match],$str);
				} elseif($model->hasAttribute($match)) {
					$value = $model->renderAttribute($match,false,true);	// get the correctly formatted attribute
					$str = preg_replace('/{'.$match.'}/',$value,$str);
				}
			}
		}
		return $str;
	}
}