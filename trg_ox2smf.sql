DELIMITER $$

USE `vaspun`$$

DROP TRIGGER /*!50032 IF EXISTS */ `ow_forum_post_after_insert`$$

CREATE /*!50017 DEFINER = 'root'@'localhost' */ TRIGGER `ow_forum_post_after_insert` AFTER INSERT 
ON `ow_forum_post` FOR EACH ROW 
BEGIN
  DECLARE v_ow_id INTEGER ;
  DECLARE v_id_topic INTEGER ;
  DECLARE v_id_board INTEGER ;
  DECLARE v_id_member INTEGER ;
  DECLARE v_subject VARCHAR (255) ;
  DECLARE v_poster_name VARCHAR (255) ;
  DECLARE v_poster_email VARCHAR (255) ;
  DECLARE v_body MEDIUMTEXT ;
  
IF NEW.isFromImport = 0 THEN  
  
  SET v_ow_id = NEW.id ;
  SET v_body = NEW.text ;
  SELECT 
    id_topic,
    id_board INTO v_id_topic,
    v_id_board 
  FROM
    smf_topics t 
  WHERE t.ow_id = NEW.topicID ;
  SELECT 
    'subject' INTO v_subject 
  FROM
    smf_messages 
  WHERE id_topic = v_id_topic 
  LIMIT 1 ;
  SELECT 
    id_member,
    member_name,
    email_address INTO v_id_member,
    v_poster_name,
    v_poster_email 
  FROM
    smf_members 
  WHERE ow_id = NEW.userId ;
  IF v_id_topic IS NOT NULL 
  AND v_id_board IS NOT NULL 
  AND v_id_memeber IS NOT NULL 
  THEN 
  INSERT INTO `vaspun`.`smf_messages` (
    `id_topic`,
    `id_board`,
    `poster_time`,
    `id_member`,
    `id_msg_modified`,
    `subject`,
    `poster_name`,
    `poster_email`,
    `poster_ip`,
    `smileys_enabled`,
    `modified_time`,
    `modified_name`,
    `body`,
    `icon`,
    `approved`,
    `gpbp_score`,
    `ow_id`
  ) 
  VALUES
    (
      v_id_topic,
      v_id_board,
      NEW.createStamp,
      v_id_member,
      0,
      v_subject,
      v_poster_name,
      v_poster_email,
      0,
      1,
      0,
      '',
       v_body,
      'xx',
      1,
      0,
      v_ow_id
    ) ;
  END IF ;
END IF;
END ;
$$

DELIMITER ;