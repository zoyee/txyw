CREATE TABLE `ecs_questionnaire` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����',
  `keyword` varchar(100) NOT NULL COMMENT '�ؼ���',
  `keyword_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '�ؼ�������',
  `title` varchar(255) NOT NULL COMMENT '����',
  `intro` text COMMENT '������',
  `mTime` int(10) DEFAULT NULL COMMENT '�޸�ʱ��',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '����ͼƬ',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '����ʱ��',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `finish_tip` text COMMENT '������',
  `template` varchar(255) DEFAULT 'default' COMMENT '�ز�ģ��',
  `start_time` int(10) DEFAULT NULL COMMENT '��ʼʱ��',
  `end_time` int(10) DEFAULT NULL COMMENT '����ʱ��',
  `text` text NOT NULL,
  `song` tinyint(1) NOT NULL,
  `jine` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�����ʾ�����';


CREATE TABLE `ecs_questionnaire_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����',
  `intro` text COMMENT '��������',
  `is_must` tinyint(2) DEFAULT '0' COMMENT '�Ƿ����',
  `extra` text COMMENT '����',
  `type` char(50) NOT NULL DEFAULT 'radio' COMMENT '��������',
  `q_id` int(10) unsigned NOT NULL COMMENT '����id',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '�����',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�����ʾ������';

CREATE TABLE `ecs_questionnaire_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '����',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '�û�UID',
  `question_id` int(10) unsigned NOT NULL COMMENT '����id',
  `Time` int(10) unsigned DEFAULT NULL COMMENT '����ʱ��',
  `survey_id` int(10) unsigned NOT NULL COMMENT '�ʾ������id',
  `answer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='���н����';
