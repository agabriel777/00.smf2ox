DELIMITER $$

USE `vaspun`$$

DROP FUNCTION IF EXISTS `getLastReadInSmf`$$

CREATE DEFINER=`vaspun`@`localhost` FUNCTION `getLastReadInSmf`(puid INT, ptid INT, smfuid INT , smftid INT) RETURNS INT(11)
    READS SQL DATA
BEGIN
      DECLARE last_read INT;

      SELECT id_msg
      INTO last_read
      FROM smf_log_topics l
      WHERE id_member = smfuid 
      AND id_topic = smftid; 
      
      IF last_read IS NOT NULL THEN 
      
        SELECT MAX(ow_id)
        INTO last_read
          FROM smf_messages
          WHERE id_topic = (SELECT id_topic FROM smf_topics WHERE ow_id=ptid) AND id_msg < last_read;
          
      ELSE 
 	SELECT MAX(ow_id)
	INTO last_read
	  FROM vaspun.smf_messages m 
	 WHERE m.id_topic = smftid 
	   AND m.id_msg<=(SELECT id_msg FROM smf_log_mark_read mr WHERE mr.id_board=m.id_board AND mr.id_member = smfuid);
     
      END IF;
       RETURN last_read;
    END$$

DELIMITER ;