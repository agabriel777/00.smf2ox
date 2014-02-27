DELIMITER $$

USE `vaspun`$$

DROP FUNCTION IF EXISTS `updOxWall`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `updOxWall`(smfPid INT) RETURNS INT(11)
    READS SQL DATA
BEGIN
	DECLARE owPid, owTid, owUid INT;
	
      DELETE FROM sync_log;	
      
      INSERT sync_log(what) VALUES (CONCAT("id SMF=",smfPid));
      
      INSERT INTO ow_forum_post (topicId, userId, `text`, createStamp, isFromImport) 
      SELECT t.ow_id AS topicId, u.ow_id AS userId, m.body AS `text`, m.poster_time AS createStamp , 1 AS isFromImport
      FROM smf_messages m
      LEFT JOIN smf_topics t ON t.id_topic=m.id_topic
      LEFT JOIN smf_members u ON u.id_member = m.id_member
      WHERE m.id_msg=smfPid;
       
      SET owPid = LAST_INSERT_ID();
      
      
        
      IF owPid != 0 THEN
         INSERT sync_log(what) VALUES (CONCAT("id inserat OW=",owPid));
         
         UPDATE smf_messages SET ow_id = owPid WHERE id_msg = smfPid;
          
         SELECT m.topicid, m.userid 
         INTO owtid, owuid
         FROM ow_forum_post m
         WHERE m.id = owPid;
      
         INSERT sync_log(what) VALUES (CONCAT("found: topic=",owtid," user=",owuid));
	 
	 UPDATE ow_forum_read_topic SET postid = owPid WHERE topicid = owtid AND userid=owuid;
      ELSE
        INSERT sync_log(what) VALUES ("E! insert");      
      END IF;
      
      RETURN owPid;
    END$$

DELIMITER ;