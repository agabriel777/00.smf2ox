DELIMITER $$

USE `vaspun`$$

DROP FUNCTION IF EXISTS `markOxWall`$$

CREATE DEFINER=`root`@`localhost` FUNCTION `markOxWall`(uid INT, tid INT, pid INT) RETURNS INT(11)
    READS SQL DATA
BEGIN
      DECLARE owuid, owtid, owpid INT;
      
      SELECT ow_id INTO owuid FROM smf_members WHERE id_member = uid;
      SELECT ow_id INTO owtid FROM smf_topics WHERE id_topic = tid;
      SELECT MAX(ow_id) 
      INTO owpid 
      FROM smf_messages m
      WHERE m.id_msg<pid 
      AND m.id_topic=tid;
      
       UPDATE ow_forum_read_topic SET postid = owpid WHERE topicid=owtid AND userid=owuid;
      RETURN owpid;
    END$$

DELIMITER ;