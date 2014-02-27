DELIMITER $$

USE `vaspun`$$

DROP FUNCTION IF EXISTS `updOxWall`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `updOxWall`(smfPid INT) RETURNS INT(11)
    READS SQL DATA
BEGIN
  
      DECLARE owPid, owTid, owUid, chk INT;
       
      INSERT sync_log(what) VALUES (CONCAT("id SMF=",smfPid));
      
      SELECT id_msg INTO chk FROM smf_messages WHERE id_msg=smfPid;
      IF chk IS NULL THEN 
         INSERT sync_log(what) VALUES ("E! message not found in SMF");
         SET owPid:=-1;      
      ELSE
	      /* caut sa vad daca a mai fost importat*/
	      SELECT ow_id INTO owPid FROM smf_messages WHERE id_msg=smfPid; 
	     
	      IF owPid IS NULL THEN 
		      INSERT sync_log(what) VALUES ("nu am gasit -> inserez");      
	      
		      INSERT INTO ow_forum_post (topicId, userId, `text`, createStamp, isFromImport) 
		      SELECT t.ow_id AS topicId, u.ow_id AS userId, m.body AS `text`, m.poster_time AS createStamp , 1 AS isFromImport
		      FROM smf_messages m
		      LEFT JOIN smf_topics t ON t.id_topic=m.id_topic
		      LEFT JOIN smf_members u ON u.id_member = m.id_member
		      WHERE m.id_msg=smfPid;
		      
		      SET owPid = LAST_INSERT_ID();
	      ELSE
		 INSERT sync_log(what) VALUES (CONCAT("am gasit -> fac update pentru OW=",owPid));
			UPDATE ow_forum_post 
			SET TEXT=(SELECT body FROM smf_messages WHERE id_msg=smfPid)
			WHERE id = owPid;
	      
	      END IF;
		
	      /*daca am inserat sau am updatat*/  
	      IF owPid != 0 THEN
		 INSERT sync_log(what) VALUES (CONCAT("update ow_id OW=",owPid));
		 
		 UPDATE smf_messages SET ow_id = owPid WHERE id_msg = smfPid;
		  
		 SELECT m.topicid, m.userid 
		 INTO owtid, owuid
		 FROM ow_forum_post m
		 WHERE m.id = owPid;
	      
		 INSERT sync_log(what) VALUES (CONCAT("update for values found OW: topic=",IFNULL(owtid,-1)," user=",IFNULL(owuid,-1)));
		 
		 UPDATE ow_forum_read_topic SET postid = owPid WHERE topicid = owtid AND userid=owuid;
	      ELSE
		INSERT sync_log(what) VALUES ("E! insert");      
	      END IF;
      END IF;      
      RETURN owPid;
    END$$

DELIMITER ;