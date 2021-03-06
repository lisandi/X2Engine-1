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
INSERT INTO x2_dropdowns (`id`, `name`, `options`) VALUES

(100,	'Product Status',	'{"Active":"Active","Inactive":"Inactive"}'),
(101,	'Currency List',	'{"USD":"USD","EUR":"EUR","GBP":"GBP","CAD":"CAD","JPY":"JPY","CNY":"CNY","CHF":"CHF","INR":"INR","BRL":"BRL"}'),
(102,	'Lead Type',		'{"None":"None","Web":"Web","In Person":"In Person","Phone":"Phone","E-Mail":"E-Mail"}'),
(103,	'Lead Source',		'{"None":"None","Google":"Google","Facebook":"Facebook","Walk In":"Walk In"}'),
(104,	'Lead Status',		'{"Unassigned":"Unassigned","Assigned":"Assigned","Accepted":"Accepted","Working":"Working","Dead":"Dead","Rejected":"Rejected"}'),
(105,	'Sales Stage',		'{"Working":"Working","Won":"Won","Lost":"Lost"}'),
(106,	'Quote Status',		'{"Draft":"Draft","Presented":"Presented","Issued":"Issued","Won":"Won"}'),
(107,	'Campaign Type',	'{"Email":"Email","Call List":"Call List","Physical Mail":"Physical Mail"}'),
(108,	'Case Impact', 		'{"1 - Severe":"1 - Severe"," 2 - Critical":" 2 - Critical","3 - Moderate":"3 - Moderate","4 - Minor":"4 - Minor"}'),
(109,	'Case Status', 		'{"New":"New","WIP":"WIP","Waiting for response":"Waiting for response","Needs more info":"Needs more info","Escalated":"Escalated","Reopened":"Reopened","Work around provided, waiting for fix":"Work around provided, waiting for fix","Program Manager investigation":"Program Manager investigation","Closed - Resolved":"Closed - Resolved","Closed - No Response":"Closed - No Response"}'),
(110,	'Case Main Issue',	'{"Hardware":"Hardware","Software":"Software","Internet Connection":"Internet Connection","LMS":"LMS","General Request":"General Request"}'),
(111,	'Case Sub Issue', 	'{"Laptop":"Laptop","Desktop":"Desktop","WiFi":"WiFi","Loss Connection":"Loss Connection","Windows OS":"Windows OS","MS Office":"MS Office","Class Access":"Class Access","Lost Password":"Lost Password","Download\\/Upload":"Download\\/Upload","Other":"Other"}'),
(112,	'Case Origin', 		'{"Email":"Email","Web":"Web","Phone":"Phone"}'),
(113,	'Social Subtypes',	'{"Social Post":"Social Post","Link":"Link","Announcement":"Announcement","Product Info":"Product Info","Competitive Info":"Competitive Info","Confidential":"Confidential"}'),
(114,	'Invoice Status',	'{"Pending":"Pending","Issued":"Issued","Paid":"Paid","Open":"Open","Canceled":"Canceled","Other":"Other"}');
/*&*/
ALTER TABLE x2_profile CHANGE `language` language varchar(40) DEFAULT '{language}', CHANGE `timeZone` timeZone varchar(100) DEFAULT '{timezone}';
/*&*/
ALTER TABLE x2_admin CHANGE `emailFromAddr` emailFromAddr varchar(255) NOT NULL DEFAULT '{bulkEmail}';
/*&*/
INSERT INTO x2_users (firstName, lastName, username, password, emailAddress, status, lastLogin, userKey) 
        VALUES ('web','admin','admin','{adminPass}','{adminEmail}','1', '0', '{adminUserKey}');
/*&*/
INSERT INTO x2_users (firstName, lastName, username, password, emailAddress, status, lastLogin) 
        VALUES ('API','User','api','{apiKey}','{adminEmail}' ,'0', '0');
/*&*/
INSERT INTO x2_profile (fullName, username, officePhone, emailAddress, status) 
		VALUES ('Web Admin', 'admin', '831-555-5555', '{adminEmail}','1');
/*&*/
INSERT INTO x2_profile (fullName, username, officePhone, emailAddress, status)
		VALUES ('API User', 'api', '831-555-5555', '{adminEmail}','0');
/*&*/
INSERT INTO x2_social (`type`, `data`) VALUES ('motd', 'Please enter a message of the day!');
/*&*/
INSERT INTO x2_admin (timeout,webLeadEmail,emailFromAddr,currency,installDate,updateDate,quoteStrictLock,unique_id,edition,serviceCaseFromEmailAddress,serviceCaseFromEmailName,serviceCaseEmailSubject,serviceCaseEmailMessage,eventDeletionTime,eventDeletionTypes) VALUES (
	'3600',
	'{adminEmail}',
	'{bulkEmail}',
	'{currency}',
	'{time}',
	0,
	0,
	'{unique_id}',
	'{edition}',
	'{adminEmail}',
	'Tech Support',
	'Tech Support',
	'Hello {first} {last},\n\nJust wanted to check in with you about the support case you created. It is number {case}. We will get back to you as soon as possible.',
    7,
    '["record_create","record_deleted","action_reminder","action_complete","calendar_event","case_escalated","email_opened","email_sent","notif","weblead_create","web_activity","workflow_complete","workflow_revert","workflow_start"]'
);
/*&*/
UPDATE x2_profile SET `widgets`='0:1:1:1:1:0:0:0:0:0:0:0:0',
	`widgetOrder`='OnlineUsers:TimeZone:GoogleMaps:TagCloud:TwitterFeed:MessageBox:ChatBox:QuickContact:NoteBox:ActionMenu:MediaBox:DocViewer:TopSites';
/*&*/
UPDATE `x2_auth_item` SET `bizrule`="return Yii::app()->user->name === '{adminUsername}';" WHERE `name`='admin';
/*&*/
UPDATE `x2_users` SET `username`='{adminUsername}' WHERE `username`='admin';
/*&*/
UPDATE `x2_profile` SET `username`='{adminUsername}' WHERE `username`='admin';
/*&*/
UPDATE `x2_modules` SET `visible`=0
/*&*/
UPDATE `x2_modules` SET `visible`=1 WHERE `name` IN {visibleModules}